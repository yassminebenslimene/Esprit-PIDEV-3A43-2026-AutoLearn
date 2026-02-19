<?php

namespace App\Repository\Cours;

use App\Entity\GestionDeCours\ChapitreTraduction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ChapitreTraductionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChapitreTraduction::class);
    }

    /**
     * Trouve une traduction en cache
     */
    public function findCachedTranslation(int $chapitreId, string $langue): ?ChapitreTraduction
    {
        return $this->createQueryBuilder('t')
            ->where('t.chapitre = :chapitreId')
            ->andWhere('t.langue = :langue')
            ->setParameter('chapitreId', $chapitreId)
            ->setParameter('langue', $langue)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Supprime les traductions obsolètes (plus de 30 jours)
     */
    public function deleteOldTranslations(): int
    {
        $date = new \DateTimeImmutable('-30 days');
        
        return $this->createQueryBuilder('t')
            ->delete()
            ->where('t.createdAt < :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->execute();
    }
}
