# 📋 Guide Trello - Étiquettes pour Module Gestion Utilisateur

## 🎨 Étiquettes Recommandées

### 1. Par Type de Tâche (Couleurs)

| Étiquette | Couleur | Description | Exemple |
|-----------|---------|-------------|---------|
| **Backend** | 🔵 Bleu | Code PHP, Services, Controllers | SecurityController, RAGService |
| **Frontend** | 🟢 Vert | Templates Twig, CSS, JavaScript | login.html.twig, chat_widget |
| **Base de données** | 🟣 Violet | Entités, Migrations, SQL | User.php, migrations |
| **Configuration** | 🟡 Jaune | Fichiers config, .env, YAML | security.yaml, mailer.yaml |
| **Documentation** | ⚪ Blanc | Fichiers .md, guides | README, GUIDE_INSTALLATION |
| **Tests** | 🟠 Orange | Tests unitaires, Commands de test | TestBrevoCommand |
| **Bug/Fix** | 🔴 Rouge | Corrections, fixes après merge | Fix recursion Twig |

### 2. Par Priorité

| Étiquette | Couleur | Description |
|-----------|---------|-------------|
| **Priorité Haute** | 🔴 Rouge foncé | Critique, bloquant |
| **Priorité Moyenne** | 🟡 Jaune | Important mais pas urgent |
| **Priorité Basse** | 🟢 Vert clair | Nice to have |

### 3. Par Sprint

| Étiquette | Couleur | Description |
|-----------|---------|-------------|
| **Sprint 1** | 🔵 Bleu clair | CRUD & Authentification |
| **Sprint 2** | 🟣 Violet clair | Bundles & Email |
| **Sprint 3** | 🟢 Vert clair | IA & Documentation |

### 4. Par Statut

| Étiquette | Couleur | Description |
|-----------|---------|-------------|
| **À faire** | ⚪ Gris | Pas encore commencé |
| **En cours** | 🟡 Jaune | En développement |
| **En test** | 🟠 Orange | À tester |
| **Terminé** | 🟢 Vert | Complété et validé |
| **Bloqué** | 🔴 Rouge | Problème bloquant |

### 5. Par Fonctionnalité

| Étiquette | Couleur | Description |
|-----------|---------|-------------|
| **Authentification** | 🔵 Bleu | Login, Register, Logout |
| **Profil** | 🟢 Vert | Consultation, Modification |
| **Admin** | 🟣 Violet | Gestion utilisateurs, Recherche |
| **Email** | 🟡 Jaune | Mailer, Reset password |
| **Audit** | 🟠 Orange | EntityAudit Bundle |
| **Activity** | 🔵 Bleu clair | UserActivity Bundle |
| **Suspension** | 🔴 Rouge | Suspension auto/manuelle |
| **IA** | 🟣 Violet foncé | Ollama, RAG, Agent actif |

---

## 🎯 Configuration Recommandée (Minimaliste)

Si tu veux garder ça simple, utilise seulement ces 10 étiquettes:

### Étiquettes Essentielles:

1. **Backend** (🔵 Bleu)
2. **Frontend** (🟢 Vert)
3. **Database** (🟣 Violet)
4. **Config** (🟡 Jaune)
5. **Documentation** (⚪ Blanc)
6. **Sprint 1** (🔵 Bleu clair)
7. **Sprint 2** (🟣 Violet clair)
8. **Sprint 3** (🟢 Vert clair)
9. **Bug** (🔴 Rouge)
10. **IA** (🟣 Violet foncé)

---

## 📊 Structure Trello Recommandée

### Listes (Colonnes):

1. **📋 Product Backlog** - Toutes les User Stories
2. **🎯 Sprint 1 - CRUD** - Tâches Sprint 1
3. **🎯 Sprint 2 - Bundles** - Tâches Sprint 2
4. **🎯 Sprint 3 - IA** - Tâches Sprint 3
5. **✅ Terminé** - Tâches complétées

OU (plus simple):

1. **📋 À faire**
2. **🔄 En cours**
3. **🧪 En test**
4. **✅ Terminé**

---

## 🎴 Exemple de Carte Trello

### Carte: "Créer SecurityController"

**Titre**: Créer SecurityController avec méthode login()

**Description**:
```
User Story: US-1.2 - Connexion utilisateur
Sprint: Sprint 1
Estimation: 2h

Tâches:
- [ ] Créer SecurityController.php
- [ ] Implémenter méthode login()
- [ ] Configurer routes
- [ ] Redirection selon rôle
- [ ] Bloquer comptes suspendus

Fichiers:
- src/Controller/SecurityController.php
- config/routes.yaml
```

**Étiquettes**:
- Backend (🔵)
- Sprint 1 (🔵 clair)
- Authentification (🔵)

**Checklist**:
- ✅ Créer fichier
- ✅ Implémenter login
- ✅ Tester
- ✅ Documenter

---

## 🎨 Codes Couleur Trello

### Couleurs Disponibles dans Trello:

