<?php

namespace App\Repository;

use App\Entity\ChapterProgress;
use App\Entity\User;
use App\Entity\GestionDeCours\Chapitre;
use App\Entity\GestionDeCours\Cours;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChapterProgress>
 */
class ChapterProgressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChapterProgress::class);
    }

    /**
     * Trouve la progression d'un utilisateur pour un chapitre spécifique
     */
    public function findByUserAndChapter(User $user, Chapitre $chapitre): ?ChapterProgress
    {
        return $this->findOneBy([
            'user' => $user,
            'chapitre' => $chapitre
        ]);
    }

    /**
     * Compte le nombre de chapitres complétés par un utilisateur dans un cours
     */
    public function countCompletedChaptersByCourse(User $user, Cours $cours): int
    {
        return $this->createQueryBuilder('cp')
            ->select('COUNT(cp.id)')
            ->join('cp.chapitre', 'ch')
            ->where('cp.user = :user')
            ->andWhere('ch.cours = :cours')
            ->andWhere('cp.completedAt IS NOT NULL')
            ->setParameter('user', $user)
            ->setParameter('cours', $cours)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Compte les chapitres complétés pour plusieurs cours en une seule requête (optimisé avec DTO)
     * Évite le problème N+1 en récupérant toutes les progressions en une fois
     */
    public function countCompletedChaptersByCoursesForUser(User $user, array $coursIds): array
    {
        if (empty($coursIds)) {
            return [];
        }

        $results = $this->createQueryBuilder('cp')
            ->select('NEW App\DTO\CourseProgressDTO(IDENTITY(ch.cours), COUNT(cp.id))')
            ->join('cp.chapitre', 'ch')
            ->where('cp.user = :user')
            ->andWhere('ch.cours IN (:coursIds)')
            ->andWhere('cp.completedAt IS NOT NULL')
            ->setParameter('user', $user)
            ->setParameter('coursIds', $coursIds)
            ->groupBy('ch.cours')
            ->getQuery()
            ->getResult();

        // Convert DTOs to associative array [coursId => count]
        $progressMap = [];
        foreach ($results as $dto) {
            $progressMap[$dto->coursId] = $dto->completedChapters;
        }

        return $progressMap;
    }

    /**
     * Récupère tous les chapitres complétés par un utilisateur dans un cours
     */
    public function findCompletedChaptersByCourse(User $user, Cours $cours): array
    {
        return $this->createQueryBuilder('cp')
            ->join('cp.chapitre', 'ch')
            ->where('cp.user = :user')
            ->andWhere('ch.cours = :cours')
            ->andWhere('cp.completedAt IS NOT NULL')
            ->setParameter('user', $user)
            ->setParameter('cours', $cours)
            ->getQuery()
            ->getResult();
    }

    /**
     * Vérifie si un chapitre est complété par un utilisateur
     */
    public function isChapterCompleted(User $user, Chapitre $chapitre): bool
    {
        $progress = $this->findByUserAndChapter($user, $chapitre);
        return $progress !== null && $progress->isCompleted();
    }
}
