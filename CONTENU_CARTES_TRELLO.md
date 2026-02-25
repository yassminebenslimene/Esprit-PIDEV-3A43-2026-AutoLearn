# 🎴 Contenu Détaillé des Cartes Trello - Module Gestion Utilisateur

## 📋 Guide d'Utilisation

Ce document contient le contenu détaillé pour chaque carte Trello (20 User Stories).
Chaque carte est prête à être copiée-collée directement dans Trello.

**Format de chaque carte**:
- Titre
- Description complète
- Checklist des tâches
- Fichiers créés/modifiés
- Estimation
- Critères d'acceptation
- Étiquettes recommandées

---

## 🎯 SPRINT 1 - CRUD & Authentification

---

### 📌 CARTE 1: US-1.1 - Inscription Utilisateur

**Titre**: US-1.1 - En tant qu'utilisateur je souhaite m'inscrire

**Description**:
```
📌 User Story: En tant qu'utilisateur je souhaite m'inscrire

🎯 Sprint: Sprint 1
⏱️ Estimation: 6h
👤 Responsable: Ilef Yousfi

📝 Contexte:
Créer le système d'entités utilisateur avec Single Table Inheritance (STI).
User est abstract, Etudiant et Admin héritent de User.
Ajouter les champs de suspension et tracking de connexion.

🔧 Technologies:
- Symfony 6.4
- Doctrine ORM
- MySQL (colonnes camelCase)
```

**Checklist**:
```
☐ Créer entité User (abstract) avec Single Table Inheritance
☐ Créer entité Etudiant (extends User) avec attribut niveau
☐ Créer entité Admin (extends User)
☐ Ajouter champs suspension (isSuspended, suspendedAt, suspendedBy, suspensionReason)
☐ Ajouter champ lastLoginAt pour tracking connexion
☐ Créer migrations avec colonnes camelCase
☐ Configurer annotations Doctrine avec name explicites
```

**Fichiers créés/modifiés**:
```
- src/Entity/User.php
- src/Entity/Etudiant.php
- src/Entity/Admin.php
- migrations/VersionXXXXXXXXXXXXXX.php
```

**Critères d'acceptation**:
```
✅ User est abstract avec @InheritanceType("SINGLE_TABLE")
✅ Etudiant et Admin héritent correctement de User
✅ Colonnes database en camelCase (pas snake_case)
✅ Champs suspension fonctionnels
✅ Migration exécutée sans erreur
✅ Schema validation passe
```

**Étiquettes**: Backend (🔵), Database (🟣), Sprint 1 (🔵 clair)

---

### 📌 CARTE 2: US-1.2 - Connexion Utilisateur

**Titre**: US-1.2 - En tant qu'utilisateur je souhaite me connecter

**Description**:
```
📌 User Story: En tant qu'utilisateur je souhaite me connecter

🎯 Sprint: Sprint 1
⏱️ Estimation: 8.5h
👤 Responsable: Ilef Yousfi

📝 Contexte:
Implémenter le système d'authentification complet avec Symfony Security.
Redirection intelligente selon le rôle (Admin → backoffice, Etudiant → frontoffice).
Bloquer les comptes suspendus et mettre à jour lastLoginAt.

🔧 Technologies:
- Symfony Security Component
- Form Login Authenticator
- Twig templates
```

**Checklist**:
```
☐ Configurer Symfony Security (security.yaml) avec rôles
☐ Créer SecurityController avec méthode login()
☐ Implémenter authentification par email/password
☐ Redirection selon rôle (Admin→backoffice, Etudiant→frontoffice)
☐ Mettre à jour lastLoginAt lors de la connexion
☐ Créer vue Twig login avec design moderne
☐ Bloquer connexion des comptes suspendus
```

**Fichiers créés/modifiés**:
```
- config/packages/security.yaml
- src/Controller/SecurityController.php
- templates/security/login.html.twig
- config/routes.yaml
```

**Critères d'acceptation**:
```
✅ Login fonctionne avec email/password
✅ Admin redirigé vers /backoffice
✅ Etudiant redirigé vers /frontoffice
✅ Comptes suspendus bloqués avec message
✅ lastLoginAt mis à jour automatiquement
✅ Design moderne et responsive
✅ Messages d'erreur clairs
```

**Étiquettes**: Backend (🔵), Frontend (🟢), Sprint 1 (🔵 clair)

---

### 📌 CARTE 3: US-1.3 - Déconnexion Utilisateur

**Titre**: US-1.3 - En tant qu'utilisateur je souhaite me déconnecter

**Description**:
```
📌 User Story: En tant qu'utilisateur je souhaite me déconnecter

🎯 Sprint: Sprint 1
⏱️ Estimation: 1h
👤 Responsable: Ilef Yousfi

📝 Contexte:
Configurer la route de déconnexion dans security.yaml.
Ajouter un lien de déconnexion visible dans le menu avec icône.

🔧 Technologies:
- Symfony Security (logout)
- Twig templates
```

**Checklist**:
```
☐ Configurer route déconnexion dans security.yaml
☐ Créer lien déconnexion dans menu avec icône
```

**Fichiers créés/modifiés**:
```
- config/packages/security.yaml
- templates/backoffice/base.html.twig
- templates/frontoffice/base.html.twig
```

**Critères d'acceptation**:
```
✅ Bouton déconnexion visible dans menu
✅ Déconnexion fonctionne correctement
✅ Redirection vers page login après déconnexion
✅ Session détruite complètement
```

