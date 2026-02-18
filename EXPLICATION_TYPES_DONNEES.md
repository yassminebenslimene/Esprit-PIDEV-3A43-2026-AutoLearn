# 📚 Explication des Types de Données en PHP

## 🎯 C'est quoi `string` ?

**`string`** = **Chaîne de caractères** = **Texte**

C'est un type de données qui représente du **texte** (lettres, chiffres, symboles).

---

## 📝 Types de Données en PHP

### 1. **string** (Chaîne de caractères / Texte)

```php
private ?string $titre = null;
```

**Exemples de valeurs** :
- `"Bonjour"`
- `"Cours de Mathématiques"`
- `"123"` (nombre sous forme de texte)
- `"user@example.com"`
- `""`  (chaîne vide)

**Utilisation** :
- Noms, titres, descriptions
- Emails, URLs
- Textes longs
- Tout ce qui contient des lettres

---

### 2. **int** (Integer / Nombre entier)

```php
private ?int $id = null;
private ?int $ordre = null;
private ?int $points = null;
```

**Exemples de valeurs** :
- `1`, `2`, `3`, `100`
- `-5`, `-10`
- `0`

**Utilisation** :
- IDs, compteurs
- Âges, quantités
- Points, scores
- Ordre, position

---

### 3. **float** (Nombre décimal)

```php
private ?float $prix = null;
private ?float $note = null;
```

**Exemples de valeurs** :
- `19.99`
- `3.14`
- `0.5`
- `-2.75`

**Utilisation** :
- Prix, montants
- Notes, moyennes
- Pourcentages
- Mesures précises

---

### 4. **bool** (Boolean / Vrai ou Faux)

```php
private ?bool $actif = null;
private ?bool $estPublie = null;
```

**Exemples de valeurs** :
- `true` (vrai)
- `false` (faux)

**Utilisation** :
- États on/off
- Validations oui/non
- Permissions accordées/refusées
- Visibilité publique/privée

---

### 5. **DateTime** (Date et Heure)

```php
private ?\DateTime $date_debut = null;
private ?\DateTimeImmutable $createdAt = null;
```

**Exemples de valeurs** :
- `2026-02-11 14:30:00`
- `2025-12-25`
- `new \DateTime('now')`

**Utilisation** :
- Dates de création
- Dates de début/fin
- Timestamps
- Anniversaires

---

### 6. **array** (Tableau)

```php
private array $tags = [];
private array $options = [];
```

**Exemples de valeurs** :
- `['rouge', 'vert', 'bleu']`
- `[1, 2, 3, 4, 5]`
- `['nom' => 'Dupont', 'age' => 25]`

**Utilisation** :
- Listes d'éléments
- Collections de données
- Options multiples

---

### 7. **Collection** (Collection Doctrine)

```php
private Collection $chapitres;
private Collection $quizzes;
```

**Exemples** :
- Collection de chapitres
- Collection de quiz
- Collection de posts

**Utilisation** :
- Relations OneToMany
- Listes d'objets liés

---

## 🔍 Le symbole `?` (Nullable)

### Sans `?` (NON nullable)
```php
private string $titre;  // ❌ Ne peut PAS être null
```
- Le champ DOIT avoir une valeur
- Obligatoire en base de données

### Avec `?` (Nullable)
```php
private ?string $titre = null;  // ✅ Peut être null
```
- Le champ PEUT être vide (null)
- Optionnel en base de données

---

## 📊 Exemples dans Votre Projet

### Entité Quiz

```php
class Quiz
{
    // ID (nombre entier, auto-généré)
    private ?int $id = null;

    // Titre (texte, obligatoire)
    private ?string $titre = null;

    // Description (texte long, obligatoire)
    private ?string $description = null;

    // État (texte court, obligatoire)
    private ?string $etat = null;

    // Relation (objet Chapitre, optionnel)
    private ?Chapitre $chapitre = null;

    // Collection de questions
    private Collection $questions;
}
```

### Entité Challenge

```php
class Challenge
{
    // ID (nombre entier)
    private ?int $id = null;

    // Titre (texte court)
    private ?string $titre = null;

    // Description (texte)
    private ?string $description = null;

    // Date de début (date/heure)
    private ?\DateTime $date_debut = null;

    // Date de fin (date/heure)
    private ?\DateTime $date_fin = null;

    // Niveau (texte court)
    private ?string $niveau = null;

    // Collection d'exercices
    private Collection $exercices;

    // Créateur (objet User)
    private ?User $created_by = null;
}
```

