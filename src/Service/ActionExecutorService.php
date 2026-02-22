<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Equipe;
use App\Repository\UserRepository;
use App\Repository\EquipeRepository;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Service d'exécution d'actions pour l'assistant IA
 * Permet à l'IA d'effectuer des actions réelles (créer étudiant, équipe, etc.)
 */
class ActionExecutorService
{
    private EntityManagerInterface $em;
    private UserRepository $userRepository;
    private EquipeRepository $equipeRepository;
    private EvenementRepository $evenementRepository;
    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $em,
        UserRepository $userRepository,
        EquipeRepository $equipeRepository,
        EvenementRepository $evenementRepository,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->equipeRepository = $equipeRepository;
        $this->evenementRepository = $evenementRepository;
        $this->logger = $logger;
    }

    /**
     * Exécute une action demandée par l'IA
     */
    public function executeAction(string $action, array $params, User $requestingUser): array
    {
        // Vérifier les permissions
        if (!$this->hasPermission($requestingUser, $action)) {
            return [
                'success' => false,
                'error' => 'Permission refusée. Action réservée aux administrateurs.'
            ];
        }

        try {
            return match($action) {
                'create_student' => $this->createStudent($params),
                'create_team' => $this->createTeam($params),
                'suspend_user' => $this->suspendUser($params),
                'unsuspend_user' => $this->unsuspendUser($params),
                'get_inactive_users' => $this->getInactiveUsers($params),
                'get_popular_courses' => $this->getPopularCourses($params),
                default => [
                    'success' => false,
                    'error' => "Action inconnue: {$action}"
                ]
            };
        } catch (\Exception $e) {
            $this->logger->error('Action execution error', [
                'action' => $action,
                'params' => $params,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Erreur lors de l\'exécution: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Vérifie si l'utilisateur a la permission d'exécuter l'action
     */
    private function hasPermission(User $user, string $action): bool
    {
        // Actions réservées aux admins
        $adminOnlyActions = [
            'create_student',
            'suspend_user',
            'unsuspend_user',
            'get_inactive_users'
        ];

        // Actions réservées aux étudiants
        $studentOnlyActions = [
            'create_team'
        ];

        // Actions admin uniquement
        if (in_array($action, $adminOnlyActions)) {
            return $user->getRole() === 'ADMIN';
        }

        // Actions étudiant uniquement
        if (in_array($action, $studentOnlyActions)) {
            return $user->getRole() === 'ETUDIANT';
        }

        // Actions publiques
        return true;
    }

    /**
     * Crée un nouvel étudiant
     */
    private function createStudent(array $params): array
    {
        // Validation
        $required = ['nom', 'prenom', 'email'];
        foreach ($required as $field) {
            if (empty($params[$field])) {
                return [
                    'success' => false,
                    'error' => "Champ requis manquant: {$field}"
                ];
            }
        }

        // Vérifier si l'email existe déjà
        $existing = $this->userRepository->findOneBy(['email' => $params['email']]);
        if ($existing) {
            return [
                'success' => false,
                'error' => 'Un utilisateur avec cet email existe déjà'
            ];
        }

        // Créer l'étudiant (pas User qui est abstract!)
        $user = new \App\Entity\Etudiant();
        $user->setNom($params['nom']);
        $user->setPrenom($params['prenom']);
        $user->setEmail($params['email']);
        $user->setRole('ETUDIANT');
        $user->setNiveau($params['niveau'] ?? 'DEBUTANT');
        
        // Mot de passe par défaut (à changer lors de la première connexion)
        $defaultPassword = password_hash('AutoLearn2026!', PASSWORD_BCRYPT);
        $user->setPassword($defaultPassword);

        $this->em->persist($user);
        $this->em->flush();

        return [
            'success' => true,
            'message' => "Étudiant créé avec succès: {$params['prenom']} {$params['nom']}",
            'user_id' => $user->getId(),
            'email' => $user->getEmail(),
            'default_password' => 'AutoLearn2026!'
        ];
    }

    /**
     * Crée une nouvelle équipe
     */
    private function createTeam(array $params): array
    {
        // Validation
        if (empty($params['nom'])) {
            return [
                'success' => false,
                'error' => 'Le nom de l\'équipe est requis'
            ];
        }

        if (empty($params['evenement_id'])) {
            return [
                'success' => false,
                'error' => 'L\'ID de l\'événement est requis'
            ];
        }

        // Vérifier que l'événement existe
        $evenement = $this->evenementRepository->find($params['evenement_id']);
        if (!$evenement) {
            return [
                'success' => false,
                'error' => 'Événement introuvable'
            ];
        }

        // Créer l'équipe
        $equipe = new Equipe();
        $equipe->setNom($params['nom']);
        $equipe->setEvenement($evenement);

        $this->em->persist($equipe);
        $this->em->flush();

        return [
            'success' => true,
            'message' => "Équipe créée avec succès: {$params['nom']}",
            'team_id' => $equipe->getId(),
            'event' => $evenement->getTitre()
        ];
    }

    /**
     * Suspend un utilisateur
     */
    private function suspendUser(array $params): array
    {
        if (empty($params['user_id'])) {
            return [
                'success' => false,
                'error' => 'L\'ID de l\'utilisateur est requis'
            ];
        }

        $user = $this->userRepository->find($params['user_id']);
        if (!$user) {
            return [
                'success' => false,
                'error' => 'Utilisateur introuvable'
            ];
        }

        if ($user->isSuspended()) {
            return [
                'success' => false,
                'error' => 'Cet utilisateur est déjà suspendu'
            ];
        }

        $user->setIsSuspended(true);
        $user->setSuspendedAt(new \DateTime());
        $user->setSuspensionReason($params['reason'] ?? 'Suspendu par l\'administrateur');

        $this->em->flush();

        return [
            'success' => true,
            'message' => "Utilisateur suspendu: {$user->getPrenom()} {$user->getNom()}",
            'user_id' => $user->getId()
        ];
    }

    /**
     * Réactive un utilisateur suspendu
     */
    private function unsuspendUser(array $params): array
    {
        if (empty($params['user_id'])) {
            return [
                'success' => false,
                'error' => 'L\'ID de l\'utilisateur est requis'
            ];
        }

        $user = $this->userRepository->find($params['user_id']);
        if (!$user) {
            return [
                'success' => false,
                'error' => 'Utilisateur introuvable'
            ];
        }

        if (!$user->isSuspended()) {
            return [
                'success' => false,
                'error' => 'Cet utilisateur n\'est pas suspendu'
            ];
        }

        $user->setIsSuspended(false);
        $user->setSuspendedAt(null);
        $user->setSuspensionReason(null);

        $this->em->flush();

        return [
            'success' => true,
            'message' => "Utilisateur réactivé: {$user->getPrenom()} {$user->getNom()}",
            'user_id' => $user->getId()
        ];
    }

    /**
     * Récupère les utilisateurs inactifs
     */
    private function getInactiveUsers(array $params): array
    {
        $days = $params['days'] ?? 7;
        $date = new \DateTime("-{$days} days");

        $qb = $this->em->createQueryBuilder();
        $users = $qb->select('u')
            ->from('App\Entity\User', 'u')
            ->where('u.lastLoginAt < :date OR u.lastLoginAt IS NULL')
            ->andWhere('u.isSuspended = false')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();

        $usersList = array_map(function($user) {
            $lastLogin = $user->getLastLoginAt();
            return [
                'id' => $user->getId(),
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'email' => $user->getEmail(),
                'last_login' => $lastLogin ? $lastLogin->format('d/m/Y H:i') : 'Jamais'
            ];
        }, $users);

        return [
            'success' => true,
            'count' => count($usersList),
            'users' => $usersList,
            'period' => "{$days} jours"
        ];
    }

    /**
     * Récupère les cours les plus populaires
     */
    private function getPopularCourses(array $params): array
    {
        $limit = $params['limit'] ?? 5;

        // TODO: Implémenter la logique de popularité basée sur les inscriptions
        // Pour l'instant, retourner les cours avec le plus de chapitres
        $qb = $this->em->createQueryBuilder();
        $courses = $qb->select('c')
            ->from('App\Entity\Cours\Cours', 'c')
            ->orderBy('c.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $coursesList = array_map(function($cours) {
            return [
                'id' => $cours->getId(),
                'titre' => $cours->getTitre(),
                'niveau' => $cours->getNiveau(),
                'chapitres' => $cours->getChapitres()->count()
            ];
        }, $courses);

        return [
            'success' => true,
            'count' => count($coursesList),
            'courses' => $coursesList
        ];
    }

    /**
     * Liste toutes les actions disponibles pour un utilisateur
     */
    public function getAvailableActions(User $user): array
    {
        $actions = [
            'public' => [
                'get_popular_courses' => 'Voir les cours populaires'
            ]
        ];

        if ($user->getRole() === 'ADMIN') {
            $actions['admin'] = [
                'create_student' => 'Créer un nouvel étudiant',
                'suspend_user' => 'Suspendre un utilisateur',
                'unsuspend_user' => 'Réactiver un utilisateur',
                'get_inactive_users' => 'Lister les utilisateurs inactifs'
            ];
        }

        if ($user->getRole() === 'ETUDIANT') {
            $actions['student'] = [
                'create_team' => 'Créer une équipe pour un événement'
            ];
        }

        return $actions;
    }
}
