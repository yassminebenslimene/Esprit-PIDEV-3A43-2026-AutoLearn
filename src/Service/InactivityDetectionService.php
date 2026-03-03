<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Etudiant;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service métier responsable de la détection d'inactivité des étudiants
 * 
 * Règle métier : Un étudiant est considéré inactif s'il n'a validé aucun chapitre
 * pendant une période de 3 jours consécutifs.
 */
class InactivityDetectionService
{
    private const INACTIVITY_THRESHOLD_DAYS = 3;

    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Détecte tous les étudiants inactifs selon la règle métier
     * 
     * @return array<User> Liste des étudiants inactifs
     */
    public function detectInactiveStudents(): array
    {
        $thresholdDate = new \DateTime();
        $thresholdDate->modify('-' . self::INACTIVITY_THRESHOLD_DAYS . ' days');

        // Utilisation du UserRepository pour une requête plus fiable
        $qb = $this->userRepository->createQueryBuilder('u');
        
        return $qb
            ->where('u.role = :role')
            ->andWhere('u.isSuspended = :suspended')
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->lt('u.lastActivityAt', ':threshold'),
                    $qb->expr()->isNull('u.lastActivityAt')
                )
            )
            ->setParameter('role', 'ETUDIANT')
            ->setParameter('suspended', false)
            ->setParameter('threshold', $thresholdDate)
            ->getQuery()
            ->getResult();
    }

    /**
     * Vérifie si un étudiant spécifique est inactif
     * 
     * @param User $user L'utilisateur à vérifier
     * @return bool True si l'étudiant est inactif
     */
    public function isStudentInactive(User $user): bool
    {
        if (!$user instanceof Etudiant) {
            return false;
        }

        if ($user->getIsSuspended()) {
            return false;
        }

        $lastActivity = $user->getLastActivityAt();
        
        if ($lastActivity === null) {
            return true; // Jamais eu d'activité
        }

        $thresholdDate = new \DateTime();
        $thresholdDate->modify('-' . self::INACTIVITY_THRESHOLD_DAYS . ' days');

        return $lastActivity < $thresholdDate;
    }

    /**
     * Calcule le nombre de jours d'inactivité d'un étudiant
     * 
     * @param User $user L'utilisateur
     * @return int Nombre de jours d'inactivité
     */
    public function getInactivityDays(User $user): int
    {
        $lastActivity = $user->getLastActivityAt();
        
        if ($lastActivity === null) {
            // Si jamais d'activité, calculer depuis la création du compte
            $lastActivity = $user->getCreatedAt();
        }

        $now = new \DateTime();
        $interval = $now->diff($lastActivity);
        
        return $interval->days;
    }

    /**
     * Met à jour la date de dernière activité d'un utilisateur
     * 
     * @param User $user L'utilisateur
     */
    public function updateLastActivity(User $user): void
    {
        $user->setLastActivityAt(new \DateTime());
        $this->entityManager->flush();
    }

    /**
     * Retourne le seuil d'inactivité en jours
     * 
     * @return int Nombre de jours
     */
    public function getInactivityThreshold(): int
    {
        return self::INACTIVITY_THRESHOLD_DAYS;
    }
}
