<?php

namespace App\Service;

use App\Repository\Cours\CoursRepository;
use App\Repository\EvenementRepository;
use App\Repository\UserRepository;
use App\Repository\CommunauteRepository;
use App\Bundle\UserActivityBundle\Repository\UserActivityRepository;
use App\Service\CourseProgressService;
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
    private CommunauteRepository $communauteRepository;
    private ?UserActivityRepository $activityRepository;
    private CourseProgressService $progressService;

    public function __construct(
        EntityManagerInterface $em,
        Security $security,
        CoursRepository $coursRepository,
        EvenementRepository $evenementRepository,
        UserRepository $userRepository,
        CommunauteRepository $communauteRepository,
        CourseProgressService $progressService,
        ?UserActivityRepository $activityRepository = null
    ) {
        $this->em = $em;
        $this->security = $security;
        $this->coursRepository = $coursRepository;
        $this->evenementRepository = $evenementRepository;
        $this->userRepository = $userRepository;
        $this->communauteRepository = $communauteRepository;
        $this->activityRepository = $activityRepository;
        $this->progressService = $progressService;
    }

    /**
     * Collecte le contexte selon l'intention de l'utilisateur
     */
    public function retrieveContext(string $query, ?string $intent = null): array
    {
        $user = $this->security->getUser();
        $context = [
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
            case 'course_progress':
                // Retourner uniquement les cours avec progression
                $coursesData = $this->getCoursesContext($user);
                
                // Format ultra-strict pour l'IA
                $strictProgress = [];
                if (!empty($coursesData['user_progress'])) {
                    foreach ($coursesData['user_progress'] as $prog) {
                        $strictProgress[] = [
                            'nom_cours' => $prog['cours'],
                            'chapitres_completes' => (int)$prog['chapitres_completes'],
                            'chapitres_total' => (int)$prog['chapitres_total'],
                            'pourcentage' => $prog['progression']
                        ];
                    }
                }
                
                $context['data'] = [
                    'INSTRUCTION_IMPORTANTE' => 'Affiche UNIQUEMENT les cours listés ci-dessous avec leurs VRAIES données. N\'invente AUCUNE donnée.',
                    'nombre_cours_commences' => count($strictProgress),
                    'cours_avec_progression' => $strictProgress,
                    'format_reponse_attendu' => 'Pour chaque cours: [Nom du cours]: [X]/[Y] chapitres complétés ([Z]%)'
                ];
                break;
            
            case 'recommend_course':
                $context['data'] = $this->getCoursesContext($user);
                break;
            
            case 'list_events':
                $context['data'] = $this->getEventsContext();
                break;
            
            case 'list_communities':
                $context['data'] = $this->getCommunitiesContext();
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

        // Progression dans les cours (priorité haute)
        if (preg_match('/(ma progression|mon progrès|mes progrès|progression dans|progrès dans|avancement|où j\'en suis|combien.*complété)/i', $query)) {
            return 'course_progress';
        }

        // Recommandation de cours
        if (preg_match('/(cours|apprendre|étudier|recommand|python|java|web|programming)/i', $query)) {
            return 'recommend_course';
        }

        // Événements
        if (preg_match('/(événement|event|semaine|mois|particip)/i', $query)) {
            return 'list_events';
        }

        // Communautés
        if (preg_match('/(communauté|communaute|community|groupe|équipe|team|rejoindre)/i', $query)) {
            return 'list_communities';
        }

        // Statistiques utilisateur
        if (preg_match('/(statistique|activité|historique)/i', $query)) {
            return 'user_stats';
        }

        // Gestion utilisateurs (admin)
        if (preg_match('/(utilisateur|étudiant|inactif|suspendu|admin)/i', $query)) {
            return 'user_management';
        }

        return 'general';
    }

    /**
     * Contexte des cours disponibles
     */
    private function getCoursesContext($user): array
    {
        $niveau = $user && method_exists($user, 'getNiveau') ? $user->getNiveau() : null;
        
        try {
            $cours = $this->coursRepository->findAll();
            
            // Vérification de sécurité
            if (!is_array($cours) && !($cours instanceof \Traversable)) {
                $cours = [];
            }
            
            $coursData = [];
            $userProgress = [];
            
            foreach ($cours as $c) {
                $coursInfo = [
                    'id' => $c->getId(),
                    'titre' => $c->getTitre(),
                    'matiere' => $c->getMatiere(),
                    'niveau' => $c->getNiveau(),
                    'duree' => $c->getDuree(),
                    'chapitres_count' => $c->getChapitres()->count(),
                    'description' => substr($c->getDescription(), 0, 150) . '...'
                ];
                
                // Récupérer la progression DIRECTEMENT depuis la BD avec SQL
                if ($user) {
                    try {
                        $userId = method_exists($user, 'getUserId') ? $user->getUserId() : $user->getId();
                        $coursId = $c->getId();
                        
                        // Requête SQL directe pour les vraies données
                        $conn = $this->em->getConnection();
                        $sql = "
                            SELECT 
                                COUNT(DISTINCT cp.id) as chapitres_completes,
                                (SELECT COUNT(*) FROM chapitre WHERE cours_id = :cours_id) as total_chapitres
                            FROM chapter_progress cp
                            JOIN chapitre ch ON cp.chapitre_id = ch.id
                            WHERE cp.user_id = :user_id 
                            AND ch.cours_id = :cours_id
                            AND cp.completed_at IS NOT NULL
                        ";
                        
                        $stmt = $conn->prepare($sql);
                        $result = $stmt->executeQuery([
                            'user_id' => $userId,
                            'cours_id' => $coursId
                        ]);
                        $data = $result->fetchAssociative();
                        
                        $completedChapters = (int)($data['chapitres_completes'] ?? 0);
                        $totalChapters = (int)($data['total_chapitres'] ?? $c->getChapitres()->count());
                        $percentage = $totalChapters > 0 ? round(($completedChapters / $totalChapters) * 100, 1) : 0;
                        
                        $coursInfo['progression'] = [
                            'pourcentage' => $percentage,
                            'chapitres_completes' => $completedChapters,
                            'chapitres_total' => $totalChapters,
                            'chapitres_restants' => $totalChapters - $completedChapters,
                            'cours_termine' => $percentage >= 100
                        ];
                        
                        // Ajouter aux cours en progression SEULEMENT si > 0
                        if ($completedChapters > 0) {
                            $userProgress[] = [
                                'cours' => $c->getTitre(),
                                'progression' => $percentage . '%',
                                'chapitres_completes' => $completedChapters,
                                'chapitres_total' => $totalChapters,
                                'details' => $completedChapters . '/' . $totalChapters . ' chapitres'
                            ];
                        }
                    } catch (\Exception $e) {
                        // En cas d'erreur, progression = 0
                        $coursInfo['progression'] = [
                            'pourcentage' => 0,
                            'chapitres_completes' => 0,
                            'chapitres_total' => $c->getChapitres()->count(),
                            'error' => $e->getMessage()
                        ];
                    }
                }
                
                $coursData[] = $coursInfo;
            }

            return [
                'user_level' => $niveau,
                'available_courses' => $coursData,
                'total_courses' => count($coursData),
                'user_progress' => $userProgress,
                'courses_in_progress' => count($userProgress)
            ];
        } catch (\Exception $e) {
            return [
                'user_level' => $niveau,
                'available_courses' => [],
                'total_courses' => 0,
                'user_progress' => [],
                'error' => 'Erreur lors de la récupération des cours: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Contexte des événements
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
                $eventsData[] = [
                    'id' => $event->getId(),
                    'titre' => $event->getTitre(),
                    'date' => $event->getDateDebut()->format('d/m/Y H:i'),
                    'lieu' => $event->getLieu(),
                    'places_disponibles' => $event->getNbMax() - $event->getParticipations()->count(),
                    'description' => substr($event->getDescription(), 0, 100) . '...'
                ];
            }

            return [
                'upcoming_events' => $eventsData,
                'total_events' => count($eventsData),
                'period' => '7 prochains jours'
            ];
        } catch (\Exception $e) {
            return [
                'upcoming_events' => [],
                'total_events' => 0,
                'period' => '7 prochains jours',
                'error' => 'Erreur lors de la récupération des événements'
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

        $stats = [
            'user_id' => $user->getId(),
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
    }

    /**
     * Contexte de gestion des utilisateurs (admin)
     */
    private function getUserManagementContext(): array
    {
        $totalUsers = $this->userRepository->count([]);
        $totalStudents = $this->userRepository->count(['role' => 'ETUDIANT']);
        $totalAdmins = $this->userRepository->count(['role' => 'ADMIN']);
        
        // Utilisateurs suspendus
        $suspendedUsers = $this->userRepository->count(['isSuspended' => true]);
        
        // Utilisateurs inactifs (plus de 7 jours)
        $sevenDaysAgo = new \DateTime('-7 days');
        $qb = $this->em->createQueryBuilder();
        $inactiveCount = (int) $qb->select('COUNT(u.userId)')
            ->from('App\Entity\User', 'u')
            ->where('u.lastLoginAt < :date OR u.lastLoginAt IS NULL')
            ->setParameter('date', $sevenDaysAgo)
            ->getQuery()
            ->getSingleScalarResult();

        return [
            'total_users' => (int) $totalUsers,
            'total_students' => (int) $totalStudents,
            'total_admins' => (int) $totalAdmins,
            'suspended_users' => (int) $suspendedUsers,
            'inactive_users_7days' => $inactiveCount,
            'active_users' => (int) ($totalUsers - $suspendedUsers)
        ];
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

    /**
     * Contexte des communautés disponibles
     */
    private function getCommunitiesContext(): array
    {
        try {
            $communautes = $this->communauteRepository->findAll();
            
            // Vérification de sécurité
            if (!is_array($communautes) && !($communautes instanceof \Traversable)) {
                $communautes = [];
            }
            
            $communautesData = [];
            foreach ($communautes as $c) {
                $communautesData[] = [
                    'id' => $c->getId(),
                    'nom' => $c->getNom(),
                    'description' => $c->getDescription(),
                    'membres_count' => $c->getMembers()->count(),
                    'posts_count' => $c->getPosts()->count(),
                    'owner' => $c->getOwner() ? $c->getOwner()->getPrenom() . ' ' . $c->getOwner()->getNom() : 'Aucun'
                ];
            }

            return [
                'available_communities' => $communautesData,
                'total_communities' => count($communautesData)
            ];
        } catch (\Exception $e) {
            return [
                'available_communities' => [],
                'total_communities' => 0,
                'error' => 'Erreur lors de la récupération des communautés'
            ];
        }
    }
}