**Étiquettes**: Backend (🔵), Frontend (🟢), Config (🟡), Sprint 1 (🔵 clair)

---

### 📌 CARTE 4: US-1.4 - Consulter Profil

**Titre**: US-1.4 - En tant qu'utilisateur je souhaite consulter mon profil

**Description**:
```
📌 User Story: En tant qu'utilisateur je souhaite consulter mon profil

🎯 Sprint: Sprint 1
⏱️ Estimation: 3h
👤 Responsable: Ilef Yousfi

📝 Contexte:
Créer le FrontofficeController pour gérer les pages utilisateur.
Afficher les informations du profil avec un design moderne.

🔧 Technologies:
- Symfony Controller
- Twig templates
- Bootstrap/CSS
```

**Checklist**:
```
☐ Créer FrontofficeController
☐ Créer vue profil avec design moderne
```

**Fichiers créés/modifiés**:
```
- src/Controller/FrontofficeController.php
- templates/frontoffice/profile.html.twig
- templates/frontoffice/base.html.twig
```

**Critères d'acceptation**:
```
✅ Page profil accessible via /frontoffice/profile
✅ Affiche nom, prénom, email, niveau (pour Etudiant)
✅ Design moderne et responsive
✅ Seulement l'utilisateur connecté peut voir son profil
```

**Étiquettes**: Backend (🔵), Frontend (🟢), Sprint 1 (🔵 clair)

---

### 📌 CARTE 5: US-1.5 - Modifier Informations

**Titre**: US-1.5 - En tant qu'utilisateur je souhaite modifier mes informations

**Description**:
```
📌 User Story: En tant qu'utilisateur je souhaite modifier mes informations

🎯 Sprint: Sprint 1
⏱️ Estimation: 2.5h
👤 Responsable: Ilef Yousfi

📝 Contexte:
Permettre à l'utilisateur de modifier son nom, prénom et email.
Créer un formulaire Symfony avec validation.

🔧 Technologies:
- Symfony Forms
- Form validation
- Doctrine ORM
```

**Checklist**:
```
☐ Implémenter modification nom, prénom, email
☐ Créer formulaire édition profil
```

**Fichiers créés/modifiés**:
```
- src/Controller/FrontofficeController.php
- src/Form/UserProfileType.php
- templates/frontoffice/edit_profile.html.twig
```

**Critères d'acceptation**:
```
✅ Formulaire pré-rempli avec données actuelles
✅ Validation des champs (email valide, champs requis)
✅ Sauvegarde en base de données
✅ Message de confirmation après modification
✅ Redirection vers page profil
```

**Étiquettes**: Backend (🔵), Frontend (🟢), Sprint 1 (🔵 clair)

---

### 📌 CARTE 6: US-1.6 - Rechercher Étudiant (Admin)

**Titre**: US-1.6 - En tant qu'administrateur je souhaite rechercher un étudiant

**Description**:
```
📌 User Story: En tant qu'administrateur je souhaite rechercher un étudiant

🎯 Sprint: Sprint 1
⏱️ Estimation: 8h
👤 Responsable: Ilef Yousfi

📝 Contexte:
Créer le UserController pour le backoffice admin.
Implémenter recherche par nom et email avec LIKE query.
Ajouter pagination et filtres pour gérer beaucoup d'utilisateurs.

🔧 Technologies:
- Symfony Controller
- Doctrine QueryBuilder
- Pagination (KnpPaginatorBundle ou manuel)
```

**Checklist**:
```
☐ Créer UserController pour backoffice
☐ Implémenter recherche par nom et email (LIKE query)
☐ Ajouter pagination et filtres
☐ Créer vue users.html.twig avec tableau moderne
```

**Fichiers créés/modifiés**:
```
- src/Controller/UserController.php
- templates/backoffice/users/users.html.twig
- templates/backoffice/base.html.twig (sidebar)
```

**Critères d'acceptation**:
```
✅ Recherche fonctionne par nom (LIKE %query%)
✅ Recherche fonctionne par email (LIKE %query%)
✅ Pagination affiche 20 résultats par page
✅ Filtres: Tous / Actifs / Suspendus
✅ Tableau moderne avec colonnes: Nom, Email, Rôle, Statut, Actions
✅ Accessible uniquement par ROLE_ADMIN
```

**Étiquettes**: Backend (🔵), Frontend (🟢), Sprint 1 (🔵 clair)

---

### 📌 CARTE 7: US-1.7 - Consulter Profil Détaillé (Admin)

**Titre**: US-1.7 - En tant qu'administrateur je souhaite consulter profil détaillé utilisateur

**Description**:
```
📌 User Story: En tant qu'administrateur je souhaite consulter profil détaillé utilisateur

🎯 Sprint: Sprint 1
⏱️ Estimation: 3h
👤 Responsable: Ilef Yousfi

📝 Contexte:
Créer une page de détail utilisateur pour les admins.
Afficher toutes les informations + statistiques (dernière connexion, statut, etc.).

🔧 Technologies:
- Symfony Controller
- Twig templates
- Doctrine ORM
```

**Checklist**:
```
☐ Créer méthode show() dans UserController
☐ Créer vue détail utilisateur avec statistiques
```

**Fichiers créés/modifiés**:
```
- src/Controller/UserController.php
- templates/backoffice/users/show.html.twig
```

