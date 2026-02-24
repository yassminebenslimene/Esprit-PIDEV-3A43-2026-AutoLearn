# 📋 Guide d'Import Trello - AutoLearn Sprint 1

## 🎯 Structure du Tableau

**Nom du tableau:** AutoLearn Sprint 1 - Gestion Cours + IA

**4 Listes à créer:**
1. 📌 TO DO
2. 🔄 IN PROGRESS
3. ✅ TO VERIFY
4. ✅ DONE

---

## 📌 TO DO (10 cartes)

### Carte 1: IA-1 Recommandation de cours
- **Titre:** [5 pts] IA-1 : Recommandation de cours
- **Labels:** 🔵 Backend, 🟡 IA
- **Description:**
  ```
  Algorithme de suggestion basé sur profil étudiant
  - Analyse des préférences et historique
  - Recommandations personnalisées
  ```

### Carte 2: IA-2 Explication simplifiée
- **Titre:** [8 pts] IA-2 : Explication simplifiée du contenu
- **Labels:** 🔵 Backend, 🟡 IA
- **Description:**
  ```
  Reformulation du contenu complexe en langage simple
  - Génération d'exemples pratiques via IA
  - API pour obtenir explications simplifiées
  ```

### Carte 3: IA-3 Résumé automatique
- **Titre:** [5 pts] IA-3 : Résumé automatique de chapitre
- **Labels:** 🔵 Backend, 🟡 IA
- **Description:**
  ```
  Synthèse du contenu via API IA (GPT / Claude)
  - Endpoint API pour récupérer résumés
  - Cache des résumés générés
  ```

### Carte 4: IA-4 Chatbot assistant
- **Titre:** [8 pts] IA-4 : Chatbot assistant pédagogique
- **Labels:** 🔵 Backend, 🟣 Frontend, 🟡 IA
- **Description:**
  ```
  Q&A contextuel sur le contenu du cours
  - Interface chat dans frontoffice
  - Intégration API conversationnelle
  ```

### Carte 5: B-1 Entité Cours complète
- **Titre:** [3 pts] B-1 : Entité Cours complète
- **Labels:** 🔵 Backend
- **Description:**
  ```
  Finaliser relations Cours ↔ Chapitre ↔ Quiz
  - Validation des contraintes
  - Doctrine ORM optimisé
  ```

### Carte 6: B-2 CRUD Chapitres
- **Titre:** [5 pts] B-2 : CRUD Chapitres
- **Labels:** 🔵 Backend, 🟣 Frontend
- **Description:**
  ```
  Création, édition, suppression chapitres
  - Ordre des chapitres + routes imbriquées
  - Formulaires Symfony
  ```

### Carte 7: B-3 Templates backoffice
- **Titre:** [3 pts] B-3 : Templates backoffice
- **Labels:** 🟣 Frontend
- **Description:**
  ```
  Templates Twig pour gestion cours/chapitres
  - Formulaires avec validation
  - Interface admin responsive
  ```

### Carte 8: B-4 Upload fichiers cours
- **Titre:** [5 pts] B-4 : Upload fichiers cours
- **Labels:** 🔵 Backend
- **Description:**
  ```
  Support PDF, vidéos, images
  - Stockage et gestion des médias
  - VichUploaderBundle
  ```

### Carte 9: B-5 Système tags/catégories
- **Titre:** [3 pts] B-5 : Système de tags/catégories
- **Labels:** 🔵 Backend
- **Description:**
  ```
  Classification des cours par thématique
  - Filtrage et recherche par tags
  - Entité Tag + relations ManyToMany
  ```

### Carte 10: B-6 Prévisualisation cours
- **Titre:** [3 pts] B-6 : Prévisualisation cours
- **Labels:** 🟣 Frontend
- **Description:**
  ```
  Vue aperçu avant publication
  - Mode brouillon vs publié
  - Preview en temps réel
  ```

---

## 🔄 IN PROGRESS (9 cartes)

### Carte 11: US-8 Progression étudiant
- **Titre:** [5 pts] US-8 : Progression étudiant
- **Labels:** 🟢 User Story, 🔵 Backend
- **Description:**
  ```
  En tant qu'étudiant, je veux voir ma progression dans chaque cours
  - Service CourseProgressService ✅
  - Entité UserProgress + relations
  ```

