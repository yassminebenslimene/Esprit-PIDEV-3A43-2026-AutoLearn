<?php

namespace App\Repository;

use App\Entity\Communaute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Utils\LikeEscaper;

/**
 * @extends ServiceEntityRepository<Communaute>
 */
class CommunauteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Communaute::class);
    }

    /**
     * Recherche des communautés par nom ou description
     * @return Communaute[]
     */
    public function searchByNameOrDescription(string $search): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.nom LIKE :search')
            ->orWhere('c.description LIKE :search')
            ->setParameter('search', LikeEscaper::escapeAndWrap($search))
            ->orderBy('c.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all communities with optional limit
     * @return Communaute[]
     */
    public function findAllWithLimit(?int $limit = null): array
    {
        $qb = $this->createQueryBuilder('c')
            ->orderBy('c.id', 'DESC');
        
        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }
        
        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return Communaute[] Returns an array of Communaute objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Communaute
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