**Critères d'acceptation**:
```
✅ Page accessible via /backoffice/users/{id}
✅ Affiche: Nom, Email, Rôle, Date création, Dernière connexion
✅ Affiche statut suspension si applicable
✅ Boutons d'action: Modifier, Suspendre/Réactiver
✅ Design moderne avec cards Bootstrap
```

**Étiquettes**: Backend (🔵), Frontend (🟢), Sprint 1 (🔵 clair)

---

### 📌 CARTE 8: US-1.8 - Désactiver Compte Étudiant

**Titre**: US-1.8 - En tant qu'administrateur je souhaite désactiver un compte étudiant

**Description**:
```
📌 User Story: En tant qu'administrateur je souhaite désactiver un compte étudiant

🎯 Sprint: Sprint 1
⏱️ Estimation: 4h
👤 Responsable: Ilef Yousfi

📝 Contexte:
Implémenter suspension manuelle avec raison obligatoire.
Créer modal pour saisir la raison de suspension.
Permettre aussi la réactivation des comptes.

🔧 Technologies:
- Symfony Controller
- JavaScript (modal)
- Doctrine ORM
```

**Checklist**:
```
☐ Implémenter méthode suspend() avec raison
☐ Créer modal pour raison suspension
☐ Implémenter réactivation compte (unsuspend)
```

**Fichiers créés/modifiés**:
```
- src/Controller/UserController.php
- templates/backoffice/users/users.html.twig
- templates/backoffice/users/show.html.twig
```

**Critères d'acceptation**:
```
✅ Bouton "Suspendre" ouvre modal
✅ Modal demande raison de suspension (obligatoire)
✅ Suspension enregistre: isSuspended=true, suspendedAt, suspendedBy, suspensionReason
✅ Utilisateur suspendu ne peut plus se connecter
✅ Bouton "Réactiver" pour annuler suspension
✅ Message de confirmation après action
```

**Étiquettes**: Backend (🔵), Frontend (🟢), Sprint 1 (🔵 clair)

---

## 🎯 SPRINT 2 - Mot de Passe & Bundles

---

### 📌 CARTE 9: US-1.9 - Réinitialisation Mot de Passe

**Titre**: US-1.9 - En tant qu'utilisateur je souhaite réinitialiser mot de passe via email

**Description**:
```
📌 User Story: En tant qu'utilisateur je souhaite réinitialiser mot de passe via email

🎯 Sprint: Sprint 2
⏱️ Estimation: 10h
👤 Responsable: Ilef Yousfi

📝 Contexte:
Implémenter système complet de réinitialisation mot de passe.
Configurer Symfony Mailer avec Brevo (SMTP).
Générer token unique avec expiration (1h).
Envoyer email avec lien de réinitialisation.

🔧 Technologies:
- Symfony Mailer
- Brevo SMTP
- Token generation
- Twig email templates
```

**Checklist**:
```
☐ Créer SimpleResetPasswordController
☐ Configurer Symfony Mailer avec Brevo (SMTP)
☐ Créer TestBrevoCommand pour tester email
☐ Implémenter génération token unique avec expiration
☐ Créer template email de réinitialisation
☐ Créer page réinitialisation avec validation token
```

**Fichiers créés/modifiés**:
```
- src/Controller/SimpleResetPasswordController.php
- config/packages/mailer.yaml
- .env (MAILER_DSN)
- src/Command/TestBrevoCommand.php
- templates/emails/reset_password.html.twig
- templates/security/reset_password.html.twig
- templates/security/login.html.twig (lien "Mot de passe oublié")
```

**Critères d'acceptation**:
```
✅ Lien "Mot de passe oublié" visible sur page login
✅ Formulaire demande email utilisateur
✅ Email envoyé via Brevo avec lien réinitialisation
✅ Token expire après 1h
✅ Page réinitialisation valide le token
✅ Nouveau mot de passe enregistré avec hash
✅ Message de confirmation
✅ TestBrevoCommand fonctionne pour tester
```

**Étiquettes**: Backend (🔵), Frontend (🟢), Config (🟡), Sprint 2 (🟣 clair)

---

### 📌 CARTE 10: US-1.10 - Historique Modifications (Audit Bundle)

**Titre**: US-1.10 - En tant qu'administrateur je souhaite voir historique modifications étudiant

**Description**:
```
📌 User Story: En tant qu'administrateur je souhaite voir historique modifications étudiant

🎯 Sprint: Sprint 2
⏱️ Estimation: 12h
👤 Responsable: Ilef Yousfi

📝 Contexte:
Installer et configurer SimpleThings EntityAudit Bundle.
Auditer uniquement l'entité Etudiant (pas Admin).
Créer interface complète pour consulter l'historique des modifications.

🔧 Technologies:
- SimpleThings EntityAudit Bundle
- Doctrine ORM
- MySQL (tables: user_audit, revisions)
```

**Checklist**:
```
☐ Installer SimpleThings EntityAudit Bundle via Composer
☐ Configurer doctrine_audit.yaml (auditer uniquement Etudiant)
☐ Créer table user_audit avec colonnes camelCase
☐ Créer table revisions
☐ Créer AuditController (/backoffice/audit)
☐ Créer vue liste révisions (index.html.twig)
☐ Créer vue détail révision (revision_details.html.twig)
☐ Créer vue historique utilisateur (user_history.html.twig)
☐ Créer vue statistiques audit (stats.html.twig)
☐ Intégrer liens Audit dans sidebar backoffice
```

