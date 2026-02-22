<?php

namespace App\Service;

use App\Repository\Cours\CoursRepository;
use App\Repository\EvenementRepository;
use App\Repository\UserRepository;
use App\Bundle\UserActivityBundle\Repository\UserActivityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Service RAG (Retrieval-Augmented Generation)
 * Collecte le contexte depuis la base de données pour l'IA
 */
class RAGService
{
    private EntityManagerInterface $em;
    private Security $security;
    private CoursRepository $coursRepository;
    private EvenementRepository $evenementRepository;
    private UserRepository $userRepository;
    private ?UserActivityRepository $activityRepository;
    private ?\App\Service\ActionExecutorService $actionExecutor;

    public function __construct(
        EntityManagerInterface $em,
        Security $security,
        CoursRepository $coursRepository,
        EvenementRepository $evenementRepository,
        UserRepository $userRepository,
        ?UserActivityRepository $activityRepository = null,
        ?\App\Service\ActionExecutorService $actionExecutor = null
    ) {
        $this->em = $em;
        $this->security = $security;
        $this->coursRepository = $coursRepository;
        $this->evenementRepository = $evenementRepository;
        $this->userRepository = $userRepository;
        $this->activityRepository = $activityRepository;
        $this->actionExecutor = $actionExecutor;
    }

    /**
     * Collecte le contexte selon l'intention de l'utilisateur
     */
    public function retrieveContext(string $query, ?string $intent = null): array
    {
        $user = $this->security->getUser();
        $context = [
            'user' => $user, // Ajouter l'objet utilisateur complet
            'user_name' => $user ? $user->getPrenom() . ' ' . $user->getNom() : 'Invité',
            'user_role' => $user ? $user->getRole() : 'GUEST',
            'user_level' => $user && method_exists($user, 'getNiveau') ? $user->getNiveau() : 'DEBUTANT',
            'locale' => 'fr', // TODO: Get from request
            'data' => []
        ];

        // Détection automatique de l'intention si non fournie
        if (!$intent) {
            $intent = $this->detectIntent($query);
        }

        // Collecte des données selon l'intention
        switch ($intent) {
            case 'recommend_course':
                $context['data'] = $this->getCoursesContext($user);
                break;
            
            case 'list_events':
                $context['data'] = $this->getEventsContext();
                break;
            
            case 'user_stats':
                $context['data'] = $this->getUserStatsContext($user);
                break;
            
            case 'user_management':
                if ($user && $user->getRole() === 'ADMIN') {
                    $context['data'] = $this->getUserManagementContext();
                }
                break;
            
            case 'general':
            default:
                $context['data'] = $this->getGeneralContext($user);
                break;
        }

        return $context;
    }

    /**
     * Détecte l'intention de l'utilisateur
     */
    private function detectIntent(string $query): string
    {
        $query = strtolower($query);

        // Recommandation de cours
        if (preg_match('/(cours|apprendre|étudier|recommand|python|java|web|programming)/i', $query)) {
            return 'recommend_course';
        }

        // Événements et équipes
        if (preg_match('/(événement|event|semaine|mois|particip|équipe|team|rejoindre|groupe)/i', $query)) {
            return 'list_events';
        }

        // Statistiques utilisateur
        if (preg_match('/(progrès|statistique|activité|historique|mes cours)/i', $query)) {
            return 'user_stats';
        }

        // Gestion utilisateurs (admin)
        if (preg_match('/(utilisateur|étudiant|inactif|suspendu|admin)/i', $query)) {
            return 'user_management';
        }

        return 'general';
    }

