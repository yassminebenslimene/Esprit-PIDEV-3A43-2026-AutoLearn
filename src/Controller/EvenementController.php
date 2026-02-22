<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use App\Repository\ParticipationRepository;
use App\Service\FeedbackAnalyticsService;
use App\Service\AIReportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Workflow\WorkflowInterface;

#[Route('/backoffice/evenement')]
final class EvenementController extends AbstractController
{
    public function __construct(
        private WorkflowInterface $evenementPublishingStateMachine
    ) {}
    
    #[Route('/', name: 'backoffice_evenements', methods: ['GET'])]
    public function index(
        EvenementRepository $evenementRepository, 
        EntityManagerInterface $entityManager,
        FeedbackAnalyticsService $analyticsService
    ): Response {
        $evenements = $evenementRepository->findAll();
        
        // Mettre à jour le statut de chaque événement
        foreach ($evenements as $evenement) {
            $evenement->updateStatus();
        }
        $entityManager->flush();
        
        // Récupérer les statistiques pour la section AI
        $statsByType = $analyticsService->analyzeByEventType();
        
        return $this->render('backoffice/evenement/index.html.twig', [
            'evenements' => $evenements,
            'stats_by_type' => $statsByType,
        ]);
    }

    #[Route('/new', name: 'backoffice_evenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evenement->updateStatus();
            $entityManager->persist($evenement);
            $entityManager->flush();

            $this->addFlash('success', 'Événement créé avec succès');
            return $this->redirectToRoute('backoffice_evenements', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/evenement/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'backoffice_evenement_show', methods: ['GET'])]
    public function show(Evenement $evenement): Response
    {
        return $this->render('backoffice/evenement/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    #[Route('/{id}/edit', name: 'backoffice_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        // Ne pas afficher le checkbox isCanceled, on utilise le bouton "Annuler" à la place
        $form = $this->createForm(EvenementType::class, $evenement, ['is_edit' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evenement->updateStatus();
            $entityManager->flush();

            $this->addFlash('success', 'Événement modifié avec succès');
            return $this->redirectToRoute('backoffice_evenements', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
            'can_annuler' => $this->evenementPublishingStateMachine->can($evenement, 'annuler'),
        ]);
    }

    #[Route('/{id}/delete', name: 'backoffice_evenement_delete', methods: ['GET'])]
    public function delete(Evenement $evenement, EntityManagerInterface $entityManager, ParticipationRepository $participationRepository): Response
    {
        // IMPORTANT: Ordre de suppression pour respecter les contraintes de clés étrangères
        
        // 1. Récupérer toutes les équipes de cet événement
        $equipes = $evenement->getEquipes()->toArray();
        
        // 2. Pour chaque équipe, supprimer toutes ses participations
        foreach ($equipes as $equipe) {
            $participations = $participationRepository->findBy(['equipe' => $equipe]);
            foreach ($participations as $participation) {
                $entityManager->remove($participation);
            }
        }
        
        // 3. Flush pour appliquer la suppression des participations
        $entityManager->flush();
        
        // 4. Supprimer toutes les équipes
        foreach ($equipes as $equipe) {
            $entityManager->remove($equipe);
        }
        
        // 5. Flush pour appliquer la suppression des équipes
        $entityManager->flush();
        
        // 6. Enfin supprimer l'événement
        $entityManager->remove($evenement);
        $entityManager->flush();

        $this->addFlash('success', 'Événement, équipes et participations supprimés avec succès');
        return $this->redirectToRoute('backoffice_evenements', [], Response::HTTP_SEE_OTHER);
    }
    
    /**
     * Route pour annuler manuellement un événement via le Workflow
     */
    #[Route('/{id}/annuler', name: 'backoffice_evenement_annuler', methods: ['POST'])]
    public function annuler(Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si la transition est possible
        if (!$this->evenementPublishingStateMachine->can($evenement, 'annuler')) {
            $this->addFlash('error', 'Impossible d\'annuler cet événement (déjà annulé ou terminé)');
            return $this->redirectToRoute('backoffice_evenements');
        }
        
        try {
            // Appliquer la transition via le workflow
            $this->evenementPublishingStateMachine->apply($evenement, 'annuler');
            
            // Marquer comme annulé
            $evenement->setIsCanceled(true);
            
            // Sauvegarder
            $entityManager->flush();
            
            $this->addFlash('success', sprintf('L\'événement "%s" a été annulé avec succès', $evenement->getTitre()));
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'annulation: ' . $e->getMessage());
        }
        
        return $this->redirectToRoute('backoffice_evenements');
    }
    
    // ===== ROUTES POUR LES RAPPORTS AI =====
    
    #[Route('/ai/generate-analysis', name: 'backoffice_evenement_ai_analysis', methods: ['POST'])]
    public function generateAIAnalysis(AIReportService $aiReportService): JsonResponse
    {
        try {
            $report = $aiReportService->generateAnalysisReport();
            
            if (!$report) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Erreur lors de la génération du rapport. Vérifiez votre clé API Hugging Face dans .env.local'
                ], 500);
            }

            return new JsonResponse([
                'success' => true,
                'report' => $report
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/ai/generate-recommendations', name: 'backoffice_evenement_ai_recommendations', methods: ['POST'])]
    public function generateAIRecommendations(AIReportService $aiReportService): JsonResponse
    {
        try {
            $recommendations = $aiReportService->generateEventRecommendations();
            
            if (!$recommendations) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Erreur lors de la génération des recommandations.'
                ], 500);
            }

            return new JsonResponse([
                'success' => true,
                'recommendations' => $recommendations
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/ai/generate-improvements', name: 'backoffice_evenement_ai_improvements', methods: ['POST'])]
    public function generateAIImprovements(AIReportService $aiReportService): JsonResponse
    {
        try {
            $improvements = $aiReportService->generateImprovementSuggestions();
            
            if (!$improvements) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Erreur lors de la génération des suggestions.'
                ], 500);
            }

            return new JsonResponse([
                'success' => true,
                'improvements' => $improvements
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }
}
