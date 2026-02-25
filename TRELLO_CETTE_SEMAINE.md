# 🎴 Trello - Travail de Cette Semaine (Bundles + IA)

## 📋 Vue d'ensemble

**Sprints concernés**: Sprint 2 + Sprint 3  
**Total cartes**: 12 cartes  
**Total tâches**: 88 tâches  
**Estimation**: ~75h  

---

## 🎯 SPRINT 2 - BUNDLES & EMAIL (5 cartes)

---

### 📌 CARTE 1: US-1.9 - Réinitialisation Mot de Passe

**Titre**: US-1.9 - Réinitialisation mot de passe via email

```
📌 User Story: En tant qu'utilisateur je souhaite réinitialiser mot de passe via email
🎯 Sprint: Sprint 2 | ⏱️ 10h | 👤 Ilef Yousfi

Système complet reset password avec email Brevo.

Tâches:
☐ Créer SimpleResetPasswordController
☐ Configurer Symfony Mailer + Brevo
☐ Créer TestBrevoCommand
☐ Implémenter génération token
☐ Créer template email
☐ Créer page réinitialisation

Fichiers:
- SimpleResetPasswordController.php
- mailer.yaml
- TestBrevoCommand.php
- reset_password.html.twig

Étiquettes: Backend, Frontend, Config, Sprint 2
```

---

### 📌 CARTE 2: US-1.10 - Audit Bundle

**Titre**: US-1.10 - Historique modifications étudiant (Audit Bundle)

```
📌 User Story: En tant qu'administrateur je souhaite voir historique modifications
🎯 Sprint: Sprint 2 | ⏱️ 12h | 👤 Ilef Yousfi

Installer EntityAudit Bundle et créer interface consultation.

Tâches:
☐ Installer EntityAudit Bundle
☐ Configurer doctrine_audit.yaml
☐ Créer tables user_audit, revisions
☐ Créer AuditController
☐ Créer vue liste révisions
☐ Créer vue détail révision
☐ Créer vue historique utilisateur
☐ Créer vue statistiques
☐ Intégrer dans sidebar
☐ Tester avec modifications Etudiant

Fichiers:
- AuditController.php
- doctrine_audit.yaml
- templates/backoffice/audit/ (4 vues)
- Tables: user_audit, revisions

Étiquettes: Backend, Database, Frontend, Sprint 2
```

---

### 📌 CARTE 3: US-1.11 - UserActivity Bundle

**Titre**: US-1.11 - Suivi activité utilisateurs en temps réel

```
📌 User Story: En tant qu'administrateur je souhaite suivre activité en temps réel
🎯 Sprint: Sprint 2 | ⏱️ 9.5h | 👤 Ilef Yousfi

Créer UserActivityBundle pour logger toutes les actions.

Tâches:
☐ Créer structure Bundle
☐ Créer entité UserActivity
☐ Créer migration table user_activity
☐ Créer ActivityLogger Service
☐ Intégrer logging dans UserController
☐ Créer ActivityController
☐ Créer vue liste activités
☐ Créer vue activités par utilisateur

Fichiers:
- src/Bundle/UserActivityBundle/
- UserActivity.php
- ActivityLogger.php
- ActivityController.php
- templates/bundles/UserActivityBundle/admin/ (2 vues)

Étiquettes: Backend, Database, Frontend, Sprint 2
```

---

### 📌 CARTE 4: US-1.12 - Suspension Automatique

**Titre**: US-1.12 - Suspension automatique après 90 jours inactivité

```
📌 User Story: En tant qu'administrateur je souhaite suspension auto après 90 jours
🎯 Sprint: Sprint 2 | ⏱️ 6.5h | 👤 Ilef Yousfi

Commande automatique suspension comptes inactifs.

Tâches:
☐ Créer AutoSuspendInactiveUsersCommand
☐ Implémenter logique 90 jours (lastLoginAt)
☐ Envoyer email notification
☐ Créer SimulateInactivityCommand pour tests
☐ Documenter dans SUSPENSION_AUTOMATIQUE_GUIDE.md
☐ Tester avec utilisateurs simulés

Fichiers:
- AutoSuspendInactiveUsersCommand.php
- SimulateInactivityCommand.php
- inactivity_warning.html.twig
- SUSPENSION_AUTOMATIQUE_GUIDE.md

Étiquettes: Backend, Config, Documentation, Sprint 2
```

---

### 📌 CARTE 5: US-1.13 - Sidebar Fixe

**Titre**: US-1.13 - Sidebar fixe dans backoffice

