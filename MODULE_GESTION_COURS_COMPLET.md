# 📚 MODULE GESTION DE COURS - DOCUMENTATION COMPLÈTE

## 📋 Vue d'Ensemble

Le module de gestion de cours est un système complet permettant de créer, gérer et consulter des cours structurés en chapitres avec des fonctionnalités avancées (quiz, progression, traduction, PDF, ressources multimédias).

---

## 🎯 Objectifs du Module

- ✅ Créer et gérer des cours avec métadonnées complètes
- ✅ Structurer les cours en chapitres ordonnés
- ✅ Attacher des ressources multimédias aux chapitres
- ✅ Suivre la progression des étudiants
- ✅ Générer des PDF des chapitres
- ✅ Traduire les chapitres en plusieurs langues
- ✅ Intégrer des quiz pour valider les connaissances
- ✅ Interface backoffice pour les administrateurs
- ✅ Interface frontoffice pour les étudiants

---

## 🏗️ ARCHITECTURE DU MODULE

### 1. Structure des Entités

#### 📘 Entité: Cours
**Fichier:** `src/Entity/GestionDeCours/Cours.php`

**Propriétés:**
- `id` (int) - Identifiant unique
- `titre` (string, 255) - Titre du cours
- `description` (text) - Description détaillée
- `matiere` (string, 255) - Matière (ex: Informatique, Mathématiques)
- `niveau` (string, 50) - Niveau (Débutant, Intermédiaire, Avancé)
- `duree` (int) - Durée en heures
- `createdAt` (DateTimeImmutable) - Date de création
- `chapitres` (Collection<Chapitre>) - Liste des chapitres
- `communaute` (Communaute) - Communauté associée (optionnel)

**Validations:**
- Titre: 3-255 caractères, caractères alphanumériques + accents
- Description: max 2000 caractères
- Matière: obligatoire, max 255 caractères
- Niveau: obligatoire, max 50 caractères
- Durée: nombre positif obligatoire

**Relations:**
- OneToMany avec Chapitre (cascade persist/remove)
- OneToOne avec Communaute (optionnel)


#### 📄 Entité: Chapitre
**Fichier:** `src/Entity/GestionDeCours/Chapitre.php`

**Propriétés:**
- `id` (int) - Identifiant unique
- `titre` (string, 255) - Titre du chapitre
- `contenu` (text) - Contenu HTML du chapitre
- `ordre` (int) - Ordre d'affichage dans le cours
- `ressources` (string, 255) - Lien vers ressource externe (optionnel)
- `ressourceType` (string, 50) - Type: 'lien' ou 'fichier'
- `ressourceFichier` (string, 255) - Nom du fichier uploadé
- `cours` (Cours) - Cours parent
- `quizzes` (Collection<Quiz>) - Quiz associés
- `ressourcesMultiples` (Collection<Ressource>) - Ressources multiples

**Validations:**
- Titre: 2-255 caractères, caractères alphanumériques + accents
- Contenu: obligatoire, max 10000 caractères
- Ordre: nombre positif obligatoire
- Ressources: max 255 caractères

**Relations:**
- ManyToOne avec Cours (obligatoire)
- OneToMany avec Quiz (cascade persist/remove)
- OneToMany avec Ressource (cascade persist/remove)

---

#### 🌐 Entité: ChapitreTraduction
**Fichier:** `src/Entity/GestionDeCours/ChapitreTraduction.php`

**Propriétés:**
- `id` (int) - Identifiant unique
- `chapitre` (Chapitre) - Chapitre source
- `langue` (string, 5) - Code langue (fr, en, es, de, it)
- `titreTraduit` (string, 500) - Titre traduit
- `contenuTraduit` (text) - Contenu traduit
- `createdAt` (DateTimeImmutable) - Date de création

**Index:**
- Index composite sur (chapitre_id, langue) pour optimisation

**Relations:**
- ManyToOne avec Chapitre (cascade delete)

---

#### 📎 Entité: Ressource
**Fichier:** `src/Entity/GestionDeCours/Ressource.php`

**Propriétés:**
- `id` (int) - Identifiant unique
- `titre` (string, 255) - Titre de la ressource
- `type` (string, 50) - Type de ressource
- `lien` (string, 500) - URL externe (optionnel)
- `fichier` (string, 255) - Nom du fichier (optionnel)
- `chapitre` (Chapitre) - Chapitre parent
- `createdAt` (DateTimeImmutable) - Date de création

**Types de ressources supportés:**
- `lien_image` - Lien vers image externe
- `upload_image` - Image uploadée
- `lien_video` - Lien vers vidéo (YouTube, Vimeo)
- `upload_video` - Vidéo uploadée
- `pdf` - Document PDF
- `document` - Autre document (DOCX, PPTX, etc.)

**Validations:**
- Titre: obligatoire
- Type: obligatoire, choix limité
- Lien ou Fichier: au moins un des deux requis

**Relations:**
- ManyToOne avec Chapitre (cascade delete)

---

## 🗄️ BASE DE DONNÉES