| Nom | Code Hex | Usage Recommandé |
|-----|----------|------------------|
| Vert | #61BD4F | Frontend, Terminé |
| Jaune | #F2D600 | Config, En cours |
| Orange | #FF9F1A | Tests, En test |
| Rouge | #EB5A46 | Bug, Priorité haute |
| Violet | #C377E0 | Database, Admin |
| Bleu | #0079BF | Backend, Sprint 1 |
| Bleu ciel | #00C2E0 | Sprint 2, Activity |
| Vert lime | #51E898 | Sprint 3, IA |
| Rose | #FF78CB | Nice to have |
| Noir | #344563 | Bloqué, Critique |

---

## 📝 Template de Carte pour Chaque User Story

### Format Recommandé:

```
Titre: US-1.X - [Nom User Story]

Description:
📌 User Story: [Description complète]
🎯 Sprint: Sprint X
⏱️ Estimation: Xh
👤 Responsable: Ilef Yousfi

📋 Tâches:
- [ ] Tâche 1
- [ ] Tâche 2
- [ ] Tâche 3

📁 Fichiers créés/modifiés:
- src/...
- templates/...

🔗 Liens:
- Documentation: [lien]
- Commit: [lien]

✅ Critères d'acceptation:
- [ ] Code fonctionne
- [ ] Tests passent
- [ ] Documentation à jour
```

---

## 🚀 Workflow Trello

### Déplacement des Cartes:

1. **Product Backlog** → Toutes les US au départ
2. **Sprint X** → Déplacer les US du sprint en cours
3. **En cours** → Quand tu commences à travailler
4. **En test** → Quand le code est prêt à tester
5. **Terminé** → Quand tout est validé

### Exemple de Flux:

```
📋 Product Backlog
    ↓
🎯 Sprint 1
    ↓
🔄 En cours (tu travailles dessus)
    ↓
🧪 En test (tu testes)
    ↓
✅ Terminé (validé)
```

---

## 💡 Conseils Pratiques

### 1. Utilise les Checklists
- Ajoute une checklist pour chaque tâche
- Coche au fur et à mesure
- Trello affiche la progression (3/5)

### 2. Ajoute des Dates d'Échéance
- Sprint 1: Semaine 1
- Sprint 2: Semaine 2
- Sprint 3: Semaine 3

### 3. Attache des Fichiers
- Screenshots
- Diagrammes
- Documentation

### 4. Commente les Cartes
- Notes de développement
- Problèmes rencontrés
- Solutions trouvées

### 5. Utilise les Power-Ups
- **Calendar**: Vue calendrier
- **Card Aging**: Vieillissement des cartes
- **Custom Fields**: Champs personnalisés (Estimation, Points)

---

## 📊 Exemple Complet pour Ton Projet

### Board: "AutoLearn - Module Gestion Utilisateur"

### Listes:
1. 📋 Product Backlog (20 cartes)
2. 🎯 Sprint 1 - CRUD (8 cartes)
3. 🎯 Sprint 2 - Bundles (5 cartes)
4. 🎯 Sprint 3 - IA (7 cartes)
5. ✅ Terminé (116 tâches)

### Étiquettes:
- Backend (🔵)
- Frontend (🟢)
- Database (🟣)
- Config (🟡)
- Documentation (⚪)
- Sprint 1 (🔵 clair)
- Sprint 2 (🟣 clair)
- Sprint 3 (🟢 clair)
- Bug (🔴)
- IA (🟣 foncé)

### Cartes Exemple:

**US-1.1 - Inscription utilisateur**
- Étiquettes: Backend, Database, Sprint 1
- Checklist: 7 tâches
- Estimation: 6h
- Statut: ✅ Terminé

**US-1.14 - Assistant IA intelligent**
- Étiquettes: Backend, IA, Sprint 3
- Checklist: 6 tâches
- Estimation: 6h
- Statut: ✅ Terminé

---

## 🎯 Quick Start

### Étapes pour Configurer Ton Trello:

1. **Créer le Board**: "AutoLearn - Gestion Utilisateur"

2. **Créer les Listes**:
   - Product Backlog
   - Sprint 1
   - Sprint 2
   - Sprint 3
   - Terminé

3. **Créer les Étiquettes** (10 essentielles):
   - Backend (Bleu)
   - Frontend (Vert)
   - Database (Violet)
   - Config (Jaune)
   - Documentation (Blanc)
   - Sprint 1 (Bleu clair)
   - Sprint 2 (Violet clair)
   - Sprint 3 (Vert clair)
   - Bug (Rouge)
   - IA (Violet foncé)

4. **Créer les Cartes** (20 User Stories):
   - Copier depuis SPRINT_BACKLOG_REEL.xlsx
   - Une carte par User Story
   - Ajouter checklist avec les tâches

5. **Organiser**:
   - Toutes les cartes dans Product Backlog
   - Déplacer selon les sprints
   - Marquer comme "Terminé"

---

## 📥 Import depuis Excel

Tu peux importer ton Sprint Backlog dans Trello:

1. **Exporter en CSV** depuis Excel
2. **Utiliser Trello Import**:
   - Menu → Import → CSV
   - Mapper les colonnes
   - Importer

Ou utiliser un Power-Up comme **"Placker"** pour synchroniser Excel ↔ Trello

---

**Responsable**: Ilef Yousfi  
**Date**: Février 2026  
**Outil**: Trello  
**Projet**: AutoLearn - Module Gestion Utilisateur
