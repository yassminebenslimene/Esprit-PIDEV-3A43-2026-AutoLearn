# 📚 GUIDE DE RÉVISION - GESTION DES UTILISATEURS
## AutoLearn Platform - Documentation Complète pour Validation

---

## 📋 TABLE DES MATIÈRES

1. [Architecture Générale](#architecture)
2. [Gestion des Utilisateurs](#gestion-utilisateurs)
3. [Bundles Utilisés](#bundles)
4. [APIs et Services](#apis)
5. [Métiers Avancés](#metiers)
6. [Intelligence Artificielle](#ia)
7. [Structure des Fichiers](#structure)

---

## 🏗️ 1. ARCHITECTURE GÉNÉRALE

### Stack Technique
- **Framework:** Symfony 6.4
- **PHP:** 8.1+
- **Base de données:** MySQL (autolearn_db)
- **Template Engine:** Twig
- **Frontend:** HTML5, CSS3, JavaScript (Vanilla)

### Pattern MVC
```
Controller → Service → Repository → Entity → Database
     ↓
  Template (Twig)
```

---

## 👥 2. GESTION DES UTILISATEURS

### 2.1 Entités Utilisateur

#### Hiérarchie des Entités

```
User (Abstract)
├── Admin (Administrateur)
└── Etudiant (Étudiant)
```

**Fichiers:**
- `src/Entity/User.php` - Classe abstraite de base
- `src/Entity/Admin.php` - Hérite de User
- `src/Entity/Etudiant.php` - Hérite de User

**Propriétés communes (User):**
- `userId` (ID unique)
- `nom`, `prenom`, `email`
- `password` (hashé)
- `role` (ADMIN ou ETUDIANT)
- `createdAt` (date de création)
- `isSuspended` (statut de suspension)

**Propriétés spécifiques Etudiant:**
- `niveau` (DEBUTANT, INTERMEDIAIRE, AVANCE)
- Relations: cours, quiz, participations événements

### 2.2 Controllers Utilisateur

#### BackofficeController.php
**Emplacement:** `src/Controller/BackofficeController.php`

**Méthodes principales:**


1. **`users()`** - Liste des utilisateurs
   - Route: `/backoffice/users`
   - Fonctionnalités:
     * Recherche par nom/prénom/email
     * Filtre par rôle (Admin/Étudiant)
     * Statistiques en temps réel
   
2. **`userNew()`** - Créer un utilisateur
   - Route: `/backoffice/users/new`
   - Envoie email de bienvenue via BrevoMailService
   
3. **`userEdit()`** - Modifier un utilisateur
   - Route: `/backoffice/users/{id}/edit`
   - Peut changer le rôle (conversion Admin ↔ Étudiant)
   
4. **`userShow()`** - Détails d'un utilisateur
   - Route: `/backoffice/users/{id}`
   
5. **`userSuspend()`** - Suspendre un utilisateur
   - Route: `/backoffice/users/{id}/suspend`
   - Enregistre la raison de suspension
   - Log dans UserActivityBundle
   
6. **`userReactivate()`** - Réactiver un utilisateur
   - Route: `/backoffice/users/{id}/reactivate`
   
7. **`exportUsers()`** - Export CSV
   - Route: `/backoffice/users/export`



### 2.3 Templates Utilisateur

**Emplacement:** `templates/backoffice/users/`

1. **users.html.twig** - Liste des utilisateurs
   - Barre de recherche en temps réel
   - Filtres par rôle (All/Students/Admins)
   - Statistiques (Total, Students, Admins, New Today)
   - Actions: View, Edit, Suspend/Reactivate, Activities
   - Modal de suspension avec raisons prédéfinies
   - Modal d'activités avec historique complet

2. **show.html.twig** - Détails utilisateur
   - Informations personnelles
   - Historique d'activités
   - Statistiques de participation

3. **edit.html.twig** - Formulaire d'édition
   - Validation côté serveur
   - Changement de rôle possible

### 2.4 Sécurité

**Fichier:** `config/packages/security.yaml`

**Authentification:**
- Firewall: `main`
- Login: `/backoffice/login`
- Logout: `/backoffice/logout`
- Remember me: activé

**Autorisation:**
- `ROLE_ADMIN` - Accès backoffice complet
- `ROLE_ETUDIANT` - Accès frontoffice uniquement



**Hash des mots de passe:**
```php
UserPasswordHasherInterface $passwordHasher
$hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
```

---

## 📦 3. BUNDLES UTILISÉS

### 3.1 Bundles Symfony Officiels

1. **FrameworkBundle** - Core Symfony
2. **DoctrineBundle** - ORM pour base de données
3. **TwigBundle** - Moteur de templates
4. **SecurityBundle** - Authentification/Autorisation
5. **MakerBundle** - Génération de code (dev)
6. **WebProfilerBundle** - Debug toolbar (dev)

### 3.2 Bundles Tiers

1. **SimpleThingsEntityAuditBundle**
   - **Emplacement config:** `config/packages/simple_things_entity_audit.yaml`
   - **Fonction:** Audit trail des modifications d'entités
   - **Tables créées:** `*_audit`, `revisions`
   - **Utilisation:** Tracking des actions admin sur étudiants

2. **VichUploaderBundle**
   - **Config:** `config/packages/vich_uploader.yaml`
   - **Fonction:** Upload de fichiers (images, vidéos, PDFs)
   - **Mappings:**
     * `post_images` → uploads/images
     * `post_videos` → uploads/videos
     * `chapter_pdfs` → uploads/pdfs



3. **KnpPaginatorBundle**
   - **Fonction:** Pagination des listes
   - **Utilisation:** Liste des cours, quiz, utilisateurs

4. **SymfonyCasts ResetPasswordBundle**
   - **Fonction:** Réinitialisation de mot de passe
   - **Routes:** `/reset-password`, `/reset-password/check-email`

5. **CalendarBundle (Tattali)**
   - **Fonction:** Gestion du calendrier d'événements
   - **Intégration:** FullCalendar.js

### 3.3 Bundle Custom: UserActivityBundle

**Emplacement:** `src/Bundle/UserActivityBundle/`

**Structure:**
```
UserActivityBundle/
├── Controller/
│   └── Admin/
│       └── ActivityController.php
├── Entity/
│   └── UserActivity.php
├── Repository/
│   └── UserActivityRepository.php
├── Service/
│   └── ActivityLogger.php
└── UserActivityBundle.php
```

**Fonction:** Logging détaillé des activités utilisateur

**Événements trackés:**
- Login/Logout
- Création/Modification/Suppression d'utilisateur
- Suspension/Réactivation
- Consultation de profils



**Utilisation dans le code:**
```php
// Injection du service
private ActivityLogger $activityLogger;

// Log d'une activité
$this->activityLogger->log(
    action: 'user.suspended',
    user: $etudiant,
    success: true,
    metadata: [
        'suspended_by' => $admin->getId(),
        'suspended_by_name' => $admin->getPrenom() . ' ' . $admin->getNom(),
        'suspension_reason' => $reason
    ]
);
```

**Routes:**
- `/backoffice/user-activity` - Liste des activités
- `/backoffice/user-activity/user/{id}` - Activités d'un utilisateur
- `/backoffice/user-activity/user/{id}/json` - API JSON

---

## 🔌 4. APIs ET SERVICES

### 4.1 Services Métier

#### BrevoMailService
**Emplacement:** `src/Service/BrevoMailService.php`

**Fonction:** Envoi d'emails via Brevo (ex-Sendinblue)

**Configuration:** `.env`
```
BREVO_API_KEY=your_api_key
BREVO_SENDER_EMAIL=noreply@autolearn.com
BREVO_SENDER_NAME=AutoLearn
```

**Méthodes:**
- `sendWelcomeEmail()` - Email de bienvenue avec identifiants
- `sendPasswordResetEmail()` - Réinitialisation mot de passe
- `sendEventNotification()` - Notification d'événement



#### NotificationService
**Emplacement:** `src/Service/NotificationService.php`

**Fonction:** Système de notifications in-app

**Types de notifications:**
- `event_invitation` - Invitation à un événement
- `event_reminder` - Rappel d'événement
- `quiz_result` - Résultat de quiz
- `community_post` - Nouveau post dans communauté

**Méthodes:**
- `createNotification()` - Créer une notification
- `markAsRead()` - Marquer comme lue
- `getUnreadCount()` - Nombre de non-lues

### 4.2 APIs REST

#### Endpoints disponibles:

1. **Chapitres API**
   - `GET /api/chapitres/{id}` - Détails d'un chapitre
   - `GET /api/chapitres/{id}/translate` - Traduction

2. **Quiz API**
   - `GET /api/quiz/{id}/questions` - Questions d'un quiz
   - `GET /api/question/{id}/options` - Options d'une question

3. **Cours API**
   - `GET /api/cours/{id}/chapitres` - Chapitres d'un cours

4. **Notifications API**
   - `GET /notifications/api/unread-count` - Nombre non lues
   - `GET /notifications/api/recent` - Notifications récentes

5. **Languages API**
   - `GET /api/languages` - Langues disponibles



---

## 🎯 5. MÉTIERS AVANCÉS

### 5.1 Système d'Audit (SimpleThingsEntityAuditBundle)

**Emplacement:** `src/Controller/AuditController.php`

**Fonctionnalités:**

1. **Tracking des modifications**
   - Détecte automatiquement: CREATE, UPDATE, SUSPEND, REACTIVATE
   - Utilise self-join pour comparer révisions
   - Stocke dans tables `*_audit` et `revisions`

2. **Statistiques d'audit**
   - Actions par type (CREATE, UPDATE, SUSPEND, etc.)
   - Admins les plus actifs
   - Étudiants les plus modifiés
   - Graphiques de tendances

3. **Historique utilisateur**
   - Timeline complète des modifications
   - Détails de chaque révision
   - Comparaison avant/après

**Routes:**
- `/backoffice/audit` - Liste des audits
- `/backoffice/audit/stats` - Statistiques
- `/backoffice/audit/user/{id}` - Historique utilisateur
- `/backoffice/audit/revision/{id}` - Détails révision

**Code clé - Détection du type d'action:**
```sql
SELECT 
    ua.rev,
    ua.revtype,
    CASE
        WHEN ua.revtype = 'INS' THEN 'CREATE'
        WHEN prev.is_suspended = 0 AND ua.is_suspended = 1 THEN 'SUSPEND'
        WHEN prev.is_suspended = 1 AND ua.is_suspended = 0 THEN 'REACTIVATE'
        ELSE 'UPDATE'
    END as action_type
FROM user_audit ua
LEFT JOIN user_audit prev ON prev.user_id = ua.user_id 
    AND prev.rev < ua.rev
```



### 5.2 Système de Suspension Automatique

**Emplacement:** `src/Command/AutoSuspendInactiveUsersCommand.php`

**Fonction:** Suspend automatiquement les étudiants inactifs

**Critères:**
- Pas de connexion depuis 30 jours
- Pas déjà suspendu
- Rôle ETUDIANT uniquement

**Exécution:**
```bash
php bin/console app:auto-suspend-inactive-users
```

**Configuration cron (optionnel):**
```
0 2 * * * cd /path/to/autolearn && php bin/console app:auto-suspend-inactive-users
```

### 5.3 Système de Workflow (Événements)

**Emplacement:** `config/packages/workflow.yaml`

**Workflow: Participation Événement**

**États:**
- `pending` - En attente
- `approved` - Approuvé
- `rejected` - Rejeté
- `cancelled` - Annulé

**Transitions:**
- `approve` - Approuver une participation
- `reject` - Rejeter une participation
- `cancel` - Annuler une participation

**Utilisation dans le code:**
```php
use Symfony\Component\Workflow\WorkflowInterface;

$workflow->apply($participation, 'approve');
```



---

## 🤖 6. INTELLIGENCE ARTIFICIELLE

### 6.1 GroqService - Service IA Principal

**Emplacement:** `src/Service/GroqService.php`

**Configuration:** `.env`
```
GROQ_API_KEY=your_groq_api_key
GROQ_MODEL=llama-3.3-70b-versatile
```

**Fonction:** Interface avec l'API Groq (LLM)

**Méthodes:**
- `chat()` - Conversation générale
- `generateCompletion()` - Génération de texte
- `analyzeText()` - Analyse de texte

### 6.2 Services IA Spécialisés

#### QuizCorrectorAIService
**Emplacement:** `src/Service/QuizCorrectorAIService.php`

**Fonction:** Correction automatique de quiz avec IA

**Fonctionnalités:**
- Analyse des réponses de l'étudiant
- Génération de feedback personnalisé
- Suggestions d'amélioration
- Score de compréhension

**Utilisation:**
```php
$result = $quizCorrectorAI->correctQuiz(
    $quiz,
    $studentAnswers,
    $correctAnswers
);
```

**Route:** `/quiz/{id}/result-with-ai`



#### AIAssistantService
**Emplacement:** `src/Service/AIAssistantService.php`

**Fonction:** Assistant IA conversationnel

**Capacités:**
- Répondre aux questions sur les cours
- Expliquer des concepts
- Générer des exemples
- Aide à la résolution de problèmes

**Widget Chat:** `templates/ai_assistant/chat_widget.html.twig`
- Disponible sur toutes les pages
- Interface chat en temps réel
- Historique de conversation

**Routes:**
- `/ai-assistant/chat` - Interface principale
- `/ai-assistant/test` - Page de test

#### ChapterExplainerService
**Emplacement:** Intégré dans AIAssistantService

**Fonction:** Explication détaillée de chapitres

**Fonctionnalités:**
- Résumé du chapitre
- Points clés
- Exemples pratiques
- Questions de compréhension

**Route:** `/chapter-explainer/{id}`

#### LanguageDetectorService
**Emplacement:** `src/Service/LanguageDetectorService.php`

**Fonction:** Détection automatique de la langue

**Méthodes:**
- `detectLanguage($text)` - Détecte la langue d'un texte
- Supporte: FR, EN, AR, ES

**Utilisation:**
```php
$language = $languageDetector->detectLanguage($userInput);
// Retourne: 'fr', 'en', 'ar', ou 'es'
```



### 6.3 Services IA Communauté

#### AiReactionService
**Emplacement:** `src/Service/AiReactionService.php`

**Fonction:** Génère des réactions emoji automatiques pour les posts

**Exemple:**
```php
$reaction = $aiReactionService->generateReaction($postContent);
// Retourne: ['emoji' => '👍', 'sentiment' => 'positive']
```

#### SentimentAnalysisService
**Emplacement:** `src/Service/SentimentAnalysisService.php`

**Fonction:** Analyse le sentiment d'un texte

**Résultats possibles:**
- `positive` - Sentiment positif
- `negative` - Sentiment négatif
- `neutral` - Sentiment neutre

#### TitleSuggestionService
**Emplacement:** `src/Service/TitleSuggestionService.php`

**Fonction:** Suggère des titres pour les posts

**Utilisation:**
```php
$title = $titleSuggestionService->suggestTitle($postContent);
```

#### AiModerationService
**Emplacement:** `src/Service/AiModerationService.php`

**Fonction:** Modération automatique de contenu

**Vérifie:**
- Langage inapproprié
- Spam
- Contenu offensant

**Retourne:**
```php
[
    'is_appropriate' => true/false,
    'reason' => 'Raison si inapproprié',
    'confidence' => 0.95
]
```



#### AiSummaryService
**Emplacement:** `src/Service/AiSummaryService.php`

**Fonction:** Génère des résumés automatiques

**Utilisation:**
```php
$summary = $aiSummaryService->generateSummary($longText, $maxLength = 200);
```

### 6.4 RAGService (Retrieval-Augmented Generation)

**Emplacement:** `src/Service/RAGService.php`

**Fonction:** Recherche et génération augmentée

**Processus:**
1. Recherche dans la base de connaissances
2. Récupère les documents pertinents
3. Génère une réponse basée sur les documents

**Utilisation:**
```php
$response = $ragService->query(
    question: "Comment créer un cours?",
    context: $courseDocuments
);
```

### 6.5 ActionExecutorService

**Emplacement:** `src/Service/ActionExecutorService.php`

**Fonction:** Exécute des actions basées sur les commandes IA

**Actions supportées:**
- Créer un cours
- Ajouter un chapitre
- Créer un quiz
- Envoyer une notification

**Exemple:**
```php
$result = $actionExecutor->execute([
    'action' => 'create_course',
    'params' => ['title' => 'Nouveau cours', 'description' => '...']
]);
```



---

## 📁 7. STRUCTURE DES FICHIERS

### 7.1 Organisation des Controllers

```
src/Controller/
├── AIAssistantController.php          # Assistant IA
├── AuditController.php                 # Système d'audit
├── BackofficeController.php            # Gestion utilisateurs, dashboard
├── ChapterExplainerController.php      # Explication chapitres
├── CoursController.php                 # Gestion cours
├── EvenementController.php             # Gestion événements
├── NotificationController.php          # Notifications
├── QuizController.php                  # Gestion quiz
├── SecurityController.php              # Login/Logout
├── UserController.php                  # CRUD utilisateurs (legacy)
└── Backoffice/
    ├── CommunauteBackofficeController.php
    ├── PostBackofficeController.php
    └── CommentaireBackofficeController.php
```

### 7.2 Organisation des Services

```
src/Service/
├── BrevoMailService.php               # Emails
├── NotificationService.php            # Notifications in-app
├── GroqService.php                    # API Groq (IA)
├── QuizCorrectorAIService.php         # Correction quiz IA
├── AIAssistantService.php             # Assistant IA
├── RAGService.php                     # RAG
├── ActionExecutorService.php          # Exécution actions IA
├── LanguageDetectorService.php        # Détection langue
├── AiReactionService.php              # Réactions IA
├── SentimentAnalysisService.php       # Analyse sentiment
├── TitleSuggestionService.php         # Suggestion titres
├── AiModerationService.php            # Modération IA
└── AiSummaryService.php               # Résumés IA
```



### 7.3 Organisation des Entités

```
src/Entity/
├── User.php                    # Classe abstraite
├── Admin.php                   # Hérite de User
├── Etudiant.php               # Hérite de User
├── Cours.php                  # Cours
├── Chapitre.php               # Chapitres
├── Quiz.php                   # Quiz
├── Question.php               # Questions
├── OptionQuestion.php         # Options de questions
├── Evenement.php              # Événements
├── Participation.php          # Participations événements
├── Communaute.php             # Communautés
├── Post.php                   # Posts communauté
├── PostReaction.php           # Réactions posts
├── Commentaire.php            # Commentaires
└── Notification.php           # Notifications
```

### 7.4 Organisation des Templates

```
templates/
├── backoffice/
│   ├── base.html.twig                 # Layout backoffice
│   ├── _navbar.html.twig              # Navbar
│   ├── _sidebar.html.twig             # Sidebar
│   ├── users/
│   │   ├── users.html.twig            # Liste utilisateurs
│   │   ├── show.html.twig             # Détails utilisateur
│   │   └── edit.html.twig             # Édition utilisateur
│   ├── audit/
│   │   ├── index.html.twig            # Liste audits
│   │   ├── stats.html.twig            # Statistiques
│   │   ├── user_history.html.twig    # Historique utilisateur
│   │   └── revision_details.html.twig # Détails révision
│   └── communaute/
│       ├── index.html.twig
│       ├── show.html.twig
│       └── edit.html.twig
└── frontoffice/
    ├── base.html.twig                 # Layout frontoffice
    ├── profile.html.twig              # Profil étudiant
    ├── notifications/
    │   └── index.html.twig
    └── quiz/
        └── result_with_ai.html.twig
```



### 7.5 Configuration

```
config/
├── packages/
│   ├── doctrine.yaml              # Configuration ORM
│   ├── security.yaml              # Sécurité
│   ├── simple_things_entity_audit.yaml  # Audit
│   ├── vich_uploader.yaml         # Upload fichiers
│   ├── workflow.yaml              # Workflows
│   ├── mailer.yaml                # Configuration email
│   └── user_activity.yaml         # UserActivityBundle
├── routes/
│   └── routes.yaml                # Routes principales
└── services.yaml                  # Services DI
```

---

## 🎓 8. FLUX DE TRAVAIL PRINCIPAUX

### 8.1 Création d'un Utilisateur

```
1. Admin accède à /backoffice/users/new
2. Remplit le formulaire (UserType)
3. BackofficeController::userNew()
   ├── Validation du formulaire
   ├── Hash du mot de passe
   ├── Création entité (Admin ou Etudiant)
   ├── Persist + Flush
   ├── BrevoMailService::sendWelcomeEmail()
   └── ActivityLogger::log('user.created')
4. Redirection vers /backoffice/users
```

### 8.2 Suspension d'un Utilisateur

```
1. Admin clique sur "Suspendre" dans users.html.twig
2. Modal s'ouvre avec raisons prédéfinies
3. Soumission vers /backoffice/users/{id}/suspend
4. BackofficeController::userSuspend()
   ├── Vérifie que c'est un étudiant
   ├── Set isSuspended = true
   ├── Enregistre la raison
   ├── Flush
   ├── ActivityLogger::log('user.suspended')
   └── SimpleThingsEntityAudit enregistre automatiquement
5. Redirection avec message de succès
```



### 8.3 Recherche et Filtrage Utilisateurs

```
1. Utilisateur tape dans la barre de recherche
2. Formulaire GET vers /backoffice/users?search=...
3. BackofficeController::users()
   ├── Récupère paramètre 'search'
   ├── Récupère paramètre 'role' (filtre)
   ├── Query Builder Doctrine:
   │   WHERE nom LIKE %search%
   │   OR prenom LIKE %search%
   │   OR email LIKE %search%
   │   AND role = :role (si filtre actif)
   ├── Calcule statistiques (total, students, admins)
   └── Render users.html.twig
4. Template affiche résultats filtrés
```

### 8.4 Correction de Quiz avec IA

```
1. Étudiant termine un quiz
2. Soumission vers /quiz/{id}/submit
3. QuizController::submit()
   ├── Calcule le score
   ├── Enregistre les réponses
   └── Redirige vers /quiz/{id}/result-with-ai
4. QuizController::resultWithAI()
   ├── Récupère quiz + réponses
   ├── QuizCorrectorAIService::correctQuiz()
   │   ├── Prépare le prompt pour Groq
   │   ├── GroqService::chat()
   │   ├── Parse la réponse JSON
   │   └── Retourne feedback + suggestions
   └── Render result_with_ai.html.twig
5. Affichage du feedback personnalisé
```

---

## 🔍 9. POINTS CLÉS POUR LA VALIDATION

### 9.1 Démontrer la Gestion Utilisateurs

**À montrer:**
1. Liste des utilisateurs avec recherche/filtres
2. Création d'un nouvel étudiant (email envoyé)
3. Modification d'un utilisateur
4. Suspension avec raison
5. Réactivation
6. Historique d'activités
7. Export CSV



### 9.2 Démontrer les Bundles

**SimpleThingsEntityAuditBundle:**
- Montrer `/backoffice/audit`
- Expliquer la détection automatique des actions
- Montrer les statistiques
- Montrer l'historique d'un utilisateur

**UserActivityBundle (Custom):**
- Montrer `/backoffice/user-activity`
- Expliquer le logging manuel vs automatique
- Montrer les métadonnées enrichies
- Montrer l'API JSON

**VichUploaderBundle:**
- Montrer l'upload d'une image de post
- Montrer l'upload d'un PDF de chapitre
- Expliquer les mappings

### 9.3 Démontrer les APIs

**À tester:**
```bash
# Chapitres
curl http://127.0.0.1:8000/api/chapitres/1

# Quiz questions
curl http://127.0.0.1:8000/api/quiz/1/questions

# Notifications
curl http://127.0.0.1:8000/notifications/api/unread-count

# Languages
curl http://127.0.0.1:8000/api/languages
```

### 9.4 Démontrer les Métiers Avancés

**Audit:**
- Créer un utilisateur → Voir dans audit (CREATE)
- Modifier un utilisateur → Voir dans audit (UPDATE)
- Suspendre un utilisateur → Voir dans audit (SUSPEND)
- Montrer les statistiques

**Workflow:**
- Créer un événement
- Étudiant s'inscrit (pending)
- Admin approuve (approved)
- Montrer le changement d'état

**Suspension automatique:**
```bash
php bin/console app:auto-suspend-inactive-users
```



### 9.5 Démontrer l'Intelligence Artificielle

**QuizCorrectorAI:**
1. Faire un quiz en tant qu'étudiant
2. Soumettre les réponses
3. Montrer le feedback IA personnalisé
4. Expliquer l'intégration avec Groq

**AI Assistant:**
1. Ouvrir le widget chat (coin inférieur droit)
2. Poser une question sur un cours
3. Montrer la réponse contextuelle
4. Expliquer le RAG

**Chapter Explainer:**
1. Aller sur un chapitre
2. Cliquer sur "Expliquer avec IA"
3. Montrer l'explication générée

**Services Communauté:**
1. Créer un post
2. Montrer la réaction IA automatique
3. Montrer l'analyse de sentiment
4. Montrer la suggestion de titre

---

## 📊 10. COMMANDES UTILES

### Développement
```bash
# Démarrer le serveur
symfony server:start
# ou
php -S 127.0.0.1:8000 -t public

# Vider le cache
php bin/console cache:clear

# Lister les routes
php bin/console debug:router

# Lister les services
php bin/console debug:container
```

### Base de données
```bash
# Créer la base
php bin/console doctrine:database:create

# Mettre à jour le schéma
php bin/console doctrine:schema:update --force

# Charger les fixtures
php bin/console doctrine:fixtures:load
```



### Tests
```bash
# Test Groq API
php bin/console app:test-groq

# Test Brevo Email
php bin/console app:test-brevo

# Test Audit
php bin/console app:test-audit

# Simuler inactivité
php bin/console app:simulate-inactivity

# Suspension automatique
php bin/console app:auto-suspend-inactive-users
```

---

## 🎯 11. CHECKLIST VALIDATION

### Fonctionnalités Utilisateur
- [ ] Créer un utilisateur (Admin/Étudiant)
- [ ] Modifier un utilisateur
- [ ] Suspendre un utilisateur avec raison
- [ ] Réactiver un utilisateur
- [ ] Rechercher des utilisateurs
- [ ] Filtrer par rôle
- [ ] Voir l'historique d'activités
- [ ] Export CSV

### Bundles
- [ ] SimpleThingsEntityAuditBundle configuré et fonctionnel
- [ ] UserActivityBundle (custom) opérationnel
- [ ] VichUploaderBundle pour uploads
- [ ] Workflow pour événements
- [ ] Mailer (Brevo) configuré

### APIs
- [ ] API Chapitres fonctionnelle
- [ ] API Quiz fonctionnelle
- [ ] API Notifications fonctionnelle
- [ ] API Languages fonctionnelle

### Métiers Avancés
- [ ] Système d'audit complet
- [ ] Détection automatique des actions
- [ ] Statistiques d'audit
- [ ] Suspension automatique
- [ ] Workflow événements



### Intelligence Artificielle
- [ ] GroqService configuré
- [ ] QuizCorrectorAI fonctionnel
- [ ] AI Assistant opérationnel
- [ ] Chapter Explainer
- [ ] Services communauté IA (réactions, sentiment, etc.)
- [ ] RAG Service
- [ ] Language Detector

---

## 💡 12. CONSEILS POUR LA PRÉSENTATION

### Structure de Présentation Recommandée

**1. Introduction (2 min)**
- Présentation du projet AutoLearn
- Technologies utilisées
- Architecture générale

**2. Gestion des Utilisateurs (5 min)**
- Démonstration live:
  * Créer un étudiant
  * Rechercher/Filtrer
  * Suspendre avec raison
  * Voir l'historique
- Expliquer le code clé

**3. Bundles (5 min)**
- SimpleThingsEntityAuditBundle
  * Montrer l'audit trail
  * Expliquer la détection automatique
- UserActivityBundle (custom)
  * Montrer les logs enrichis
  * Expliquer l'architecture
- Autres bundles importants

**4. APIs (3 min)**
- Montrer les endpoints
- Tester avec curl ou Postman
- Expliquer l'utilisation

**5. Métiers Avancés (5 min)**
- Système d'audit avancé
- Workflow événements
- Suspension automatique
- Statistiques



**6. Intelligence Artificielle (5 min)**
- QuizCorrectorAI
  * Faire un quiz
  * Montrer le feedback IA
- AI Assistant
  * Poser des questions
  * Montrer les réponses contextuelles
- Services communauté IA

**7. Questions/Réponses (5 min)**

### Points à Mettre en Avant

**Complexité Technique:**
- Architecture MVC bien structurée
- Séparation des responsabilités (Services)
- Utilisation de bundles avancés
- Custom bundle développé
- Intégration IA

**Qualité du Code:**
- Code commenté et documenté
- Respect des conventions Symfony
- Gestion des erreurs
- Validation des données
- Sécurité (CSRF, hash passwords)

**Fonctionnalités Avancées:**
- Audit trail automatique
- Détection intelligente des actions
- Suspension automatique
- Workflow avec états
- IA intégrée

**Expérience Utilisateur:**
- Interface moderne (glassmorphism)
- Recherche en temps réel
- Filtres dynamiques
- Modals interactifs
- Notifications

---

## 📝 13. GLOSSAIRE

**MVC:** Model-View-Controller - Pattern architectural
**ORM:** Object-Relational Mapping - Doctrine
**DI:** Dependency Injection - Injection de dépendances
**CRUD:** Create, Read, Update, Delete
**API:** Application Programming Interface
**REST:** Representational State Transfer
**IA/AI:** Intelligence Artificielle
**LLM:** Large Language Model
**RAG:** Retrieval-Augmented Generation
**CSRF:** Cross-Site Request Forgery
**Bundle:** Package Symfony réutilisable



---

## 🔗 14. RÉFÉRENCES RAPIDES

### URLs Importantes
- **Backoffice:** http://127.0.0.1:8000/backoffice
- **Login:** http://127.0.0.1:8000/backoffice/login
- **Users:** http://127.0.0.1:8000/backoffice/users
- **Audit:** http://127.0.0.1:8000/backoffice/audit
- **Activities:** http://127.0.0.1:8000/backoffice/user-activity
- **AI Assistant:** http://127.0.0.1:8000/ai-assistant/chat

### Fichiers Clés à Connaître
- `src/Controller/BackofficeController.php` - Gestion utilisateurs
- `src/Controller/AuditController.php` - Système d'audit
- `src/Service/GroqService.php` - Service IA principal
- `src/Bundle/UserActivityBundle/` - Bundle custom
- `config/packages/security.yaml` - Configuration sécurité
- `templates/backoffice/users/users.html.twig` - Interface utilisateurs

### Variables d'Environnement (.env)
```
DATABASE_URL="mysql://root:@127.0.0.1:3306/autolearn_db"
GROQ_API_KEY=your_groq_api_key
GROQ_MODEL=llama-3.3-70b-versatile
BREVO_API_KEY=your_brevo_api_key
BREVO_SENDER_EMAIL=noreply@autolearn.com
```

---

## ✅ CONCLUSION

Ce guide couvre l'ensemble de votre travail sur la gestion des utilisateurs et les fonctionnalités avancées de la plateforme AutoLearn. 

**Points forts à souligner:**
- Architecture propre et maintenable
- Bundles avancés bien intégrés
- Custom bundle développé
- Intelligence artificielle intégrée
- Système d'audit complet
- APIs REST fonctionnelles
- Expérience utilisateur moderne

**Bonne chance pour votre validation! 🎓**