```
📌 User Story: En tant qu'administrateur je souhaite sidebar fixe
🎯 Sprint: Sprint 2 | ⏱️ 3h | 👤 Ilef Yousfi

Corriger sidebar pour qu'elle reste visible au scroll.

Tâches:
☐ Fixer sidebar (position sticky)
☐ Corriger tous templates backoffice

Fichiers:
- templates/backoffice/base.html.twig
- Tous templates backoffice/

Étiquettes: Frontend, Sprint 2
```

---

## 🎯 SPRINT 3 - IA & DOCUMENTATION (7 cartes)

---

### 📌 CARTE 6: US-1.14 - Assistant IA (Ollama)

**Titre**: US-1.14 - Assistant IA intelligent avec Ollama

```
📌 User Story: En tant qu'utilisateur je souhaite interagir avec IA
🎯 Sprint: Sprint 3 | ⏱️ 6h | 👤 Ilef Yousfi

Installer Ollama et créer service communication.

Tâches:
☐ Installer Ollama localement
☐ Télécharger modèle llama3.2:1b
☐ Créer OllamaService
☐ Configurer .env (OLLAMA_API_URL, OLLAMA_MODEL)
☐ Optimiser paramètres (temperature=0.7, max_tokens=500)
☐ Gérer erreurs et timeouts

Fichiers:
- OllamaService.php
- .env
- .env.example

Étiquettes: Backend, IA, Config, Sprint 3
```

---

### 📌 CARTE 7: US-1.15 - IA avec Contexte (RAG)

**Titre**: US-1.15 - IA comprend contexte avec RAG

```
📌 User Story: En tant qu'utilisateur je souhaite IA comprenne contexte
🎯 Sprint: Sprint 3 | ⏱️ 11h | 👤 Ilef Yousfi

Implémenter RAG pour contexte intelligent.

Tâches:
☐ Créer RAGService
☐ Récupération contexte cours (Chapitre, Ressource)
☐ Récupération contexte utilisateur
☐ Récupération contexte exercices/quiz
☐ Système scoring pertinence
☐ Optimiser requêtes Doctrine
☐ Limiter tokens (4000 max)
☐ Ajouter safety checks foreach

Fichiers:
- RAGService.php

Étiquettes: Backend, IA, Database, Sprint 3
```

---

### 📌 CARTE 8: US-1.16 - IA Agent Actif

**Titre**: US-1.16 - IA peut exécuter actions sur base de données

```
📌 User Story: En tant qu'administrateur je souhaite IA exécute actions
🎯 Sprint: Sprint 3 | ⏱️ 12h | 👤 Ilef Yousfi

IA peut créer/modifier cours, chapitres, ressources.

Tâches:
☐ Créer ActionExecutorService
☐ Détection actions dans réponse IA (JSON)
☐ Action: créer cours
☐ Action: créer chapitre
☐ Action: créer ressource
☐ Action: créer exercice
☐ Action: modifier cours/chapitre
☐ Gérer permissions (ROLE_ADMIN only)
☐ Corriger format JSON actions

Fichiers:
- ActionExecutorService.php

Étiquettes: Backend, IA, Database, Sprint 3
```

---

### 📌 CARTE 9: US-1.17 - Interface Chat Moderne

**Titre**: US-1.17 - Interface chat moderne avec IA

```
📌 User Story: En tant qu'utilisateur je souhaite interface chat moderne
🎯 Sprint: Sprint 3 | ⏱️ 15h | 👤 Ilef Yousfi

Widget chat complet avec AJAX et design moderne.

Tâches:
☐ Créer AIAssistantService (orchestration)
☐ Implémenter prompt système intelligent
☐ Intégrer RAGService pour contexte
☐ Intégrer ActionExecutorService
☐ Gérer historique conversation (session)
☐ Créer AIAssistantController
☐ Créer widget chat (chat_widget.html.twig)
☐ Implémenter AJAX asynchrone
☐ Indicateur "IA en train d'écrire..."
☐ Bulle bienvenue
☐ Styliser interface moderne
☐ Intégrer frontoffice + backoffice
☐ Optimiser vitesse réponse

Fichiers:
- AIAssistantService.php
- AIAssistantController.php
- chat_widget.html.twig
- test.html.twig

Étiquettes: Backend, Frontend, IA, Sprint 3
```

---

### 📌 CARTE 10: US-1.18 - Sécurité Avancée

**Titre**: US-1.18 - Système sécurité avancé

