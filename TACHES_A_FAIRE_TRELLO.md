# ✅ Tâches à Faire dans Trello - Guide Étape par Étape

## 🎯 Objectif
Créer un board Trello pour documenter ton travail Bundles + IA de cette semaine.

---

## 📋 ÉTAPE 1: Créer le Board (2 min)

1. Va sur **https://trello.com**
2. Clique sur **"Créer un tableau"**
3. Nom du board: **"AutoLearn - Bundles & IA"**
4. Clique sur **"Créer"**

✅ Board créé!

---

## 📋 ÉTAPE 2: Créer les Listes (2 min)

Clique sur **"Ajouter une liste"** et crée ces 3 listes:

1. **📋 Sprint 2 - Bundles**
2. **🤖 Sprint 3 - IA**
3. **✅ Terminé**

✅ Listes créées!

---

## 🏷️ ÉTAPE 3: Créer les Étiquettes (3 min)

1. Clique sur **"Menu"** (en haut à droite)
2. Clique sur **"Étiquettes"**
3. Crée ces 9 étiquettes:

| Nom | Couleur |
|-----|---------|
| Backend | Bleu |
| Frontend | Vert |
| Database | Violet |
| Config | Jaune |
| Documentation | Blanc |
| IA | Violet foncé |
| Bug | Rouge |
| Sprint 2 | Bleu clair |
| Sprint 3 | Vert clair |

✅ Étiquettes créées!

---

## 🎴 ÉTAPE 4: Créer les Cartes Sprint 2 (15 min)

### Carte 1: Reset Password

1. Dans liste **"Sprint 2 - Bundles"**, clique **"Ajouter une carte"**
2. Titre: **US-1.9 - Réinitialisation mot de passe**
3. Ouvre la carte, clique **"Description"**
4. Copie-colle:
```
📌 Système complet reset password avec email Brevo
🎯 Sprint 2 | ⏱️ 10h | 👤 Ilef Yousfi

Fichiers:
- SimpleResetPasswordController.php
- mailer.yaml
- TestBrevoCommand.php
```
5. Clique **"Checklist"**, nom: "Tâches"
6. Ajoute ces items:
   - Créer SimpleResetPasswordController
   - Configurer Symfony Mailer + Brevo
   - Créer TestBrevoCommand
   - Implémenter génération token
   - Créer template email
   - Créer page réinitialisation
7. Clique **"Étiquettes"**, ajoute: Backend, Frontend, Config, Sprint 2
8. **Coche toutes les tâches** (travail déjà fait!)

---

### Carte 2: Audit Bundle

1. Nouvelle carte dans **"Sprint 2 - Bundles"**
2. Titre: **US-1.10 - Audit Bundle**
3. Description:
```
📌 Historique modifications étudiant avec EntityAudit Bundle
🎯 Sprint 2 | ⏱️ 12h | 👤 Ilef Yousfi

Fichiers:
- AuditController.php
- doctrine_audit.yaml
- templates/backoffice/audit/ (4 vues)
```
4. Checklist "Tâches":
   - Installer EntityAudit Bundle
   - Configurer doctrine_audit.yaml
   - Créer tables user_audit, revisions
   - Créer AuditController
   - Créer 4 vues audit
   - Intégrer dans sidebar
5. Étiquettes: Backend, Database, Frontend, Sprint 2
6. **Coche toutes les tâches**

---

### Carte 3: UserActivity Bundle

1. Nouvelle carte dans **"Sprint 2 - Bundles"**
2. Titre: **US-1.11 - UserActivity Bundle**
3. Description:
```
📌 Suivi activité utilisateurs en temps réel
🎯 Sprint 2 | ⏱️ 9.5h | 👤 Ilef Yousfi

Fichiers:
- src/Bundle/UserActivityBundle/
- ActivityLogger.php
- ActivityController.php
```
4. Checklist "Tâches":
   - Créer structure Bundle
   - Créer entité UserActivity
   - Créer migration
   - Créer ActivityLogger Service
   - Intégrer logging
   - Créer ActivityController
   - Créer 2 vues activités
5. Étiquettes: Backend, Database, Frontend, Sprint 2
6. **Coche toutes les tâches**

---

### Carte 4: Suspension Automatique

1. Nouvelle carte dans **"Sprint 2 - Bundles"**
2. Titre: **US-1.12 - Suspension automatique**
3. Description:
```
📌 Suspension auto après 90 jours inactivité
🎯 Sprint 2 | ⏱️ 6.5h | 👤 Ilef Yousfi

Fichiers:
- AutoSuspendInactiveUsersCommand.php
- SimulateInactivityCommand.php
```
4. Checklist "Tâches":
   - Créer AutoSuspendInactiveUsersCommand
   - Implémenter logique 90 jours
   - Envoyer email notification
   - Créer SimulateInactivityCommand
   - Documenter
