# 📋 Trello - Templates Copier-Coller Rapide

## 🎯 Instructions

Pour chaque carte ci-dessous:
1. Copier le bloc complet
2. Créer nouvelle carte dans Trello
3. Coller dans la description
4. Créer checklist séparée avec les tâches
5. Ajouter les étiquettes indiquées

---

## SPRINT 1

### US-1.1 - Inscription Utilisateur
```
📌 User Story: En tant qu'utilisateur je souhaite m'inscrire
🎯 Sprint: Sprint 1 | ⏱️ 6h | 👤 Ilef Yousfi

Créer le système d'entités utilisateur avec Single Table Inheritance (STI).

Tâches:
☐ Créer entité User (abstract) avec STI
☐ Créer entité Etudiant (extends User)
☐ Créer entité Admin (extends User)
☐ Ajouter champs suspension
☐ Ajouter champ lastLoginAt
☐ Créer migrations camelCase
☐ Configurer annotations Doctrine

Fichiers: User.php, Etudiant.php, Admin.php, migrations/

Étiquettes: Backend, Database, Sprint 1
```

### US-1.2 - Connexion Utilisateur
```
📌 User Story: En tant qu'utilisateur je souhaite me connecter
🎯 Sprint: Sprint 1 | ⏱️ 8.5h | 👤 Ilef Yousfi

Implémenter authentification complète avec redirection selon rôle.

Tâches:
☐ Configurer Symfony Security
☐ Créer SecurityController
☐ Implémenter authentification email/password
☐ Redirection selon rôle
☐ Mettre à jour lastLoginAt
☐ Créer vue login moderne
☐ Bloquer comptes suspendus

Fichiers: SecurityController.php, security.yaml, login.html.twig

Étiquettes: Backend, Frontend, Sprint 1
```

### US-1.3 - Déconnexion Utilisateur
```
📌 User Story: En tant qu'utilisateur je souhaite me déconnecter
🎯 Sprint: Sprint 1 | ⏱️ 1h | 👤 Ilef Yousfi

Configurer déconnexion et lien dans menu.

Tâches:
☐ Configurer route déconnexion
☐ Créer lien déconnexion avec icône

Fichiers: security.yaml, base.html.twig

Étiquettes: Backend, Frontend, Config, Sprint 1
```

### US-1.4 - Consulter Profil
```
📌 User Story: En tant qu'utilisateur je souhaite consulter mon profil
🎯 Sprint: Sprint 1 | ⏱️ 3h | 👤 Ilef Yousfi

Créer page profil utilisateur avec design moderne.

Tâches:
☐ Créer FrontofficeController
☐ Créer vue profil moderne

Fichiers: FrontofficeController.php, profile.html.twig

Étiquettes: Backend, Frontend, Sprint 1
```

### US-1.5 - Modifier Informations
```
📌 User Story: En tant qu'utilisateur je souhaite modifier mes informations
🎯 Sprint: Sprint 1 | ⏱️ 2.5h | 👤 Ilef Yousfi

Permettre modification nom, prénom, email avec formulaire.

Tâches:
☐ Implémenter modification profil
☐ Créer formulaire édition

Fichiers: FrontofficeController.php, UserProfileType.php, edit_profile.html.twig

Étiquettes: Backend, Frontend, Sprint 1
```

### US-1.6 - Rechercher Étudiant (Admin)
```
📌 User Story: En tant qu'administrateur je souhaite rechercher un étudiant
🎯 Sprint: Sprint 1 | ⏱️ 8h | 👤 Ilef Yousfi

Créer interface recherche avec pagination et filtres.

Tâches:
☐ Créer UserController backoffice
☐ Implémenter recherche LIKE
☐ Ajouter pagination et filtres
☐ Créer vue tableau moderne

Fichiers: UserController.php, users.html.twig

Étiquettes: Backend, Frontend, Sprint 1
```

### US-1.7 - Profil Détaillé (Admin)
```
📌 User Story: En tant qu'administrateur je souhaite consulter profil détaillé
🎯 Sprint: Sprint 1 | ⏱️ 3h | 👤 Ilef Yousfi

Page détail utilisateur avec statistiques pour admin.

Tâches:
☐ Créer méthode show()
☐ Créer vue détail avec stats

Fichiers: UserController.php, show.html.twig

Étiquettes: Backend, Frontend, Sprint 1
```

