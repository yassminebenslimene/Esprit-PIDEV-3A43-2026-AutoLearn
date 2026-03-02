<?php

namespace App\Controller;

use App\Entity\GestionDeCours\Chapitre;
use App\Entity\GestionDeCours\Cours;
use App\Service\CourseGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backoffice/ai-generator')]
class AIGeneratorController extends AbstractController
{
    private CourseGeneratorService $generatorService;
    private EntityManagerInterface $entityManager;

    public function __construct(
        CourseGeneratorService $generatorService,
        EntityManagerInterface $entityManager
    ) {
        $this->generatorService = $generatorService;
        $this->entityManager = $entityManager;
    }

    /**
     * API endpoint pour générer un chapitre
     */
    #[Route('/generate-chapter/{coursId}', name: 'app_ai_generate_chapter', methods: ['POST'])]
    public function generateChapter(int $coursId, Request $request): JsonResponse
    {
        try {
            // Récupérer le cours
            $cours = $this->entityManager->getRepository(Cours::class)->find($coursId);
            
            if (!$cours) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'Cours non trouvé'
                ], Response::HTTP_NOT_FOUND);
            }

            // Générer le chapitre avec l'IA
            $result = $this->generatorService->generateChapter(
                $cours->getTitre(),
                $cours->getMatiere() ?? '',
                $cours->getNiveau() ?? ''
            );

            if (!$result['success']) {
                return new JsonResponse([
                    'success' => false,
                    'error' => $result['error'] ?? 'Erreur lors de la génération'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $chapterData = $result['chapter'];

            // Calculer le prochain ordre
            $lastChapitre = $this->entityManager->getRepository(Chapitre::class)
                ->findOneBy(['cours' => $cours], ['ordre' => 'DESC']);
            
            $nextOrdre = $lastChapitre ? $lastChapitre->getOrdre() + 1 : 1;

            // Créer le nouveau chapitre
            $chapitre = new Chapitre();
            $chapitre->setTitre($chapterData['titre'] ?? 'Nouveau chapitre');
            $chapitre->setContenu($chapterData['contenu'] ?? '');
            $chapitre->setRessources($chapterData['ressources'] ?? '');
            $chapitre->setOrdre($nextOrdre);
            $chapitre->setCours($cours);

            $this->entityManager->persist($chapitre);
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Chapitre généré avec succès',
                'chapter' => [
                    'id' => $chapitre->getId(),
                    'titre' => $chapitre->getTitre(),
                    'contenu' => $chapitre->getContenu(),
                    'ressources' => $chapitre->getRessources(),
                    'ordre' => $chapitre->getOrdre()
                ]
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Erreur serveur: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
