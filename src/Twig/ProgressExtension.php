<?php

namespace App\Twig;

use App\Entity\User;
use App\Entity\GestionDeCours\Cours;
use App\Entity\GestionDeCours\Chapitre;
use App\Service\CourseProgressService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ProgressExtension extends AbstractExtension
{
    public function __construct(
        private CourseProgressService $progressService
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('course_progress', [$this, 'getCourseProgress']),
            new TwigFunction('course_progress_stats', [$this, 'getCourseProgressStats']),
            new TwigFunction('is_chapter_completed', [$this, 'isChapterCompleted']),
        ];
    }

    /**
     * Retourne le pourcentage de progression d'un cours
     */
    public function getCourseProgress(User $user, Cours $cours): float
    {
        return $this->progressService->calculateCourseProgress($user, $cours);
    }

    /**
     * Retourne les statistiques complètes de progression
     */
    public function getCourseProgressStats(User $user, Cours $cours): array
    {
        return $this->progressService->getCourseProgressStats($user, $cours);
    }

    /**
     * Vérifie si un chapitre est complété
     */
    public function isChapterCompleted(User $user, Chapitre $chapitre): bool
    {
        return $this->progressService->isChapterCompleted($user, $chapitre);
    }
}
