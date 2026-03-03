<?php

namespace App\Repository;

use App\Entity\UserChallenge;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserChallenge>
 */
class UserChallengeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserChallenge::class);
    }

    /**
     * Find user challenge by user and challenge
     */
    public function findOneByUserAndChallenge($user, $challenge): ?UserChallenge
    {
        return $this->createQueryBuilder('uc')
            ->andWhere('uc.user = :user')
            ->andWhere('uc.challenge = :challenge')
            ->setParameter('user', $user)
            ->setParameter('challenge', $challenge)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Get user's completed challenges
     */
    public function findCompletedByUser($user): array
    {
        return $this->createQueryBuilder('uc')
            ->andWhere('uc.user = :user')
            ->andWhere('uc.completed = true')
            ->setParameter('user', $user)
            ->orderBy('uc.completedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get user's in-progress challenges
     */
    public function findInProgressByUser($user): array
    {
        return $this->createQueryBuilder('uc')
            ->andWhere('uc.user = :user')
            ->andWhere('uc.completed = false')
            ->setParameter('user', $user)
            ->orderBy('uc.startedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
