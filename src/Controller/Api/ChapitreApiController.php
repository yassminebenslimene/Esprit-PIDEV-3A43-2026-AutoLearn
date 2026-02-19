<?php

namespace App\Controller\Api;

use App\Entity\GestionDeCours\Chapitre;
use App\Entity\GestionDeCours\ChapitreTraduction;
use App\Service\TranslationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/chapitres')]
class ChapitreApiController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TranslationService $translationService
    ) {}

    #[Route('/{id}', name: 'api_chapitre_show', methods: ['GET'])]
    public function show(Chapitre $chapitre, Request $request): JsonResponse
    {
        $lang = $request->query->get('lang', 'fr');

        // Validation de la langue
        if (!$this->translationService->isLanguageSupported($lang)) {
            return $this->json([
                'status' => 'error',
                'message' => 'Langue non supportée'
            ], 400);
        }

        // Si langue française, retourner contenu original
        if ($lang === 'fr') {
            return $this->json([
                'id' => $chapitre->getId(),
                'titre' => $chapitre->getTitre(),
                'contenu' => $chapitre->getContenu(),
                'ordre' => $chapitre->getOrdre(),
                'langue' => 'fr'
            ]);
        }

        // Vérifier si traduction existe en cache
        $traduction = $this->entityManager
            ->getRepository(ChapitreTraduction::class)
            ->findOneBy([
                'chapitre' => $chapitre,
                'langue' => $lang
            ]);

        if ($traduction) {
            // Retourner traduction depuis cache
            return $this->json([
                'id' => $chapitre->getId(),
                'titre' => $traduction->getTitreTraduit(),
                'contenu' => $traduction->getContenuTraduit(),
                'ordre' => $chapitre->getOrdre(),
                'langue' => $lang,
                'cached' => true
            ]);
        }

        // Traduire via API LibreTranslate
        $titreTraduit = $this->translationService->translate(
            $chapitre->getTitre(),
            'fr',
            $lang
        );

        $contenuTraduit = $this->translationService->translate(
            $chapitre->getContenu(),
            'fr',
            $lang
        );

        // Vérifier si traduction a réussi
        if (!$titreTraduit || !$contenuTraduit) {
            return $this->json([
                'status' => 'error',
                'message' => 'Service de traduction temporairement indisponible'
            ], 503);
        }

        // Sauvegarder traduction en cache
        $nouvelleTraduction = new ChapitreTraduction();
        $nouvelleTraduction->setChapitre($chapitre);
        $nouvelleTraduction->setLangue($lang);
        $nouvelleTraduction->setTitreTraduit($titreTraduit);
        $nouvelleTraduction->setContenuTraduit($contenuTraduit);

        $this->entityManager->persist($nouvelleTraduction);
        $this->entityManager->flush();

        return $this->json([
            'id' => $chapitre->getId(),
            'titre' => $titreTraduit,
            'contenu' => $contenuTraduit,
            'ordre' => $chapitre->getOrdre(),
            'langue' => $lang,
            'cached' => false
        ]);
    }
}
