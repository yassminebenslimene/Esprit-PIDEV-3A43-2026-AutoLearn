<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Equipe;
use App\Repository\UserRepository;
use App\Repository\EquipeRepository;
use App\Repository\EvenementRepository;
use App\Repository\Cours\CoursRepository;
use App\Repository\Cours\ChapitreRepository;
use App\Repository\ChallengeRepository;
use App\Repository\CommunauteRepository;
use App\Repository\PostRepository;
use App\Repository\CommentaireRepository;
use App\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Service d'exécution d'actions pour l'assistant IA
 * Permet à l'IA d'effectuer des actions réelles sur TOUTES les entités
 */
class ActionExecutorService
{
    private EntityManagerInterface $em;
    private UserRepository $userRepository;
    private EquipeRepository $equipeRepository;
    private EvenementRepository $evenementRepository;
    private CoursRepository $coursRepository;
    private ChapitreRepository $chapitreRepository;
    private ChallengeRepository $challengeRepository;
    private CommunauteRepository $communauteRepository;
    private PostRepository $postRepository;
    private CommentaireRepository $commentaireRepository;
    private QuizRepository $quizRepository;
    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $em,
        UserRepository $userRepository,
        EquipeRepository $equipeRepository,
        EvenementRepository $evenementRepository,
        CoursRepository $coursRepository,
        ChapitreRepository $chapitreRepository,
        ChallengeRepository $challengeRepository,
        CommunauteRepository $communauteRepository,
        PostRepository $postRepository,
        CommentaireRepository $commentaireRepository,
        QuizRepository $quizRepository,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->equipeRepository = $equipeRepository;
        $this->evenementRepository = $evenementRepository;
        $this->coursRepository = $coursRepository;
        $this->chapitreRepository = $chapitreRepository;
        $this->challengeRepository = $challengeRepository;
        $this->communauteRepository = $communauteRepository;
        $this->postRepository = $postRepository;
        $this->commentaireRepository = $commentaireRepository;
        $this->quizRepository = $quizRepository;
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
                // USER ACTIONS
                'create_student' => $this->createStudent($params),
                'update_student' => $this->updateStudent($params),
                'update_user' => $this->updateStudent($params),
                'filter_students' => $this->filterStudents($params),
                'get_user' => $this->getUser($params),
                'suspend_user' => $this->suspendUser($params),
                'unsuspend_user' => $this->unsuspendUser($params),
                'get_inactive_users' => $this->getInactiveUsers($params),
                
                // COURSE ACTIONS
                'create_course' => $this->createCourse($params),
                'update_course' => $this->updateCourse($params),
                'get_course' => $this->getCourse($params),
                'list_courses' => $this->listCourses($params),
                'add_chapter' => $this->addChapter($params),
                
                // EVENT ACTIONS
                'create_event' => $this->createEvent($params),
                'update_event' => $this->updateEvent($params),
                'delete_event' => $this->deleteEvent($params),
                'get_event' => $this->getEvent($params),
                'list_events' => $this->listEvents($params),
                
                // CHALLENGE ACTIONS
                'create_challenge' => $this->createChallenge($params),
                'update_challenge' => $this->updateChallenge($params),
                'get_challenge' => $this->getChallenge($params),
                'list_challenges' => $this->listChallenges($params),
                
                // COMMUNITY ACTIONS
                'create_community' => $this->createCommunity($params, $requestingUser),
                'update_community' => $this->updateCommunity($params, $requestingUser),
                'delete_community' => $this->deleteCommunity($params, $requestingUser),
                'get_community' => $this->getCommunity($params),
                'list_communities' => $this->listCommunities($params),
                
                // QUIZ ACTIONS
                'create_quiz' => $this->createQuiz($params),
                'get_quiz' => $this->getQuiz($params),
                
                // POST ACTIONS
                'list_posts' => $this->listPosts($params),
                'get_post' => $this->getPost($params),
                
                // COMMENT ACTIONS
                'list_comments' => $this->listComments($params),
                'get_comment' => $this->getComment($params),
                
                // TEAM ACTIONS
                'list_teams' => $this->listTeams($params),
                'get_team' => $this->getTeam($params),
                'list_students' => $this->listStudents($params),
                
                // STUDENT ACTIONS
                'create_team' => $this->createTeam($params),
                'enroll_in_course' => $this->enrollInCourse($params),
                'join_community' => $this->joinCommunity($params),
                