### Entité Question

```php
class Question
{
    // ID (nombre entier)
    private ?int $id = null;

    // Texte de la question (texte long)
    private ?string $texteQuestion = null;

    // Points (nombre entier)
    private ?int $point = null;

    // Quiz associé (objet Quiz)
    private ?Quiz $quiz = null;

    // Collection d'options
    private Collection $options;
}
```

---

## 🎨 Types de Champs dans les Formulaires

### Correspondance Type PHP → Type Formulaire

| Type PHP | Type Formulaire Symfony | Exemple |
|----------|------------------------|---------|
| `string` | `TextType::class` | Champ texte court |
| `string` (long) | `TextareaType::class` | Zone de texte |
| `int` | `IntegerType::class` | Nombre entier |
| `float` | `NumberType::class` | Nombre décimal |
| `bool` | `CheckboxType::class` | Case à cocher |
| `DateTime` | `DateTimeType::class` | Sélecteur date/heure |
| `string` (choix) | `ChoiceType::class` | Liste déroulante |
| Entité | `EntityType::class` | Sélection d'entité |

### Exemple : QuizType

```php
class QuizType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // string → TextType
            ->add('titre', TextType::class, [
                'label' => 'Titre du quiz',
            ])
            
            // string (long) → TextareaType
            ->add('description', TextareaType::class, [
                'label' => 'Description',
            ])
            
            // string (choix) → ChoiceType
            ->add('etat', ChoiceType::class, [
                'label' => 'État',
                'choices' => [
                    'Actif' => 'actif',
                    'Inactif' => 'inactif',
                ]
            ])
        ;
    }
}
```

---

## 🗄️ Types de Colonnes en Base de Données

### Correspondance Type PHP → Type MySQL

| Type PHP | Type MySQL | Exemple |
|----------|-----------|---------|
| `string` (court) | `VARCHAR(255)` | Titre, nom |
| `string` (long) | `TEXT` | Description, contenu |
| `int` | `INT` | ID, compteur |
| `float` | `DECIMAL` ou `FLOAT` | Prix, note |
| `bool` | `TINYINT(1)` | 0 ou 1 |
| `DateTime` | `DATETIME` | Date et heure |
| Relation | `INT` (clé étrangère) | `chapitre_id` |

### Exemple dans l'Entité

```php
class Quiz
{
    // VARCHAR(255) en base de données
    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    // TEXT en base de données
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    // VARCHAR(50) en base de données
    #[ORM\Column(length: 50)]
    private ?string $etat = null;

    // INT en base de données (clé étrangère)
    #[ORM\ManyToOne(inversedBy: 'quizzes')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Chapitre $chapitre = null;
}
```

---

## 💡 Pourquoi Typer les Variables ?

### Avantages du typage

1. **Sécurité** : Évite les erreurs de type
   ```php
   // ❌ Erreur détectée
   $quiz->setTitre(123);  // Attend un string, reçoit un int
   
   // ✅ Correct
   $quiz->setTitre("Mon Quiz");
   ```

2. **Autocomplétion** : L'IDE vous aide
   ```php
   $quiz->getTitre()->  // L'IDE propose les méthodes de string
   ```

3. **Documentation** : Le code est plus clair
   ```php
   // On sait que $titre est un texte
   private ?string $titre = null;
   ```

4. **Validation** : Symfony vérifie automatiquement
   ```php
   #[Assert\Type(type: 'string')]
   private ?string $titre = null;
   ```

---

## 📖 Résumé Simple

| Type | C'est quoi ? | Exemple |
|------|--------------|---------|
| **string** | Texte | `"Bonjour"` |
| **int** | Nombre entier | `42` |
| **float** | Nombre décimal | `19.99` |
| **bool** | Vrai/Faux | `true` ou `false` |
| **DateTime** | Date et heure | `2026-02-11` |
| **array** | Liste | `[1, 2, 3]` |
| **Collection** | Collection d'objets | Liste de Quiz |
| **?** | Peut être null | Optionnel |

---

## 🎯 Dans Votre Code

Quand vous voyez :
```php
private ?string $titre = null;
```

Cela signifie :
- `private` = Propriété privée (accessible uniquement dans la classe)
- `?` = Peut être null (optionnel)
- `string` = Type texte
- `$titre` = Nom de la variable
- `= null` = Valeur par défaut

---

**Date**: 11 février 2026  
**Version**: 1.0
