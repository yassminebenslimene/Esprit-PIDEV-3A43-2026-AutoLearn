<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;
use App\Repository\UserRepository;
use App\Repository\Cours\CoursRepository;
use App\Repository\EvenementRepository;
use App\Repository\CommunauteRepository;
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
    private EvenementRepository $evenementRepository;
    private CommunauteRepository $communauteRepository;
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
        ?UserActivityRepository $activityRepository = null
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
            $response = $this->groqService->chat($messages, $options);

            if (!$response) {
                return $this->getFallbackResponse($question, $language);
            }

            // 7. Détecter et exécuter les actions (Admin uniquement)
            $actionResult = null;
            if (in_array('ROLE_ADMIN', $user?->getRoles() ?? [])) {
                $actionResult = $this->actionExecutor->detectAndExecute($response, $user);
                if ($actionResult['action_executed']) {
                    $response .= "\n\n✅ " . $actionResult['message'];
                }
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
     * Collecte TOUTES les données de la base de données pour Groq
     * Groq comprend le langage naturel et peut chercher dans toutes les données
     */
    private function getAllDatabaseData(string $question, $user): array
    {
        $data = [];

        try {
            // 1. TOUS LES UTILISATEURS (pour les admins)
            if ($user && in_array('ROLE_ADMIN', $user->getRoles() ?? [])) {
                $allUsers = $this->userRepository->findAll();
                $usersData = [];
                foreach ($allUsers as $u) {
                    $usersData[] = [
                        'id' => $u->getId(),
                        'nom' => $u->getNom(),
                        'prenom' => $u->getPrenom(),
                        'email' => $u->getEmail(),
                        'role' => $u->getRole(),
                        'niveau' => method_exists($u, 'getNiveau') ? $u->getNiveau() : null,
                        'is_suspended' => $u->getIsSuspended(),
                        'created_at' => $u->getCreatedAt() ? $u->getCreatedAt()->format('Y-m-d H:i:s') : null,
                        'last_login' => $u->getLastLoginAt() ? $u->getLastLoginAt()->format('Y-m-d H:i:s') : null,
                    ];
                }
                $data['all_users'] = $usersData;
                $data['total_users'] = count($usersData);
                
                // Statistiques utilisateurs
                $data['stats'] = [
                    'total_students' => count(array_filter($usersData, fn($u) => $u['role'] === 'ETUDIANT')),
                    'total_admins' => count(array_filter($usersData, fn($u) => $u['role'] === 'ADMIN')),
                    'suspended_users' => count(array_filter($usersData, fn($u) => $u['is_suspended'])),
                ];
            }

            // 2. TOUS LES COURS
            $allCours = $this->coursRepository->findAll();
            $coursData = [];
            foreach ($allCours as $c) {
                $coursData[] = [
                    'id' => $c->getId(),
                    'titre' => $c->getTitre(),
                    'matiere' => $c->getMatiere(),
                    'niveau' => $c->getNiveau(),
                    'duree' => $c->getDuree(),
                    'description' => $c->getDescription(),
                    'chapitres_count' => $c->getChapitres()->count(),
                ];
            }
            $data['all_courses'] = $coursData;
            $data['total_courses'] = count($coursData);

            // 3. TOUS LES ÉVÉNEMENTS
            $allEvents = $this->evenementRepository->findAll();
            $eventsData = [];
            foreach ($allEvents as $e) {
                $eventsData[] = [
                    'id' => $e->getId(),
                    'titre' => $e->getTitre(),
                    'description' => $e->getDescription(),
                    'date_debut' => $e->getDateDebut() ? $e->getDateDebut()->format('Y-m-d H:i:s') : null,
                    'date_fin' => $e->getDateFin() ? $e->getDateFin()->format('Y-m-d H:i:s') : null,
                    'lieu' => $e->getLieu(),
                    'nb_max' => $e->getNbMax(),
                    'participations_count' => $e->getParticipations()->count(),
                    'places_disponibles' => $e->getNbMax() - $e->getParticipations()->count(),
                ];
            }
            $data['all_events'] = $eventsData;
            $data['total_events'] = count($eventsData);

            // 4. TOUTES LES COMMUNAUTÉS
            $allCommunautes = $this->communauteRepository->findAll();
            $communautesData = [];
            foreach ($allCommunautes as $c) {
                $communautesData[] = [
                    'id' => $c->getId(),
                    'nom' => $c->getNom(),
                    'description' => $c->getDescription(),
                    'membres_count' => $c->getMembers()->count(),
                    'posts_count' => $c->getPosts()->count(),
                    'owner' => $c->getOwner() ? [
                        'id' => $c->getOwner()->getId(),
                        'nom' => $c->getOwner()->getNom(),
                        'prenom' => $c->getOwner()->getPrenom(),
                    ] : null,
                ];
            }
            $data['all_communities'] = $communautesData;
            $data['total_communities'] = count($communautesData);

            // 5. INFORMATIONS UTILISATEUR CONNECTÉ
            if ($user) {
                $data['current_user'] = [
                    'id' => $user->getId(),
                    'nom' => $user->getNom(),
                    'prenom' => $user->getPrenom(),
                    'email' => $user->getEmail(),
                    'role' => $user->getRole(),
                    'niveau' => method_exists($user, 'getNiveau') ? $user->getNiveau() : null,
                ];
            }

        } catch (\Exception $e) {
            $this->logger->error('Error collecting database data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $data['error'] = 'Erreur lors de la collecte des données';
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

2. 💪 EXERCICES & CHALLENGES
   - Suggérer des exercices selon le niveau
   - Recommander des challenges pour améliorer les compétences
   - Expliquer les concepts des exercices
   - Fournir des conseils d'apprentissage

3. 📅 ÉVÉNEMENTS & WORKSHOPS
   - Lister les événements à venir
   - Afficher les détails des événements (date, lieu, capacité)
   - Aider les étudiants à s'inscrire aux événements
   - Suggérer des événements pertinents selon les intérêts

4. 👥 COMMUNAUTÉS & ÉQUIPES
   - Lister les communautés disponibles
   - Recommander des communautés selon les intérêts
   - Aider les étudiants à rejoindre des communautés
   - Montrer les membres et activités des équipes

5. 📊 SUIVI DES PROGRÈS
   - Afficher les progrès d'apprentissage de l'étudiant
   - Montrer les cours et exercices complétés
   - Fournir des feedbacks personnalisés
   - Suggérer les prochaines étapes dans le parcours d'apprentissage

6. 🔍 RECHERCHE & DÉCOUVERTE
   - Rechercher des cours, chapitres ou ressources spécifiques
   - Trouver des communautés par sujet
   - Découvrir de nouvelles opportunités d'apprentissage

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
   - Add chapters to courses
   - Create resources and exercises
   - Manage challenges and quizzes
   - Organize course content

4. 📅 EVENT MANAGEMENT
   - Create new events
   - Manage event registrations
   - View event participation statistics
   - Update event details

5. 🔍 ADVANCED SEARCH & FILTERING
   - Filter students by multiple criteria
   - Search across all platform data
   - Generate custom reports
   - Export data for analysis

ACTIONS YOU CAN EXECUTE:
To perform an action, respond with JSON format:
{"action": "action_name", "data": {"param1": "value1", "param2": "value2"}}

Available actions:
- create_student: Create a new student
- update_student: Update student information
- filter_students: Filter students by criteria
- suspend_user: Suspend a user account
- unsuspend_user: Reactivate a suspended user
- get_inactive_users: List inactive users
- get_popular_courses: Show most popular courses

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
Pour effectuer une action, réponds avec le format JSON:
{"action": "nom_action", "data": {"param1": "valeur1", "param2": "valeur2"}}

Actions disponibles:
- create_student: Créer un nouvel étudiant
- update_student: Modifier les informations d'un étudiant
- filter_students: Filtrer les étudiants par critères
- suspend_user: Suspendre un compte utilisateur
- unsuspend_user: Réactiver un utilisateur suspendu
- get_inactive_users: Lister les utilisateurs inactifs
- get_popular_courses: Afficher les cours les plus populaires

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
        // Note: La génération automatique de liens est désactivée car les routes
        // peuvent ne pas exister. L'IA doit mentionner comment accéder au contenu
        // dans sa réponse (ex: "Visitez la page Événements pour voir plus de détails")
        
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
