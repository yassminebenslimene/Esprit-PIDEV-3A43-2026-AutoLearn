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
     * Détecte et exécute une action depuis la réponse de l'IA
     */
    public function detectAndExecute(string $response, User $requestingUser): array
    {
        // Chercher un JSON d'action dans la réponse (plus flexible)
        // Cherche { "action": "xxx", "data": {...} } sur la première ligne ou n'importe où
        
        // Essayer d'extraire le JSON de manière plus flexible
        $jsonPattern = '/\{\s*"action"\s*:\s*"([^"]+)"\s*,\s*"data"\s*:\s*\{[^}]*\}\s*\}/s';
        
        if (preg_match($jsonPattern, $response, $matches)) {
            $jsonStr = $matches[0];
            
            $this->logger->info('Action JSON detected', ['json' => $jsonStr]);
            
            try {
                $actionData = json_decode($jsonStr, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->logger->error('JSON decode error', ['error' => json_last_error_msg()]);
                    return [
                        'action_executed' => false,
                        'message' => ''
                    ];
                }
                
                $action = $actionData['action'] ?? null;
                $params = $actionData['data'] ?? [];
                
                if (!$action) {
                    return [
                        'action_executed' => false,
                        'message' => ''
                    ];
                }
                
                $this->logger->info('Executing action', ['action' => $action, 'params' => $params]);
                
                // Exécuter l'action
                $result = $this->executeAction($action, $params, $requestingUser);
                
                $this->logger->info('Action result', ['result' => $result]);
                
                // Ne pas ajouter de message supplémentaire, l'IA a déjà répondu
                return [
                    'action_executed' => $result['success'],
                    'message' => '', // Pas de message supplémentaire
                    'result' => $result
                ];
                
            } catch (\Exception $e) {
                $this->logger->error('Action detection error', [
                    'response' => substr($response, 0, 500),
                    'error' => $e->getMessage()
                ]);
                
                return [
                    'action_executed' => false,
                    'message' => ''
                ];
            }
        }
        
        $this->logger->info('No action detected in response', [
            'response_preview' => substr($response, 0, 200)
        ]);
        
        // Aucune action détectée
        return [
            'action_executed' => false,
            'message' => ''
        ];
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
                'update_student' => $this->updateStudent($params),
                'update_user' => $this->updateStudent($params),
                'filter_students' => $this->filterStudents($params),
                'get_user' => $this->getUser($params),
                'create_team' => $this->createTeam($params),
                'suspend_user' => $this->suspendUser($params),
                'unsuspend_user' => $this->unsuspendUser($params),
                'get_inactive_users' => $this->getInactiveUsers($params),
                'get_popular_courses' => $this->getPopularCourses($params),
                default => [
                    'success' => false,
                    'error' => "Action non autorisée ou inconnue"
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
            'update_student',
            'update_user',
            'filter_students',
            'get_user',
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
     * Trouve un utilisateur de manière intelligente (par ID, nom, prénom, email)
     */
    private function findUserIntelligently(array $params): ?User
    {
        // 1. Chercher par ID direct
        if (!empty($params['id']) || !empty($params['user_id'])) {
            $userId = $params['id'] ?? $params['user_id'];
            $user = $this->userRepository->find($userId);
            if ($user) {
                return $user;
            }
        }

        // 2. Chercher par email
        if (!empty($params['email'])) {
            $user = $this->userRepository->findOneBy(['email' => $params['email']]);
            if ($user) {
                return $user;
            }
        }

        // 3. Chercher par nom complet (nom + prenom)
        if (!empty($params['nom']) && !empty($params['prenom'])) {
            $user = $this->userRepository->findOneBy([
                'nom' => $params['nom'],
                'prenom' => $params['prenom']
            ]);
            if ($user) {
                return $user;
            }
        }

        // 4. Chercher par nom seul (case insensitive)
        if (!empty($params['nom'])) {
            $qb = $this->em->createQueryBuilder();
            $users = $qb->select('u')
                ->from('App\Entity\User', 'u')
                ->where('LOWER(u.nom) LIKE LOWER(:nom)')
                ->setParameter('nom', '%' . $params['nom'] . '%')
                ->setMaxResults(1)
                ->getQuery()
                ->getResult();
            
            if (!empty($users)) {
                return $users[0];
            }
        }

        // 5. Chercher par prénom seul (case insensitive)
        if (!empty($params['prenom'])) {
            $qb = $this->em->createQueryBuilder();
            $users = $qb->select('u')
                ->from('App\Entity\User', 'u')
                ->where('LOWER(u.prenom) LIKE LOWER(:prenom)')
                ->setParameter('prenom', '%' . $params['prenom'] . '%')
                ->setMaxResults(1)
                ->getQuery()
                ->getResult();
            
            if (!empty($users)) {
                return $users[0];
            }
        }

        return null;
    }

    /**
     * Modifie un étudiant existant
     */
    private function updateStudent(array $params): array
    {
        // Recherche intelligente de l'utilisateur
        $user = $this->findUserIntelligently($params);
        
        if (!$user) {
            return [
                'success' => false,
                'error' => 'Utilisateur introuvable'
            ];
        }
        if (!$user) {
            return [
                'success' => false,
                'error' => 'Utilisateur introuvable'
            ];
        }

        // Mettre à jour les champs fournis
        if (isset($params['nom'])) {
            $user->setNom($params['nom']);
        }
        if (isset($params['prenom'])) {
            $user->setPrenom($params['prenom']);
        }
        if (isset($params['email'])) {
            // Vérifier si l'email n'est pas déjà utilisé
            $existing = $this->userRepository->findOneBy(['email' => $params['email']]);
            if ($existing && $existing->getId() !== $user->getId()) {
                return [
                    'success' => false,
                    'error' => 'Cet email est déjà utilisé par un autre utilisateur'
                ];
            }
            $user->setEmail($params['email']);
        }
        if (isset($params['niveau']) && method_exists($user, 'setNiveau')) {
            $user->setNiveau($params['niveau']);
        }

        $this->em->flush();

        return [
            'success' => true,
            'message' => "Étudiant modifié avec succès: {$user->getPrenom()} {$user->getNom()}",
            'user_id' => $user->getId()
        ];
    }

    /**
     * Supprime un utilisateur
     */
    private function deleteUser(array $params): array
    {
        // Recherche intelligente de l'utilisateur
        $user = $this->findUserIntelligently($params);
        
        if (!$user) {
            return [
                'success' => false,
                'error' => 'Utilisateur introuvable'
            ];
        }

        $userName = $user->getPrenom() . ' ' . $user->getNom();
        
        try {
            // Supprimer les relations d'équipe d'abord
            $connection = $this->em->getConnection();
            
            // Supprimer les memberships d'équipe (table many-to-many)
            $connection->executeStatement(
                'DELETE FROM equipe_etudiant WHERE etudiant_id = ?',
                [$user->getId()]
            );
            
            // Supprimer l'utilisateur
            $this->em->remove($user);
            $this->em->flush();

            return [
                'success' => true,
                'message' => "✅ Utilisateur {$userName} supprimé"
            ];
        } catch (\Exception $e) {
            $this->logger->error('Delete user error', ['error' => $e->getMessage()]);
            
            // Analyser l'erreur pour donner un message utile
            $errorMsg = $e->getMessage();
            
            if (strpos($errorMsg, 'foreign key constraint') !== false || strpos($errorMsg, 'Integrity constraint') !== false) {
                // Identifier quelle table cause le problème
                if (strpos($errorMsg, 'equipe') !== false) {
                    return [
                        'success' => false,
                        'error' => "❌ Impossible: l'utilisateur est dans une équipe. Retirez-le d'abord."
                    ];
                } elseif (strpos($errorMsg, 'cours') !== false || strpos($errorMsg, 'course') !== false) {
                    return [
                        'success' => false,
                        'error' => "❌ Impossible: l'utilisateur est inscrit à des cours. Désinscrivez-le d'abord."
                    ];
                } elseif (strpos($errorMsg, 'communaute') !== false || strpos($errorMsg, 'community') !== false) {
                    return [
                        'success' => false,
                        'error' => "❌ Impossible: l'utilisateur est dans des communautés. Retirez-le d'abord."
                    ];
                } else {
                    return [
                        'success' => false,
                        'error' => "❌ Impossible: l'utilisateur a des données liées. Supprimez-les d'abord."
                    ];
                }
            }
            
            return [
                'success' => false,
                'error' => '❌ Erreur lors de la suppression'
            ];
        }
    }

    /**
     * Supprime un utilisateur en forçant (supprime toutes les relations d'abord)
     */
    private function forceDeleteUser(array $params): array
    {
        // Recherche intelligente de l'utilisateur
        $user = $this->findUserIntelligently($params);
        
        if (!$user) {
            return [
                'success' => false,
                'error' => 'Utilisateur introuvable'
            ];
        }

        $userName = $user->getPrenom() . ' ' . $user->getNom();
        $userId = $user->getId();
        
        try {
            $connection = $this->em->getConnection();
            
            // Supprimer TOUTES les relations dans l'ordre
            
            // 1. Équipes
            $connection->executeStatement(
                'DELETE FROM equipe_etudiant WHERE etudiant_id = ?',
                [$userId]
            );
            
            // 2. Inscriptions aux cours (si la table existe)
            try {
                $connection->executeStatement(
                    'DELETE FROM cours_etudiant WHERE etudiant_id = ?',
                    [$userId]
                );
            } catch (\Exception $e) {
                // Table n'existe peut-être pas
            }
            
            // 3. Membres de communautés (si la table existe)
            try {
                $connection->executeStatement(
                    'DELETE FROM communaute_membre WHERE user_id = ?',
                    [$userId]
                );
            } catch (\Exception $e) {
                // Table n'existe peut-être pas
            }
            
            // 4. Posts et commentaires (si les tables existent)
            try {
                $connection->executeStatement(
                    'DELETE FROM commentaire WHERE user_id = ?',
                    [$userId]
                );
                $connection->executeStatement(
                    'DELETE FROM post WHERE user_id = ?',
                    [$userId]
                );
            } catch (\Exception $e) {
                // Tables n'existent peut-être pas
            }
            
            // 5. Supprimer l'utilisateur
            $this->em->remove($user);
            $this->em->flush();

            return [
                'success' => true,
                'message' => "✅ Utilisateur {$userName} supprimé (avec toutes ses données)"
            ];
        } catch (\Exception $e) {
            $this->logger->error('Force delete user error', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'error' => '❌ Erreur: ' . substr($e->getMessage(), 0, 100)
            ];
        }
    }

    /**
     * Récupère les détails d'un utilisateur
     */
    private function getUser(array $params): array
    {
        // Recherche intelligente de l'utilisateur
        $user = $this->findUserIntelligently($params);
        
        if (!$user) {
            return [
                'success' => false,
                'error' => 'Utilisateur introuvable'
            ];
        }

        $userDetails = [
            'id' => $user->getId(),
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'email' => $user->getEmail(),
            'role' => $user->getRole(),
            'niveau' => method_exists($user, 'getNiveau') ? $user->getNiveau() : null,
            'suspended' => $user->getIsSuspended(),
            'created_at' => $user->getCreatedAt()->format('d/m/Y'),
            'last_login' => $user->getLastLoginAt() ? $user->getLastLoginAt()->format('d/m/Y H:i') : 'Jamais'
        ];

        return [
            'success' => true,
            'user' => $userDetails
        ];
    }

    /**
     * Filtre les étudiants selon des critères
     */
    private function filterStudents(array $params): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('u')
            ->from('App\Entity\User', 'u')
            ->where('u.role = :role')
            ->setParameter('role', 'ETUDIANT');

        // Filtrer par niveau
        if (!empty($params['niveau'])) {
            $qb->andWhere('u.niveau = :niveau')
                ->setParameter('niveau', $params['niveau']);
        }

        // Filtrer par date d'inscription
        if (!empty($params['date_from'])) {
            $qb->andWhere('u.createdAt >= :date_from')
                ->setParameter('date_from', new \DateTime($params['date_from']));
        }
        if (!empty($params['date_to'])) {
            $qb->andWhere('u.createdAt <= :date_to')
                ->setParameter('date_to', new \DateTime($params['date_to']));
        }

        // Filtrer par statut de suspension
        if (isset($params['suspended'])) {
            $qb->andWhere('u.isSuspended = :suspended')
                ->setParameter('suspended', (bool)$params['suspended']);
        }

        // Recherche par nom ou email
        if (!empty($params['search'])) {
            $qb->andWhere('u.nom LIKE :search OR u.prenom LIKE :search OR u.email LIKE :search')
                ->setParameter('search', '%' . $params['search'] . '%');
        }

        // Limiter les résultats
        $limit = $params['limit'] ?? 20;
        $qb->setMaxResults($limit);

        // Ordre
        $qb->orderBy('u.createdAt', 'DESC');

        $users = $qb->getQuery()->getResult();

        $usersList = array_map(function($user) {
            return [
                'id' => $user->getId(),
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'email' => $user->getEmail(),
                'niveau' => method_exists($user, 'getNiveau') ? $user->getNiveau() : 'N/A',
                'created_at' => $user->getCreatedAt()->format('d/m/Y'),
                'suspended' => $user->isSuspended(),
                'last_login' => $user->getLastLoginAt() ? $user->getLastLoginAt()->format('d/m/Y H:i') : 'Jamais'
            ];
        }, $users);

        return [
            'success' => true,
            'count' => count($usersList),
            'students' => $usersList,
            'filters_applied' => array_filter($params, fn($v) => !empty($v))
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
        // Recherche intelligente de l'utilisateur
        $user = $this->findUserIntelligently($params);
        
        if (!$user) {
            return [
                'success' => false,
                'error' => 'Utilisateur introuvable'
            ];
        }

        if ($user->getIsSuspended()) {
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
        // Recherche intelligente de l'utilisateur
        $user = $this->findUserIntelligently($params);
        
        if (!$user) {
            return [
                'success' => false,
                'error' => 'Utilisateur introuvable'
            ];
        }

        if (!$user->getIsSuspended()) {
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
                'update_student' => 'Modifier un étudiant',
                'get_user' => 'Voir les détails d\'un utilisateur',
                'suspend_user' => 'Suspendre un utilisateur',
                'unsuspend_user' => 'Réactiver un utilisateur',
                'filter_students' => 'Filtrer les étudiants',
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
