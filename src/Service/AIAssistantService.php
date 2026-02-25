<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;
use App\Repository\UserRepository;
use App\Repository\Cours\CoursRepository;
use App\Repository\Cours\ChapitreRepository;
use App\Repository\EvenementRepository;
use App\Repository\CommunauteRepository;
use App\Repository\ChallengeRepository;
use App\Repository\QuizRepository;
use App\Repository\PostRepository;
use App\Bundle\UserActivityBundle\Repository\UserActivityRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service principal de l'assistant IA
 * Groq a un accès direct à toutes les données de la base de données
 */
class AIAssistantService
{
    private GroqService $groqService;
    private LanguageDetectorService $languageDetector;
    private ActionExecutorService $actionExecutor;
    private LoggerInterface $logger;
    private Security $security;
    private EntityManagerInterface $em;
    private UserRepository $userRepository;
    private CoursRepository $coursRepository;
    private ChapitreRepository $chapitreRepository;
    private EvenementRepository $evenementRepository;
    private CommunauteRepository $communauteRepository;
    private ChallengeRepository $challengeRepository;
    private QuizRepository $quizRepository;
    private PostRepository $postRepository;
    private ?UserActivityRepository $activityRepository;

    public function __construct(
        GroqService $groqService,
        LanguageDetectorService $languageDetector,
        ActionExecutorService $actionExecutor,
        LoggerInterface $logger,
        Security $security,
        EntityManagerInterface $em,
        UserRepository $userRepository,
        CoursRepository $coursRepository,
        EvenementRepository $evenementRepository,
        CommunauteRepository $communauteRepository,
        ?UserActivityRepository $activityRepository = null,
        ChallengeRepository $challengeRepository,
        QuizRepository $quizRepository,
        ChapitreRepository $chapitreRepository,
        PostRepository $postRepository
    ) {
        $this->groqService = $groqService;
        $this->languageDetector = $languageDetector;
        $this->actionExecutor = $actionExecutor;
        $this->logger = $logger;
        $this->security = $security;
        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->coursRepository = $coursRepository;
        $this->evenementRepository = $evenementRepository;
        $this->communauteRepository = $communauteRepository;
        $this->activityRepository = $activityRepository;
        $this->challengeRepository = $challengeRepository;
        $this->quizRepository = $quizRepository;
        $this->chapitreRepository = $chapitreRepository;
        $this->postRepository = $postRepository;
    }