```
📌 User Story: En tant qu'administrateur je souhaite sécurité avancée
🎯 Sprint: Sprint 3 | ⏱️ 3.5h | 👤 Ilef Yousfi

Renforcer sécurité application.

Tâches:
☐ Protection CSRF routes sensibles
☐ Validation stricte inputs utilisateur
☐ Logger toutes actions IA dans UserActivity

Fichiers:
- security.yaml
- User.php (validation)
- AIAssistantService.php (logging)

Étiquettes: Backend, Config, Sprint 3
```

---

### 📌 CARTE 11: US-1.19 - Documentation Complète

**Titre**: US-1.19 - Documentation complète tous modules

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
☐ 20+ autres fichiers documentation

Fichiers:
- 30+ fichiers .md

Étiquettes: Documentation, Sprint 3
```

---

### 📌 CARTE 12: US-1.20 - Corrections Merges

**Titre**: US-1.20 - Corrections problèmes après merges

```
📌 User Story: En tant que développeur je souhaite corriger problèmes merges
🎯 Sprint: Sprint 3 | ⏱️ 11h | 👤 Ilef Yousfi

Merger Amira et Baha, corriger tous conflits.

Tâches:
☐ Merger branche Amira (Events & Participations)
☐ Fixer conflits migrations
☐ Corriger colonnes snake_case → camelCase
☐ Recréer table user_audit
☐ Merger branche Baha (VichUploader + Community)
☐ Fixer relations Post/Commentaire
☐ Fixer affichage owner dans communauté
☐ Fixer récursion infinie Twig

Fichiers:
- migrations/
- User.php
- Post.php, Commentaire.php
- show.html.twig
- SELECTIVE_MERGE_COMPLETE.md
- MERGE_BAHA_INTO_ILEF_SUCCESS.md

Étiquettes: Backend, Database, Frontend, Bug, Sprint 3
```

---

## 📊 Résumé

### Sprint 2 - Bundles & Email
| Carte | Tâches | Heures |
|-------|--------|--------|
| US-1.9 - Reset Password | 6 | 10h |
| US-1.10 - Audit Bundle | 10 | 12h |
| US-1.11 - Activity Bundle | 8 | 9.5h |
| US-1.12 - Suspension Auto | 6 | 6.5h |
| US-1.13 - Sidebar | 2 | 3h |
| **Total Sprint 2** | **32** | **41h** |

### Sprint 3 - IA & Documentation
| Carte | Tâches | Heures |
|-------|--------|--------|
| US-1.14 - Ollama | 6 | 6h |
| US-1.15 - RAG | 8 | 11h |
| US-1.16 - Agent Actif | 9 | 12h |
| US-1.17 - Chat Interface | 13 | 15h |
| US-1.18 - Sécurité | 3 | 3.5h |
| US-1.19 - Documentation | 9 | 9.5h |
| US-1.20 - Corrections | 8 | 11h |
| **Total Sprint 3** | **56** | **68h** |

### Total Cette Semaine
- **Cartes**: 12
- **Tâches**: 88
- **Heures**: ~109h (réparties sur 2 sprints)

---

## 🎯 Organisation Trello

### Listes à Créer:
1. **📋 Sprint 2 - Bundles**
2. **🤖 Sprint 3 - IA**
3. **✅ Terminé**

### Étiquettes à Créer:
1. **Backend** (Bleu)
2. **Frontend** (Vert)
3. **Database** (Violet)
4. **Config** (Jaune)
5. **Documentation** (Blanc)
6. **IA** (Violet foncé)
7. **Bug** (Rouge)
8. **Sprint 2** (Violet clair)
9. **Sprint 3** (Vert clair)

---

## 🚀 Quick Start

### Étape 1: Créer Board
- Nom: "AutoLearn - Bundles & IA"

### Étape 2: Créer 3 Listes
- Sprint 2 - Bundles
- Sprint 3 - IA
- Terminé

### Étape 3: Créer 9 Étiquettes
(Voir liste ci-dessus)

### Étape 4: Créer 12 Cartes
- Copier-coller le contenu de chaque carte ci-dessus
- Ajouter les étiquettes
- Créer les checklists

### Étape 5: Organiser
- 5 cartes dans "Sprint 2"
- 7 cartes dans "Sprint 3"
- Déplacer vers "Terminé" au fur et à mesure

---

## 💡 Conseil

Puisque tout le travail est déjà fait, tu peux:
1. Créer toutes les cartes
2. Cocher toutes les checklists
3. Déplacer toutes les cartes dans "Terminé"
4. Utiliser pour présentation/documentation

---

**Responsable**: Ilef Yousfi  
**Période**: Cette semaine  
**Focus**: Bundles (Audit, Activity, Suspension) + IA (Ollama, RAG, Agent)  
**Statut**: Travail terminé ✅

**Prêt à copier dans Trello!** 🚀