### Table: cours
```sql
CREATE TABLE cours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description LONGTEXT NOT NULL,
    matiere VARCHAR(255) NOT NULL,
    niveau VARCHAR(50) NOT NULL,
    duree INT NOT NULL,
    created_at DATETIME NOT NULL,
    communaute_id INT DEFAULT NULL,
    UNIQUE INDEX UNIQ_FDCA8C9CC903E5B8 (communaute_id),
    CONSTRAINT FK_FDCA8C9CC903E5B8 
        FOREIGN KEY (communaute_id) 
        REFERENCES communaute (id) 
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Table: chapitre
```sql
CREATE TABLE chapitre (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    contenu LONGTEXT NOT NULL,
    ordre INT NOT NULL,
    ressources VARCHAR(255) DEFAULT NULL,
    ressource_type VARCHAR(50) DEFAULT NULL,
    ressource_fichier VARCHAR(255) DEFAULT NULL,
    cours_id INT NOT NULL,
    INDEX IDX_8C62B0257ECF78B0 (cours_id),
    CONSTRAINT FK_8C62B0257ECF78B0 
        FOREIGN KEY (cours_id) 
        REFERENCES cours (id) 
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Table: chapitre_traduction
```sql
CREATE TABLE chapitre_traduction (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chapitre_id INT NOT NULL,
    langue VARCHAR(5) NOT NULL,
    titre_traduit VARCHAR(500) NOT NULL,
    contenu_traduit LONGTEXT NOT NULL,
    created_at DATETIME NOT NULL,
    INDEX IDX_A3FB62CB1FBEEF7B (chapitre_id),
    INDEX idx_chapitre_langue (chapitre_id, langue),
    CONSTRAINT FK_A3FB62CB1FBEEF7B 
        FOREIGN KEY (chapitre_id) 
        REFERENCES chapitre (id) 
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Table: ressource
```sql
CREATE TABLE ressource (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL,
    lien VARCHAR(500) DEFAULT NULL,
    fichier VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NOT NULL,
    chapitre_id INT NOT NULL,
    INDEX IDX_939F45441FBEEF7B (chapitre_id),
    CONSTRAINT FK_939F45441FBEEF7B 
        FOREIGN KEY (chapitre_id) 
        REFERENCES chapitre (id) 
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## 🔧 REPOSITORIES

### CoursRepository
**Fichier:** `src/Repository/Cours/CoursRepository.php`

**Méthodes disponibles:**
- `findAll()` - Récupère tous les cours
- `find($id)` - Récupère un cours par ID
- `findBy(['matiere' => 'X'])` - Filtre par matière
- `findBy(['niveau' => 'X'])` - Filtre par niveau

### ChapitreRepository
**Fichier:** `src/Repository/Cours/ChapitreRepository.php`

**Méthodes disponibles:**
- `findAll()` - Récupère tous les chapitres
- `find($id)` - Récupère un chapitre par ID
- `findBy(['cours' => $cours], ['ordre' => 'ASC'])` - Chapitres d'un cours triés

### ChapitreTraductionRepository
**Fichier:** `src/Repository/Cours/ChapitreTraductionRepository.php`

**Méthodes personnalisées:**
- `findOneBy(['chapitre' => $chapitre, 'langue' => $lang])` - Traduction en cache
- `deleteOldTranslations()` - Nettoie les traductions de +30 jours

---

## 🎨 FORMULAIRES

### CoursType
**Fichier:** `src/Form/GestionCours/CoursType.php`

**Champs:**
- titre (TextType)
- description (TextareaType)
- matiere (TextType)
- niveau (TextType)
- duree (IntegerType)
- createdAt (DateTimeType)

### ChapitreType
**Fichier:** `src/Form/GestionCours/ChapitreType.php`

**Champs:**
- titre (TextType)
- contenu (TextareaType)
- ordre (IntegerType)
- ressourceType (ChoiceType) - Aucune/Lien/Fichier
- ressources (TextType) - URL externe
- ressourceFichierUpload (FileType) - Upload fichier
- cours (EntityType) - Sélection du cours parent

**Contraintes upload:**
- Taille max: 100 MB
- Types acceptés: PDF, PPTX, DOCX, ZIP, RAR, MP4, AVI, MOV

---

## 🎮 CONTRÔLEURS

### 1. CoursController (Backoffice)
**Fichier:** `src/Controller/CoursController.php`
**Préfixe route:** `/cours`

#### Routes CRUD Cours
- `GET /cours` - Liste des cours
- `GET /cours/new` - Formulaire création
- `POST /cours/new` - Création cours
- `GET /cours/{id}` - Détail cours
- `GET /cours/{id}/edit` - Formulaire édition
- `POST /cours/{id}/edit` - Mise à jour cours
- `POST /cours/{id}/delete` - Suppression cours

#### Routes Gestion Chapitres
- `GET /cours/{id}/chapitres` - Liste chapitres du cours
- `GET /cours/{id}/chapitres/new` - Formulaire création chapitre
- `POST /cours/{id}/chapitres/new` - Création chapitre
- `GET /cours/{coursId}/chapitres/{id}` - Détail chapitre
- `GET /cours/{coursId}/chapitres/{id}/edit` - Formulaire édition chapitre
- `POST /cours/{coursId}/chapitres/{id}/edit` - Mise à jour chapitre
- `POST /cours/{coursId}/chapitres/{id}/delete` - Suppression chapitre

#### Routes Gestion Quiz
- `GET /cours/{coursId}/chapitres/{chapitreId}/quizzes` - Liste quiz
- `GET /cours/{coursId}/chapitres/{chapitreId}/quizzes/new` - Création quiz
- `GET /cours/{coursId}/chapitres/{chapitreId}/quizzes/{id}` - Détail quiz
- `GET /cours/{coursId}/chapitres/{chapitreId}/quizzes/{id}/edit` - Édition quiz
- `POST /cours/{coursId}/chapitres/{chapitreId}/quizzes/{id}/delete` - Suppression quiz

**Fonctionnalités spéciales:**
- Upload de fichiers ressources
- Gestion automatique des fichiers (suppression ancien fichier)
- Validation CSRF pour suppressions
- Vérification de cohérence (chapitre appartient au cours)


### 2. ChapitreController (Frontoffice)
**Fichier:** `src/Controller/ChapitreController.php`
**Préfixe route:** `/chapitre`

#### Routes Frontoffice
- `GET /chapitre/cours/{id}/chapitres` - Liste chapitres pour étudiants
- `GET /chapitre/front/{id}` - Affichage détaillé d'un chapitre
- `GET /chapitre/front/{id}/pdf` - Prévisualisation PDF
- `GET /chapitre/front/{id}/pdf/download` - Téléchargement PDF

**Fonctionnalités:**
- Affichage de la progression de l'étudiant
- Calcul automatique du pourcentage de complétion
- Génération PDF dynamique avec branding
- Slugification des noms de fichiers PDF

---

### 3. ChapitreApiController (API REST)
**Fichier:** `src/Controller/Api/ChapitreApiController.php`
**Préfixe route:** `/api/chapitres`

#### Endpoints API
- `GET /api/chapitres/{id}?lang={code}` - Récupération chapitre traduit

**Paramètres:**
- `id` (path) - ID du chapitre
- `lang` (query) - Code langue (fr, en, es, de, it)

**Réponse JSON:**
```json
{
  "id": 1,
  "titre": "Introduction to Python",
  "contenu": "Welcome to this chapter...",
  "ordre": 1,
  "langue": "en",
  "cached": true
}
```

**Fonctionnalités:**
- Validation des langues supportées
- Mise en cache des traductions
- Appel API LibreTranslate si pas en cache
- Gestion des erreurs (timeout, API indisponible)

---

## 🛠️ SERVICES MÉTIERS

### 1. CourseProgressService
**Fichier:** `src/Service/CourseProgressService.php`

**Responsabilités:**
- Calcul de la progression des étudiants
- Marquage des chapitres comme complétés
- Statistiques de progression par cours

**Méthodes principales:**

#### `calculateCourseProgress(User $user, Cours $cours): float`
Calcule le pourcentage de progression d'un étudiant dans un cours.
```php
Formule: (Chapitres complétés / Total chapitres) × 100
```

#### `markChapterAsCompleted(User $user, Chapitre $chapitre, int $quizScore): ChapterProgress`
Marque un chapitre comme complété après réussite du quiz.
- Crée ou met à jour l'enregistrement de progression
- Sauvegarde le score du quiz
- Met à jour la date de dernière activité de l'utilisateur

#### `isChapterCompleted(User $user, Chapitre $chapitre): bool`
Vérifie si un chapitre est complété par un utilisateur.

#### `getCompletedChapters(User $user, Cours $cours): array`
Récupère tous les chapitres complétés d'un cours pour un utilisateur.

#### `getCourseProgressStats(User $user, Cours $cours): array`
Récupère les statistiques complètes de progression.
```php
[
    'total_chapters' => 8,
    'completed_chapters' => 3,
    'remaining_chapters' => 5,
    'percentage' => 37.5,
    'is_completed' => false
]
```

#### `getAllCoursesProgress(User $user, array $courses): array`
Récupère la progression de tous les cours d'un utilisateur.

---

### 2. PdfGeneratorService
**Fichier:** `src/Service/PdfGeneratorService.php`

**Responsabilités:**
- Génération de PDF depuis templates Twig
- Configuration Dompdf
- Personnalisation des options PDF

**Méthodes principales:**

#### `generatePdf(string $template, array $data, array $options): Dompdf`
Génère un PDF depuis un template Twig.
```php
$pdf = $pdfGenerator->generatePdf('pdf/chapitre.html.twig', [
    'chapitre' => $chapitre
]);
```

**Options Dompdf:**
- `defaultFont`: 'DejaVu Sans'
- `isRemoteEnabled`: true (chargement images externes)
- `isHtml5ParserEnabled`: true
- `isFontSubsettingEnabled`: true

#### `generateChapterPdf(Chapitre $chapitre): Dompdf`
Méthode spécialisée pour générer le PDF d'un chapitre.

**Caractéristiques du PDF généré:**
- Header fixe avec logo Autolearn
- Footer avec pagination automatique
- Métadonnées du chapitre (ordre, cours, matière, niveau)
- Contenu HTML formaté
- Branding professionnel

---

### 3. TranslationService
**Fichier:** `src/Service/TranslationService.php`

**Responsabilités:**
- Traduction de texte via API externe
- Gestion du cache de traductions
- Fallback en cas d'erreur

**Méthodes principales:**

#### `translate(string $text, string $sourceLang, string $targetLang): ?string`
Traduit un texte d'une langue source vers une langue cible.
```php
$translated = $translationService->translate(
    'Bonjour le monde',
    'fr',
    'en'
);
// Résultat: "Hello world"
```

**Stratégie de traduction:**
1. Essai avec MyMemory API (gratuit)
2. Si échec, mode démo (préfixe langue)
3. Logging des erreurs

#### `isLanguageSupported(string $lang): bool`
Vérifie si une langue est supportée.

**Langues supportées:**
- fr (Français)
- en (English)
- es (Español)
- de (Deutsch)
- it (Italiano)
- pt (Português)
- ru (Русский)
- zh (中文)
- ja (日本語)
- ar (العربية)

**Configuration:**
- Timeout: 10 secondes
- API: MyMemory (gratuit, sans clé)
- Fallback: Mode démo avec préfixe

---

## 📊 FONCTIONNALITÉS AVANCÉES

### 1. Système de Progression
**Documentation:** `SYSTEME_PROGRESSION_FINAL.md`

**Workflow:**
1. Étudiant consulte un chapitre
2. Passe le quiz associé
3. Si score ≥ seuil (60%) → Chapitre validé
4. Progression mise à jour automatiquement
5. Barre de progression affichée

**Affichage:**
- Liste des chapitres: "3 of 8 completed - 37.5%"
- Vue détail chapitre: Barre compacte en haut
- Condition: Utilisateur connecté avec rôle étudiant

**Table:** `chapter_progress`
```sql
CREATE TABLE chapter_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    chapitre_id INT NOT NULL,
    completed_at DATETIME NOT NULL,
    quiz_score INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES user(id),
    FOREIGN KEY (chapitre_id) REFERENCES chapitre(id)
);
```

---

### 2. Traduction Dynamique
**Documentation:** `API_TRADUCTION_CHAPITRES.md`

**Workflow:**
1. Étudiant sélectionne une langue (dropdown)
2. Frontend appelle `/api/chapitres/{id}?lang=en`
3. Backend vérifie le cache
4. Si pas en cache → Appel API LibreTranslate
5. Sauvegarde en cache
6. Retour JSON traduit

**Optimisations:**
- Mise en cache en base de données
- Index composite (chapitre_id, langue)
- Vérification cache avant appel API
- Réponse immédiate pour français (langue source)

**Interface utilisateur:**
- Dropdown avec drapeaux
- Traduction en temps réel (AJAX)
- Indicateur de chargement
- Gestion des erreurs avec message

---

### 3. Génération PDF
**Documentation:** `GUIDE_GENERATION_PDF_CHAPITRES.md`

**Caractéristiques:**
- Génération dynamique depuis base de données
- Template Twig personnalisable
- Branding Autolearn (logo, couleurs)
- Header/Footer fixes
- Pagination automatique
- Support HTML/CSS avancé

**Structure du PDF:**
```
┌─────────────────────────────────────┐
│  [LOGO AUTOLEARN]                   │
│  Titre du Chapitre                  │
├─────────────────────────────────────┤
│  Métadonnées (ordre, cours, etc.)   │
│                                     │
│  Contenu formaté                    │
│  - Titres stylisés                  │
│  - Paragraphes justifiés            │
│  - Code avec coloration             │
│  - Listes à puces                   │
│                                     │
│  Branding Autolearn                 │
├─────────────────────────────────────┤
│  Footer - Page X                    │
└─────────────────────────────────────┘
```

**Modes d'accès:**
- Prévisualisation (inline dans navigateur)
- Téléchargement direct (attachment)
- Nom fichier: `chapitre-{ordre}-{titre-slug}.pdf`

---

### 4. Gestion des Ressources
**Types de ressources:**

#### Ressources simples (champ unique)
- Lien externe (Google Drive, YouTube)
- Fichier uploadé (PDF, PPTX, vidéo)

#### Ressources multiples (entité Ressource)
- Images (lien ou upload)
- Vidéos (lien ou upload)
- Documents (PDF, DOCX, PPTX)
- Archives (ZIP, RAR)

**Upload:**
- Dossier: `public/uploads/chapitres/`
- Taille max: 100 MB
- Validation MIME types
- Nommage sécurisé (translitération + uniqid)
- Suppression automatique ancien fichier

---

### 5. Intégration Quiz
**Relation:** Chapitre → Quiz (OneToMany)

**Workflow:**
1. Administrateur crée un quiz pour un chapitre
2. Étudiant consulte le chapitre
3. Accède au quiz depuis la page du chapitre
4. Répond aux questions
5. Soumet le quiz
6. Si validé → Chapitre marqué comme complété
7. Progression mise à jour

**Routes quiz:**
- Liste: `/cours/{coursId}/chapitres/{chapitreId}/quizzes`
- Création: `/cours/{coursId}/chapitres/{chapitreId}/quizzes/new`
- Détail: `/cours/{coursId}/chapitres/{chapitreId}/quizzes/{id}`
- Édition: `/cours/{coursId}/chapitres/{chapitreId}/quizzes/{id}/edit`
- Suppression: `/cours/{coursId}/chapitres/{chapitreId}/quizzes/{id}/delete`


## 🎨 INTERFACES UTILISATEUR

### BACKOFFICE (Administrateurs)

#### 1. Liste des Cours
**Template:** `templates/backoffice/cours/index.html.twig`
**Route:** `/cours`

**Fonctionnalités:**
- Tableau avec tous les cours
- Colonnes: Titre, Matière, Niveau, Durée, Actions
- Actions: Voir, Éditer, Supprimer, Gérer Chapitres
- Bouton "Créer un cours"

#### 2. Formulaire Cours
**Templates:** 
- `templates/backoffice/cours/new.html.twig`
- `templates/backoffice/cours/edit.html.twig`

**Champs:**
- Titre (input text)
- Description (textarea)
- Matière (input text)
- Niveau (input text)
- Durée en heures (input number)
- Date de création (date picker)

#### 3. Gestion des Chapitres
**Template:** `templates/backoffice/cours/chapitres.html.twig`
**Route:** `/cours/{id}/chapitres`

**Fonctionnalités:**
- Liste des chapitres du cours triés par ordre
- Affichage: Ordre, Titre, Ressources, Actions
- Actions: Voir, Éditer, Supprimer, Gérer Quiz
- Bouton "Ajouter un chapitre"

#### 4. Formulaire Chapitre
**Templates:**
- `templates/backoffice/cours/chapitre_new.html.twig`
- `templates/backoffice/cours/chapitre_edit.html.twig`

**Champs:**
- Titre (input text)
- Contenu (textarea riche / HTML)
- Ordre (input number)
- Type de ressource (radio: Aucune/Lien/Fichier)
- Lien ressource (input URL, conditionnel)
- Upload fichier (file input, conditionnel)
- Cours parent (select)

**Gestion conditionnelle:**
- Si "Lien" → Affiche champ URL
- Si "Fichier" → Affiche upload
- Si "Aucune" → Masque les deux

#### 5. Gestion des Quiz
**Template:** `templates/backoffice/cours/quizzes.html.twig`
**Route:** `/cours/{coursId}/chapitres/{chapitreId}/quizzes`

**Fonctionnalités:**
- Liste des quiz du chapitre
- Affichage: Titre, Nombre de questions, Seuil, Actions
- Actions: Voir, Éditer, Supprimer
- Bouton "Créer un quiz"

---

### FRONTOFFICE (Étudiants)

#### 1. Liste des Cours
**Template:** `templates/frontoffice/cours/index.html.twig`
**Route:** `/frontoffice`

**Fonctionnalités:**
- Cartes de cours avec image
- Informations: Titre, Description, Matière, Niveau, Durée
- Bouton "Voir les chapitres"
- Design moderne avec animations

#### 2. Liste des Chapitres
**Template:** `templates/frontoffice/chapitre/index.html.twig`
**Route:** `/chapitre/cours/{id}/chapitres`

**Fonctionnalités:**
- Barre de progression en haut
- Cartes de chapitres numérotées
- Affichage: Ordre, Titre, Extrait du contenu
- Icône de validation si chapitre complété
- Bouton "Lire le chapitre"
- Bouton "Télécharger PDF"
- Design avec animations au scroll

**Barre de progression:**
```
┌────────────────────────────────────────┐
│  Progression du cours                  │
│  3 of 8 completed - 37.5%              │
│  ████████░░░░░░░░░░░░░░░░░░░░░░░░░░   │
└────────────────────────────────────────┘
```

#### 3. Vue Détaillée Chapitre
**Template:** `templates/frontoffice/chapitre/show.html.twig`
**Route:** `/chapitre/front/{id}`

**Sections:**

**A. Header**
- Barre de progression compacte
- Numéro et titre du chapitre
- Informations du cours

**B. Contenu Principal**
- Contenu HTML formaté
- Support markdown/HTML
- Code avec coloration syntaxique
- Images, vidéos intégrées

**C. Ressources**
- Affichage des ressources attachées
- Liens externes cliquables
- Fichiers téléchargeables
- Icônes selon type de ressource

**D. Traduction**
- Dropdown sélection langue
- Drapeaux pour identification visuelle
- Traduction en temps réel (AJAX)
- Indicateur de chargement

**E. Téléchargement PDF**
- Section dédiée
- Bouton "Prévisualiser PDF"
- Bouton "Télécharger PDF"
- Description de la fonctionnalité

**F. Quiz**
- Lien vers le quiz du chapitre
- Affichage du score si déjà passé
- Bouton "Passer le quiz"

**G. Navigation**
- Bouton "Chapitre précédent"
- Bouton "Chapitre suivant"
- Bouton "Retour à la liste"

---

## 🎨 DESIGN ET STYLES

### CSS Personnalisés

#### 1. Chapitres Style
**Fichier:** `public/frontoffice/css/chapitres-style.css`

**Éléments stylisés:**
- Cartes de chapitres avec ombres
- Badges de numérotation
- Boutons avec dégradés
- Barre de progression animée
- Icônes de validation

**Couleurs principales:**
- Violet: #667eea
- Mauve: #764ba2
- Vert validation: #10b981
- Gris texte: #4b5563

#### 2. Animations
**Fichier:** `public/frontoffice/css/chapitres-animations.css`

**Animations:**
- Fade-in au scroll (Intersection Observer)
- Hover effects sur cartes
- Transition smooth sur progression
- Pulse sur badges de validation

#### 3. JavaScript Interactions
**Fichier:** `public/frontoffice/js/chapitres-interactions.js`

**Fonctionnalités:**
- Traduction AJAX en temps réel
- Animations au scroll
- Gestion du dropdown langue
- Indicateurs de chargement
- Gestion des erreurs

---

## 📦 BUNDLES ET DÉPENDANCES

### Bundles Symfony Utilisés

#### 1. DoctrineBundle
- ORM pour gestion base de données
- Migrations automatiques
- Relations entre entités

#### 2. TwigBundle
- Moteur de templates
- Génération HTML/PDF
- Filtres personnalisés

#### 3. FormBundle
- Génération de formulaires
- Validation automatique
- Gestion des uploads

#### 4. SecurityBundle
- Authentification utilisateurs
- Contrôle d'accès (ROLE_ADMIN, ROLE_ETUDIANT)
- Protection CSRF

#### 5. ValidatorBundle
- Validation des entités
- Contraintes personnalisées
- Messages d'erreur

### Packages Externes

#### 1. Dompdf
**Package:** `dompdf/dompdf`
**Version:** ^3.1
**Usage:** Génération de PDF

**Configuration:**
```php
$options = new Options();
$options->set('defaultFont', 'DejaVu Sans');
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);
```

#### 2. Guzzle HTTP Client
**Package:** `guzzlehttp/guzzle`
**Version:** ^7.10
**Usage:** Appels API externes (traduction)

#### 3. Symfony HTTP Client
**Package:** `symfony/http-client`
**Version:** 6.4.*
**Usage:** Alternative à Guzzle pour API

---

## 🔐 SÉCURITÉ

### 1. Contrôle d'Accès

#### Backoffice
- Accès réservé: `ROLE_ADMIN`
- Vérification sur toutes les routes `/cours`
- Protection CSRF sur formulaires
- Validation des données entrantes

#### Frontoffice
- Accès public pour consultation
- Progression: `ROLE_ETUDIANT` requis
- Quiz: Authentification obligatoire

### 2. Upload de Fichiers

**Validations:**
- Taille maximale: 100 MB
- Types MIME autorisés uniquement
- Nommage sécurisé (translitération)
- Stockage hors webroot si sensible

**Sécurisation:**
```php
$safeFilename = transliterator_transliterate(
    'Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
    $originalFilename
);
$newFilename = $safeFilename . '-' . uniqid() . '.' . $extension;
```

### 3. Validation des Données

**Entités:**
- Contraintes Assert sur toutes les propriétés
- Validation longueur, format, type
- Messages d'erreur personnalisés

**Formulaires:**
- Validation côté serveur obligatoire
- Protection XSS (échappement Twig)
- Validation CSRF tokens

### 4. Injection SQL

**Protection:**
- Utilisation exclusive de Doctrine ORM
- Requêtes préparées automatiques
- Pas de requêtes SQL brutes
- Paramètres bindés

---

## 🧪 TESTS ET VALIDATION

### Tests Manuels

#### Test 1: Création d'un Cours
```
1. Aller sur /cours
2. Cliquer "Créer un cours"
3. Remplir le formulaire
4. Soumettre
5. Vérifier redirection vers liste
6. Vérifier cours dans la liste
```

#### Test 2: Ajout de Chapitre
```
1. Depuis liste cours, cliquer "Chapitres"
2. Cliquer "Ajouter un chapitre"
3. Remplir titre, contenu, ordre
4. Choisir type ressource
5. Uploader fichier ou saisir lien
6. Soumettre
7. Vérifier chapitre dans la liste
```

#### Test 3: Progression Étudiant
```
1. Se connecter comme étudiant
2. Aller sur liste chapitres d'un cours
3. Vérifier barre: "0 of X completed - 0%"
4. Ouvrir un chapitre
5. Passer le quiz
6. Obtenir score ≥ 60%
7. Retour liste chapitres
8. Vérifier barre: "1 of X completed - Y%"
```

#### Test 4: Traduction
```
1. Ouvrir un chapitre
2. Sélectionner langue (ex: English)
3. Attendre chargement
4. Vérifier contenu traduit
5. Changer de langue
6. Vérifier nouvelle traduction
```

#### Test 5: Génération PDF
```
1. Ouvrir un chapitre
2. Cliquer "Prévisualiser PDF"
3. Vérifier ouverture nouvel onglet
4. Vérifier contenu PDF correct
5. Retour chapitre
6. Cliquer "Télécharger PDF"
7. Vérifier téléchargement fichier
```

### Commandes de Test

#### Test Progression
```bash
php bin/console app:test-progress {userId} {chapitreId} {score}
```

#### Charger Cours de Test
```bash
php bin/console doctrine:fixtures:load --group=java
php bin/console doctrine:fixtures:load --group=web
```

---

## 📚 DATA FIXTURES

### JavaCourseFixtures
**Fichier:** `src/DataFixtures/JavaCourseFixtures.php`
**Groupe:** `java`

**Contenu:**
- 1 cours Java complet
- 8 chapitres ordonnés
- Contenu pédagogique réel
- Ressources attachées

**Chargement:**
```bash
php bin/console doctrine:fixtures:load --group=java --append
```

### WebDevelopmentFixtures
**Fichier:** `src/DataFixtures/WebDevelopmentFixtures.php`
**Groupe:** `web`

**Contenu:**
- 1 cours Développement Web
- 10 chapitres (HTML, CSS, JS, etc.)
- Exemples de code
- Ressources multimédias

**Chargement:**
```bash
php bin/console doctrine:fixtures:load --group=web --append
```

### PythonCourseFixtures
**Fichier:** `insert_python_course.sql`
**Type:** SQL direct

**Contenu:**
- 1 cours Python Programming
- 8 chapitres avec contenu riche
- Exemples de code Python
- Exercices pratiques

**Chargement:**
```bash
mysql -u root -p autolearn < insert_python_course.sql
```


## 🚀 WORKFLOW COMPLET

### Workflow Administrateur

```
1. CRÉATION D'UN COURS
   ↓
   Aller sur /cours
   ↓
   Cliquer "Créer un cours"
   ↓
   Remplir: Titre, Description, Matière, Niveau, Durée
   ↓
   Soumettre le formulaire
   ↓
   Cours créé et visible dans la liste

2. AJOUT DE CHAPITRES
   ↓
   Depuis liste cours, cliquer "Chapitres"
   ↓
   Cliquer "Ajouter un chapitre"
   ↓
   Remplir: Titre, Contenu, Ordre
   ↓
   Choisir type de ressource (Lien/Fichier/Aucune)
   ↓
   Si Lien: Saisir URL (Google Drive, YouTube)
   Si Fichier: Uploader (PDF, PPTX, Vidéo, etc.)
   ↓
   Soumettre le formulaire
   ↓
   Chapitre créé et attaché au cours

3. CRÉATION DE QUIZ
   ↓
   Depuis liste chapitres, cliquer "Quiz"
   ↓
   Cliquer "Créer un quiz"
   ↓
   Remplir: Titre, Questions, Options, Réponses
   ↓
   Définir seuil de réussite (ex: 60%)
   ↓
   Soumettre le formulaire
   ↓
   Quiz créé et attaché au chapitre

4. PUBLICATION
   ↓
   Cours visible dans frontoffice
   ↓
   Étudiants peuvent consulter et progresser
```

### Workflow Étudiant

```
1. CONSULTATION DES COURS
   ↓
   Aller sur /frontoffice
   ↓
   Voir liste des cours disponibles
   ↓
   Cliquer "Voir les chapitres"

2. NAVIGATION DANS LES CHAPITRES
   ↓
   Voir liste des chapitres du cours
   ↓
   Barre de progression affichée: "0 of 8 - 0%"
   ↓
   Cliquer sur un chapitre

3. LECTURE DU CHAPITRE
   ↓
   Contenu affiché avec formatage
   ↓
   Ressources disponibles (liens, fichiers)
   ↓
   Option de traduction (dropdown langues)
   ↓
   Option de téléchargement PDF

4. PASSAGE DU QUIZ
   ↓
   Cliquer "Passer le quiz"
   ↓
   Répondre aux questions
   ↓
   Soumettre les réponses
   ↓
   Calcul du score automatique

5. VALIDATION ET PROGRESSION
   ↓
   Si score ≥ 60% → Chapitre validé
   ↓
   Enregistrement dans chapter_progress
   ↓
   Mise à jour de la barre de progression
   ↓
   Retour liste: "1 of 8 - 12.5%"

6. CONTINUATION
   ↓
   Passer au chapitre suivant
   ↓
   Répéter le processus
   ↓
   Progression augmente progressivement
   ↓
   Cours complété à 100%
```

---

## 📈 STATISTIQUES ET MÉTRIQUES

### Métriques Cours

**Par cours:**
- Nombre total de chapitres
- Durée totale estimée
- Nombre d'étudiants inscrits
- Taux de complétion moyen
- Note moyenne des quiz

**Requête exemple:**
```php
$stats = [
    'total_chapters' => $cours->getChapitres()->count(),
    'duration' => $cours->getDuree(),
    'enrolled_students' => $enrollmentRepository->countByCourse($cours),
    'avg_completion' => $progressService->getAverageCompletion($cours),
    'avg_quiz_score' => $quizRepository->getAverageScore($cours)
];
```

### Métriques Étudiant

**Par étudiant:**
- Nombre de cours suivis
- Nombre de chapitres complétés
- Taux de réussite aux quiz
- Temps moyen par chapitre
- Cours en cours / terminés

**Requête exemple:**
```php
$stats = [
    'courses_enrolled' => $enrollmentRepository->countByUser($user),
    'chapters_completed' => $progressRepository->countByUser($user),
    'quiz_success_rate' => $quizRepository->getSuccessRate($user),
    'avg_time_per_chapter' => $activityRepository->getAverageTime($user),
    'courses_completed' => $progressService->getCompletedCourses($user)
];
```

---

## 🔄 MIGRATIONS

### Migrations Principales

#### Version20260218210953
**Date:** 18 février 2026
**Description:** Création initiale des tables cours et chapitre

**Tables créées:**
- `cours` (id, titre, description, matiere, niveau, duree, created_at)
- `chapitre` (id, titre, contenu, ordre, ressources, ressource_type, ressource_fichier, cours_id)

**Relations:**
- chapitre.cours_id → cours.id (CASCADE)
- quiz.chapitre_id → chapitre.id

#### Version20260219220022
**Date:** 19 février 2026
**Description:** Ajout traductions et ressources multiples

**Tables créées:**
- `chapitre_traduction` (id, chapitre_id, langue, titre_traduit, contenu_traduit, created_at)
- `ressource` (id, titre, type, lien, fichier, created_at, chapitre_id)

**Index:**
- idx_chapitre_langue sur (chapitre_id, langue)

**Relations:**
- cours.communaute_id → communaute.id (SET NULL)

#### Version20260221112129
**Date:** 21 février 2026
**Description:** Ajout système de progression

**Modifications:**
- Ajout contraintes sur chapter_progress
- Relation avec chapitre et user

**Commande:**
```bash
php bin/console doctrine:migrations:migrate
```

---

## 📖 DOCUMENTATION ASSOCIÉE

### Documents Créés

1. **SYSTEME_PROGRESSION_FINAL.md**
   - Guide complet du système de progression
   - Workflow automatique
   - Tests et validation

2. **API_TRADUCTION_CHAPITRES.md**
   - Documentation API REST
   - Endpoints et paramètres
   - Gestion du cache

3. **GUIDE_GENERATION_PDF_CHAPITRES.md**
   - Configuration Dompdf
   - Personnalisation templates
   - Exemples d'utilisation

4. **GUIDE_FIXTURES_JAVA.md**
   - Chargement cours Java
   - Structure des données
   - Commandes

5. **GUIDE_FIXTURES_WEB.md**
   - Chargement cours Web
   - Contenu des chapitres
   - Ressources

6. **CHARGER_COURS_JAVA.md**
   - Instructions détaillées
   - Vérifications
   - Dépannage

7. **CHARGER_TOUS_LES_COURS.md**
   - Chargement multiple
   - Ordre recommandé
   - Validation

8. **INDEX_DOCUMENTATION_PDF.md**
   - Index de toute la documentation PDF
   - Liens rapides
   - Organisation

9. **PERSONNALISATION_PDF_EXEMPLES.md**
   - Exemples de personnalisation
   - Modifications CSS
   - Ajout d'éléments

---

## 🛠️ MAINTENANCE ET ÉVOLUTION

### Tâches de Maintenance

#### Nettoyage des Traductions
```php
// Supprimer traductions de plus de 30 jours
$repository = $entityManager->getRepository(ChapitreTraduction::class);
$repository->deleteOldTranslations();
```

#### Nettoyage des Fichiers Orphelins
```php
// Supprimer fichiers sans référence en base
$uploadsDir = $this->getParameter('uploads_dir') . '/chapitres';
$files = scandir($uploadsDir);
foreach ($files as $file) {
    if (!$chapitreRepository->findByFilename($file)) {
        unlink($uploadsDir . '/' . $file);
    }
}
```

#### Optimisation Base de Données
```sql
-- Analyser tables
ANALYZE TABLE cours, chapitre, chapitre_traduction, ressource;

-- Optimiser tables
OPTIMIZE TABLE cours, chapitre, chapitre_traduction, ressource;

-- Vérifier index
SHOW INDEX FROM chapitre_traduction;
```

### Évolutions Futures

#### 1. Gamification
- Badges de progression (25%, 50%, 75%, 100%)
- Points par chapitre complété
- Classement des étudiants
- Récompenses et achievements

#### 2. Statistiques Avancées
- Graphiques de progression
- Temps moyen par chapitre
- Taux d'abandon par chapitre
- Comparaison avec moyenne classe

#### 3. Notifications
- Alerte nouveau chapitre disponible
- Rappel cours non terminé
- Félicitations pour jalons (50%, 100%)
- Notification quiz disponible

#### 4. Certificats
- Génération automatique à 100%
- PDF personnalisé avec nom étudiant
- QR code de vérification
- Envoi par email

#### 5. Collaboration
- Commentaires sur chapitres
- Questions/Réponses entre étudiants
- Forum par cours
- Partage de notes

#### 6. Accessibilité
- Mode sombre
- Taille de police ajustable
- Lecteur d'écran optimisé
- Sous-titres pour vidéos

#### 7. Mobile
- Application mobile native
- Téléchargement offline
- Synchronisation progression
- Notifications push

#### 8. Analytics
- Temps passé par chapitre
- Taux de réussite par question
- Chapitres les plus difficiles
- Parcours d'apprentissage

---

## 🎯 POINTS CLÉS DU MODULE

### ✅ Fonctionnalités Implémentées

1. **CRUD Complet**
   - Création, lecture, mise à jour, suppression
   - Cours et chapitres
   - Interface backoffice intuitive

2. **Gestion des Ressources**
   - Upload de fichiers (PDF, PPTX, vidéos)
   - Liens externes (Google Drive, YouTube)
   - Validation et sécurisation

3. **Système de Progression**
   - Calcul automatique du pourcentage
   - Barre de progression visuelle
   - Persistance en base de données

4. **Traduction Dynamique**
   - API REST pour traduction
   - Cache en base de données
   - Support 5+ langues

5. **Génération PDF**
   - PDF dynamique depuis base
   - Branding professionnel
   - Prévisualisation et téléchargement

6. **Intégration Quiz**
   - Quiz par chapitre
   - Validation automatique
   - Mise à jour progression

7. **Interface Moderne**
   - Design responsive
   - Animations fluides
   - UX optimisée

8. **Sécurité**
   - Contrôle d'accès par rôle
   - Validation des données
   - Protection CSRF

### 🎨 Technologies Utilisées

**Backend:**
- Symfony 6.4
- Doctrine ORM
- PHP 8.1+

**Frontend:**
- Twig templates
- CSS3 (animations, gradients)
- JavaScript vanilla (AJAX)

**Packages:**
- Dompdf (génération PDF)
- Guzzle (appels API)
- Symfony HTTP Client

**Base de données:**
- MySQL 8.0
- Index optimisés
- Relations CASCADE

### 📊 Métriques du Module

**Entités:** 4 (Cours, Chapitre, ChapitreTraduction, Ressource)
**Contrôleurs:** 3 (CoursController, ChapitreController, ChapitreApiController)
**Services:** 3 (CourseProgressService, PdfGeneratorService, TranslationService)
**Repositories:** 4 (CoursRepository, ChapitreRepository, ChapitreTraductionRepository, RessourceRepository)
**Templates:** 15+ (backoffice + frontoffice)
**Routes:** 25+ (CRUD + fonctionnalités avancées)
**Migrations:** 3 principales
**Fixtures:** 3 (Java, Web, Python)

---

## 🎓 CONCLUSION

Le module de gestion de cours est un système complet et robuste qui permet:

✅ **Aux administrateurs:**
- Créer et gérer des cours structurés
- Organiser le contenu en chapitres
- Attacher des ressources multimédias
- Créer des quiz d'évaluation
- Suivre la progression des étudiants

✅ **Aux étudiants:**
- Consulter des cours de qualité
- Progresser à leur rythme
- Valider leurs connaissances par quiz
- Télécharger le contenu en PDF
- Traduire dans leur langue

✅ **Au système:**
- Architecture modulaire et extensible
- Code maintenable et documenté
- Performance optimisée
- Sécurité renforcée
- Évolutivité garantie

**Le module est prêt pour la production et peut être étendu avec de nouvelles fonctionnalités selon les besoins.**

---

## 📞 SUPPORT ET RESSOURCES

### Documentation Technique
- Symfony Docs: https://symfony.com/doc/current/
- Doctrine ORM: https://www.doctrine-project.org/
- Dompdf: https://github.com/dompdf/dompdf
- Twig: https://twig.symfony.com/

### Fichiers de Configuration
- `config/packages/doctrine.yaml` - Configuration ORM
- `config/routes.yaml` - Routes globales
- `config/services.yaml` - Services et paramètres
- `.env` - Variables d'environnement

### Commandes Utiles
```bash
# Créer une entité
php bin/console make:entity

# Générer migration
php bin/console make:migration

# Exécuter migrations
php bin/console doctrine:migrations:migrate

# Charger fixtures
php bin/console doctrine:fixtures:load

# Vider cache
php bin/console cache:clear

# Lister routes
php bin/console debug:router

# Vérifier services
php bin/console debug:container
```

---

**Date de création:** 22 février 2026  
**Version:** 1.0.0  
**Statut:** ✅ Production Ready  
**Auteur:** Équipe de développement Autolearn

