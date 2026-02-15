# 📋 Explication des Contrôles de Saisie

## 🎯 Vue d'ensemble

Dans Symfony, les contrôles de saisie (validation) se font à **3 niveaux** :

1. **Entités** (`src/Entity/*.php`) - Validation côté serveur avec annotations
2. **Formulaires** (`src/Form/*Type.php`) - Configuration des champs et contraintes HTML
3. **Contrôleurs** (`src/Controller/*.php`) - Vérification de la soumission et validation

---

## 1️⃣ Niveau 1 : Entités (Validation Serveur) ⭐ PRINCIPAL

### 📁 Emplacement : `src/Entity/*.php`

C'est ici que se trouvent **les contrôles de saisie les plus importants**.

### Exemple : `src/Entity/Quiz.php`

```php
use Symfony\Component\Validator\Constraints as Assert;

class Quiz
{
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le titre du quiz est obligatoire.")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Le titre doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères."
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z0-9àâäéèêëïîôùûüÿçÀÂÄÉÈÊËÏÎÔÙÛÜŸÇ\s\-',.!?]+$/u",
        message: "Le titre contient des caractères non autorisés."
    )]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    #[Assert\Length(
        min: 10,
        max: 2000,
        minMessage: "La description doit contenir au moins {{ limit }} caractères.",
        maxMessage: "La description ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "L'état du quiz est obligatoire.")]
    #[Assert\Choice(
        choices: ['actif', 'inactif', 'brouillon', 'archive'],
        message: "L'état doit être: actif, inactif, brouillon ou archive."
    )]
    private ?string $etat = null;
}
```

### 📝 Contraintes disponibles dans les Entités

| Contrainte | Description | Exemple |
|------------|-------------|---------|
| `@Assert\NotBlank` | Champ obligatoire (non vide) | `message: "Le titre est obligatoire"` |
| `@Assert\NotNull` | Valeur non nulle | `message: "Le champ ne peut pas être null"` |
| `@Assert\Length` | Longueur min/max | `min: 3, max: 255` |
| `@Assert\Regex` | Expression régulière | `pattern: "/^[a-zA-Z]+$/"` |
| `@Assert\Choice` | Valeur parmi une liste | `choices: ['actif', 'inactif']` |
| `@Assert\Positive` | Nombre positif | `message: "Doit être positif"` |
| `@Assert\Range` | Plage de valeurs | `min: 1, max: 100` |
| `@Assert\Type` | Type de données | `type: 'integer'` |
| `@Assert\Email` | Format email | `message: "Email invalide"` |
| `@Assert\Url` | Format URL | `message: "URL invalide"` |

### 📂 Vos fichiers d'entités avec validation

```
src/Entity/
├── Quiz.php           ✅ Validation complète (titre, description, état)
├── Question.php       ✅ Validation complète (texte, points 1-100)
├── Chapitre.php       ✅ Validation complète (titre, contenu, ordre)
├── Cours.php          ✅ Validation (titre, matière, niveau)
├── Challenge.php      ⚠️  Validation basique
├── Exercice.php       ⚠️  Validation basique
├── Post.php           ⚠️  Validation basique
├── Communaute.php     ⚠️  Validation basique
└── User.php           ✅ Validation (email, password)
```

---

## 2️⃣ Niveau 2 : Formulaires (Configuration HTML)

### 📁 Emplacement : `src/Form/*Type.php`

Les formulaires configurent les champs et ajoutent des contraintes HTML5.

### Exemple : `src/Form/QuizType.php`