### Carte 12: T-8.1 Entité ChapterProgress
- **Titre:** [2 pts] T-8.1 : Entité ChapterProgress
- **Labels:** 🔵 Backend
- **Description:**
  ```
  Relations User ↔ Chapitre
  - Champs: completed, progress_percentage
  - Migration Doctrine
  ```

### Carte 13: T-8.2 CoursProgressService
- **Titre:** [2 pts] T-8.2 : CoursProgressService
- **Labels:** 🔵 Backend
- **Description:**
  ```
  Calcul automatique de %
  - Méthodes: markChapterComplete(), getProgress()
  - Tests unitaires
  ```

### Carte 14: T-8.3 Extension Twig Progress
- **Titre:** [3 pts] T-8.3 : Extension Twig ProgressExtension
- **Labels:** 🟣 Frontend
- **Description:**
  ```
  Filtre progress() pour afficher barre
  - {{ chapter.completed() }} dans templates
  - Helper Twig personnalisé
  ```

### Carte 15: T-8.4 Barre progression Moodle
- **Titre:** [2 pts] T-8.4 : Barre progression Moodle
- **Labels:** 🟣 Frontend
- **Description:**
  ```
  Style "3 of 8 - 37.5%" dans frontoffice
  - Animation CSS progressive
  - Design inspiré Moodle
  ```

### Carte 16: US-7 Traduction multilingue
- **Titre:** [8 pts] US-7 : Traduction multilingue
- **Labels:** 🟢 User Story, 🟡 IA, 🔵 Backend
- **Description:**
  ```
  Traduire les chapitres (FR/EN/ES/DE/AR) via API
  - Service TranslationService ✅
  - Cache des traductions
  ```

### Carte 17: T-7.1 ChapitreApiController
- **Titre:** [3 pts] T-7.1 : ChapitreApiController
- **Labels:** 🔵 Backend
- **Description:**
  ```
  GET /api/chapitres/{id}/translations
  - Paramètre ?lang=en
  - Réponse JSON formatée
  ```

### Carte 18: T-7.2 TranslationService
- **Titre:** [3 pts] T-7.2 : TranslationService
- **Labels:** 🔵 Backend
- **Description:**
  ```
  MyMemory API + cache DB
  - Fallback si API indisponible
  - Gestion des erreurs
  ```

### Carte 19: T-7.3 Dropdown langues AJAX
- **Titre:** [3 pts] T-7.3 : Dropdown langues AJAX
- **Labels:** 🟣 Frontend
- **Description:**
  ```
  Sélecteur de langue dynamique
  - Rechargement contenu sans refresh
  - JavaScript fetch API
  ```

---

## ✅ TO VERIFY (5 cartes)

### Carte 20: US-2 CRUD Cours (Test)
- **Titre:** [5 pts] US-2 : CRUD Cours - Tests
- **Labels:** 🔴 Test QA
- **Description:**
  ```
  Gestion complète des cours - backoffice admin
  - Vérifier toutes les routes CRUD
  - Tests fonctionnels complets
  ```

### Carte 21: T-1.1 Entité Cours ORM (Test)
- **Titre:** [3 pts] T-1.1 : Entité Cours ORM - Validation
- **Labels:** 🔴 Test QA
- **Description:**
  ```
  Doctrine ORM validé
  - Relations fonctionnelles
  - Contraintes respectées
  ```

### Carte 22: T-1.4 Templates Twig (Test)
- **Titre:** [3 pts] T-1.4 : Templates Twig backoffice - Test
- **Labels:** 🔴 Test QA
- **Description:**
  ```
  index, show, edit, new
  - Validation formulaires
  - Tests d'affichage
  ```

### Carte 23: T-1.5 Tests CRUD validés
- **Titre:** [5 pts] T-1.5 : Tests CRUD validés
- **Labels:** 🔴 Test QA
- **Description:**
  ```
  Tests unitaires + fonctionnels
  - Couverture > 80%
  - PHPUnit + Panther
  ```

### Carte 24: T-2.4 Dropdown langues (Test)
- **Titre:** [3 pts] T-2.4 : Dropdown langues AJAX - Test
- **Labels:** 🔴 Test QA
- **Description:**
  ```
  Dropdown + update contenu
  - Tests navigateurs multiples
  - Validation UX
  ```

---

## ✅ DONE (8 cartes)