5. Étiquettes: Backend, Config, Documentation, Sprint 2
6. **Coche toutes les tâches**

---

### Carte 5: Sidebar Fixe

1. Nouvelle carte dans **"Sprint 2 - Bundles"**
2. Titre: **US-1.13 - Sidebar fixe**
3. Description:
```
📌 Sidebar reste visible au scroll
🎯 Sprint 2 | ⏱️ 3h | 👤 Ilef Yousfi

Fichiers:
- templates/backoffice/base.html.twig
```
4. Checklist "Tâches":
   - Fixer sidebar (position sticky)
   - Corriger tous templates backoffice
5. Étiquettes: Frontend, Sprint 2
6. **Coche toutes les tâches**

✅ Sprint 2 terminé! (5 cartes)

---

## 🤖 ÉTAPE 5: Créer les Cartes Sprint 3 (20 min)

### Carte 6: Assistant IA (Ollama)

1. Dans liste **"Sprint 3 - IA"**, nouvelle carte
2. Titre: **US-1.14 - Assistant IA (Ollama)**
3. Description:
```
📌 Assistant IA intelligent avec Ollama
🎯 Sprint 3 | ⏱️ 6h | 👤 Ilef Yousfi

Fichiers:
- OllamaService.php
- .env
```
4. Checklist "Tâches":
   - Installer Ollama localement
   - Télécharger modèle llama3.2:1b
   - Créer OllamaService
   - Configurer .env
   - Optimiser paramètres
   - Gérer erreurs
5. Étiquettes: Backend, IA, Config, Sprint 3
6. **Coche toutes les tâches**

---

### Carte 7: IA avec Contexte (RAG)

1. Nouvelle carte dans **"Sprint 3 - IA"**
2. Titre: **US-1.15 - IA avec contexte (RAG)**
3. Description:
```
📌 IA comprend contexte avec RAG
🎯 Sprint 3 | ⏱️ 11h | 👤 Ilef Yousfi

Fichiers:
- RAGService.php
```
4. Checklist "Tâches":
   - Créer RAGService
   - Récupération contexte cours
   - Récupération contexte utilisateur
   - Récupération contexte exercices
   - Système scoring pertinence
   - Optimiser requêtes
   - Limiter tokens (4000 max)
   - Safety checks
5. Étiquettes: Backend, IA, Database, Sprint 3
6. **Coche toutes les tâches**

---

### Carte 8: IA Agent Actif

1. Nouvelle carte dans **"Sprint 3 - IA"**
2. Titre: **US-1.16 - IA Agent Actif**
3. Description:
```
📌 IA peut exécuter actions sur base de données
🎯 Sprint 3 | ⏱️ 12h | 👤 Ilef Yousfi

Fichiers:
- ActionExecutorService.php
```
4. Checklist "Tâches":
   - Créer ActionExecutorService
   - Détection actions JSON
   - Action: créer cours
   - Action: créer chapitre
   - Action: créer ressource
   - Action: créer exercice
   - Action: modifier
   - Gérer permissions
   - Corriger format JSON
5. Étiquettes: Backend, IA, Database, Sprint 3
6. **Coche toutes les tâches**

---

### Carte 9: Interface Chat Moderne

1. Nouvelle carte dans **"Sprint 3 - IA"**
2. Titre: **US-1.17 - Interface Chat Moderne**
3. Description:
```
📌 Widget chat complet avec AJAX
🎯 Sprint 3 | ⏱️ 15h | 👤 Ilef Yousfi

Fichiers:
- AIAssistantService.php
- AIAssistantController.php
- chat_widget.html.twig
```
4. Checklist "Tâches":
   - Créer AIAssistantService
   - Prompt système intelligent
   - Intégrer RAGService
   - Intégrer ActionExecutorService
   - Gérer historique
   - Créer AIAssistantController
   - Créer widget chat
   - AJAX asynchrone
   - Indicateur "en train d'écrire"
   - Bulle bienvenue
   - Styliser interface
   - Intégrer frontoffice + backoffice
   - Optimiser vitesse
5. Étiquettes: Backend, Frontend, IA, Sprint 3
6. **Coche toutes les tâches**

---

### Carte 10: Sécurité Avancée

1. Nouvelle carte dans **"Sprint 3 - IA"**
2. Titre: **US-1.18 - Sécurité Avancée**
3. Description:
```
📌 Renforcer sécurité application
🎯 Sprint 3 | ⏱️ 3.5h | 👤 Ilef Yousfi

Fichiers:
- security.yaml
- User.php
- AIAssistantService.php
```
4. Checklist "Tâches":
   - Protection CSRF routes sensibles
   - Validation stricte inputs
   - Logger actions IA
5. Étiquettes: Backend, Config, Sprint 3
6. **Coche toutes les tâches**

---