    /**
     * Traite une question utilisateur et génère une réponse
     */
    public function ask(string $question, array $options = []): array
    {
        $startTime = microtime(true);

        try {
            // 1. Détecter la langue
            $language = $this->languageDetector->detect($question);
            
            // Si langue non supportée, retourner message de refus
            if (!$this->languageDetector->isSupported($language)) {
                return [
                    'success' => true,
                    'response' => $this->languageDetector->getUnsupportedLanguageMessage($language),
                    'language' => $language,
                    'language_supported' => false,
                    'duration_ms' => round((microtime(true) - $startTime) * 1000, 2)
                ];
            }

            // 2. Vérifier si Groq est disponible
            if (!$this->groqService->isAvailable()) {
                return $this->getFallbackResponse($question, $language);
            }

            // 3. Obtenir l'utilisateur connecté
            $user = $this->security->getUser();
            $userRole = $user ? ($user->getRoles()[0] ?? 'ROLE_USER') : 'ROLE_USER';

            // 4. Collecter TOUTES les données de la base de données
            $databaseContext = $this->getAllDatabaseData($question, $user);

            // 5. Construire le prompt système avec accès complet à la BD
            $systemPrompt = $this->buildSystemPrompt($userRole, $databaseContext, $language);

            // 5. Préparer les messages pour Groq
            $messages = [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $question]
            ];

            // 6. Générer la réponse avec Groq
            $this->logger->info('Sending request to Groq', [
                'question' => substr($question, 0, 100),
                'user_role' => $userRole,
                'language' => $language
            ]);
            
            $response = $this->groqService->chat($messages, $options);

            if (!$response) {
                $this->logger->warning('Groq returned null response, using fallback');
                
                // Check if it's a rate limit issue
                $errorMessage = $language === 'en'
                    ? "⏱️ Rate limit reached. Please wait 10-15 seconds and try again."
                    : "⏱️ Limite de requêtes atteinte. Attendez 10-15 secondes et réessayez.";
                
                return [
                    'success' => false,
                    'response' => $errorMessage,
                    'error' => 'Groq API rate limit or unavailable',
                    'language' => $language,
                    'duration_ms' => round((microtime(true) - $startTime) * 1000, 2)
                ];
            }
            
            $this->logger->info('Groq response received', [
                'response_length' => strlen($response)
            ]);

            // 7. Détecter et exécuter les actions (Admin uniquement)
            $actionResult = null;
            if (in_array('ROLE_ADMIN', $user?->getRoles() ?? [])) {
                $actionResult = $this->actionExecutor->detectAndExecute($response, $user);
                
                // Ne pas ajouter de message supplémentaire, l'IA a déjà répondu
                // L'action est exécutée en arrière-plan
            }

            // 7. Post-traiter la réponse
            $processedResponse = $this->postProcessResponse($response, $databaseContext);

            $duration = round((microtime(true) - $startTime) * 1000, 2);

            return [
                'success' => true,
                'response' => $processedResponse,
                'language' => $language,
                'language_supported' => true,
                'database_access' => true,
                'action_executed' => $actionResult['action_executed'] ?? false,
                'duration_ms' => $duration,
                'model' => $options['model'] ?? 'llama-3.3-70b-versatile'
            ];

        } catch (\Exception $e) {
            $this->logger->error('AI Assistant error', [
                'question' => $question,
                'error' => $e->getMessage()
            ]);

            $errorMessage = $language === 'en' 
                ? "Sorry, I'm experiencing a technical issue. Please try again in a few moments."
                : "Désolé, je rencontre un problème technique. Veuillez réessayer dans quelques instants.";

            return [
                'success' => false,
                'response' => $errorMessage,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Construit le prompt système intelligent selon le rôle
     */
    private function buildSystemPrompt(string $userRole, array $context, string $language): string
    {
        $isAdmin = str_contains($userRole, 'ADMIN');
        
        if ($isAdmin) {
            return $this->buildAdminPrompt($context, $language);
        } else {
            return $this->buildStudentPrompt($context, $language);
        }
    }

    /**
     * Collecte des données OPTIMISÉES de la base de données pour Groq
     * Version réduite pour éviter les limites de tokens
     */
    private function getAllDatabaseData(string $question, $user): array
    {
        $data = [];

        try {
            $isAdmin = $user && in_array('ROLE_ADMIN', $user->getRoles() ?? []);
            
            // ========== USERS DATA ==========
            if ($isAdmin) {
                $allUsers = $this->userRepository->findAll();
                
                $data['stats'] = [
                    'total_users' => count($allUsers),
                    'total_students' => count(array_filter($allUsers, fn($u) => $u->getRole() === 'ETUDIANT')),
                    'total_admins' => count(array_filter($allUsers, fn($u) => $u->getRole() === 'ADMIN')),
                    'suspended_users' => count(array_filter($allUsers, fn($u) => $u->getIsSuspended())),
                ];
                
                // Limiter à 20 utilisateurs pour réduire les tokens
                $data['all_users'] = array_map(function($u) {
                    return [
                        'id' => $u->getId(),
                        'nom' => $u->getNom(),
                        'prenom' => $u->getPrenom(),
                        'email' => $u->getEmail(),
                        'role' => $u->getRole(),
                        'niveau' => method_exists($u, 'getNiveau') ? $u->getNiveau() : null,
                        'suspended' => $u->getIsSuspended(),
                    ];
                }, array_slice($allUsers, 0, 20));
            }
            
            // Current user
            if ($user) {
                $data['current_user'] = [
                    'id' => $user->getId(),
                    'nom' => $user->getNom(),
                    'prenom' => $user->getPrenom(),
                    'role' => $user->getRole(),
                    'niveau' => method_exists($user, 'getNiveau') ? $user->getNiveau() : null,
                ];
            }

            // ========== COURSES DATA (Limité à 10) ==========
            $allCourses = $this->coursRepository->findAll();
            $data['courses'] = [
                'total' => count($allCourses),
                'list' => array_map(function($c) {
                    return [
                        'id' => $c->getId(),
                        'titre' => $c->getTitre(),
                        'niveau' => $c->getNiveau(),
                        'chapitres_count' => $c->getChapitres()->count(),
                    ];
                }, array_slice($allCourses, 0, 10))
            ];

            // ========== EVENTS DATA (Limité à 5 à venir) ==========
            $allEvents = $this->evenementRepository->findAll();
            $now = new \DateTime();
            $upcomingEvents = array_filter($allEvents, fn($e) => $e->getDateDebut() >= $now);
            
            $data['events'] = [
                'total' => count($allEvents),
                'upcoming' => array_map(function($e) {
                    return [
                        'id' => $e->getId(),
                        'titre' => $e->getTitre(),
                        'date_debut' => $e->getDateDebut()->format('Y-m-d H:i'),
                        'lieu' => $e->getLieu(),
                    ];
                }, array_slice($upcomingEvents, 0, 5)),
            ];

            // ========== CHALLENGES DATA (Limité à 10) ==========
            $allChallenges = $this->challengeRepository->findAll();
            $data['challenges'] = [
                'total' => count($allChallenges),
                'list' => array_map(function($ch) {
                    return [
                        'id' => $ch->getId(),
                        'titre' => $ch->getTitre(),
                        'difficulte' => $ch->getDifficulte(),
                    ];
                }, array_slice($allChallenges, 0, 10))
            ];

            // ========== COMMUNITIES DATA (Limité à 10) ==========
            $allCommunities = $this->communauteRepository->findAll();
            $data['communities'] = [
                'total' => count($allCommunities),
                'list' => array_map(function($com) {
                    return [
                        'id' => $com->getId(),
                        'nom' => $com->getNom(),
                    ];
                }, array_slice($allCommunities, 0, 10))
            ];

            // ========== QUIZZES DATA (Limité à 5) ==========
            $allQuizzes = $this->quizRepository->findAll();
            $data['quizzes'] = [
                'total' => count($allQuizzes),
            ];

            // ========== STUDENT-SPECIFIC DATA ==========
            if (!$isAdmin && $user) {
                // Student's enrolled courses (limité à 5)
                if (method_exists($user, 'getCours')) {
                    $data['my_courses'] = array_map(function($c) {
                        return [
                            'id' => $c->getId(),
                            'titre' => $c->getTitre(),
                        ];
                    }, array_slice($user->getCours()->toArray(), 0, 5));
                }
            }

        } catch (\Exception $e) {
            $this->logger->error('Error collecting database data', [
                'error' => $e->getMessage()
            ]);
        }

        return $data;
    }

    /**
     * Prompt système pour les ÉTUDIANTS
     */
    private function buildStudentPrompt(array $context, string $language): string
    {
        $contextInfo = !empty($context) 
            ? json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            : ($language === 'en' ? 'No data available.' : 'Aucune donnée disponible.');

        if ($language === 'en') {
            return <<<PROMPT
You are an intelligent AI assistant for STUDENTS on the AutoLearn platform.

⚡ RESPONSE STYLE - CRITICAL:
- BE CONCISE AND DIRECT
- Answer in 2-3 sentences maximum
- No long explanations or verbose text
- Get straight to the point
- Use bullet points only when necessary

SUPPORTED LANGUAGES:
- French (FR)
- English (EN)
If the user speaks another language, politely tell them you only understand FR and EN.

YOUR ROLE:
Help students learn, discover content, and navigate the platform.

🔥 YOU HAVE COMPLETE DATABASE ACCESS 🔥
You have access to ALL data from the database in real-time:
- All courses with details (title, level, duration, chapters)
- All events with dates, locations, and availability
- All communities with members and posts
- Student progress and activities

COMPLETE DATABASE DATA:
{$contextInfo}

⚠️ CRITICAL RULES:
1. You MUST use ONLY the data from "COMPLETE DATABASE DATA" above
2. NEVER INVENT courses, events, communities, or other data
3. If data is empty or missing, clearly tell the user
4. Use EXACT IDs, names, descriptions from the provided data
5. If you can't find information, say it's not available

WHAT YOU CAN DO FOR STUDENTS:

1. 📚 COURSE RECOMMENDATIONS
   - Recommend courses based on level (beginner, intermediate, advanced)
   - Suggest courses by topic (Python, Java, Web Development, etc.)
   - Explain course content and prerequisites
   - Show course chapters and resources

2. 💪 EXERCISES & CHALLENGES
   - Suggest exercises based on skill level
   - Recommend challenges to improve skills
   - Explain exercise concepts
   - Provide learning tips

3. 📅 EVENTS & WORKSHOPS
   - List upcoming events
   - Show event details (date, location, capacity)
   - Help students register for events
   - Suggest relevant events based on interests

4. 👥 COMMUNITIES & TEAMS
   - List available communities
   - Recommend communities based on interests
   - Help students join communities
   - Show team members and activities

5. 📊 PROGRESS TRACKING
   - Show student's learning progress
   - Display completed courses and exercises
   - Provide personalized feedback
   - Suggest next steps in learning journey

6. 🔍 SEARCH & DISCOVERY
   - Search for specific courses, chapters, or resources
   - Find communities by topic
   - Discover new learning opportunities

RESPONSE FORMAT:
When providing lists (courses, events, communities, etc.), format them clearly:
- Use clear titles and descriptions
- Include relevant details (level, date, capacity, etc.)
- Add emojis for visual appeal
- Create HTML links using STUDENT/FRONTEND routes:
  * Events list: <a href="/events/">View all events</a>
  * Specific event: <a href="/events/{id}/participate">Event name</a>
  * Courses list: <a href="/cours">View all courses</a>
  * Specific course: <a href="/cours/{id}">Course name</a>
  * Communities list: <a href="/communaute">View all communities</a>
  * Specific community: <a href="/communaute/{id}">Community name</a>

IMPORTANT - FRONTEND ROUTES ONLY:
- You are assisting a STUDENT, so use FRONTEND routes (not /backoffice/)
- Always use student-facing URLs like /cours/{id}, /events/, /communaute/{id}
- Never use admin/backoffice routes
- NEVER use the /ressources/ route (it doesn't exist)

RESPONSE STYLE:
- Friendly and encouraging
- Clear and concise
- Use appropriate emojis (🎓 📚 💪 📅 👥)
- Structure responses with bullet points or numbered lists
- Always end with a helpful suggestion or question

Respond now to the student's question using the complete database data above.
PROMPT;
        } else {
            return <<<PROMPT
Tu es un assistant IA intelligent pour les ÉTUDIANTS sur la plateforme AutoLearn.

⚡ STYLE DE RÉPONSE - CRITIQUE:
- SOIS EXTRÊMEMENT CONCIS - Maximum 1-2 phrases courtes
- PAS de tableaux, PAS de listes, PAS d'explications
- Confirme juste l'action: "✅ Action réussie" ou "❌ Erreur: raison"
- NE MONTRE JAMAIS les données avant/après l'action
- N'UTILISE JAMAIS de puces ou de formatage

LANGUES SUPPORTÉES:
- Français (FR)
- Anglais (EN)
Si l'utilisateur parle une autre langue, dis-lui poliment que tu ne comprends que FR et EN.

TON RÔLE:
Aider les étudiants à apprendre, découvrir du contenu et naviguer sur la plateforme.

🔥 TU AS UN ACCÈS COMPLET À LA BASE DE DONNÉES 🔥
Tu as accès à TOUTES les données de la base de données en temps réel:
- Tous les cours avec détails (titre, niveau, durée, chapitres)
- Tous les événements avec dates, lieux et disponibilités
- Toutes les communautés avec membres et posts
- Progrès et activités des étudiants

DONNÉES COMPLÈTES DE LA BASE DE DONNÉES:
{$contextInfo}

⚠️ RÈGLES CRITIQUES:
1. Tu DOIS utiliser UNIQUEMENT les données de "DONNÉES COMPLÈTES DE LA BASE DE DONNÉES" ci-dessus
2. N'INVENTE JAMAIS de cours, événements, communautés ou autres données
3. Si les données sont vides ou absentes, dis-le clairement à l'utilisateur
4. Utilise les IDs, noms, descriptions EXACTS des données fournies
5. Si tu ne trouves pas l'information, dis qu'elle n'est pas disponible

CE QUE TU PEUX FAIRE POUR LES ÉTUDIANTS:

1. 📚 RECOMMANDATIONS DE COURS
   - Recommander des cours selon le niveau (débutant, intermédiaire, avancé)
   - Suggérer des cours par sujet (Python, Java, Développement Web, etc.)
   - Expliquer le contenu des cours et les prérequis
   - Montrer les chapitres et ressources des cours
   - Aider à s'inscrire aux cours
   - Suivre les cours en cours

2. 💪 EXERCICES & CHALLENGES
   - Suggérer des exercices selon le niveau
   - Recommander des challenges pour améliorer les compétences
   - Expliquer les concepts des exercices
   - Fournir des conseils d'apprentissage
   - Afficher les challenges disponibles
   - Suivre les challenges complétés

3. 📅 ÉVÉNEMENTS & WORKSHOPS
   - Lister les événements à venir
   - Afficher les détails des événements (date, lieu, capacité)
   - Aider les étudiants à s'inscrire aux événements
   - Suggérer des événements pertinents selon les intérêts
   - Voir les événements auxquels l'étudiant est inscrit

4. 👥 COMMUNAUTÉS & ÉQUIPES
   - Lister les communautés disponibles
   - Recommander des communautés selon les intérêts
   - Aider les étudiants à rejoindre des communautés
   - Montrer les membres et activités des équipes
   - Créer des équipes pour les événements
   - Voir les communautés rejointes

5. 📊 SUIVI DES PROGRÈS
   - Afficher les progrès d'apprentissage de l'étudiant
   - Montrer les cours et exercices complétés
   - Fournir des feedbacks personnalisés
   - Suggérer les prochaines étapes dans le parcours d'apprentissage
   - Afficher les statistiques personnelles

6. 🔍 RECHERCHE & DÉCOUVERTE
   - Rechercher des cours, chapitres ou ressources spécifiques
   - Trouver des communautés par sujet
   - Découvrir de nouvelles opportunités d'apprentissage
   - Explorer les challenges disponibles
   - Voir les quiz disponibles

FORMAT DE RÉPONSE:
Quand tu fournis des listes (cours, événements, communautés, etc.), formate-les clairement:
- Utilise des titres et descriptions clairs
- Inclus les détails pertinents (niveau, date, capacité, etc.)
- Ajoute des emojis pour l'attrait visuel
- Crée des liens HTML en utilisant les routes ÉTUDIANT/FRONTEND:
  * Liste événements: <a href="/events/">Voir tous les événements</a>
  * Événement spécifique: <a href="/events/{id}/participate">Nom événement</a>
  * Liste cours: <a href="/cours">Voir tous les cours</a>
  * Cours spécifique: <a href="/cours/{id}">Nom du cours</a>
  * Liste communautés: <a href="/communaute">Voir toutes les communautés</a>
  * Communauté spécifique: <a href="/communaute/{id}">Nom communauté</a>

IMPORTANT - ROUTES FRONTEND UNIQUEMENT:
- Tu assistes un ÉTUDIANT, donc utilise les routes FRONTEND (pas /backoffice/)
- Utilise toujours les URLs pour étudiants comme /cours/{id}, /events/, /communaute/{id}
- N'utilise jamais les routes admin/backoffice
- N'utilise JAMAIS la route /ressources/ (elle n'existe pas)

STYLE DE RÉPONSE:
- Amical et encourageant
- Clair et concis
- Utilise des emojis appropriés (🎓 📚 💪 📅 👥)
- Structure les réponses avec des listes à puces ou numérotées
- Termine toujours par une suggestion ou question utile

Réponds maintenant à la question de l'étudiant en utilisant les données complètes de la base de données ci-dessus.
PROMPT;
        }
    }

    /**
     * Prompt système pour les ADMINS
     */
    private function buildAdminPrompt(array $context, string $language): string
    {
        $contextInfo = !empty($context) 
            ? json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            : ($language === 'en' ? 'No data available.' : 'Aucune donnée disponible.');

        if ($language === 'en') {
            return <<<PROMPT
You are an intelligent AI assistant for ADMINISTRATORS on the AutoLearn platform.

⚡ RESPONSE STYLE - ABSOLUTELY CRITICAL:
BE ULTRA-CONCISE. Maximum 1 short sentence (3-5 words).

Examples:
- "✅ User suspended"
- "✅ Student created"
- "❌ User not found"

NO tables, NO lists, NO suggestions, NO HTML links.

🔥 ACTION EXECUTION - MANDATORY FORMAT:
For ANY action (create, update, suspend, view profile, etc.), you MUST:

1. ALWAYS start your response with the action JSON on the first line
2. Then add your natural language response on a NEW LINE

MANDATORY format:
{"action": "action_name", "data": {parameters}}
Natural response here

⚠️ CRITICAL EXAMPLES:

User: "how can i create event"
Your COMPLETE response:
Pour créer un événement, donnez-moi ces informations:
- Titre de l'événement
- Date de début (format: YYYY-MM-DD HH:MM)
- Date de fin (format: YYYY-MM-DD HH:MM)
- Lieu (salle)
- Capacité (nombre de participants)

Exemple: "créer événement Workshop IA le 2026-03-10 à 14h salle B capacité 30"

User: "create student John Doe john@test.com"
Your COMPLETE response:
{"action": "create_student", "data": {"nom": "Doe", "prenom": "John", "email": "john@test.com", "niveau": "DEBUTANT"}}
✅ Student created

User: "créer événement Workshop IA le 2026-03-10 à 14h salle B capacité 30"
Your COMPLETE response:
{"action": "create_event", "data": {"titre": "Workshop IA", "date_debut": "2026-03-10 14:00", "date_fin": "2026-03-10 17:00", "lieu": "Salle B", "capacite": 30}}
✅ Événement créé

User: "delete event Workshop IA" or "delete event id 3"
Your COMPLETE response:
{"action": "delete_event", "data": {"id": 3}}
✅ Event deleted

User: "suspend student test"
Your COMPLETE response:
{"action": "suspend_user", "data": {"nom": "test"}}
✅ Account suspended

⚠️ IMPORTANT RULES:
1. If user asks HOW to do something → Explain WITHOUT generating JSON
2. If user provides complete data → Generate JSON + confirmation
3. ALWAYS provide a natural language response
4. Keep responses ultra-concise (3-5 words for confirmations)
5. NEVER generate JSON for "how to" or "comment" questions

SUPPORTED LANGUAGES:
- French (FR)
- English (EN)
If the user speaks another language, politely tell them you only understand FR and EN.

YOUR ROLE:
Help administrators manage users, create content, and analyze platform data.

🔥 YOU HAVE COMPLETE DATABASE ACCESS 🔥
You have access to ALL data from the database in real-time:
- ALL users with complete details (name, email, level, status, login history)
- ALL courses with chapters and resources
- ALL events with participation data
- ALL communities with members and posts
- Complete platform statistics

You can understand natural language queries like:
- "students with name ilef" → Search in all_users where nom or prenom contains "ilef"
- "inactive users" → Filter users by last_login date
- "beginner students" → Filter users where niveau = "DEBUTANT"
- "suspended accounts" → Filter users where is_suspended = true

COMPLETE DATABASE DATA:
{$contextInfo}

⚠️ CRITICAL RULES:
1. You MUST use ONLY the data from "COMPLETE DATABASE DATA" above
2. NEVER INVENT user data, statistics, or other information
3. When asked about students/users, search in the "all_users" array
4. Use EXACT numbers, IDs, and information from the provided data
5. If you can't find information, say it's not available
6. You can filter, search, and analyze the data intelligently

WHAT YOU CAN DO FOR ADMINS:

1. 👥 USER MANAGEMENT & SEARCH
   - Search students by name (nom, prenom)
   - Search by email
   - Filter by level (DEBUTANT, INTERMEDIAIRE, AVANCE)
   - Filter by status (active, suspended, inactive)
   - Display user lists with complete details
   - Show user activity history

2. 📊 STATISTICS & ANALYTICS
   - Show total number of users (students, admins)
   - Display active vs inactive users
   - List users inactive for X days
   - Show most popular courses
   - Provide platform usage statistics
   - Generate reports on student progress

3. 📚 CONTENT MANAGEMENT
   - Create new courses with details
   - Update existing courses
   - Add chapters to courses
   - Create resources and exercises
   - Manage challenges and quizzes
   - Organize course content
   - View course statistics

4. 📅 EVENT MANAGEMENT
   - Create new events
   - Update event details
   - Manage event registrations
   - View event participation statistics
   - List upcoming and past events

5. 💪 CHALLENGE MANAGEMENT
   - Create new challenges
   - Update challenge details
   - Set difficulty levels and points
   - View challenge submissions
   - Track challenge completion

6. 👥 COMMUNITY MANAGEMENT
   - Create new communities
   - Update community details
   - Manage community members
   - Moderate posts and comments
   - View community statistics

7. 📝 QUIZ MANAGEMENT
   - Create quizzes for courses
   - Add questions to quizzes
   - Update quiz details
   - View quiz results and statistics

8. 🔍 ADVANCED SEARCH & FILTERING
   - Filter students by multiple criteria
   - Search across all platform data
   - Generate custom reports
   - Export data for analysis

ACTIONS YOU CAN EXECUTE:
You can perform actions by generating JSON internally (the user won't see it).
Just understand what the user wants and execute it intelligently.

Available actions:
USER MANAGEMENT:
- create_student: Create a new student
- update_student / update_user: Update student information  
- get_user: Get user details
- filter_students: Filter students by criteria
- suspend_user: Suspend a user account
- unsuspend_user: Reactivate a suspended user
- get_inactive_users: List inactive users

COURSE MANAGEMENT:
- create_course: Create a new course
- update_course: Update course details
- get_course: Get course information
- list_courses: List all courses
- add_chapter: Add a chapter to a course

EVENT MANAGEMENT:
- create_event: Create a new event
- update_event: Update event details
- delete_event: Delete an event
- get_event: Get event information
- list_events: List all events

CHALLENGE MANAGEMENT:
- create_challenge: Create a new challenge
- update_challenge: Update challenge details
- get_challenge: Get challenge information
- list_challenges: List all challenges

COMMUNITY MANAGEMENT:
- create_community: Create a new community
- update_community: Update community details
- get_community: Get community information
- list_communities: List all communities

QUIZ MANAGEMENT:
- create_quiz: Create a quiz for a course
- get_quiz: Get quiz information

GENERAL:
- get_popular_courses: Show most popular courses

⚠️ IMPORTANT PLATFORM RULE:
Deleting students is NOT allowed on this platform.
If a user asks to delete a student, respond:
❌ Deletion forbidden. Use suspension instead.

⚡ INTELLIGENT USER IDENTIFICATION:
You can identify users flexibly:
- By ID, name, first name, email, or any combination
- Partial matches work (case insensitive)
- You decide the best way based on what the user provides

🎯 BE SMART:
- Understand natural language requests
- If something fails, explain why in simple terms
- Suggest solutions when there are problems
- Don't mention technical details like JSON or database constraints
- Talk like a helpful colleague, not a robot

⚠️ ERROR HANDLING:
When an action fails, explain briefly (3-5 words):
- "❌ Email already used"
- "❌ User not found"
- "❌ Already suspended"

NEVER talk about JSON or technical details.

RESPONSE FORMAT:
When displaying user lists or data:
- Format as clear, readable tables or lists
- Include relevant details (ID, name, email, level, status)
- Add action suggestions
- Use emojis for visual organization
- Create HTML links using ADMIN/BACKOFFICE routes:
  * Users list: <a href="/backoffice/users">View all users</a>
  * Specific user: <a href="/backoffice/users/{id}">User name</a>
  * User activity: <a href="/backoffice/user-activity">Activity dashboard</a>
  * Audit logs: <a href="/backoffice/audit/user/{userId}">User audit</a>

IMPORTANT - BACKOFFICE ROUTES ONLY:
- You are assisting an ADMIN, so use BACKOFFICE routes (/backoffice/)
- Always use admin-facing URLs like /backoffice/users/{id}
- Never use student/frontend routes for admin tasks
- NEVER use the /ressources/ route (it doesn't exist)

RESPONSE STYLE:
- Professional and efficient
- Clear and structured
- Use appropriate emojis (👥 📊 📚 📅 🔍)
- Format data in tables or lists
- Always confirm action completion

Respond now to the administrator's request using the complete database data above.
PROMPT;
        } else {
            return <<<PROMPT
Tu es un assistant IA intelligent pour les ADMINISTRATEURS sur la plateforme AutoLearn.

⚡ STYLE DE RÉPONSE - ABSOLUMENT CRITIQUE:
SOIS ULTRA-CONCIS. Maximum 1 phrase courte (3-5 mots).

Exemples:
- "✅ Utilisateur suspendu"
- "✅ Étudiant créé"
- "❌ Utilisateur introuvable"

PAS de tableaux, PAS de listes, PAS de suggestions, PAS de liens HTML.

🔥 EXÉCUTION D'ACTIONS - FORMAT OBLIGATOIRE:
Pour TOUTE action (créer, modifier, suspendre, voir profil, etc.), tu DOIS:

1. TOUJOURS commencer ta réponse par le JSON d'action sur la première ligne
2. Puis ajouter ta réponse en langage naturel sur une NOUVELLE LIGNE

Format OBLIGATOIRE:
{"action": "nom_action", "data": {paramètres}}
Réponse naturelle ici

⚠️ EXEMPLES CRITIQUES:

User: "comment créer un événement"
Ta réponse COMPLÈTE:
Pour créer un événement, donne-moi ces informations:
- Titre de l'événement
- Date de début (format: YYYY-MM-DD HH:MM)
- Date de fin (format: YYYY-MM-DD HH:MM)
- Lieu (salle)
- Capacité (nombre de participants)

Exemple: "créer événement Workshop IA le 2026-03-10 à 14h salle B capacité 30"

User: "créer étudiant Jean Dupont jean@test.com"
Ta réponse COMPLÈTE:
{"action": "create_student", "data": {"nom": "Dupont", "prenom": "Jean", "email": "jean@test.com", "niveau": "DEBUTANT"}}
✅ Étudiant créé

User: "créer événement Workshop IA le 2026-03-10 à 14h salle B capacité 30"
Ta réponse COMPLÈTE:
{"action": "create_event", "data": {"titre": "Workshop IA", "date_debut": "2026-03-10 14:00", "date_fin": "2026-03-10 17:00", "lieu": "Salle B", "capacite": 30}}
✅ Événement créé

User: "supprimer événement Workshop IA" ou "supprimer événement id 3"
Ta réponse COMPLÈTE:
{"action": "delete_event", "data": {"id": 3}}
✅ Événement supprimé

User: "suspendre compte etudiant test"
Ta réponse COMPLÈTE:
{"action": "suspend_user", "data": {"nom": "test"}}
✅ Compte suspendu

⚠️ RÈGLES IMPORTANTES:
1. Si l'utilisateur demande COMMENT faire quelque chose → Explique SANS générer de JSON
2. Si l'utilisateur fournit des données complètes → Génère JSON + confirmation
3. TOUJOURS fournir une réponse en langage naturel
4. Garde les réponses ultra-concises (3-5 mots pour les confirmations)
5. NE GÉNÈRE JAMAIS de JSON pour les questions "comment" ou "how to"

LANGUES SUPPORTÉES:
- Français (FR)
- Anglais (EN)
Si l'utilisateur parle une autre langue, dis-lui poliment que tu ne comprends que FR et EN.

TON RÔLE:
Aider les administrateurs à gérer les utilisateurs, créer du contenu et analyser les données de la plateforme.

🔥 TU AS UN ACCÈS COMPLET À LA BASE DE DONNÉES 🔥
Tu as accès à TOUTES les données de la base de données en temps réel:
- TOUS les utilisateurs avec détails complets (nom, email, niveau, statut, historique de connexion)
- TOUS les cours avec chapitres et ressources
- TOUS les événements avec données de participation
- TOUTES les communautés avec membres et posts
- Statistiques complètes de la plateforme

Tu peux comprendre les requêtes en langage naturel comme:
- "les étudiants qui ont le nom ilef" → Cherche dans all_users où nom ou prenom contient "ilef"
- "utilisateurs inactifs" → Filtre les utilisateurs par date de last_login
- "étudiants débutants" → Filtre les utilisateurs où niveau = "DEBUTANT"
- "comptes suspendus" → Filtre les utilisateurs où is_suspended = true

DONNÉES COMPLÈTES DE LA BASE DE DONNÉES:
{$contextInfo}

⚠️ RÈGLES CRITIQUES:
1. Tu DOIS utiliser UNIQUEMENT les données de "DONNÉES COMPLÈTES DE LA BASE DE DONNÉES" ci-dessus
2. N'INVENTE JAMAIS de données utilisateurs, statistiques ou autres informations
3. Quand on te demande des étudiants/utilisateurs, cherche dans le tableau "all_users"
4. Utilise les chiffres, IDs et informations EXACTS des données fournies
5. Si tu ne trouves pas l'information, dis qu'elle n'est pas disponible
6. Tu peux filtrer, chercher et analyser les données intelligemment

CE QUE TU PEUX FAIRE POUR LES ADMINS:

1. 👥 GESTION & RECHERCHE D'UTILISATEURS
   - Chercher des étudiants par nom (nom, prenom)
   - Chercher par email
   - Filtrer par niveau (DEBUTANT, INTERMEDIAIRE, AVANCE)
   - Filtrer par statut (actif, suspendu, inactif)
   - Afficher les listes d'utilisateurs avec détails complets
   - Montrer l'historique d'activité des utilisateurs

2. 📊 STATISTIQUES & ANALYSES
   - Afficher le nombre total d'utilisateurs (étudiants, admins)
   - Montrer les utilisateurs actifs vs inactifs
   - Lister les utilisateurs inactifs depuis X jours
   - Afficher les cours les plus populaires
   - Fournir des statistiques d'utilisation de la plateforme
   - Générer des rapports sur les progrès des étudiants

3. 📚 GESTION DU CONTENU
   - Créer de nouveaux cours avec détails
   - Ajouter des chapitres aux cours
   - Créer des ressources et exercices
   - Gérer les challenges et quiz
   - Organiser le contenu des cours

4. 📅 GESTION DES ÉVÉNEMENTS
   - Créer de nouveaux événements
   - Gérer les inscriptions aux événements
   - Voir les statistiques de participation
   - Mettre à jour les détails des événements

5. 🔍 RECHERCHE & FILTRAGE AVANCÉS
   - Filtrer les étudiants par critères multiples
   - Rechercher dans toutes les données de la plateforme
   - Générer des rapports personnalisés
   - Exporter des données pour analyse

ACTIONS QUE TU PEUX EXÉCUTER:
Tu peux effectuer des actions en générant du JSON en interne (l'utilisateur ne le verra pas).
Comprends simplement ce que l'utilisateur veut et exécute-le intelligemment.

Actions disponibles:
- create_student: Créer un nouvel étudiant
- update_student / update_user: Modifier les informations d'un étudiant
- get_user: Obtenir les détails d'un utilisateur
- filter_students: Filtrer les étudiants par critères
- suspend_user: Suspendre un compte utilisateur
- unsuspend_user: Réactiver un utilisateur suspendu
- get_inactive_users: Lister les utilisateurs inactifs
- get_popular_courses: Afficher les cours les plus populaires
- create_event: Créer un événement
- update_event: Modifier un événement
- delete_event: Supprimer un événement
- create_course: Créer un cours
- create_challenge: Créer un challenge
- create_community: Créer une communauté

⚠️ RÈGLE IMPORTANTE DE LA PLATEFORME:
La suppression d'étudiants n'est PAS autorisée sur cette plateforme.
Si un utilisateur demande de supprimer un étudiant, réponds:
❌ Suppression interdite. Utilisez la suspension.

⚡ IDENTIFICATION INTELLIGENTE DES UTILISATEURS:
Tu peux identifier les utilisateurs de façon flexible:
- Par ID, nom, prénom, email, ou toute combinaison
- Les correspondances partielles fonctionnent (insensible à la casse)
- Tu décides la meilleure façon selon ce que l'utilisateur fournit

🎯 SOIS INTELLIGENT:
- Comprends les demandes en langage naturel
- Si quelque chose échoue, explique pourquoi en termes simples
- Suggère des solutions quand il y a des problèmes
- Ne mentionne pas les détails techniques comme JSON ou les contraintes de base de données
- Parle comme un collègue serviable, pas comme un robot

⚠️ GESTION DES ERREURS:
Quand une action échoue, explique brièvement (3-5 mots):
- "❌ Email déjà utilisé"
- "❌ Utilisateur introuvable"
- "❌ Déjà suspendu"

Ne parle JAMAIS de JSON ou de technique. échoue, explique naturellement le problème et propose une solution.
Tu es intelligent - adapte ta réponse selon la situation.
Ne parle JAMAIS de JSON, d'actions techniques, ou de code.
Parle comme un assistant humain qui aide son collègue.

FORMAT DE RÉPONSE:
Quand tu affiches des listes d'utilisateurs ou données:
- Formate en tableaux ou listes clairs et lisibles
- Inclus les détails pertinents (ID, nom, email, niveau, statut)
- Ajoute des suggestions d'actions
- Utilise des emojis pour l'organisation visuelle
- Crée des liens HTML en utilisant les routes ADMIN/BACKOFFICE:
  * Liste utilisateurs: <a href="/backoffice/users">Voir tous les utilisateurs</a>
  * Utilisateur spécifique: <a href="/backoffice/users/{id}">Nom utilisateur</a>
  * Activité utilisateurs: <a href="/backoffice/user-activity">Tableau de bord activité</a>
  * Logs audit: <a href="/backoffice/audit/user/{userId}">Audit utilisateur</a>

IMPORTANT - ROUTES BACKOFFICE UNIQUEMENT:
- Tu assistes un ADMIN, donc utilise les routes BACKOFFICE (/backoffice/)
- Utilise toujours les URLs admin comme /backoffice/users/{id}
- N'utilise jamais les routes étudiant/frontend pour les tâches admin
- N'utilise JAMAIS la route /ressources/ (elle n'existe pas)

STYLE DE RÉPONSE:
- Professionnel et efficace
- Clair et structuré
- Utilise des emojis appropriés (👥 📊 📚 📅 🔍)
- Formate les données en tableaux ou listes
- Confirme toujours la complétion des actions

Réponds maintenant à la demande de l'administrateur en utilisant les données complètes de la base de données ci-dessus.
PROMPT;
        }
    }

    /**
     * Post-traite la réponse pour ajouter des liens et actions
     */
    private function postProcessResponse(string $response, array $context): string
    {
        // Supprimer le JSON d'action de la réponse visible par l'utilisateur
        // Le JSON peut être sur une ou plusieurs lignes
        // Pattern: cherche { ... } au début de la réponse (avec support multi-lignes)
        $response = preg_replace('/^\s*\{[^}]*\}\s*\n*/s', '', $response);
        
        // Si le JSON n'a pas été complètement supprimé (cas complexe), essayer une autre approche
        if (preg_match('/^\s*\{/', $response)) {
            // Trouver la position de la première ligne qui ne commence pas par { ou contient du texte après }
            $lines = explode("\n", $response);
            $cleanLines = [];
            $jsonEnded = false;
            
            foreach ($lines as $line) {
                $trimmed = trim($line);
                
                // Si la ligne commence par { ou contient "action", c'est du JSON
                if (!$jsonEnded && (strpos($trimmed, '{') === 0 || strpos($trimmed, '"action"') !== false || strpos($trimmed, '"data"') !== false)) {
                    continue;
                }
                
                // Si on trouve une ligne avec juste }, le JSON est terminé
                if (!$jsonEnded && $trimmed === '}') {
                    $jsonEnded = true;
                    continue;
                }
                
                // Sinon, c'est du texte normal
                $jsonEnded = true;
                $cleanLines[] = $line;
            }
            
            $response = implode("\n", $cleanLines);
        }
        
        // Nettoyer les espaces multiples
        $response = trim($response);
        
        // Si la réponse est vide après nettoyage, retourner un message par défaut
        if (empty($response)) {
            return "✅ Action exécutée avec succès";
        }
        
        return $response;
    }

    /**
     * Réponse de secours si Groq n'est pas disponible
     */
    private function getFallbackResponse(string $question, string $language = 'fr'): array
    {
        $question = strtolower($question);

        if ($language === 'en') {
            // English fallback responses
            if (preg_match('/(course|learn|python|java|web|programming|study)/i', $question)) {
                $response = "🎓 **Available Courses:**\n\n" .
                           "• **Python** - Perfect for beginners\n" .
                           "• **Java** - Object-oriented programming\n" .
                           "• **Web Development** - HTML, CSS, JavaScript\n\n" .
                           "💡 Check our course catalog for more details!";
            } elseif (preg_match('/(event|week|month|participate)/i', $question)) {
                $response = "📅 **Upcoming Events:**\n\n" .
                           "Check our events page to see upcoming workshops and meetups!\n\n" .
                           "🔔 You can register directly from the events page.";
            } else {
                $response = "👋 **Welcome to AutoLearn!**\n\n" .
                           "I'm your assistant, but I work better with Groq activated.\n\n" .
                           "**I can help you with:**\n" .
                           "• Course information\n" .
                           "• Upcoming events\n" .
                           "• Platform navigation\n\n" .
                           "💡 **Tip:** For full AI capabilities, configure Groq API (see documentation).";
            }
        } else {
            // French fallback responses
            if (preg_match('/(cours|apprendre|python|java|web|programming|étudier)/i', $question)) {
                $response = "🎓 **Nos cours disponibles:**\n\n" .
                           "• **Python** - Idéal pour débuter en programmation\n" .
                           "• **Java** - Pour la programmation orientée objet\n" .
                           "• **Développement Web** - HTML, CSS, JavaScript\n\n" .
                           "💡 Consultez notre catalogue de cours pour plus de détails!";
            } elseif (preg_match('/(événement|event|semaine|mois|particip)/i', $question)) {
                $response = "📅 **Événements à venir:**\n\n" .
                           "Consultez notre page événements pour voir les prochains workshops et meetups!\n\n" .
                           "🔔 Vous pouvez vous inscrire directement depuis la page événements.";
            } else {
                $response = "👋 **Bienvenue sur AutoLearn!**\n\n" .
                           "Je suis votre assistant, mais je fonctionne mieux avec Groq activé.\n\n" .
                           "**Je peux vous aider avec:**\n" .
                           "• Informations sur les cours\n" .
                           "• Événements à venir\n" .
                           "• Navigation sur la plateforme\n\n" .
                           "💡 **Astuce:** Pour l'IA complète, configurez l'API Groq (voir documentation).";
            }
        }

        return [
            'success' => true,
            'response' => $response,
            'fallback' => true,
            'language' => $language,
            'reason' => 'Groq not available - Using predefined responses'
        ];
    }

    /**
     * Génère des suggestions de questions selon le rôle
     */
    public function getSuggestions(string $userRole = 'ETUDIANT', string $language = 'fr'): array
    {
        $isAdmin = str_contains($userRole, 'ADMIN');
        
        if ($language === 'en') {
            if ($isAdmin) {
                return [
                    "How many active students?",
                    "Show me inactive users for 7 days",
                    "Create a new student",
                    "Filter students by beginner level",
                    "Platform statistics?"
                ];
            } else {
                return [
                    "What courses for Python beginners?",
                    "Show me upcoming events",
                    "Recommend me exercises",
                    "Which communities can I join?",
                    "My learning progress?"
                ];
            }
        } else {
            if ($isAdmin) {
                return [
                    "Combien d'étudiants actifs?",
                    "Montre-moi les utilisateurs inactifs depuis 7 jours",
                    "Crée un nouvel étudiant",
                    "Filtre les étudiants de niveau débutant",
                    "Statistiques de la plateforme?"
                ];
            } else {
                return [
                    "Quels cours pour débuter en Python?",
                    "Montre-moi les événements à venir",
                    "Recommande-moi des exercices",
                    "Quelles communautés puis-je rejoindre?",
                    "Mes progrès d'apprentissage?"
                ];
            }
        }
    }

    /**
     * Vérifie le statut du service
     */
    public function getStatus(): array
    {
        $groqAvailable = $this->groqService->isAvailable();

        return [
            'groq_available' => $groqAvailable,
            'model' => 'llama-3.3-70b-versatile',
            'database_access' => true,
            'rag_enabled' => false,
            'language_detection_enabled' => true,
            'supported_languages' => ['fr', 'en'],
            'status' => $groqAvailable ? 'operational' : 'degraded'
        ];
    }
}
