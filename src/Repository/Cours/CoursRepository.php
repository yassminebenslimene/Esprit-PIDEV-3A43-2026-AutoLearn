<?php

namespace App\Repository\Cours;

use App\Entity\GestionDeCours\Cours;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cours>
 */
class CoursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cours::class);
    }

    /**
     * Trouve tous les cours avec pagination
     * 
     * @param int $limit Nombre maximum de résultats
     * @param int $offset Décalage pour la pagination
     * @return Cours[] Returns an array of Cours objects
     */
    public function findAllPaginated(int $limit = 50, int $offset = 0): array
    {
        return $this->createQueryBuilder('c')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Cours[] Returns an array of Cours objects
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

//    public function findOneBySomeField($value): ?Cours
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