### Carte 11: Documentation Complète

1. Nouvelle carte dans **"Sprint 3 - IA"**
2. Titre: **US-1.19 - Documentation Complète**
3. Description:
```
📌 Documentation complète tous modules
🎯 Sprint 3 | ⏱️ 9.5h | 👤 Ilef Yousfi

Fichiers:
- 30+ fichiers .md
```
4. Checklist "Tâches":
   - ASSISTANT_IA_ARCHITECTURE.md
   - GUIDE_INSTALLATION_IA.md
   - TESTEZ_IA_AGENT_ACTIF.md
   - PROMPT_SYSTEM_IA.md
   - README_ASSISTANT_IA.md
   - AUDIT_READY_TO_USE.md
   - USER_ACTIVITY_BUNDLE_COMPLETE.md
   - SUSPENSION_AUTOMATIQUE_GUIDE.md
   - 20+ autres fichiers
5. Étiquettes: Documentation, Sprint 3
6. **Coche toutes les tâches**

---

### Carte 12: Corrections Merges

1. Nouvelle carte dans **"Sprint 3 - IA"**
2. Titre: **US-1.20 - Corrections Merges**
3. Description:
```
📌 Corrections problèmes après merges
🎯 Sprint 3 | ⏱️ 11h | 👤 Ilef Yousfi

Fichiers:
- migrations/
- User.php
- Post.php, Commentaire.php
- show.html.twig
```
4. Checklist "Tâches":
   - Merger branche Amira
   - Fixer conflits migrations
   - Corriger colonnes camelCase
   - Recréer user_audit
   - Merger branche Baha
   - Fixer relations Post/Commentaire
   - Fixer affichage owner
   - Fixer récursion Twig
5. Étiquettes: Backend, Database, Frontend, Bug, Sprint 3
6. **Coche toutes les tâches**

✅ Sprint 3 terminé! (7 cartes)

---

## 📦 ÉTAPE 6: Déplacer vers "Terminé" (2 min)

Puisque tout le travail est déjà fait:

1. Sélectionne toutes les cartes de **"Sprint 2 - Bundles"**
2. Glisse-les vers **"✅ Terminé"**
3. Sélectionne toutes les cartes de **"Sprint 3 - IA"**
4. Glisse-les vers **"✅ Terminé"**

✅ Toutes les cartes dans "Terminé"!

---

## 🎯 ÉTAPE 7: Ajouter Dates (Optionnel - 3 min)

Pour chaque carte dans "Terminé":
1. Ouvre la carte
2. Clique **"Dates"**
3. Ajoute date de complétion: **Février 2026**
4. Coche **"Terminé"**

---

## 📊 Résultat Final

Ton board Trello contient maintenant:

✅ **3 listes**:
- Sprint 2 - Bundles (vide)
- Sprint 3 - IA (vide)
- Terminé (12 cartes)

✅ **9 étiquettes**:
- Backend, Frontend, Database, Config, Documentation, IA, Bug, Sprint 2, Sprint 3

✅ **12 cartes** dans "Terminé":
- 5 cartes Sprint 2 (Bundles)
- 7 cartes Sprint 3 (IA)

✅ **88 tâches** cochées

✅ **~109h** de travail documenté

---

## 💡 Astuces Rapides

### Raccourcis Clavier:
- **N** = Nouvelle carte
- **E** = Éditer carte
- **L** = Ajouter étiquette
- **Space** = Assigner à moi

### Copier une Carte:
1. Ouvre la carte
2. Menu → Copier
3. Choisis la liste destination

### Archiver une Liste:
1. Menu liste → Archiver tous les éléments

---

## ⏱️ Temps Total

- Étape 1: 2 min (Créer board)
- Étape 2: 2 min (Créer listes)
- Étape 3: 3 min (Créer étiquettes)
- Étape 4: 15 min (5 cartes Sprint 2)
- Étape 5: 20 min (7 cartes Sprint 3)
- Étape 6: 2 min (Déplacer vers Terminé)
- Étape 7: 3 min (Ajouter dates - optionnel)

**Total: 45-50 minutes**

---

## 📱 Version Mobile

Tu peux aussi faire tout ça depuis l'app mobile Trello:
- iOS: App Store
- Android: Google Play

---

## ✅ Checklist Finale

- [ ] Board créé
- [ ] 3 listes créées
- [ ] 9 étiquettes créées
- [ ] 5 cartes Sprint 2 créées
- [ ] 7 cartes Sprint 3 créées
- [ ] Toutes les checklists cochées
- [ ] Toutes les cartes dans "Terminé"
- [ ] Dates ajoutées (optionnel)

---

**Félicitations! Ton board Trello est prêt!** 🎉

**Responsable**: Ilef Yousfi  
**Projet**: AutoLearn - Bundles & IA  
**Statut**: Prêt pour présentation ✅
