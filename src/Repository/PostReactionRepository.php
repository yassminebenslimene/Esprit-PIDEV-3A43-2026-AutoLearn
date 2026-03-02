<?php

namespace App\Repository;

use App\Entity\PostReaction;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PostReactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostReaction::class);
    }

    /**
     * Compte les réactions par type pour un post
     */
    public function countByType(Post $post): array
    {
        $qb = $this->createQueryBuilder('r')
            ->select('r.type, COUNT(r.id) as count')
            ->where('r.post = :post')
            ->setParameter('post', $post)
            ->groupBy('r.type');

        $results = $qb->getQuery()->getResult();

        $counts = [];
        foreach ($results as $result) {
            $counts[$result['type']] = (int) $result['count'];
        }

        return $counts;
    }

    /**
     * Trouve la réaction d'un utilisateur pour un post
     */
    public function findUserReaction(Post $post, User $user): ?PostReaction
    {
        return $this->findOneBy([
            'post' => $post,
            'user' => $user
        ]);
    }
}