```php
class QuizType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre du quiz',
                'attr' => [
                    'placeholder' => 'Entrez le titre du quiz',
                    'maxlength' => 255  // ← Contrainte HTML5
                ],
                'required' => true,  // ← Contrainte HTML5
                'help' => 'Entre 3 et 255 caractères'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Décrivez le contenu du quiz',
                    'rows' => 5,
                    'maxlength' => 2000  // ← Contrainte HTML5
                ],
                'required' => true,
                'help' => 'Entre 10 et 2000 caractères'
            ])
            ->add('etat', ChoiceType::class, [
                'label' => 'État',
                'choices' => [
                    'Actif' => 'actif',
                    'Inactif' => 'inactif',
                    'Brouillon' => 'brouillon',
                    'Archivé' => 'archive'
                ],
                'required' => true,
                'help' => 'Définissez le statut du quiz'
            ])
        ;
    }
}
```

### 📝 Options de validation dans les formulaires

| Option | Description | Effet |
|--------|-------------|-------|
| `'required' => true` | Champ obligatoire | Ajoute `required` HTML5 |
| `'attr' => ['maxlength' => 255]` | Longueur max | Limite la saisie dans le navigateur |
| `'attr' => ['min' => 1, 'max' => 100]` | Plage numérique | Contrainte HTML5 pour nombres |
| `'attr' => ['pattern' => '...']` | Regex HTML5 | Validation côté client |
| `'choices' => [...]` | Liste de choix | Limite les valeurs possibles |

### 📂 Vos fichiers de formulaires

```
src/Form/
├── QuizType.php           ✅ Configuration complète
├── QuestionType.php       ✅ Configuration complète
├── ChapitreType.php       ✅ Configuration complète
├── CoursType.php          ✅ Configuration complète
├── ChallengeType.php      ⚠️  À vérifier
├── ExerciceType.php       ⚠️  À vérifier
├── PostType.php           ⚠️  À vérifier
└── CommunauteType.php     ⚠️  À vérifier
```

---

## 3️⃣ Niveau 3 : Contrôleurs (Vérification)

### 📁 Emplacement : `src/Controller/*.php`

Les contrôleurs vérifient si le formulaire est soumis et valide.

### Exemple : `src/Controller/CoursController.php`

```php
public function newQuiz(Request $request, ...): Response
{
    $quiz = new Quiz();
    $quiz->setChapitre($chapitre);
    
    $form = $this->createForm(QuizType::class, $quiz);
    $form->handleRequest($request);  // ← Récupère les données

    if ($form->isSubmitted()) {  // ← Vérifie si soumis
        if ($form->isValid()) {  // ← Vérifie la validation
            // ✅ Données valides
            $entityManager->persist($quiz);
            $entityManager->flush();
            
            $this->addFlash('success', 'Le quiz a été créé avec succès.');
            return $this->redirectToRoute('...');
        } else {
            // ❌ Données invalides
            $this->addFlash('error', 'Le formulaire contient des erreurs.');
        }
    }

    return $this->render('...', [
        'form' => $form,
    ]);
}
```

### 📝 Méthodes de validation dans les contrôleurs

| Méthode | Description |
|---------|-------------|
| `$form->handleRequest($request)` | Récupère les données POST |
| `$form->isSubmitted()` | Vérifie si le formulaire a été soumis |
| `$form->isValid()` | Vérifie toutes les contraintes de l'entité |
| `$this->addFlash('success', '...')` | Message de succès |
| `$this->addFlash('error', '...')` | Message d'erreur |

---

## 🎨 Affichage des Erreurs dans les Templates

### 📁 Emplacement : `templates/backoffice/cours/quiz_new.html.twig`

```twig
{{ form_start(form) }}
    {# Erreurs globales du formulaire #}
    {{ form_errors(form) }}
    
    <div class="form-group">
        {{ form_label(form.titre) }}
        {{ form_widget(form.titre, {'attr': {'class': 'form-control'}}) }}
        
        {# Erreurs spécifiques au champ #}
        {{ form_errors(form.titre) }}
        
        {# Message d'aide #}
        {% if form.titre.vars.help %}
            <small class="form-help">{{ form.titre.vars.help }}</small>
        {% endif %}
    </div>
{{ form_end(form) }}
```

### 📝 Fonctions Twig pour les erreurs

