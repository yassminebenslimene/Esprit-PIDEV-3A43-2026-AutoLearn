# 📚 DOCUMENTATION SYSTÈME DE QUIZ - PARTIE 1 : ARCHITECTURE

## 🎯 Vue d'ensemble du système

Le système de quiz est une plateforme complète permettant :
- La création et gestion de quiz par les administrateurs
- La génération automatique de quiz via IA (Groq API)
- Le passage de quiz par les étudiants
- La correction automatique et le suivi des résultats
- Un système de tuteur IA pour aider les étudiants

---

## 📊 Architecture Globale

```
┌─────────────────────────────────────────────────────────────┐
│                    SYSTÈME DE QUIZ                           │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌──────────────┐      ┌──────────────┐      ┌───────────┐ │
│  │  BACKOFFICE  │      │  FRONTOFFICE │      │    API    │ │
│  │  (Admin)     │      │  (Étudiant)  │      │   (IA)    │ │
│  └──────────────┘      └──────────────┘      └───────────┘ │
│         │                      │                     │       │
│         ├──────────────────────┼─────────────────────┤       │
│         │                      │                     │       │
│  ┌──────▼──────────────────────▼─────────────────────▼────┐ │
│  │              COUCHE SERVICES                            │ │
│  │  - QuizManagementService                                │ │
│  │  - GrokQuizGeneratorService                             │ │
│  │  - QuizCorrectorAIService                               │ │
│  │  - QuizTutorAIService                                   │ │
│  └─────────────────────────────────────────────────────────┘ │
│                              │                                │
│  ┌───────────────────────────▼──────────────────────────────┐│
│  │              COUCHE ENTITÉS (Base de données)            ││
│  │  Quiz → Question → Option                                ││
│  │  Participation → Reponse                                 ││
│  └──────────────────────────────────────────────────────────┘│
└─────────────────────────────────────────────────────────────┘
```

---

## 🗂️ Structure des Fichiers

### 📁 Entités (src/Entity/)
```
Quiz.php              - Entité principale du quiz
Question.php          - Questions du quiz
Option.php            - Options de réponse (QCM)
```

### 📁 Contrôleurs (src/Controller/)
```
Backoffice/
  └── QuizController.php           - Gestion admin des quiz

FrontOffice/
  ├── QuizController.php            - Liste et affichage des quiz
  ├── QuizPassageController.php     - Passage et soumission des quiz
  └── QuizTutorController.php       - Tuteur IA pour aide
```

### 📁 Services (src/Service/)
```
QuizManagementService.php         - Logique métier et validation
GrokQuizGeneratorService.php      - Génération automatique via IA
QuizCorrectorAIService.php        - Correction intelligente
QuizTutorAIService.php            - Assistant IA pour étudiants
```

### 📁 Formulaires (src/Form/)
```
QuizType.php          - Formulaire de création/édition de quiz
QuestionType.php      - Formulaire pour les questions
```

### 📁 Repository (src/Repository/)
```
QuizRepository.php    - Requêtes personnalisées pour les quiz
```

### 📁 Templates (templates/)
```
backoffice/quiz/
  ├── index.html.twig              - Liste des quiz (admin)
  ├── new.html.twig                - Création de quiz
  ├── edit.html.twig               - Édition de quiz
  ├── show.html.twig               - Détails d'un quiz
  ├── _form.html.twig              - Formulaire réutilisable
  ├── select_chapitre.html.twig    - Sélection chapitre pour génération IA
  └── generate.html.twig           - Interface de génération IA

frontoffice/quiz/
  ├── list.html.twig               - Liste des quiz disponibles
  ├── start.html.twig              - Démarrage d'un quiz
  ├── passage.html.twig            - Interface de passage du quiz
  └── result.html.twig             - Affichage des résultats
```

---

## 🔄 Flux de Données

### Flux 1 : Création Manuelle d'un Quiz (Admin)
```
1. Admin accède à /backoffice/quiz/new
2. Remplit le formulaire QuizType
3. QuizController::new() valide avec QuizManagementService
4. Sauvegarde en base de données
5. Redirection vers la liste des quiz
```

### Flux 2 : Génération Automatique via IA
```
1. Admin sélectionne un chapitre
2. Configure les paramètres (nb questions, difficulté)
3. GrokQuizGeneratorService appelle l'API Groq
4. L'IA génère les questions en JSON
5. Le service crée les entités Quiz, Question, Option
6. Sauvegarde en base (état: brouillon)
7. Admin peut réviser avant publication
```

### Flux 3 : Passage d'un Quiz (Étudiant)
```
1. Étudiant consulte la liste des quiz disponibles
2. Clique sur "Commencer le quiz"
3. QuizPassageController crée une Participation
4. Affiche les questions une par une
5. Étudiant soumet ses réponses
6. QuizCorrectorAIService corrige automatiquement
7. Calcul du score et affichage des résultats
8. Sauvegarde de la tentative
```

---

## 🎨 Modèle de Données

### Entité Quiz
```php
- id: int
- titre: string (3-255 caractères)
- description: text (10-2000 caractères)
- etat: string (actif|inactif|brouillon|archive)
- dureeMaxMinutes: int (nullable)
- seuilReussite: int (0-100%, défaut: 50%)
- maxTentatives: int (nullable)
- chapitre: Chapitre (relation ManyToOne)
- questions: Collection<Question>
- imageName: string (nullable)
```

### Entité Question
```php
- id: int
- texteQuestion: text (10-1000 caractères)
- point: int (1-100)
- quiz: Quiz (relation ManyToOne)
- options: Collection<Option>
- imageName: string (nullable)
- audioName: string (nullable)
- videoName: string (nullable)
```

### Entité Option
```php
- id: int
- texteOption: string (1-255 caractères)
- estCorrecte: bool
- question: Question (relation ManyToOne)
```

---

## 🔐 Sécurité et Validation

### Validation des Entités
- Contraintes Symfony Validator sur tous les champs
- Validation métier dans QuizManagementService
- Protection CSRF sur tous les formulaires

### Contrôle d'Accès
- ROLE_ADMIN : Gestion complète des quiz
- ROLE_ETUDIANT : Passage des quiz uniquement
- IsGranted sur les routes sensibles

### Règles Métier
1. Un quiz doit avoir au moins 1 question
2. Chaque question doit avoir au moins 2 options
3. Chaque question doit avoir exactement 1 bonne réponse
4. Le seuil de réussite doit être entre 0 et 100%
5. Un quiz doit être lié à un chapitre

---

## 🚀 Technologies Utilisées

- **Framework**: Symfony 7.x
- **Base de données**: MySQL/PostgreSQL
- **ORM**: Doctrine
- **Templates**: Twig
- **Validation**: Symfony Validator
- **Upload**: VichUploaderBundle
- **IA**: Groq API (Llama 3.3)
- **HTTP Client**: Symfony HttpClient

---

## 📈 Statistiques et Métriques

Le système collecte :
- Nombre de tentatives par étudiant
- Score moyen par quiz
- Taux de réussite
- Temps moyen de passage
- Questions les plus difficiles
- Progression des étudiants

---

*Suite dans PARTIE 2 : FONCTIONNALITÉS DÉTAILLÉES*