**Fichiers créés/modifiés**:
```
- composer.json (simplethings/entity-audit-bundle)
- config/packages/doctrine_audit.yaml
- config/bundles.php
- Base de données: user_audit, revisions
- src/Controller/AuditController.php
- templates/backoffice/audit/index.html.twig
- templates/backoffice/audit/revision_details.html.twig
- templates/backoffice/audit/user_history.html.twig
- templates/backoffice/audit/stats.html.twig
- templates/backoffice/base.html.twig (sidebar)
```

**Critères d'acceptation**:
```
✅ Bundle installé et configuré
✅ Tables user_audit et revisions créées
✅ Modifications Etudiant enregistrées automatiquement
✅ Page liste révisions affiche toutes les modifications
✅ Page détail révision montre avant/après
✅ Page historique utilisateur filtre par étudiant
✅ Page statistiques affiche métriques
✅ Lien "Audit" visible dans sidebar backoffice
✅ Accessible uniquement par ROLE_ADMIN
```

**Étiquettes**: Backend (🔵), Database (🟣), Frontend (🟢), Sprint 2 (🟣 clair)

---

### 📌 CARTE 11: US-1.11 - Suivi Activité Utilisateurs

**Titre**: US-1.11 - En tant qu'administrateur je souhaite suivre activité utilisateurs en temps réel

**Description**:
```
📌 User Story: En tant qu'administrateur je souhaite suivre activité utilisateurs en temps réel

🎯 Sprint: Sprint 2
⏱️ Estimation: 9.5h
👤 Responsable: Ilef Yousfi

📝 Contexte:
Créer un Bundle personnalisé UserActivityBundle.
Logger toutes les actions importantes (login, modification profil, suspension, etc.).
Créer interface pour consulter les activités.

🔧 Technologies:
- Symfony Bundle
- Doctrine ORM
- Service ActivityLogger
```

**Checklist**:
```
☐ Créer structure Bundle dans src/Bundle/UserActivityBundle/
☐ Créer entité UserActivity (userId, action, details, ipAddress, userAgent, createdAt)
☐ Créer migration pour table user_activity
☐ Créer ActivityLogger Service
☐ Intégrer logging dans UserController
☐ Créer ActivityController (/backoffice/user-activity)
☐ Créer vue liste activités (index.html.twig)
☐ Créer vue activités par utilisateur (user_activities.html.twig)
```

**Fichiers créés/modifiés**:
```
- src/Bundle/UserActivityBundle/UserActivityBundle.php
- src/Bundle/UserActivityBundle/Entity/UserActivity.php
- migrations/VersionXXXXXXXXXXXXXX.php
- src/Bundle/UserActivityBundle/Service/ActivityLogger.php
- src/Controller/UserController.php
- src/Bundle/UserActivityBundle/Controller/Admin/ActivityController.php
- templates/bundles/UserActivityBundle/admin/index.html.twig
- templates/bundles/UserActivityBundle/admin/user_activities.html.twig
- config/bundles.php
```

**Critères d'acceptation**:
```
✅ Table user_activity créée
✅ Actions loggées: login, logout, profile_update, user_suspended, user_unsuspended
✅ Enregistre userId, action, details (JSON), ipAddress, userAgent, createdAt
✅ Page liste activités affiche toutes les actions
✅ Filtres: Par utilisateur, par action, par date
✅ Pagination fonctionnelle
✅ Page activités par utilisateur filtre correctement
✅ Accessible uniquement par ROLE_ADMIN
```

**Étiquettes**: Backend (🔵), Database (🟣), Frontend (🟢), Sprint 2 (🟣 clair)

---

### 📌 CARTE 12: US-1.12 - Suspension Automatique Inactifs

**Titre**: US-1.12 - En tant qu'administrateur je souhaite suspendre automatiquement comptes inactifs après 90 jours

**Description**:
```
📌 User Story: En tant qu'administrateur je souhaite suspendre automatiquement comptes inactifs après 90 jours

🎯 Sprint: Sprint 2
⏱️ Estimation: 6.5h
👤 Responsable: Ilef Yousfi

📝 Contexte:
Créer une commande Symfony pour suspendre automatiquement les comptes inactifs.
Inactif = lastLoginAt > 90 jours.
Envoyer email de notification avant suspension.
Créer commande de simulation pour tests.

🔧 Technologies:
- Symfony Console Command
- Symfony Mailer
- Cron job (Windows Task Scheduler)
```

**Checklist**:
```
☐ Créer AutoSuspendInactiveUsersCommand
☐ Implémenter logique: suspendre si lastLoginAt > 90 jours
☐ Envoyer email notification avant suspension
☐ Créer SimulateInactivityCommand pour tests
☐ Documenter dans SUSPENSION_AUTOMATIQUE_GUIDE.md
```

**Fichiers créés/modifiés**:
```
- src/Command/AutoSuspendInactiveUsersCommand.php
- src/Command/SimulateInactivityCommand.php
- templates/emails/inactivity_warning.html.twig
- SUSPENSION_AUTOMATIQUE_GUIDE.md
```

**Critères d'acceptation**:
```
✅ Commande: php bin/console app:auto-suspend-inactive-users
✅ Détecte utilisateurs avec lastLoginAt > 90 jours
✅ Envoie email d'avertissement
✅ Suspend le compte (isSuspended=true, suspensionReason="Inactivité > 90 jours")
✅ SimulateInactivityCommand permet de tester
✅ Documentation complète créée
✅ Peut être planifié avec Windows Task Scheduler
```