    /**
     * Contexte des cours disponibles - FILTRÉ selon le niveau utilisateur
     */
    private function getCoursesContext($user): array
    {
        $niveau = $user && method_exists($user, 'getNiveau') ? $user->getNiveau() : null;
        
        try {
            $allCours = $this->coursRepository->findAll();
            
            // Vérification de sécurité
            if (!is_array($allCours) && !($allCours instanceof \Traversable)) {
                $allCours = [];
            }
            
            // Mapper les niveaux pour filtrage intelligent
            $niveauMap = [
                'DEBUTANT' => ['DEBUTANT'],
                'INTERMEDIAIRE' => ['DEBUTANT', 'INTERMEDIAIRE'],
                'AVANCE' => ['INTERMEDIAIRE', 'AVANCE', 'EXPERT']
            ];
            
            $niveauxAcceptes = $niveauMap[$niveau] ?? ['DEBUTANT', 'INTERMEDIAIRE', 'AVANCE'];
            
            $coursData = [];
            $coursRecommandes = [];
            $coursAutres = [];
            
            foreach ($allCours as $c) {
                $coursInfo = [
                    'id' => $c->getId(),
                    'titre' => $c->getTitre(),
                    'matiere' => $c->getMatiere(),
                    'niveau' => $c->getNiveau(),
                    'duree' => $c->getDuree(),
                    'chapitres_count' => $c->getChapitres()->count(),
                    'description' => $c->getDescription()
                ];
                
                // Filtrer selon le niveau
                $coursNiveau = strtoupper($c->getNiveau());
                
                // Cours recommandés = niveau égal ou supérieur
                if ($niveau === 'AVANCE' && in_array($coursNiveau, ['INTERMEDIAIRE', 'AVANCE', 'EXPERT'])) {
                    $coursRecommandes[] = $coursInfo;
                } elseif ($niveau === 'INTERMEDIAIRE' && in_array($coursNiveau, ['INTERMEDIAIRE', 'AVANCE'])) {
                    $coursRecommandes[] = $coursInfo;
                } elseif ($niveau === 'DEBUTANT' && $coursNiveau === 'DEBUTANT') {
                    $coursRecommandes[] = $coursInfo;
                } else {
                    $coursAutres[] = $coursInfo;
                }
            }
            
            // Prioriser les cours recommandés
            $coursData = array_merge($coursRecommandes, $coursAutres);

            return [
                'user_level' => $niveau,
                'recommended_courses' => $coursRecommandes,
                'other_courses' => $coursAutres,
                'available_courses' => $coursData,
                'total_courses' => count($coursData),
                'total_recommended' => count($coursRecommandes)
            ];
        } catch (\Exception $e) {
            return [
                'user_level' => $niveau,
                'recommended_courses' => [],
                'other_courses' => [],
                'available_courses' => [],
                'total_courses' => 0,
                'error' => 'Erreur lors de la récupération des cours'
            ];
        }
    }