### US-1.8 - Désactiver Compte
```
📌 User Story: En tant qu'administrateur je souhaite désactiver un compte
🎯 Sprint: Sprint 1 | ⏱️ 4h | 👤 Ilef Yousfi

Suspension manuelle avec raison et réactivation.

Tâches:
☐ Implémenter méthode suspend()
☐ Créer modal raison suspension
☐ Implémenter réactivation

Fichiers: UserController.php, users.html.twig

Étiquettes: Backend, Frontend, Sprint 1
```

---

## SPRINT 2

### US-1.9 - Réinitialisation Mot de Passe
```
📌 User Story: En tant qu'utilisateur je souhaite réinitialiser mot de passe
🎯 Sprint: Sprint 2 | ⏱️ 10h | 👤 Ilef Yousfi

Système complet reset password avec email Brevo.

Tâches:
☐ Créer SimpleResetPasswordController
☐ Configurer Symfony Mailer + Brevo
☐ Créer TestBrevoCommand
☐ Implémenter génération token
☐ Créer template email
☐ Créer page réinitialisation

Fichiers: SimpleResetPasswordController.php, mailer.yaml, TestBrevoCommand.php

Étiquettes: Backend, Frontend, Config, Sprint 2
```

### US-1.10 - Historique Modifications (Audit)
```
📌 User Story: En tant qu'administrateur je souhaite voir historique modifications
🎯 Sprint: Sprint 2 | ⏱️ 12h | 👤 Ilef Yousfi

Installer EntityAudit Bundle et créer interface consultation.

Tâches:
☐ Installer EntityAudit Bundle
☐ Configurer doctrine_audit.yaml
☐ Créer tables user_audit, revisions
☐ Créer AuditController
☐ Créer 4 vues audit
☐ Intégrer dans sidebar

Fichiers: AuditController.php, doctrine_audit.yaml, templates/audit/

Étiquettes: Backend, Database, Frontend, Sprint 2
```

### US-1.11 - Suivi Activité Utilisateurs
```
📌 User Story: En tant qu'administrateur je souhaite suivre activité en temps réel
🎯 Sprint: Sprint 2 | ⏱️ 9.5h | 👤 Ilef Yousfi

Créer UserActivityBundle pour logger toutes les actions.

Tâches:
☐ Créer structure Bundle
☐ Créer entité UserActivity
☐ Créer migration
☐ Créer ActivityLogger Service
☐ Intégrer logging
☐ Créer ActivityController
☐ Créer 2 vues activités

Fichiers: UserActivityBundle/, ActivityLogger.php, ActivityController.php

Étiquettes: Backend, Database, Frontend, Sprint 2
```

### US-1.12 - Suspension Automatique
```
📌 User Story: En tant qu'administrateur je souhaite suspension auto après 90 jours
🎯 Sprint: Sprint 2 | ⏱️ 6.5h | 👤 Ilef Yousfi

Commande automatique suspension comptes inactifs.

Tâches:
☐ Créer AutoSuspendInactiveUsersCommand
☐ Implémenter logique 90 jours
☐ Envoyer email notification
☐ Créer SimulateInactivityCommand
☐ Documenter

Fichiers: AutoSuspendInactiveUsersCommand.php, SimulateInactivityCommand.php

Étiquettes: Backend, Config, Documentation, Sprint 2
```

### US-1.13 - Sidebar Fixe
```
📌 User Story: En tant qu'administrateur je souhaite sidebar fixe
🎯 Sprint: Sprint 2 | ⏱️ 3h | 👤 Ilef Yousfi

Corriger sidebar pour qu'elle reste visible au scroll.

Tâches:
☐ Fixer sidebar (position sticky)
☐ Corriger tous templates backoffice

Fichiers: base.html.twig, tous templates backoffice/

Étiquettes: Frontend, Sprint 2
```

---

## SPRINT 3

### US-1.14 - Assistant IA (Ollama)
```
📌 User Story: En tant qu'utilisateur je souhaite interagir avec IA
🎯 Sprint: Sprint 3 | ⏱️ 6h | 👤 Ilef Yousfi

Installer Ollama et créer service communication.

Tâches:
☐ Installer Ollama
☐ Télécharger llama3.2:1b
☐ Créer OllamaService
☐ Configurer .env
☐ Optimiser paramètres
☐ Gérer erreurs

Fichiers: OllamaService.php, .env

Étiquettes: Backend, IA, Config, Sprint 3
```

