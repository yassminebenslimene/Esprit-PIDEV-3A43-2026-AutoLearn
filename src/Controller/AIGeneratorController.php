<?php

namespace App\Controller;

use App\Entity\GestionDeCours\Chapitre;
use App\Entity\GestionDeCours\Cours;
use App\Service\CourseGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/backoffice/ai-generator')]
#[IsGranted('ROLE_ADMIN')]
class AIGeneratorController extends AbstractController
{
    private CourseGeneratorService $generatorService;
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(
        CourseGeneratorService $generatorService,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ) {
        $this->generatorService = $generatorService;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * API endpoint pour générer un chapitre
     */
    #[Route('/generate-chapter/{coursId}', name: 'app_ai_generate_chapter', methods: ['POST'])]
    public function generateChapter(int $coursId, Request $request): JsonResponse
    {
        try {
            $this->logger->info('Début génération chapitre pour cours ID: ' . $coursId);
            
            // Récupérer le cours
            $cours = $this->entityManager->getRepository(Cours::class)->find($coursId);
            
            if (!$cours) {
                $this->logger->error('Cours non trouvé: ' . $coursId);
                return new JsonResponse([
                    'success' => false,
                    'error' => 'Cours non trouvé'
                ], Response::HTTP_NOT_FOUND);
            }

            // Récupérer le titre du chapitre depuis la requête
            $content = $request->getContent();
            $this->logger->info('Contenu reçu: ' . $content);
            
            $data = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->logger->error('Erreur JSON: ' . json_last_error_msg());
                return new JsonResponse([
                    'success' => false,
                    'error' => 'Données JSON invalides: ' . json_last_error_msg()
                ], Response::HTTP_BAD_REQUEST);
            }
            
            $chapterTitle = $data['chapterTitle'] ?? '';
            $chapterLevel = $data['chapterLevel'] ?? 'debutant';
            $this->logger->info('Titre du chapitre: ' . $chapterTitle . ', Niveau: ' . $chapterLevel);

            // Générer le chapitre avec l'IA
            $this->logger->info('Appel du service de génération...');
            $result = $this->generatorService->generateChapter(
                $cours->getTitre(),
                $cours->getMatiere() ?? '',
                $cours->getNiveau() ?? '',
                $chapterTitle,
                $chapterLevel
            );

            if (!$result['success']) {
                $this->logger->error('Échec génération: ' . ($result['error'] ?? 'Erreur inconnue'));
                return new JsonResponse([
                    'success' => false,
                    'error' => $result['error'] ?? 'Erreur lors de la génération'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $chapterData = $result['chapter'];
            $this->logger->info('Chapitre généré avec succès');

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
            
            $this->logger->info('Chapitre sauvegardé avec ID: ' . $chapitre->getId());

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
            $this->logger->error('Exception: ' . $e->getMessage() . ' - ' . $e->getTraceAsString());
            return new JsonResponse([
                'success' => false,
                'error' => 'Erreur serveur: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