    /**
     * Contexte des événements avec équipes
     */
    private function getEventsContext(): array
    {
        try {
            $now = new \DateTime();
            $nextWeek = (clone $now)->modify('+7 days');
            
            $qb = $this->em->createQueryBuilder();
            $events = $qb->select('e')
                ->from('App\Entity\Evenement', 'e')
                ->where('e.dateDebut >= :now')
                ->andWhere('e.dateDebut <= :nextWeek')
                ->setParameter('now', $now)
                ->setParameter('nextWeek', $nextWeek)
                ->orderBy('e.dateDebut', 'ASC')
                ->getQuery()
                ->getResult();

            // Vérification de sécurité
            if (!is_array($events) && !($events instanceof \Traversable)) {
                $events = [];
            }

            $eventsData = [];
            foreach ($events as $event) {
                // Calculer places disponibles
                $capaciteMax = method_exists($event, 'getNbMax') ? $event->getNbMax() : 0;
                $participations = method_exists($event, 'getParticipations') ? $event->getParticipations()->count() : 0;
                $placesDisponibles = max(0, $capaciteMax - $participations);
                
                // Récupérer les équipes pour cet événement
                $equipes = method_exists($event, 'getEquipes') ? $event->getEquipes() : [];
                $equipesData = [];
                $totalMembres = 0;
                
                foreach ($equipes as $equipe) {
                    $membres = $equipe->getEtudiants()->count();
                    $totalMembres += $membres;
                    $equipesData[] = [
                        'id' => $equipe->getId(),
                        'nom' => $equipe->getNom(),
                        'membres_count' => $membres,
                        'complet' => $membres >= 6, // Max 6 membres
                        'peut_rejoindre' => $membres < 6 && $membres >= 4 // Entre 4 et 6
                    ];
                }
                
                $eventsData[] = [
                    'id' => $event->getId(),
                    'titre' => $event->getTitre(),
                    'date' => $event->getDateDebut()->format('d/m/Y H:i'),
                    'lieu' => $event->getLieu(),
                    'places_disponibles' => $placesDisponibles,
                    'capacite_max' => $capaciteMax,
                    'description' => substr($event->getDescription(), 0, 100) . '...',
                    'equipes' => $equipesData,
                    'total_equipes' => count($equipesData),
                    'total_membres' => $totalMembres,
                    'regles_equipes' => [
                        'min_membres' => 4,
                        'max_membres' => 6,
                        'une_equipe_par_evenement' => true
                    ]
                ];
            }

            return [
                'upcoming_events' => $eventsData,
                'total_events' => count($eventsData),
                'period' => '7 prochains jours',
                'regles_generales' => [
                    'equipe_min' => 4,
                    'equipe_max' => 6,
                    'une_seule_equipe_par_evenement' => true
                ]
            ];
        } catch (\Exception $e) {
            return [
                'upcoming_events' => [],
                'total_events' => 0,
                'period' => '7 prochains jours',
                'error' => 'Erreur lors de la récupération des événements: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Contexte des statistiques utilisateur
     */
    private function getUserStatsContext($user): array
    {
        if (!$user) {
            return ['error' => 'Utilisateur non connecté'];
        }

        try {
            // Utiliser getId() au lieu de getUserId()
            $userId = method_exists($user, 'getId') ? $user->getId() : 
                     (method_exists($user, 'getUserId') ? $user->getUserId() : null);
            
            if (!$userId) {
                return ['error' => 'ID utilisateur introuvable'];
            }

            $stats = [
                'user_id' => $userId,
                'name' => $user->getPrenom() . ' ' . $user->getNom(),
                'email' => $user->getEmail(),
                'role' => $user->getRole(),
                'created_at' => $user->getCreatedAt()->format('d/m/Y'),
            ];

            // Ajouter les activités si disponibles
            if ($this->activityRepository) {
                $activities = $this->activityRepository->findBy(
                    ['user' => $user],
                    ['createdAt' => 'DESC'],
                    10
                );

                $stats['recent_activities'] = array_map(function($activity) {
                    return [
                        'action' => $activity->getAction(),
                        'date' => $activity->getCreatedAt()->format('d/m/Y H:i'),
                        'success' => $activity->isSuccess()
                    ];
                }, $activities);

                $stats['total_activities'] = count($activities);
            }

            // Statistiques de niveau si étudiant
            if (method_exists($user, 'getNiveau')) {
                $stats['level'] = $user->getNiveau();
            }

            return $stats;
        } catch (\Exception $e) {
            return [
                'error' => 'Erreur lors de la récupération des statistiques: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Contexte de gestion des utilisateurs (admin)
     */
    private function getUserManagementContext(): array
    {
        try {
            $totalUsers = (int) $this->userRepository->count([]);
            $totalStudents = (int) $this->userRepository->count(['role' => 'ETUDIANT']);
            $totalAdmins = (int) $this->userRepository->count(['role' => 'ADMIN']);
            
            // Utilisateurs suspendus
            $suspendedUsers = (int) $this->userRepository->count(['isSuspended' => true]);
            
            // Utilisateurs inactifs (plus de 7 jours)
            $sevenDaysAgo = new \DateTime('-7 days');
            $qb = $this->em->createQueryBuilder();
            $inactiveCount = (int) $qb->select('COUNT(u.userId)')
                ->from('App\Entity\User', 'u')
                ->where('u.lastLoginAt < :date OR u.lastLoginAt IS NULL')
                ->andWhere('u.isSuspended = false')
                ->setParameter('date', $sevenDaysAgo)
                ->getQuery()
                ->getSingleScalarResult();

            // Cours populaires (top 5)
            $popularCourses = $this->coursRepository->createQueryBuilder('c')
                ->orderBy('c.id', 'DESC')
                ->setMaxResults(5)
                ->getQuery()
                ->getResult();

            $coursesData = array_map(function($cours) {
                return [
                    'id' => $cours->getId(),
                    'titre' => $cours->getTitre(),
                    'niveau' => $cours->getNiveau(),
                    'chapitres' => $cours->getChapitres()->count()
                ];
            }, $popularCourses);

            // Actions disponibles
            $user = $this->security->getUser();
            $availableActions = [];
            if ($this->actionExecutor && $user) {
                $availableActions = $this->actionExecutor->getAvailableActions($user);
            }

            return [
                'total_users' => $totalUsers,
                'total_students' => $totalStudents,
                'total_admins' => $totalAdmins,
                'suspended_users' => $suspendedUsers,
                'inactive_users_7days' => $inactiveCount,
                'active_users' => ($totalUsers - $suspendedUsers),
                'popular_courses' => $coursesData,
                'available_actions' => $availableActions
            ];
        } catch (\Exception $e) {
            return [
                'total_users' => 0,
                'total_students' => 0,
                'total_admins' => 0,
                'suspended_users' => 0,
                'inactive_users_7days' => 0,
                'active_users' => 0,
                'popular_courses' => [],
                'available_actions' => [],
                'error' => 'Erreur lors de la récupération des statistiques'
            ];
        }
    }

    /**
     * Contexte général de la plateforme
     */
    private function getGeneralContext($user): array
    {
        return [
            'platform' => 'AutoLearn',
            'features' => [
                'Cours de programmation (Python, Java, Web Development)',
                'Événements et workshops',
                'Challenges et quiz',
                'Communauté d\'apprentissage',
                'Suivi des progrès'
            ],
            'user_connected' => $user !== null,
            'total_courses' => $this->coursRepository->count([]),
            'upcoming_events' => $this->evenementRepository->count([])
        ];
    }
}