**Étiquettes**: Backend (🔵), Config (🟡), Documentation (⚪), Sprint 2 (🟣 clair)

---

### 📌 CARTE 13: US-1.13 - Sidebar Fixe Backoffice

**Titre**: US-1.13 - En tant qu'administrateur je souhaite sidebar fixe dans backoffice

**Description**:
```
📌 User Story: En tant qu'administrateur je souhaite sidebar fixe dans backoffice

🎯 Sprint: Sprint 2
⏱️ Estimation: 3h
👤 Responsable: Ilef Yousfi

📝 Contexte:
Corriger le problème de sidebar qui scrolle avec le contenu.
Rendre la sidebar sticky (position: sticky ou fixed).
Corriger tous les templates backoffice pour uniformité.

🔧 Technologies:
- CSS (position: sticky)
- Twig templates
- Bootstrap
```

**Checklist**:
```
☐ Fixer sidebar backoffice (position sticky)
☐ Corriger tous les templates backoffice
```

**Fichiers créés/modifiés**:
```
- templates/backoffice/base.html.twig
- templates/backoffice/users/users.html.twig
- templates/backoffice/audit/index.html.twig
- templates/backoffice/communaute/index.html.twig
- templates/backoffice/post/index.html.twig
- templates/backoffice/commentaire/index.html.twig
- Et tous les autres templates backoffice
```

**Critères d'acceptation**:
```
✅ Sidebar reste visible lors du scroll
✅ Sidebar ne scroll pas avec le contenu
✅ Design cohérent sur toutes les pages backoffice
✅ Responsive (mobile/tablet/desktop)
✅ Pas de régression visuelle
```

**Étiquettes**: Frontend (🟢), Sprint 2 (🟣 clair)

---

## 🎯 SPRINT 3 - IA & Documentation

---

### 📌 CARTE 14: US-1.14 - Assistant IA Intelligent (Ollama)

**Titre**: US-1.14 - En tant qu'utilisateur je souhaite interagir avec assistant IA intelligent

**Description**:
```
📌 User Story: En tant qu'utilisateur je souhaite interagir avec assistant IA intelligent

🎯 Sprint: Sprint 3
⏱️ Estimation: 6h
👤 Responsable: Ilef Yousfi

📝 Contexte:
Installer Ollama localement et télécharger le modèle llama3.2:1b.
Créer OllamaService pour communiquer avec l'API Ollama.
Optimiser les paramètres (temperature, max_tokens) pour des réponses rapides et pertinentes.

🔧 Technologies:
- Ollama (local LLM)
- llama3.2:1b model
- HTTP Client Symfony
- API REST
```

**Checklist**:
```
☐ Installer et configurer Ollama localement
☐ Télécharger modèle llama3.2:1b
☐ Créer OllamaService pour communication API
☐ Configurer variables environnement (.env)
☐ Optimiser paramètres (temperature, max_tokens)
☐ Gérer erreurs et timeouts
```

**Fichiers créés/modifiés**:
```
- src/Service/OllamaService.php
- .env (OLLAMA_API_URL, OLLAMA_MODEL)
- .env.example
- config/services.yaml
```

**Critères d'acceptation**:
```
✅ Ollama installé et fonctionnel
✅ Modèle llama3.2:1b téléchargé
✅ OllamaService peut envoyer requêtes à Ollama
✅ Réponses reçues en JSON
✅ Gestion erreurs: timeout, connexion refusée, modèle non trouvé
✅ Paramètres optimisés: temperature=0.7, max_tokens=500
✅ Variables .env configurées
```

**Étiquettes**: Backend (🔵), IA (🟣 foncé), Config (🟡), Sprint 3 (🟢 clair)

---

### 📌 CARTE 15: US-1.15 - IA avec Contexte (RAG)

**Titre**: US-1.15 - En tant qu'utilisateur je souhaite que IA comprenne contexte questions (RAG)

**Description**:
```
📌 User Story: En tant qu'utilisateur je souhaite que IA comprenne contexte questions (RAG)

🎯 Sprint: Sprint 3
⏱️ Estimation: 11h
👤 Responsable: Ilef Yousfi

📝 Contexte:
Implémenter Retrieval-Augmented Generation (RAG).
Récupérer contexte pertinent depuis la base de données (cours, chapitres, ressources, exercices, utilisateur).
Ajouter ce contexte au prompt envoyé à Ollama pour des réponses plus précises.

🔧 Technologies:
- Doctrine ORM
- QueryBuilder
- Scoring de pertinence
- Token limiting
```

**Checklist**:
```
☐ Créer RAGService
☐ Implémenter récupération contexte cours (Chapitre, Ressource)
☐ Implémenter récupération contexte utilisateur
☐ Implémenter récupération contexte exercices/quiz
☐ Créer système scoring pertinence
☐ Optimiser requêtes Doctrine
☐ Limiter taille contexte (max 4000 tokens)
☐ Ajouter safety checks foreach loops
```

**Fichiers créés/modifiés**:
```
- src/Service/RAGService.php
- config/services.yaml
```

**Critères d'acceptation**:
```
✅ RAGService récupère contexte pertinent
✅ Contexte cours: titre, description, chapitres, ressources
✅ Contexte utilisateur: nom, email, niveau, cours inscrits
✅ Contexte exercices: questions, réponses, scores
✅ Scoring pertinence fonctionne (mots-clés matching)
✅ Contexte limité à 4000 tokens max
✅ Pas d'erreur foreach sur null
✅ Requêtes optimisées (pas de N+1)
```