### US-1.15 - IA avec Contexte (RAG)
```
📌 User Story: En tant qu'utilisateur je souhaite IA comprenne contexte
🎯 Sprint: Sprint 3 | ⏱️ 11h | 👤 Ilef Yousfi

Implémenter RAG pour contexte intelligent.

Tâches:
☐ Créer RAGService
☐ Récupération contexte cours
☐ Récupération contexte utilisateur
☐ Récupération contexte exercices
☐ Système scoring pertinence
☐ Optimiser requêtes
☐ Limiter tokens (4000 max)
☐ Safety checks

Fichiers: RAGService.php

Étiquettes: Backend, IA, Database, Sprint 3
```

### US-1.16 - IA Agent Actif
```
📌 User Story: En tant qu'administrateur je souhaite IA exécute actions
🎯 Sprint: Sprint 3 | ⏱️ 12h | 👤 Ilef Yousfi

IA peut créer/modifier cours, chapitres, ressources.

Tâches:
☐ Créer ActionExecutorService
☐ Détection actions JSON
☐ Action: créer cours
☐ Action: créer chapitre
☐ Action: créer ressource
☐ Action: créer exercice
☐ Action: modifier
☐ Gérer permissions
☐ Corriger format JSON

Fichiers: ActionExecutorService.php

Étiquettes: Backend, IA, Database, Sprint 3
```

### US-1.17 - Interface Chat Moderne
```
📌 User Story: En tant qu'utilisateur je souhaite interface chat moderne
🎯 Sprint: Sprint 3 | ⏱️ 15h | 👤 Ilef Yousfi

Widget chat complet avec AJAX et design moderne.

Tâches:
☐ Créer AIAssistantService
☐ Prompt système intelligent
☐ Intégrer RAGService
☐ Intégrer ActionExecutorService
☐ Gérer historique
☐ Créer AIAssistantController
☐ Créer widget chat
☐ AJAX asynchrone
☐ Indicateur "en train d'écrire"
☐ Bulle bienvenue
☐ Styliser interface
☐ Intégrer frontoffice + backoffice
☐ Optimiser vitesse

Fichiers: AIAssistantService.php, AIAssistantController.php, chat_widget.html.twig

Étiquettes: Backend, Frontend, IA, Sprint 3
```

### US-1.18 - Sécurité Avancée
```
📌 User Story: En tant qu'administrateur je souhaite sécurité avancée
🎯 Sprint: Sprint 3 | ⏱️ 3.5h | 👤 Ilef Yousfi

Renforcer sécurité application.

Tâches:
☐ Protection CSRF routes sensibles
☐ Validation stricte inputs
☐ Logger actions IA

Fichiers: security.yaml, User.php, AIAssistantService.php

Étiquettes: Backend, Config, Sprint 3
```

### US-1.19 - Documentation Complète
```
📌 User Story: En tant que développeur je souhaite documentation complète
🎯 Sprint: Sprint 3 | ⏱️ 9.5h | 👤 Ilef Yousfi

Créer documentation complète tous modules.

Tâches:
☐ ASSISTANT_IA_ARCHITECTURE.md
☐ GUIDE_INSTALLATION_IA.md
☐ TESTEZ_IA_AGENT_ACTIF.md
☐ PROMPT_SYSTEM_IA.md
☐ README_ASSISTANT_IA.md
☐ AUDIT_READY_TO_USE.md
☐ USER_ACTIVITY_BUNDLE_COMPLETE.md
☐ SUSPENSION_AUTOMATIQUE_GUIDE.md
☐ 20+ autres fichiers

Fichiers: 30+ fichiers .md

Étiquettes: Documentation, Sprint 3
```

### US-1.20 - Corrections Merges
```
📌 User Story: En tant que développeur je souhaite corriger problèmes merges
🎯 Sprint: Sprint 3 | ⏱️ 11h | 👤 Ilef Yousfi

Merger Amira et Baha, corriger tous conflits.

Tâches:
☐ Merger branche Amira
☐ Fixer conflits migrations
☐ Corriger colonnes camelCase
☐ Recréer user_audit
☐ Merger branche Baha
☐ Fixer relations Post/Commentaire
☐ Fixer affichage owner
☐ Fixer récursion Twig

Fichiers: migrations/, User.php, Post.php, Commentaire.php, show.html.twig

Étiquettes: Backend, Database, Frontend, Bug, Sprint 3
```

---

## 📊 Résumé

**Total**: 20 cartes
- Sprint 1: 8 cartes (CRUD & Auth)
- Sprint 2: 5 cartes (Bundles & Email)
- Sprint 3: 7 cartes (IA & Docs)

**Estimation totale**: ~110h

**Responsable**: Ilef Yousfi

**Statut**: Toutes terminées ✅

---

**Astuce**: Copie chaque bloc dans une nouvelle carte Trello, puis crée une checklist séparée avec les tâches!