### Carte 25: US-1 CRUD Cours ✓
- **Titre:** [✓] US-1 : CRUD Cours
- **Labels:** 🔵 Backend, ✅ Terminé
- **Description:**
  ```
  ✅ TERMINÉ
  Gestion complète des cours - backoffice admin
  - Entités + Controllers + Templates
  ```

### Carte 26: T-1.1 Entité Cours ORM ✓
- **Titre:** [✓] T-1.1 : Entité Cours ORM
- **Labels:** 🔵 Backend, ✅ Terminé
- **Description:**
  ```
  ✅ TERMINÉ
  Doctrine ORM + CSRF validés
  ```

### Carte 27: T-1.5 Tests CRUD ✓
- **Titre:** [✓] T-1.5 : Tests CRUD validés
- **Labels:** 🔴 Test QA, ✅ Terminé
- **Description:**
  ```
  ✅ TERMINÉ
  Tests unitaires passés
  ```

### Carte 28: T-2.1 Entité Chapitre ORM ✓
- **Titre:** [✓] T-2.1 : Entité Chapitre ORM
- **Labels:** 🔵 Backend, ✅ Terminé
- **Description:**
  ```
  ✅ TERMINÉ
  Relations Cours ↔ Chapitre
  ```

### Carte 29: T-2.3 Routes imbriquées ✓
- **Titre:** [✓] T-2.3 : Routes imbriquées
- **Labels:** 🔵 Backend, ✅ Terminé
- **Description:**
  ```
  ✅ TERMINÉ
  /cours/{id}/chapitres
  ```

### Carte 30: T-2.2 Tests CRUD cascades ✓
- **Titre:** [✓] T-2.2 : Tests CRUD + cascades
- **Labels:** 🔴 Test QA, ✅ Terminé
- **Description:**
  ```
  ✅ TERMINÉ
  Validation complète
  ```

### Carte 31: US-2 CRUD Chapitres ✓
- **Titre:** [✓] US-2 : CRUD Chapitres
- **Labels:** 🔵 Backend, 🟣 Frontend, ✅ Terminé
- **Description:**
  ```
  ✅ TERMINÉ
  Chapitres ordonnés + routes imbriquées
  ```

### Carte 32: T-2.4 Templates Twig ✓
- **Titre:** [✓] T-2.4 : Templates Twig chapitres
- **Labels:** 🟣 Frontend, ✅ Terminé
- **Description:**
  ```
  ✅ TERMINÉ
  Templates backoffice fonctionnels
  ```

---

## 🏷️ Labels à créer dans Trello

1. **🟢 User Story** (Vert)
2. **🔵 Backend** (Bleu)
3. **🟣 Frontend** (Violet)
4. **🟡 IA** (Jaune)
5. **🟠 Bloquant/Urgent** (Orange)
6. **🔴 Test QA** (Rouge)
7. **✅ Terminé** (Vert foncé)

---

## 📊 Résumé par Liste

| Liste | Nombre de cartes | Points totaux |
|-------|------------------|---------------|
| TO DO | 10 | 50 pts |
| IN PROGRESS | 9 | 31 pts |
| TO VERIFY | 5 | 19 pts |
| DONE | 8 | - |
| **TOTAL** | **32** | **100 pts** |

---

## 🚀 Ordre de Priorité (TO DO)

1. **IA-3** : Résumé automatique (5 pts) - Fonctionnalité clé
2. **B-2** : CRUD Chapitres (5 pts) - Base du module
3. **B-4** : Upload fichiers (5 pts) - Fonctionnalité importante
4. **IA-4** : Chatbot assistant (8 pts) - Valeur ajoutée
5. **IA-2** : Explication simplifiée (8 pts) - IA pédagogique
6. **IA-1** : Recommandation (5 pts) - Personnalisation
7. **B-1** : Entité Cours (3 pts) - Finalisation
8. **B-3** : Templates (3 pts) - Interface
9. **B-5** : Tags (3 pts) - Organisation
10. **B-6** : Prévisualisation (3 pts) - UX

---

## 💡 Conseils d'Import

1. **Créer d'abord les labels** avant d'ajouter les cartes
2. **Utiliser les checklists** pour les sous-tâches
3. **Ajouter des dates d'échéance** pour le sprint
4. **Assigner les membres** de l'équipe à chaque carte
5. **Activer Power-Up "Custom Fields"** pour les points

---

**Date de création:** 23 février 2026