| Fonction | Description |
|----------|-------------|
| `{{ form_errors(form) }}` | Affiche toutes les erreurs du formulaire |
| `{{ form_errors(form.titre) }}` | Affiche les erreurs d'un champ spécifique |
| `{{ form.titre.vars.help }}` | Affiche le message d'aide |

---

## 📊 Flux de Validation Complet

```
┌─────────────────────────────────────────────────────────────┐
│ 1. UTILISATEUR remplit le formulaire                        │
└─────────────────────┬───────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────┐
│ 2. NAVIGATEUR vérifie (HTML5)                               │
│    - required, maxlength, pattern, min, max                 │
│    ✅ Si OK → Envoie au serveur                             │
│    ❌ Si KO → Affiche erreur navigateur                     │
└─────────────────────┬───────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────┐
│ 3. CONTRÔLEUR reçoit les données                            │
│    - $form->handleRequest($request)                         │
│    - $form->isSubmitted() → true                            │
└─────────────────────┬───────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────┐
│ 4. SYMFONY valide avec les contraintes de l'ENTITÉ          │
│    - @Assert\NotBlank, @Assert\Length, @Assert\Regex, etc.  │
│    - $form->isValid() → true ou false                       │
└─────────────────────┬───────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────┐
│ 5. RÉSULTAT                                                  │
│    ✅ Si valide → Enregistre en base de données             │
│    ❌ Si invalide → Affiche les erreurs dans le template    │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔍 Exemples Concrets dans Votre Projet

### Exemple 1 : Quiz

**Entité** (`src/Entity/Quiz.php`)
```php
#[Assert\NotBlank(message: "Le titre du quiz est obligatoire.")]
#[Assert\Length(min: 3, max: 255)]
private ?string $titre = null;
```

**Formulaire** (`src/Form/QuizType.php`)
```php
->add('titre', TextType::class, [
    'attr' => ['maxlength' => 255],
    'required' => true,
])
```

**Contrôleur** (`src/Controller/CoursController.php`)
```php
if ($form->isSubmitted() && $form->isValid()) {
    $entityManager->persist($quiz);
    $entityManager->flush();
}
```

### Exemple 2 : Question

**Entité** (`src/Entity/Question.php`)
```php
#[Assert\Range(min: 1, max: 100)]
#[Assert\Positive]
private ?int $point = null;
```

**Formulaire** (`src/Form/QuestionType.php`)
```php
->add('point', IntegerType::class, [
    'attr' => ['min' => 1, 'max' => 100],
])
```

### Exemple 3 : Chapitre

**Entité** (`src/Entity/Chapitre.php`)
```php
#[Assert\Regex(
    pattern: '/^[a-zA-Z0-9\s\-_\'éèêëàâäîïôöûüç]+$/',
    message: 'Le titre contient des caractères non autorisés.'
)]
private ?string $titre = null;
```

---

## 📚 Résumé

| Niveau | Fichier | Rôle | Quand s'exécute |
|--------|---------|------|-----------------|
| **1. Entité** | `src/Entity/*.php` | Validation serveur (PRINCIPAL) | Lors de `$form->isValid()` |
| **2. Formulaire** | `src/Form/*Type.php` | Configuration HTML5 | Avant soumission (navigateur) |
| **3. Contrôleur** | `src/Controller/*.php` | Vérification et traitement | Après soumission |
| **4. Template** | `templates/**/*.html.twig` | Affichage des erreurs | Rendu de la page |

---

## ✅ Bonnes Pratiques

1. **Toujours valider dans l'entité** - C'est la source de vérité
2. **Ajouter des contraintes HTML5** - Améliore l'UX (feedback immédiat)
3. **Messages en français** - Plus clair pour les utilisateurs
4. **Afficher les erreurs** - Utiliser `{{ form_errors() }}` dans les templates
5. **Messages flash** - Confirmer les actions (succès/erreur)

---

**Date**: 11 février 2026  
**Version**: 1.0