**Étiquettes**: Backend (🔵), IA (🟣 foncé), Database (🟣), Sprint 3 (🟢 clair)

---

### 📌 CARTE 16: US-1.16 - IA Agent Actif (Exécution Actions)

**Titre**: US-1.16 - En tant qu'administrateur je souhaite que IA puisse exécuter actions sur base de données

**Description**:
```
📌 User Story: En tant qu'administrateur je souhaite que IA puisse exécuter actions sur base de données

🎯 Sprint: Sprint 3
⏱️ Estimation: 12h
👤 Responsable: Ilef Yousfi

📝 Contexte:
Créer ActionExecutorService pour permettre à l'IA d'exécuter des actions.
Détecter les actions dans la réponse de l'IA (format JSON).
Implémenter actions: créer cours, chapitre, ressource, exercice, modifier, etc.
Gérer permissions et sécurité (seulement ROLE_ADMIN).

🔧 Technologies:
- Doctrine ORM
- JSON parsing
- Security checks
- Entity Manager
```

**Checklist**:
```
☐ Créer ActionExecutorService
☐ Implémenter détection actions dans réponse IA
☐ Implémenter action: créer cours
☐ Implémenter action: créer chapitre
☐ Implémenter action: créer ressource
☐ Implémenter action: créer exercice
☐ Implémenter action: modifier cours/chapitre
☐ Gérer permissions et sécurité actions
☐ Corriger format JSON actions
```

**Fichiers créés/modifiés**:
```
- src/Service/ActionExecutorService.php
- config/services.yaml
```

**Critères d'acceptation**:
```
✅ IA peut créer cours avec titre et description
✅ IA peut créer chapitre lié à un cours
✅ IA peut créer ressource (PDF, vidéo, texte)
✅ IA peut créer exercice avec questions
✅ IA peut modifier cours/chapitre existant
✅ Actions détectées via format JSON: {"action": "create_course", "data": {...}}
✅ Seulement ROLE_ADMIN peut exécuter actions
✅ Validation des données avant exécution
✅ Gestion erreurs: entité non trouvée, données invalides
✅ Actions loggées dans UserActivity
```

**Étiquettes**: Backend (🔵), IA (🟣 foncé), Database (🟣), Sprint 3 (🟢 clair)

---

### 📌 CARTE 17: US-1.17 - Interface Chat Moderne

**Titre**: US-1.17 - En tant qu'utilisateur je souhaite interface chat moderne avec IA

**Description**:
```
📌 User Story: En tant qu'utilisateur je souhaite interface chat moderne avec IA

🎯 Sprint: Sprint 3
⏱️ Estimation: 15h
👤 Responsable: Ilef Yousfi

📝 Contexte:
Créer AIAssistantService pour orchestrer OllamaService, RAGService et ActionExecutorService.
Implémenter prompt système intelligent.
Créer widget chat moderne avec AJAX.
Intégrer dans frontoffice et backoffice.

🔧 Technologies:
- Symfony Service
- AJAX (JavaScript)
- Twig templates
- CSS animations
```

**Checklist**:
```
☐ Créer AIAssistantService (orchestration)
☐ Implémenter prompt système intelligent
☐ Intégrer RAGService pour contexte
☐ Intégrer ActionExecutorService
☐ Gérer historique conversation (session)
☐ Créer AIAssistantController
☐ Créer widget chat (chat_widget.html.twig)
☐ Implémenter AJAX pour requêtes asynchrones
☐ Ajouter indicateur IA en train d'écrire
☐ Ajouter bulle bienvenue
☐ Styliser interface chat moderne
☐ Intégrer chat widget dans frontoffice et backoffice
☐ Optimiser vitesse réponse
```

**Fichiers créés/modifiés**:
```
- src/Service/AIAssistantService.php
- src/Controller/AIAssistantController.php
- templates/ai_assistant/chat_widget.html.twig
- templates/ai_assistant/test.html.twig
- templates/frontoffice/base.html.twig
- templates/backoffice/base.html.twig
- config/routes.yaml
```

**Critères d'acceptation**:
```
✅ Widget chat visible en bas à droite
✅ Icône chat cliquable pour ouvrir/fermer
✅ Bulle bienvenue affichée au chargement
✅ Utilisateur peut taper message et envoyer
✅ Requête AJAX envoyée à /ai-assistant/chat
✅ Indicateur "IA en train d'écrire..." pendant traitement
✅ Réponse IA affichée dans le chat
✅ Historique conversation conservé (session)
✅ Design moderne: bulles messages, couleurs différentes user/IA
✅ Responsive (mobile/tablet/desktop)
✅ Intégré dans frontoffice ET backoffice
✅ Vitesse réponse < 3 secondes
✅ Gestion erreurs: timeout, Ollama offline
```

**Étiquettes**: Backend (🔵), Frontend (🟢), IA (🟣 foncé), Sprint 3 (🟢 clair)

---

### 📌 CARTE 18: US-1.18 - Sécurité Avancée

**Titre**: US-1.18 - En tant qu'administrateur je souhaite système sécurité avancé

**Description**:
```
📌 User Story: En tant qu'administrateur je souhaite système sécurité avancé

🎯 Sprint: Sprint 3
⏱️ Estimation: 3.5h
👤 Responsable: Ilef Yousfi

📝 Contexte:
Renforcer la sécurité de l'application.
Protection CSRF sur routes sensibles.
Validation stricte des inputs utilisateur.
Logger toutes les actions IA dans UserActivity.

🔧 Technologies:
- Symfony Security
- CSRF tokens
- Input validation
- Activity logging
```

