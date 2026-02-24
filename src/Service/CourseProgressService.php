<?php

namespace App\Service;

use App\Entity\ChapterProgress;
use App\Entity\User;
use App\Entity\GestionDeCours\Chapitre;
use App\Entity\GestionDeCours\Cours;
use App\Repository\ChapterProgressRepository;
use Doctrine\ORM\EntityManagerInterface;

class CourseProgressService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ChapterProgressRepository $progressRepository
    ) {
    }

    /**
     * Calcule le pourcentage de progression d'un étudiant dans un cours
     * Formule: (Chapitres validés / Total chapitres) × 100
     */
    public function calculateCourseProgress(User $user, Cours $cours): float
    {
        $totalChapters = $cours->getChapitres()->count();
        
        if ($totalChapters === 0) {
            return 0.0;
        }

        $completedChapters = $this->progressRepository->countCompletedChaptersByCourse($user, $cours);
        
        return round(($completedChapters / $totalChapters) * 100, 2);
    }

    /**
     * Marque un chapitre comme complété après réussite du quiz
     */
    public function markChapterAsCompleted(User $user, Chapitre $chapitre, int $quizScore): ChapterProgress
    {
        // Vérifier si une progression existe déjà
        $progress = $this->progressRepository->findByUserAndChapter($user, $chapitre);

        if ($progress === null) {
            // Créer une nouvelle progression
            $progress = new ChapterProgress();
            $progress->setUser($user);
            $progress->setChapitre($chapitre);
        }

        // Mettre à jour la progression
        $progress->setCompletedAt(new \DateTime());
        $progress->setQuizScore($quizScore);

        // 🔥 IMPORTANT : Mettre à jour la date de dernière activité
        $user->setLastActivityAt(new \DateTime());

        $this->entityManager->persist($progress);
        $this->entityManager->flush();

        return $progress;
    }

    /**
     * Vérifie si un chapitre est complété par un utilisateur
     */
    public function isChapterCompleted(User $user, Chapitre $chapitre): bool
    {
        return $this->progressRepository->isChapterCompleted($user, $chapitre);
    }

    /**
     * Récupère tous les chapitres complétés d'un cours pour un utilisateur
     */
    public function getCompletedChapters(User $user, Cours $cours): array
    {
        return $this->progressRepository->findCompletedChaptersByCourse($user, $cours);
    }

    /**
     * Récupère les statistiques de progression pour un cours
     */
    public function getCourseProgressStats(User $user, Cours $cours): array
    {
        $totalChapters = $cours->getChapitres()->count();
        $completedChapters = $this->progressRepository->countCompletedChaptersByCourse($user, $cours);
        $percentage = $this->calculateCourseProgress($user, $cours);

        return [
            'total_chapters' => $totalChapters,
            'completed_chapters' => $completedChapters,
            'remaining_chapters' => $totalChapters - $completedChapters,
            'percentage' => $percentage,
            'is_completed' => $percentage >= 100
        ];
    }

    /**
     * Récupère la progression de tous les cours d'un utilisateur
     */
    public function getAllCoursesProgress(User $user, array $courses): array
    {
        $progressData = [];

        foreach ($courses as $cours) {
            $progressData[$cours->getId()] = $this->getCourseProgressStats($user, $cours);
        }

        return $progressData;
    }
}
