<?php

namespace App\Controller;

use App\Repository\Cours\ChapitreRepository;
use App\Service\TranslationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur pour la traduction automatique des chapitres
 */
class TranslationController extends AbstractController
{
    private TranslationService $translationService;
    private ChapitreRepository $chapitreRepository;

    public function __construct(
        TranslationService $translationService,
        ChapitreRepository $chapitreRepository
    ) {
        $this->translationService = $translationService;
        $this->chapitreRepository = $chapitreRepository;
    }

    /**
     * API endpoint pour traduire un chapitre
     * 
     * @Route("/api/chapitres/{id}/translate", name="api_chapitre_translate", methods={"GET"})
     */
    #[Route('/api/chapitres/{id}/translate', name: 'api_chapitre_translate', methods: ['GET'])]
    public function translateChapter(int $id, Request $request): JsonResponse
    {
        try {
            $lang = $request->query->get('lang', 'fr');

            // Vérifier que la langue est supportée
            if (!$this->translationService->isLanguageSupported($lang)) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'Langue non supportée: ' . $lang
                ], 400);
            }

            // Récupérer le chapitre
            $chapitre = $this->chapitreRepository->find($id);
            
            if (!$chapitre) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'Chapitre non trouvé'
                ], 404);
            }

            // Si français, retourner le contenu original
            if ($lang === 'fr') {
                return $this->json([
                    'status' => 'success',
                    'titre' => $chapitre->getTitre(),
                    'contenu' => $chapitre->getContenu(),
                    'cached' => false
                ]);
            }

            // Traduire le chapitre
            $translated = $this->translationService->translateChapter($chapitre, $lang);

            return $this->json([
                'status' => 'success',
                'titre' => $translated['titre'],
                'contenu' => $translated['contenu'],
                'cached' => true
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retourne la liste des langues supportées
     * 
     * @Route("/api/languages", name="api_languages", methods={"GET"})
     */
    #[Route('/api/languages', name: 'api_languages', methods: ['GET'])]
    public function getSupportedLanguages(): JsonResponse
    {
        return $this->json([
            'status' => 'success',
            'languages' => $this->translationService->getSupportedLanguages()
        ]);
    }
}