**Checklist**:
```
☐ Renforcer protection CSRF sur routes sensibles
☐ Implémenter validation stricte inputs utilisateur
☐ Logger toutes actions IA dans UserActivity
```

**Fichiers créés/modifiés**:
```
- config/packages/security.yaml
- src/Entity/User.php (validation constraints)
- src/Service/AIAssistantService.php (logging)
```

**Critères d'acceptation**:
```
✅ CSRF tokens sur formulaires: login, reset password, edit profile
✅ Validation stricte: email format, longueur champs, caractères autorisés
✅ Toutes actions IA loggées: ai_chat_request, ai_action_executed
✅ Pas d'injection SQL possible
✅ Pas d'XSS possible (Twig escape automatique)
✅ Rate limiting sur routes sensibles (optionnel)
```

**Étiquettes**: Backend (🔵), Config (🟡), Sprint 3 (🟢 clair)

---

### 📌 CARTE 19: US-1.19 - Documentation Complète

**Titre**: US-1.19 - En tant que développeur je souhaite documentation complète

**Description**:
```
📌 User Story: En tant que développeur je souhaite documentation complète

🎯 Sprint: Sprint 3
⏱️ Estimation: 9.5h
👤 Responsable: Ilef Yousfi

📝 Contexte:
Créer documentation complète pour tous les modules développés.
Guides d'installation, d'utilisation, d'architecture.
Documentation technique pour maintenance future.

🔧 Technologies:
- Markdown
- Diagrammes (optionnel)
```

**Checklist**:
```
☐ Créer ASSISTANT_IA_ARCHITECTURE.md
☐ Créer GUIDE_INSTALLATION_IA.md
☐ Créer TESTEZ_IA_AGENT_ACTIF.md
☐ Créer PROMPT_SYSTEM_IA.md
☐ Créer README_ASSISTANT_IA.md
☐ Créer AUDIT_READY_TO_USE.md
☐ Créer USER_ACTIVITY_BUNDLE_COMPLETE.md
☐ Créer SUSPENSION_AUTOMATIQUE_GUIDE.md
☐ Créer 20+ autres fichiers documentation
```

**Fichiers créés/modifiés**:
```
- ASSISTANT_IA_ARCHITECTURE.md
- GUIDE_INSTALLATION_IA.md
- TESTEZ_IA_AGENT_ACTIF.md
- PROMPT_SYSTEM_IA.md
- README_ASSISTANT_IA.md
- AUDIT_READY_TO_USE.md
- USER_ACTIVITY_BUNDLE_COMPLETE.md
- SUSPENSION_AUTOMATIQUE_GUIDE.md
- SELECTIVE_MERGE_COMPLETE.md
- FIX_USER_AUDIT_TABLE_AFTER_MERGE.md
- MERGE_BAHA_INTO_ILEF_SUCCESS.md
- CURRENT_PROJECT_STATUS.md
- BUNDLE_INSTALLATION_COMPLETE_GUIDE.md
- PROFESSIONAL_AUDIT_BUNDLE_GUIDE.md
- IA_AGENT_ACTIF_COMPLET.md
- OPTIMISATION_IA_VITESSE.md
- AMELIORATION_IA_RESUME.md
- CORRECTIONS_IA_FINALE.md
- SOLUTION_IA_INTELLIGENTE.md
- ETAT_ASSISTANT_IA.md
- AMELIORATIONS_CHAT_IA.md
- IA_VRAIMENT_INTELLIGENTE.md
- ACTIVITY_BUNDLE_ENHANCEMENTS.md
- ACTIVITY_TRACKING_FIX.md
- BUNDLE_ACTIVITY_RESUME_AR.md
- RESULTAT_TEST_SUSPENSION_AUTO.md
- TEST_SUSPENSION_AUTO.md
- SUSPENSION_AUTO_RESUME.md
- CONFIGURATION_COMPLETE.md
- FINALISATION_ILEF.md
- GUIDE_COMPLET_GIT.md
- Et 10+ autres fichiers...
```

**Critères d'acceptation**:
```
✅ Documentation IA complète (architecture, installation, tests)
✅ Documentation Audit Bundle complète
✅ Documentation UserActivity Bundle complète
✅ Documentation Suspension automatique complète
✅ Documentation merges et corrections complète
✅ Tous les fichiers en français
✅ Format Markdown propre
✅ Exemples de code inclus
✅ Screenshots (optionnel)
✅ Facile à comprendre pour un nouveau développeur
```

**Étiquettes**: Documentation (⚪), Sprint 3 (🟢 clair)

---

### 📌 CARTE 20: US-1.20 - Corrections Après Merges

**Titre**: US-1.20 - En tant que développeur je souhaite corriger problèmes après merge

**Description**:
```
📌 User Story: En tant que développeur je souhaite corriger problèmes après merge

🎯 Sprint: Sprint 3
⏱️ Estimation: 11h
👤 Responsable: Ilef Yousfi

📝 Contexte:
Merger les branches Amira (Events & Participations) et Baha (VichUploader & Community).
Corriger tous les conflits et problèmes post-merge.
Fixer les problèmes de colonnes database, relations entités, récursion Twig, etc.

🔧 Technologies:
- Git merge
- Doctrine ORM
- Twig
- MySQL
```

