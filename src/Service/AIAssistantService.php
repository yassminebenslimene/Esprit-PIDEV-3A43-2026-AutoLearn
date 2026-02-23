<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Service principal de l'assistant IA
 * Orchestre RAG, Groq et détection de langue pour générer des réponses intelligentes
 */
class AIAssistantService
{
    private GroqService $groqService;
    private RAGService $ragService;
    private LanguageDetectorService $languageDetector;
    private ActionExecutorService $actionExecutor;
    private LoggerInterface $logger;
    private Security $security;

    public function __construct(
        GroqService $groqService,
        RAGService $ragService,
        LanguageDetectorService $languageDetector,
        ActionExecutorService $actionExecutor,
        LoggerInterface $logger,
        Security $security
    ) {
        $this->groqService = $groqService;
        $this->ragService = $ragService;
        $this->languageDetector = $languageDetector;
        $this->actionExecutor = $actionExecutor;
        $this->logger = $logger;
        $this->security = $security;
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

            // 3. Collecter le contexte via RAG
            $context = $this->ragService->retrieveContext($question);

            // 4. Construire le prompt système
            $user = $this->security->getUser();
            $userRole = $user ? ($user->getRoles()[0] ?? 'ROLE_USER') : 'ROLE_USER';
            $systemPrompt = $this->buildSystemPrompt($userRole, $context, $language);

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

            // 8. Post-traiter la réponse
            $processedResponse = $this->postProcessResponse($response, $context);

            $duration = round((microtime(true) - $startTime) * 1000, 2);

            return [
                'success' => true,
                'response' => $processedResponse,
                'language' => $language,
                'language_supported' => true,
                'context_used' => !empty($context['data']),
                'action_executed' => $actionResult['action_executed'] ?? false,
                'duration_ms' => $duration,
                'model' => $options['model'] ?? 'llama3-70b-8192'
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
     * Prompt système pour les ÉTUDIANTS
     */
    private function buildStudentPrompt(array $context, string $language): string
    {
        $contextInfo = !empty($context['data']) 
            ? json_encode($context['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            : ($language === 'en' ? 'No specific context available.' : 'Aucun contexte spécifique disponible.');

        if ($language === 'en') {
            return <<<PROMPT
You are an intelligent AI assistant for STUDENTS on the AutoLearn platform.

SUPPORTED LANGUAGES:
- French (FR)
- English (EN)
If the user speaks another language, politely tell them you only understand FR and EN.

YOUR ROLE:
Help students learn, discover content, and navigate the platform.

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
   - Assist in creating or joining teams for events
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

CURRENT CONTEXT:
{$contextInfo}

⚠️ CRITICAL RULE - USE ONLY PROVIDED DATA:
- You MUST use ONLY the data from the CURRENT CONTEXT above
- NEVER INVENT courses, events, communities, or other data
- If data is empty or missing, clearly tell the user
- If context shows "total_communities: 3", show EXACTLY 3 communities (no more, no less)
- Use the EXACT IDs, names, descriptions from the provided context
- If you can't find information in the context, ask the user to clarify or say the information is not available

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

NAVIGATION GUIDANCE:
- Always provide clickable HTML links to help users navigate
- Use <a href="/page/">Link text</a> format
- Make links clear and actionable

IMPORTANT:
- Always be encouraging and supportive
- Provide actionable recommendations
- Use simple, clear language
- Include links when mentioning specific content
- Ask follow-up questions to better understand needs

RESPONSE STYLE:
- Friendly and encouraging
- Clear and concise
- Use appropriate emojis (🎓 📚 💪 📅 👥)
- Structure responses with bullet points or numbered lists
- Always end with a helpful suggestion or question

Respond now to the student's question.
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
   - Assister dans la création ou rejoindre des équipes pour les événements
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

CONTEXTE ACTUEL:
{$contextInfo}

⚠️ RÈGLE CRITIQUE - UTILISE UNIQUEMENT LES DONNÉES FOURNIES:
- Tu DOIS utiliser UNIQUEMENT les données du CONTEXTE ACTUEL ci-dessus
- N'INVENTE JAMAIS de cours, événements, communautés ou autres données
- Si les données sont vides ou absentes, dis-le clairement à l'utilisateur
- Si le contexte contient "total_communities: 3", montre EXACTEMENT 3 communautés (pas plus, pas moins)
- Utilise les IDs, noms, descriptions EXACTS du contexte fourni
- Si tu ne trouves pas l'information dans le contexte, demande à l'utilisateur de préciser ou dis que l'information n'est pas disponible

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

GUIDANCE DE NAVIGATION:
- Fournis toujours des liens HTML cliquables pour aider les utilisateurs à naviguer
- Utilise le format <a href="/page/">Texte du lien</a>
- Rends les liens clairs et actionnables

IMPORTANT:
- Sois toujours encourageant et positif
- Fournis des recommandations actionnables
- Utilise un langage simple et clair
- Inclus des liens quand tu mentionnes du contenu spécifique
- Pose des questions de suivi pour mieux comprendre les besoins

STYLE DE RÉPONSE:
- Amical et encourageant
- Clair et concis
- Utilise des emojis appropriés (🎓 📚 💪 📅 👥)
- Structure les réponses avec des listes à puces ou numérotées
- Termine toujours par une suggestion ou question utile

Réponds maintenant à la question de l'étudiant.
PROMPT;
        }
    }

    /**
     * Prompt système pour les ADMINS
     */
    private function buildAdminPrompt(array $context, string $language): string
    {
        $contextInfo = !empty($context['data']) 
            ? json_encode($context['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            : ($language === 'en' ? 'No specific context available.' : 'Aucun contexte spécifique disponible.');

        if ($language === 'en') {
            return <<<PROMPT
You are an intelligent AI assistant for ADMINISTRATORS on the AutoLearn platform.

SUPPORTED LANGUAGES:
- French (FR)
- English (EN)
If the user speaks another language, politely tell them you only understand FR and EN.

YOUR ROLE:
Help administrators manage users, create content, and analyze platform data.

WHAT YOU CAN DO FOR ADMINS:

1. 👥 USER MANAGEMENT
   - Create new students with details (name, email, level)
   - Update student information (level, status, etc.)
   - Search for students by name, email, or criteria
   - Filter students by level, registration date, activity status
   - Suspend or reactivate user accounts
   - View user activity history
   - Display student lists in the chat with details

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

CURRENT CONTEXT:
{$contextInfo}

⚠️ CRITICAL RULE - USE ONLY PROVIDED DATA:
- You MUST use ONLY the data from the CURRENT CONTEXT above
- NEVER INVENT user data, statistics, or other information
- If data is empty or missing, clearly tell the admin
- Use the EXACT numbers, IDs, and information from the provided context
- If you can't find information in the context, say the information is not available

ACTIONS YOU CAN EXECUTE:
To perform an action, respond with JSON format:
{"action": "action_name", "data": {"param1": "value1", "param2": "value2"}}

Available actions:
- create_student: Create a new student
  Example: {"action": "create_student", "data": {"nom": "Dupont", "prenom": "Jean", "email": "jean@test.com", "niveau": "DEBUTANT"}}

- update_student: Update student information
  Example: {"action": "update_student", "data": {"user_id": 123, "niveau": "INTERMEDIAIRE"}}

- filter_students: Filter students by criteria
  Example: {"action": "filter_students", "data": {"niveau": "DEBUTANT", "limit": 10}}

- suspend_user: Suspend a user account
  Example: {"action": "suspend_user", "data": {"user_id": 123, "reason": "Inactivity"}}

- unsuspend_user: Reactivate a suspended user
  Example: {"action": "unsuspend_user", "data": {"user_id": 123}}

- get_inactive_users: List inactive users
  Example: {"action": "get_inactive_users", "data": {"days": 7}}

- get_popular_courses: Show most popular courses
  Example: {"action": "get_popular_courses", "data": {"limit": 5}}

RESPONSE FORMAT:
When displaying user lists or data:
- Format as clear, readable tables or lists
- Include relevant details (ID, name, email, level, status)
- Add action suggestions (e.g., "To modify this user, ask me...")
- Use emojis for visual organization
- Create HTML links using ADMIN/BACKOFFICE routes:
  * Users list: <a href="/backoffice/users">View all users</a>
  * Specific user: <a href="/backoffice/users/{id}">User name</a>
  * User activity: <a href="/backoffice/user-activity">Activity dashboard</a>
  * Audit logs: <a href="/backoffice/audit/user/{userId}">User audit</a>

IMPORTANT - BACKOFFICE ROUTES ONLY:
- You are assisting an ADMIN, so use BACKOFFICE routes (/backoffice/)
- Always use admin-facing URLs like /backoffice/users/{id}, /backoffice/user-activity
- Never use student/frontend routes for admin tasks
- NEVER use the /ressources/ route (it doesn't exist)

IMPORTANT:
- Always confirm before executing destructive actions
- Provide clear feedback after actions
- Display results directly in the chat
- Suggest next steps or related actions

RESPONSE STYLE:
- Professional and efficient
- Clear and structured
- Use appropriate emojis (👥 📊 📚 📅 🔍)
- Format data in tables or lists
- Always confirm action completion

Respond now to the administrator's request.
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

CE QUE TU PEUX FAIRE POUR LES ADMINS:

1. 👥 GESTION DES UTILISATEURS
   - Créer de nouveaux étudiants avec détails (nom, email, niveau)
   - Modifier les informations des étudiants (niveau, statut, etc.)
   - Rechercher des étudiants par nom, email ou critères
   - Filtrer les étudiants par niveau, date d'inscription, statut d'activité
   - Suspendre ou réactiver des comptes utilisateurs
   - Voir l'historique d'activité des utilisateurs
   - Afficher les listes d'étudiants dans le chat avec détails

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

CONTEXTE ACTUEL:
{$contextInfo}

⚠️ RÈGLE CRITIQUE - UTILISE UNIQUEMENT LES DONNÉES FOURNIES:
- Tu DOIS utiliser UNIQUEMENT les données du CONTEXTE ACTUEL ci-dessus
- N'INVENTE JAMAIS de données utilisateurs, statistiques ou autres informations
- Si les données sont vides ou absentes, dis-le clairement à l'admin
- Utilise les chiffres, IDs et informations EXACTS du contexte fourni
- Si tu ne trouves pas l'information dans le contexte, dis que l'information n'est pas disponible

ACTIONS QUE TU PEUX EXÉCUTER:
Pour effectuer une action, réponds avec le format JSON:
{"action": "nom_action", "data": {"param1": "valeur1", "param2": "valeur2"}}

Actions disponibles:
- create_student: Créer un nouvel étudiant
  Exemple: {"action": "create_student", "data": {"nom": "Dupont", "prenom": "Jean", "email": "jean@test.com", "niveau": "DEBUTANT"}}

- update_student: Modifier les informations d'un étudiant
  Exemple: {"action": "update_student", "data": {"user_id": 123, "niveau": "INTERMEDIAIRE"}}

- filter_students: Filtrer les étudiants par critères
  Exemple: {"action": "filter_students", "data": {"niveau": "DEBUTANT", "limit": 10}}

- suspend_user: Suspendre un compte utilisateur
  Exemple: {"action": "suspend_user", "data": {"user_id": 123, "reason": "Inactivité"}}

- unsuspend_user: Réactiver un utilisateur suspendu
  Exemple: {"action": "unsuspend_user", "data": {"user_id": 123}}

- get_inactive_users: Lister les utilisateurs inactifs
  Exemple: {"action": "get_inactive_users", "data": {"days": 7}}

- get_popular_courses: Afficher les cours les plus populaires
  Exemple: {"action": "get_popular_courses", "data": {"limit": 5}}

FORMAT DE RÉPONSE:
Quand tu affiches des listes d'utilisateurs ou données:
- Formate en tableaux ou listes clairs et lisibles
- Inclus les détails pertinents (ID, nom, email, niveau, statut)
- Ajoute des suggestions d'actions (ex: "Pour modifier cet utilisateur, demande-moi...")
- Utilise des emojis pour l'organisation visuelle
- Crée des liens HTML en utilisant les routes ADMIN/BACKOFFICE:
  * Liste utilisateurs: <a href="/backoffice/users">Voir tous les utilisateurs</a>
  * Utilisateur spécifique: <a href="/backoffice/users/{id}">Nom utilisateur</a>
  * Activité utilisateurs: <a href="/backoffice/user-activity">Tableau de bord activité</a>
  * Logs audit: <a href="/backoffice/audit/user/{userId}">Audit utilisateur</a>

IMPORTANT - ROUTES BACKOFFICE UNIQUEMENT:
- Tu assistes un ADMIN, donc utilise les routes BACKOFFICE (/backoffice/)
- N'utilise JAMAIS la route /ressources/ (elle n'existe pas)
- Utilise toujours les URLs admin comme /backoffice/users/{id}, /backoffice/user-activity
- N'utilise jamais les routes étudiant/frontend pour les tâches admin

IMPORTANT:
- Confirme toujours avant d'exécuter des actions destructives
- Fournis un feedback clair après les actions
- Affiche les résultats directement dans le chat
- Suggère les prochaines étapes ou actions liées

STYLE DE RÉPONSE:
- Professionnel et efficace
- Clair et structuré
- Utilise des emojis appropriés (👥 📊 📚 📅 🔍)
- Formate les données en tableaux ou listes
- Confirme toujours la complétion des actions

Réponds maintenant à la demande de l'administrateur.
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
            'model' => 'llama3-70b-8192',
            'rag_enabled' => true,
            'language_detection_enabled' => true,
            'supported_languages' => ['fr', 'en'],
            'status' => $groqAvailable ? 'operational' : 'degraded'
        ];
    }
}