                // GENERAL
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
            'create_student', 'update_student', 'update_user', 'filter_students', 'get_user',
            'suspend_user', 'unsuspend_user', 'get_inactive_users',
            'create_course', 'update_course', 'add_chapter',
            'create_event', 'update_event', 'delete_event',
            'create_challenge', 'update_challenge',
            'update_community',
            'create_quiz'
        ];

        // Actions réservées aux étudiants
        $studentOnlyActions = [
            'create_team', 'enroll_in_course', 'join_community', 'create_community',
            'update_community', 'delete_community'
        ];

        // Actions admin uniquement
        if (in_array($action, $adminOnlyActions)) {
            return $user->getRole() === 'ADMIN';
        }

        // Actions étudiant uniquement
        if (in_array($action, $studentOnlyActions)) {
            return $user->getRole() === 'ETUDIANT';
        }

        // Actions publiques (get, list)
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
     * Trouve un événement de manière intelligente (par ID ou titre)
     */
    private function findEventIntelligently(array $params): ?\App\Entity\Evenement
    {
        // 1. Chercher par ID direct
        if (!empty($params['id']) || !empty($params['event_id'])) {
            $eventId = $params['id'] ?? $params['event_id'];
            $event = $this->evenementRepository->find($eventId);
            if ($event) {
                return $event;
            }
        }

        // 2. Chercher par titre exact
        if (!empty($params['titre'])) {
            $event = $this->evenementRepository->findOneBy(['titre' => $params['titre']]);
            if ($event) {
                return $event;
            }
        }

        // 3. Chercher par titre partiel (case insensitive)
        if (!empty($params['titre'])) {
            $qb = $this->em->createQueryBuilder();
            $events = $qb->select('e')
                ->from(\App\Entity\Evenement::class, 'e')
                ->where('LOWER(e.titre) LIKE LOWER(:titre)')
                ->setParameter('titre', '%' . $params['titre'] . '%')
                ->setMaxResults(1)
                ->getQuery()
                ->getResult();
            
            if (!empty($events)) {
                return $events[0];
            }
        }

        return null;
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
                ->from(\App\Entity\User::class, 'u')
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
                ->from(\App\Entity\User::class, 'u')
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
            ->from(\App\Entity\User::class, 'u')
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

        // Vérifier que l'événement existe (par ID ou par nom)
        $evenement = null;
        if (is_numeric($params['evenement_id'])) {
            $evenement = $this->evenementRepository->find($params['evenement_id']);
        } else {
            // Chercher par titre
            $qb = $this->em->createQueryBuilder();
            $results = $qb->select('e')
                ->from(\App\Entity\Evenement::class, 'e')
                ->where('LOWER(e.titre) LIKE LOWER(:titre)')
                ->setParameter('titre', '%' . $params['evenement_id'] . '%')
                ->setMaxResults(1)
                ->getQuery()
                ->getResult();
            
            if (!empty($results)) {
                $evenement = $results[0];
            }
        }
        
        if (!$evenement) {
            return [
                'success' => false,
                'error' => 'Événement introuvable. Utilisez "voir les événements" pour voir les événements disponibles.'
            ];
        }

        // Vérifier les membres (minimum 4, maximum 6)
        if (empty($params['membres']) || !is_array($params['membres'])) {
            return [
                'success' => false,
                'error' => 'Une équipe doit avoir au moins 4 membres. Fournissez les IDs des étudiants.'
            ];
        }

        $membresIds = $params['membres'];
        if (count($membresIds) < 4) {
            return [
                'success' => false,
                'error' => 'Une équipe doit avoir au moins 4 membres (vous en avez fourni ' . count($membresIds) . ')'
            ];
        }

        if (count($membresIds) > 6) {
            return [
                'success' => false,
                'error' => 'Une équipe ne peut pas avoir plus de 6 membres (vous en avez fourni ' . count($membresIds) . ')'
            ];
        }

        // Récupérer les étudiants (par ID ou par nom)
        $etudiants = [];
        foreach ($membresIds as $membreInput) {
            $etudiant = null;
            
            // Si c'est un nombre, chercher par ID
            if (is_numeric($membreInput)) {
                $etudiant = $this->userRepository->find($membreInput);
                // Verify it's a student and not suspended
                if ($etudiant && ($etudiant->getRole() !== 'ETUDIANT' || $etudiant->getIsSuspended())) {
                    $etudiant = null;
                }
            } else {
                // Sinon, chercher par nom (format: "prenom nom" ou "nom prenom")
                $membreInput = trim($membreInput);
                $parts = preg_split('/\s+/', $membreInput); // Split by any whitespace
                
                if (count($parts) >= 2) {
                    $qb = $this->em->createQueryBuilder();
                    
                    // Try different combinations for multi-part names
                    // Example: "baha ben kileni" could be:
                    // - prenom="baha", nom="ben kileni"
                    // - prenom="baha ben", nom="kileni"
                    
                    // Strategy 1: First word = prenom, rest = nom
                    $prenom1 = $parts[0];
                    $nom1 = implode(' ', array_slice($parts, 1));
                    
                    $results = $qb->select('u')
                        ->from(\App\Entity\User::class, 'u')
                        ->where('LOWER(u.prenom) LIKE LOWER(:prenom)')
                        ->andWhere('LOWER(u.nom) LIKE LOWER(:nom)')
                        ->andWhere('u.role = :role')
                        ->andWhere('u.isSuspended = false')
                        ->setParameter('prenom', '%' . $prenom1 . '%')
                        ->setParameter('nom', '%' . $nom1 . '%')
                        ->setParameter('role', 'ETUDIANT')
                        ->setMaxResults(1)
                        ->getQuery()
                        ->getResult();
                    
                    if (!empty($results)) {
                        $etudiant = $results[0];
                    } else {
                        // Strategy 2: Last word = prenom, rest = nom (reversed)
                        $prenom2 = $parts[count($parts) - 1];
                        $nom2 = implode(' ', array_slice($parts, 0, -1));
                        
                        $qb = $this->em->createQueryBuilder();
                        $results = $qb->select('u')
                            ->from(\App\Entity\User::class, 'u')
                            ->where('LOWER(u.nom) LIKE LOWER(:nom)')
                            ->andWhere('LOWER(u.prenom) LIKE LOWER(:prenom)')
                            ->andWhere('u.role = :role')
                            ->andWhere('u.isSuspended = false')
                            ->setParameter('nom', '%' . $nom2 . '%')
                            ->setParameter('prenom', '%' . $prenom2 . '%')
                            ->setParameter('role', 'ETUDIANT')
                            ->setMaxResults(1)
                            ->getQuery()
                            ->getResult();
                        
                        if (!empty($results)) {
                            $etudiant = $results[0];
                        } else {
                            // Strategy 3: Search by full name in both fields (fuzzy)
                            $fullName = strtolower($membreInput);
                            $qb = $this->em->createQueryBuilder();
                            $results = $qb->select('u')
                                ->from(\App\Entity\User::class, 'u')
                                ->where('LOWER(CONCAT(u.prenom, \' \', u.nom)) LIKE :fullname')
                                ->orWhere('LOWER(CONCAT(u.nom, \' \', u.prenom)) LIKE :fullname')
                                ->andWhere('u.role = :role')
                                ->andWhere('u.isSuspended = false')
                                ->setParameter('fullname', '%' . $fullName . '%')
                                ->setParameter('role', 'ETUDIANT')
                                ->setMaxResults(1)
                                ->getQuery()
                                ->getResult();
                            
                            if (!empty($results)) {
                                $etudiant = $results[0];
                            }
                        }
                    }
                }
            }
            
            if (!$etudiant) {
                return [
                    'success' => false,
                    'error' => "Étudiant '{$membreInput}' introuvable. Utilisez 'voir les étudiants' pour voir les noms disponibles."
                ];
            }
            
            if ($etudiant->getRole() !== 'ETUDIANT') {
                return [
                    'success' => false,
                    'error' => "L'utilisateur {$etudiant->getPrenom()} {$etudiant->getNom()} n'est pas un étudiant"
                ];
            }
            
            $etudiants[] = $etudiant;
        }

        // Créer l'équipe
        $equipe = new Equipe();
        $equipe->setNom($params['nom']);
        $equipe->setEvenement($evenement);
        
        // Ajouter les membres
        foreach ($etudiants as $etudiant) {
            $equipe->addEtudiant($etudiant);
        }

        $this->em->persist($equipe);
        $this->em->flush();

        return [
            'success' => true,
            'message' => "Équipe créée avec succès: {$params['nom']} ({count($etudiants)} membres)",
            'team_id' => $equipe->getId(),
            'event' => $evenement->getTitre(),
            'membres_count' => count($etudiants)
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
            ->from(\App\Entity\User::class, 'u')
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
            ->from(\App\Entity\GestionDeCours\Cours::class, 'c')
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

    // ========== COURSE ACTIONS ==========
    
    private function createCourse(array $params): array
    {
        $required = ['titre', 'description', 'niveau'];
        foreach ($required as $field) {
            if (empty($params[$field])) {
                return ['success' => false, 'error' => "Champ requis: {$field}"];
            }
        }

        $cours = new \App\Entity\GestionDeCours\Cours();
        $cours->setTitre($params['titre']);
        $cours->setDescription($params['description']);
        $cours->setNiveau($params['niveau']);
        
        // Set matiere with a default value if not provided
        $cours->setMatiere($params['matiere'] ?? 'Général');
        
        $cours->setDuree($params['duree'] ?? 10); // Default 10 hours
        $cours->setCreatedAt(new \DateTimeImmutable());

        $this->em->persist($cours);
        $this->em->flush();

        return [
            'success' => true,
            'message' => "Cours créé: {$params['titre']}",
            'course_id' => $cours->getId()
        ];
    }

    private function updateCourse(array $params): array
    {
        if (empty($params['id'])) {
            return ['success' => false, 'error' => 'ID cours requis'];
        }

        $cours = $this->coursRepository->find($params['id']);
        if (!$cours) {
            return ['success' => false, 'error' => 'Cours introuvable'];
        }

        if (isset($params['titre'])) $cours->setTitre($params['titre']);
        if (isset($params['description'])) $cours->setDescription($params['description']);
        if (isset($params['niveau'])) $cours->setNiveau($params['niveau']);
        if (isset($params['duree'])) $cours->setDuree($params['duree']);

        $this->em->flush();

        return [
            'success' => true,
            'message' => "Cours modifié: {$cours->getTitre()}",
            'course_id' => $cours->getId()
        ];
    }

    private function getCourse(array $params): array
    {
        if (empty($params['id'])) {
            return ['success' => false, 'error' => 'ID cours requis'];
        }

        $cours = $this->coursRepository->find($params['id']);
        if (!$cours) {
            return ['success' => false, 'error' => 'Cours introuvable'];
        }

        return [
            'success' => true,
            'course' => [
                'id' => $cours->getId(),
                'titre' => $cours->getTitre(),
                'description' => $cours->getDescription(),
                'niveau' => $cours->getNiveau(),
                'duree' => $cours->getDuree(),
                'chapitres_count' => $cours->getChapitres()->count()
            ]
        ];
    }

    private function listCourses(array $params): array
    {
        $courses = $this->coursRepository->findAll();
        
        return [
            'success' => true,
            'count' => count($courses),
            'courses' => array_map(function($c) {
                return [
                    'id' => $c->getId(),
                    'titre' => $c->getTitre(),
                    'niveau' => $c->getNiveau(),
                    'chapitres' => $c->getChapitres()->count()
                ];
            }, $courses)
        ];
    }

    private function addChapter(array $params): array
    {
        $required = ['cours_id', 'titre'];
        foreach ($required as $field) {
            if (empty($params[$field])) {
                return ['success' => false, 'error' => "Champ requis: {$field}"];
            }
        }

        $cours = $this->coursRepository->find($params['cours_id']);
        if (!$cours) {
            return ['success' => false, 'error' => 'Cours introuvable'];
        }

        $chapitre = new \App\Entity\GestionDeCours\Chapitre();
        $chapitre->setTitre($params['titre']);
        $chapitre->setContenu($params['contenu'] ?? '');
        $chapitre->setCours($cours);

        $this->em->persist($chapitre);
        $this->em->flush();

        return [
            'success' => true,
            'message' => "Chapitre ajouté: {$params['titre']}",
            'chapter_id' => $chapitre->getId()
        ];
    }

    // ========== EVENT ACTIONS ==========
    
    private function createEvent(array $params): array
    {
        $required = ['titre', 'date_debut', 'date_fin', 'lieu'];
        foreach ($required as $field) {
            if (empty($params[$field])) {
                return ['success' => false, 'error' => "Champ requis: {$field}"];
            }
        }

        $event = new \App\Entity\Evenement();
        $event->setTitre($params['titre']);
        $event->setDescription($params['description'] ?? '');
        $event->setDateDebut(new \DateTime($params['date_debut']));
        $event->setDateFin(new \DateTime($params['date_fin']));
        $event->setLieu($params['lieu']);
        $event->setNbMax($params['capacite'] ?? $params['nbMax'] ?? 50); // Use nbMax instead of capacite
        $event->setType(\App\Enum\TypeEvenement::WORKSHOP); // Default type

        $this->em->persist($event);
        $this->em->flush();

        return [
            'success' => true,
            'message' => "Événement créé: {$params['titre']}",
            'event_id' => $event->getId()
        ];
    }

    private function updateEvent(array $params): array
    {
        if (empty($params['id'])) {
            return ['success' => false, 'error' => 'ID événement requis'];
        }

        $event = $this->evenementRepository->find($params['id']);
        if (!$event) {
            return ['success' => false, 'error' => 'Événement introuvable'];
        }

        if (isset($params['titre'])) $event->setTitre($params['titre']);
        if (isset($params['description'])) $event->setDescription($params['description']);
        if (isset($params['lieu'])) $event->setLieu($params['lieu']);
        if (isset($params['capacite'])) $event->setNbMax($params['capacite']); // Use nbMax
        if (isset($params['nbMax'])) $event->setNbMax($params['nbMax']);

        $this->em->flush();

        return [
            'success' => true,
            'message' => "Événement modifié: {$event->getTitre()}",
            'event_id' => $event->getId()
        ];
    }

    private function getEvent(array $params): array
    {
        if (empty($params['id'])) {
            return ['success' => false, 'error' => 'ID événement requis'];
        }

        $event = $this->evenementRepository->find($params['id']);
        if (!$event) {
            return ['success' => false, 'error' => 'Événement introuvable'];
        }

        return [
            'success' => true,
            'event' => [
                'id' => $event->getId(),
                'titre' => $event->getTitre(),
                'description' => $event->getDescription(),
                'date_debut' => $event->getDateDebut()->format('Y-m-d H:i'),
                'date_fin' => $event->getDateFin()->format('Y-m-d H:i'),
                'lieu' => $event->getLieu(),
                'capacite' => $event->getNbMax()
            ]
        ];
    }

    private function deleteEvent(array $params): array
    {
        if (empty($params['id'])) {
            return ['success' => false, 'error' => 'ID événement requis'];
        }

        $event = $this->evenementRepository->find($params['id']);
        if (!$event) {
            return ['success' => false, 'error' => 'Événement introuvable'];
        }

        $eventTitle = $event->getTitre();
        
        try {
            $this->em->remove($event);
            $this->em->flush();

            return [
                'success' => true,
                'message' => "Événement supprimé: {$eventTitle}"
            ];
        } catch (\Exception $e) {
            $this->logger->error('Delete event error', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'error' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ];
        }
    }

    private function listEvents(array $params): array
    {
        $events = $this->evenementRepository->findAll();
        
        return [
            'success' => true,
            'count' => count($events),
            'events' => array_map(function($e) {
                return [
                    'id' => $e->getId(),
                    'titre' => $e->getTitre(),
                    'date_debut' => $e->getDateDebut()->format('Y-m-d H:i'),
                    'lieu' => $e->getLieu()
                ];
            }, $events)
        ];
    }

    // ========== CHALLENGE ACTIONS ==========
    
    private function createChallenge(array $params): array
    {
        $required = ['titre', 'description', 'niveau'];
        foreach ($required as $field) {
            if (empty($params[$field])) {
                return ['success' => false, 'error' => "Champ requis: {$field}"];
            }
        }

        $challenge = new \App\Entity\Challenge();
        $challenge->setTitre($params['titre']);
        $challenge->setDescription($params['description']);
        $challenge->setNiveau($params['niveau']);
        $challenge->setDateDebut(new \DateTime($params['date_debut'] ?? 'now'));
        $challenge->setDateFin(new \DateTime($params['date_fin'] ?? '+7 days'));

        $this->em->persist($challenge);
        $this->em->flush();

        return [
            'success' => true,
            'message' => "Challenge créé: {$params['titre']}",
            'challenge_id' => $challenge->getId()
        ];
    }

    private function updateChallenge(array $params): array
    {
        if (empty($params['id'])) {
            return ['success' => false, 'error' => 'ID challenge requis'];
        }

        $challenge = $this->challengeRepository->find($params['id']);
        if (!$challenge) {
            return ['success' => false, 'error' => 'Challenge introuvable'];
        }

        if (isset($params['titre'])) $challenge->setTitre($params['titre']);
        if (isset($params['description'])) $challenge->setDescription($params['description']);
        if (isset($params['niveau'])) $challenge->setNiveau($params['niveau']);
        if (isset($params['date_debut'])) $challenge->setDateDebut(new \DateTime($params['date_debut']));
        if (isset($params['date_fin'])) $challenge->setDateFin(new \DateTime($params['date_fin']));

        $this->em->flush();

        return [
            'success' => true,
            'message' => "Challenge modifié: {$challenge->getTitre()}",
            'challenge_id' => $challenge->getId()
        ];
    }

    private function getChallenge(array $params): array
    {
        if (empty($params['id'])) {
            return ['success' => false, 'error' => 'ID challenge requis'];
        }

        $challenge = $this->challengeRepository->find($params['id']);
        if (!$challenge) {
            return ['success' => false, 'error' => 'Challenge introuvable'];
        }

        return [
            'success' => true,
            'challenge' => [
                'id' => $challenge->getId(),
                'titre' => $challenge->getTitre(),
                'description' => $challenge->getDescription(),
                'niveau' => $challenge->getNiveau(),
                'date_debut' => $challenge->getDateDebut()->format('Y-m-d'),
                'date_fin' => $challenge->getDateFin()->format('Y-m-d')
            ]
        ];
    }

    private function listChallenges(array $params): array
    {
        $challenges = $this->challengeRepository->findAll();
        
        return [
            'success' => true,
            'count' => count($challenges),
            'challenges' => array_map(function($ch) {
                return [
                    'id' => $ch->getId(),
                    'titre' => $ch->getTitre(),
                    'niveau' => $ch->getNiveau()
                ];
            }, $challenges)
        ];
    }

    // ========== COMMUNITY ACTIONS ==========
    
    private function createCommunity(array $params, User $creator): array
    {
        $required = ['nom', 'description'];
        foreach ($required as $field) {
            if (empty($params[$field])) {
                return ['success' => false, 'error' => "Champ requis: {$field}"];
            }
        }

        $community = new \App\Entity\Communaute();
        $community->setNom($params['nom']);
        $community->setDescription($params['description']);
        
        // Définir le créateur comme propriétaire
        $community->setOwner($creator);
        
        // Ajouter automatiquement le créateur comme premier membre
        $community->addMember($creator);

        $this->em->persist($community);
        $this->em->flush();

        return [
            'success' => true,
            'message' => "Communauté créée: {$params['nom']}",
            'community_id' => $community->getId()
        ];
    }

    private function updateCommunity(array $params, User $requestingUser): array
    {
        if (empty($params['id']) && empty($params['nom'])) {
            return ['success' => false, 'error' => 'ID ou nom de communauté requis'];
        }

        // Chercher par ID ou par nom
        $community = null;
        if (!empty($params['id']) && is_numeric($params['id'])) {
            $community = $this->communauteRepository->find($params['id']);
        } else {
            // Chercher par nom
            $searchName = $params['id'] ?? $params['nom'];
            $communities = $this->communauteRepository->findAll();
            foreach ($communities as $c) {
                if (stripos($c->getNom(), $searchName) !== false) {
                    $community = $c;
                    break;
                }
            }
        }

        if (!$community) {
            return ['success' => false, 'error' => 'Communauté introuvable'];
        }

        // Vérifier que l'utilisateur est le propriétaire
        if ($community->getOwner() && $community->getOwner()->getId() !== $requestingUser->getId()) {
            return ['success' => false, 'error' => 'Seul le propriétaire peut modifier cette communauté'];
        }

        // Mettre à jour les champs fournis
        if (isset($params['nom']) && $params['nom'] !== $community->getNom()) {
            $community->setNom($params['nom']);
        }
        if (isset($params['description'])) {
            $community->setDescription($params['description']);
        }

        $this->em->flush();

        return [
            'success' => true,
            'message' => "Communauté modifiée: {$community->getNom()}",
            'community_id' => $community->getId()
        ];
    }

    private function deleteCommunity(array $params, User $requestingUser): array
    {
        if (empty($params['id']) && empty($params['nom'])) {
            return ['success' => false, 'error' => 'ID ou nom de communauté requis'];
        }

        // Chercher par ID ou par nom
        $community = null;
        if (!empty($params['id']) && is_numeric($params['id'])) {
            $community = $this->communauteRepository->find($params['id']);
        } else {
            // Chercher par nom
            $searchName = $params['id'] ?? $params['nom'];
            $communities = $this->communauteRepository->findAll();
            foreach ($communities as $c) {
                if (stripos($c->getNom(), $searchName) !== false) {
                    $community = $c;
                    break;
                }
            }
        }

        if (!$community) {
            return ['success' => false, 'error' => 'Communauté introuvable'];
        }

        // Vérifier que l'utilisateur est le propriétaire
        if ($community->getOwner() && $community->getOwner()->getId() !== $requestingUser->getId()) {
            return ['success' => false, 'error' => 'Seul le propriétaire peut supprimer cette communauté'];
        }

        $communityName = $community->getNom();
        
        $this->em->remove($community);
        $this->em->flush();

        return [
            'success' => true,
            'message' => "Communauté supprimée: {$communityName}"
        ];
    }

    private function getCommunity(array $params): array
    {
        if (empty($params['id'])) {
            return ['success' => false, 'error' => 'ID communauté requis'];
        }

        $community = $this->communauteRepository->find($params['id']);
        if (!$community) {
            return ['success' => false, 'error' => 'Communauté introuvable'];
        }

        return [
            'success' => true,
            'community' => [
                'id' => $community->getId(),
                'nom' => $community->getNom(),
                'description' => $community->getDescription(),
                'membres_count' => $community->getMembers()->count()
            ]
        ];
    }

    private function listCommunities(array $params): array
    {
        $communities = $this->communauteRepository->findAll();
        
        return [
            'success' => true,
            'count' => count($communities),
            'communities' => array_map(function($c) {
                return [
                    'id' => $c->getId(),
                    'nom' => $c->getNom(),
                    'membres' => $c->getMembers()->count()
                ];
            }, $communities)
        ];
    }

    // ========== QUIZ ACTIONS ==========
    
    private function createQuiz(array $params): array
    {
        $required = ['titre', 'description'];
        foreach ($required as $field) {
            if (empty($params[$field])) {
                return ['success' => false, 'error' => "Champ requis: {$field}"];
            }
        }

        $quiz = new \App\Entity\Quiz();
        $quiz->setTitre($params['titre']);
        $quiz->setDescription($params['description']);
        $quiz->setEtat($params['etat'] ?? 'brouillon');
        
        // Optionally link to a chapter
        if (!empty($params['chapitre_id'])) {
            $chapitre = $this->chapitreRepository->find($params['chapitre_id']);
            if ($chapitre) {
                $quiz->setChapitre($chapitre);
            }
        }

        $this->em->persist($quiz);
        $this->em->flush();

        return [
            'success' => true,
            'message' => "Quiz créé: {$params['titre']}",
            'quiz_id' => $quiz->getId()
        ];
    }

    private function getQuiz(array $params): array
    {
        if (empty($params['id'])) {
            return ['success' => false, 'error' => 'ID quiz requis'];
        }

        $quiz = $this->quizRepository->find($params['id']);
        if (!$quiz) {
            return ['success' => false, 'error' => 'Quiz introuvable'];
        }

        return [
            'success' => true,
            'quiz' => [
                'id' => $quiz->getId(),
                'titre' => $quiz->getTitre(),
                'description' => $quiz->getDescription(),
                'etat' => $quiz->getEtat(),
                'chapitre_id' => $quiz->getChapitre() ? $quiz->getChapitre()->getId() : null,
                'questions_count' => $quiz->getQuestions()->count()
            ]
        ];
    }

    // ========== POST ACTIONS ==========
    
    private function listPosts(array $params): array
    {
        $limit = $params['limit'] ?? 50;
        $posts = $this->postRepository->findBy([], ['createdAt' => 'DESC'], $limit);
        
        return [
            'success' => true,
            'count' => count($posts),
            'posts' => array_map(function($p) {
                return [
                    'id' => $p->getId(),
                    'contenu' => substr($p->getContenu(), 0, 150) . (strlen($p->getContenu()) > 150 ? '...' : ''),
                    'auteur' => $p->getUser() ? $p->getUser()->getPrenom() . ' ' . $p->getUser()->getNom() : 'Inconnu',
                    'communaute' => $p->getCommunaute() ? $p->getCommunaute()->getNom() : 'Aucune',
                    'created_at' => $p->getCreatedAt()->format('d/m/Y H:i'),
                    'has_image' => !empty($p->getImageFile()),
                    'has_video' => !empty($p->getVideoFile()),
                    'commentaires_count' => $p->getCommentaires()->count()
                ];
            }, $posts)
        ];
    }

    private function getPost(array $params): array
    {
        if (empty($params['id'])) {
            return ['success' => false, 'error' => 'ID post requis'];
        }

        $post = $this->postRepository->find($params['id']);
        if (!$post) {
            return ['success' => false, 'error' => 'Post introuvable'];
        }

        return [
            'success' => true,
            'post' => [
                'id' => $post->getId(),
                'contenu' => $post->getContenu(),
                'auteur' => $post->getUser() ? $post->getUser()->getPrenom() . ' ' . $post->getUser()->getNom() : 'Inconnu',
                'communaute' => $post->getCommunaute() ? $post->getCommunaute()->getNom() : 'Aucune',
                'created_at' => $post->getCreatedAt()->format('d/m/Y H:i'),
                'has_image' => !empty($post->getImageFile()),
                'has_video' => !empty($post->getVideoFile()),
                'commentaires_count' => $post->getCommentaires()->count()
            ]
        ];
    }

    // ========== COMMENT ACTIONS ==========
    
    private function listComments(array $params): array
    {
        $limit = $params['limit'] ?? 50;
        
        // Filter by post if specified
        if (!empty($params['post_id'])) {
            $comments = $this->commentaireRepository->findBy(
                ['post' => $params['post_id']], 
                ['createdAt' => 'DESC'], 
                $limit
            );
        } else {
            $comments = $this->commentaireRepository->findBy([], ['createdAt' => 'DESC'], $limit);
        }
        
        return [
            'success' => true,
            'count' => count($comments),
            'comments' => array_map(function($c) {
                return [
                    'id' => $c->getId(),
                    'contenu' => $c->getContenu(),
                    'auteur' => $c->getUser() ? $c->getUser()->getPrenom() . ' ' . $c->getUser()->getNom() : 'Inconnu',
                    'post_id' => $c->getPost() ? $c->getPost()->getId() : null,
                    'post_preview' => $c->getPost() ? substr($c->getPost()->getContenu(), 0, 50) . '...' : null,
                    'created_at' => $c->getCreatedAt()->format('d/m/Y H:i')
                ];
            }, $comments)
        ];
    }

    private function getComment(array $params): array
    {
        if (empty($params['id'])) {
            return ['success' => false, 'error' => 'ID commentaire requis'];
        }

        $comment = $this->commentaireRepository->find($params['id']);
        if (!$comment) {
            return ['success' => false, 'error' => 'Commentaire introuvable'];
        }

        return [
            'success' => true,
            'comment' => [
                'id' => $comment->getId(),
                'contenu' => $comment->getContenu(),
                'auteur' => $comment->getUser() ? $comment->getUser()->getPrenom() . ' ' . $comment->getUser()->getNom() : 'Inconnu',
                'post_id' => $comment->getPost() ? $comment->getPost()->getId() : null,
                'created_at' => $comment->getCreatedAt()->format('d/m/Y H:i')
            ]
        ];
    }

    // ========== TEAM ACTIONS ==========
    
    private function listStudents(array $params): array
    {
        $limit = $params['limit'] ?? 50;
        
        // Get only students (not admins)
        $qb = $this->em->createQueryBuilder();
        $students = $qb->select('u')
            ->from(\App\Entity\User::class, 'u')
            ->where('u.role = :role')
            ->andWhere('u.isSuspended = false')
            ->setParameter('role', 'ETUDIANT')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
        
        return [
            'success' => true,
            'count' => count($students),
            'students' => array_map(function($s) {
                return [
                    'id' => $s->getId(),
                    'nom' => $s->getNom(),
                    'prenom' => $s->getPrenom(),
                    'email' => $s->getEmail(),
                    'niveau' => method_exists($s, 'getNiveau') ? $s->getNiveau() : 'N/A'
                ];
            }, $students)
        ];
    }
    
    private function listTeams(array $params): array
    {
        $limit = $params['limit'] ?? 50;
        
        // Filter by event if specified
        if (!empty($params['evenement_id'])) {
            $teams = $this->equipeRepository->findBy(
                ['evenement' => $params['evenement_id']], 
                ['nom' => 'ASC'], 
                $limit
            );
        } else {
            $teams = $this->equipeRepository->findBy([], ['nom' => 'ASC'], $limit);
        }
        
        return [
            'success' => true,
            'count' => count($teams),
            'teams' => array_map(function($t) {
                return [
                    'id' => $t->getId(),
                    'nom' => $t->getNom(),
                    'evenement' => $t->getEvenement()->getTitre(),
                    'evenement_id' => $t->getEvenement()->getId(),
                    'membres_count' => $t->getEtudiants()->count(),
                    'membres' => array_map(function($e) {
                        return $e->getPrenom() . ' ' . $e->getNom();
                    }, $t->getEtudiants()->toArray())
                ];
            }, $teams)
        ];
    }

    private function getTeam(array $params): array
    {
        if (empty($params['id'])) {
            return ['success' => false, 'error' => 'ID équipe requis'];
        }

        $team = $this->equipeRepository->find($params['id']);
        if (!$team) {
            return ['success' => false, 'error' => 'Équipe introuvable'];
        }

        return [
            'success' => true,
            'team' => [
                'id' => $team->getId(),
                'nom' => $team->getNom(),
                'evenement' => $team->getEvenement()->getTitre(),
                'evenement_id' => $team->getEvenement()->getId(),
                'membres_count' => $team->getEtudiants()->count(),
                'membres' => array_map(function($e) {
                    return [
                        'id' => $e->getId(),
                        'nom' => $e->getNom(),
                        'prenom' => $e->getPrenom(),
                        'email' => $e->getEmail()
                    ];
                }, $team->getEtudiants()->toArray())
            ]
        ];
    }

    // ========== STUDENT ACTIONS ==========
    
    private function enrollInCourse(array $params): array
    {
        if (empty($params['cours_id']) || empty($params['user_id'])) {
            return ['success' => false, 'error' => 'ID cours et utilisateur requis'];
        }

        $cours = $this->coursRepository->find($params['cours_id']);
        if (!$cours) {
            return ['success' => false, 'error' => 'Cours introuvable'];
        }

        $user = $this->userRepository->find($params['user_id']);
        if (!$user) {
            return ['success' => false, 'error' => 'Utilisateur introuvable'];
        }

        // Note: Course enrollment relationship not yet implemented in entities
        return [
            'success' => false,
            'error' => 'Fonctionnalité d\'inscription aux cours en cours de développement'
        ];
    }

    private function joinCommunity(array $params): array
    {
        if (empty($params['community_id']) || empty($params['user_id'])) {
            return ['success' => false, 'error' => 'ID communauté et utilisateur requis'];
        }

        $community = $this->communauteRepository->find($params['community_id']);
        if (!$community) {
            return ['success' => false, 'error' => 'Communauté introuvable'];
        }

        $user = $this->userRepository->find($params['user_id']);
        if (!$user) {
            return ['success' => false, 'error' => 'Utilisateur introuvable'];
        }

        $community->addMember($user);
        $this->em->flush();

        return [
            'success' => true,
            'message' => "Rejoint la communauté: {$community->getNom()}"
        ];
    }

    /**
     * Liste toutes les actions disponibles pour un utilisateur
     */
    public function getAvailableActions(User $user): array
    {
        $actions = [
            'public' => [
                'get_popular_courses' => 'Voir les cours populaires',
                'list_courses' => 'Lister tous les cours',
                'list_events' => 'Lister tous les événements',
                'list_challenges' => 'Lister tous les challenges',
                'list_communities' => 'Lister toutes les communautés',
                'list_posts' => 'Lister tous les posts',
                'list_comments' => 'Lister tous les commentaires',
                'list_teams' => 'Lister toutes les équipes',
                'list_students' => 'Lister tous les étudiants'
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
                'get_inactive_users' => 'Lister les utilisateurs inactifs',
                'create_course' => 'Créer un cours',
                'update_course' => 'Modifier un cours',
                'add_chapter' => 'Ajouter un chapitre',
                'create_event' => 'Créer un événement',
                'update_event' => 'Modifier un événement',
                'delete_event' => 'Supprimer un événement',
                'create_challenge' => 'Créer un challenge',
                'update_challenge' => 'Modifier un challenge',
                'create_community' => 'Créer une communauté',
                'update_community' => 'Modifier une communauté',
                'delete_community' => 'Supprimer une communauté',
                'create_quiz' => 'Créer un quiz',
                'list_posts' => 'Lister tous les posts',
                'get_post' => 'Voir les détails d\'un post',
                'list_comments' => 'Lister tous les commentaires',
                'get_comment' => 'Voir les détails d\'un commentaire',
                'list_teams' => 'Lister toutes les équipes',
                'get_team' => 'Voir les détails d\'une équipe'
            ];
        }

        if ($user->getRole() === 'ETUDIANT') {
            $actions['student'] = [
                'create_team' => 'Créer une équipe pour un événement',
                'enroll_in_course' => 'S\'inscrire à un cours',
                'join_community' => 'Rejoindre une communauté',
                'create_community' => 'Créer une communauté',
                'update_community' => 'Modifier une communauté',
                'delete_community' => 'Supprimer une communauté',
                'list_teams' => 'Voir toutes les équipes',
                'get_team' => 'Voir les détails d\'une équipe',
                'list_students' => 'Voir tous les étudiants'
            ];
        }

        return $actions;
    }
}