**Checklist**:
```
☐ Merger branche Amira (Events & Participations)
☐ Fixer conflits migrations database
☐ Corriger colonnes snake_case vers camelCase
☐ Recréer table user_audit après merge
☐ Merger branche Baha (VichUploader + Community)
☐ Fixer relations bidirectionnelles Post/Commentaire
☐ Fixer affichage owner dans liste membres communauté
☐ Fixer récursion infinie comparaison objets Twig
```

**Fichiers créés/modifiés**:
```
- migrations/Version20260218210953.php
- migrations/Version20260220211749.php
- src/Entity/User.php (colonnes camelCase)
- Base de données: user, user_audit
- src/Entity/Participation.php
- src/Controller/FeedbackController.php
- config/bundles.php
- composer.json, composer.lock
- src/Entity/Post.php
- src/Entity/Commentaire.php
- templates/frontoffice/communaute/show.html.twig
- SELECTIVE_MERGE_COMPLETE.md
- FIX_USER_AUDIT_TABLE_AFTER_MERGE.md
- MERGE_BAHA_INTO_ILEF_SUCCESS.md
```

**Critères d'acceptation**:
```
✅ Branche Amira mergée avec succès
✅ Branche Baha mergée avec succès
✅ Conflits migrations résolus
✅ Colonnes database en camelCase
✅ Table user_audit recréée et fonctionnelle
✅ Relations Post/Commentaire bidirectionnelles
✅ Owner visible dans liste membres communauté
✅ Pas de récursion infinie Twig
✅ Toutes les fonctionnalités ilef préservées
✅ Toutes les fonctionnalités Amira/Baha intégrées
✅ Schema validation passe
✅ Application fonctionne sans erreur
```

**Étiquettes**: Backend (🔵), Database (🟣), Frontend (🟢), Bug (🔴), Sprint 3 (🟢 clair)

---

## 📊 Résumé des Cartes

### Par Sprint:

**Sprint 1 (8 cartes)**: US-1.1 à US-1.8
- CRUD complet
- Authentification
- Gestion utilisateurs (Admin)
- Suspension manuelle

**Sprint 2 (5 cartes)**: US-1.9 à US-1.13
- Réinitialisation mot de passe
- Audit Bundle
- UserActivity Bundle
- Suspension automatique
- Sidebar fixe

**Sprint 3 (7 cartes)**: US-1.14 à US-1.20
- Assistant IA (Ollama)
- RAG (contexte)
- Agent actif (actions)
- Interface chat
- Sécurité
- Documentation
- Corrections merges

### Statistiques:

- **Total User Stories**: 20
- **Total Tâches**: 116
- **Total Heures**: ~110h
- **Responsable**: Ilef Yousfi
- **Période**: Février 2026

---

## 🎯 Comment Utiliser ce Document

### 1. Créer les Cartes Trello:

Pour chaque User Story (US-1.1 à US-1.20):

1. **Créer une nouvelle carte** dans Trello
2. **Copier le Titre** de ce document
3. **Copier la Description** dans la description Trello
4. **Copier la Checklist** et créer une checklist Trello
5. **Ajouter les Étiquettes** recommandées
6. **Ajouter en commentaire** les fichiers créés/modifiés
7. **Ajouter en commentaire** les critères d'acceptation

### 2. Organiser les Listes:

- **Product Backlog**: Toutes les 20 cartes au départ
- **Sprint 1**: Déplacer US-1.1 à US-1.8
- **Sprint 2**: Déplacer US-1.9 à US-1.13
- **Sprint 3**: Déplacer US-1.14 à US-1.20
- **Terminé**: Toutes les cartes (travail déjà fait!)

### 3. Marquer comme Terminé:

Puisque tout le travail est déjà fait:
- Cocher toutes les tâches des checklists
- Déplacer toutes les cartes dans "Terminé"
- Ajouter dates de complétion

### 4. Personnaliser:

Tu peux ajouter:
- Screenshots
- Liens vers commits Git
- Notes personnelles
- Temps réel passé
- Difficultés rencontrées

---

## 📝 Template Vide pour Nouvelle Carte

Si tu veux créer une nouvelle carte:

```
**Titre**: US-X.X - [Nom User Story]

**Description**:
📌 User Story: [Description]
🎯 Sprint: Sprint X
⏱️ Estimation: Xh
👤 Responsable: Ilef Yousfi

📝 Contexte:
[Explication du contexte]

🔧 Technologies:
- [Tech 1]
- [Tech 2]

**Checklist**:
☐ Tâche 1
☐ Tâche 2

**Fichiers créés/modifiés**:
- fichier1.php
- fichier2.twig

**Critères d'acceptation**:
✅ Critère 1
✅ Critère 2

**Étiquettes**: [Étiquettes]
```

---

**Responsable**: Ilef Yousfi  
**Date**: Février 2026  
**Projet**: AutoLearn - Module Gestion Utilisateur  
**Total Cartes**: 20  
**Statut**: Toutes terminées ✅

---

## 🎉 Félicitations!

Tu as maintenant tout le contenu nécessaire pour créer un Trello board professionnel et complet!

Chaque carte contient:
- ✅ Description détaillée
- ✅ Checklist complète
- ✅ Fichiers réels
- ✅ Critères d'acceptation
- ✅ Étiquettes recommandées
- ✅ Estimation temps

**Prêt à copier-coller dans Trello!** 🚀
