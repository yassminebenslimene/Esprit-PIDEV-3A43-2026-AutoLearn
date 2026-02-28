<?php

namespace App\Controller\FrontOffice;

use App\Entity\GestionDeCours\Chapitre;
use App\Entity\GestionDeCours\Cours;
use App\Repository\Cours\ChapitreRepository;
use App\Service\CourseProgressService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/chapitres', name: 'app_frontoffice_chapitre_')]
class ChapitreController extends AbstractController
{
    // Liste de tous les chapitres (si besoin)
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(ChapitreRepository $chapitreRepository): Response
    {
        return $this->render('frontoffice/chapitre/index.html.twig', [
            'chapitres' => $chapitreRepository->findAll(),
        ]);
    }

    // Liste des chapitres d'un cours spécifique
    #[Route('/cours/{coursId}', name: 'by_cours', methods: ['GET'])]
    public function byCours(
        int $coursId, 
        ChapitreRepository $chapitreRepository,
        CourseProgressService $progressService,
        EntityManagerInterface $entityManager
    ): Response
    {
        $chapitres = $chapitreRepository->findBy(
            ['cours' => $coursId],
            ['ordre' => 'ASC']
        );

        $user = $this->getUser();
        $progressStats = null;
        
        // Si l'utilisateur est connecté, calculer la progression
        if ($user) {
            $cours = $entityManager->getRepository(Cours::class)->find($coursId);
            if ($cours) {
                $progressStats = $progressService->getCourseProgressStats($user, $cours);
            }
        }

        return $this->render('frontoffice/chapitre/index.html.twig', [
            'chapitres' => $chapitres,
            'coursId' => $coursId,
            'progress_stats' => $progressStats,
        ]);
    }

    // Détail d'un chapitre
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(
        Chapitre $chapitre,
        CourseProgressService $progressService
    ): Response
    {
        $user = $this->getUser();
        $progressStats = null;
        
        // Si l'utilisateur est connecté, calculer la progression du cours
        if ($user && $chapitre->getCours()) {
            $progressStats = $progressService->getCourseProgressStats($user, $chapitre->getCours());
        }
        
        return $this->render('frontoffice/chapitre/show.html.twig', [
            'chapitre' => $chapitre,
            'progress_stats' => $progressStats,
        ]);
    }

    // Quiz d'un chapitre
    #[Route('/{id}/quiz', name: 'quiz', methods: ['GET'])]
    public function quiz(Chapitre $chapitre): Response
    {
        // Récupérer tous les quiz associés à ce chapitre
        $quizzes = $chapitre->getQuizzes();

        return $this->render('frontoffice/chapitre/quiz.html.twig', [
            'chapitre' => $chapitre,
            'quizzes' => $quizzes,
        ]);
    }
}