# Validations Côté Serveur

## Vue d'ensemble

Toutes les validations sont effectuées **côté serveur uniquement** via les contraintes Symfony Validator. Aucune validation HTML5 ou JavaScript n'est utilisée.

## Entité Quiz

### Champ: `titre`
- ✅ **NotBlank**: Le titre est obligatoire
- ✅ **Length**: 
  - Minimum: 3 caractères
  - Maximum: 255 caractères
- ✅ **Regex**: Accepte uniquement lettres, chiffres, espaces et ponctuation de base (français inclus)
  - Pattern: `/^[a-zA-Z0-9àâäéèêëïîôùûüÿçÀÂÄÉÈÊËÏÎÔÙÛÜŸÇ\s\-',.!?]+$/u`

**Messages d'erreur:**
- "Le titre du quiz est obligatoire."
- "Le titre doit contenir au moins 3 caractères."
- "Le titre ne peut pas dépasser 255 caractères."
- "Le titre contient des caractères non autorisés."

### Champ: `description`
- ✅ **NotBlank**: La description est obligatoire
- ✅ **Length**: 
  - Minimum: 10 caractères
  - Maximum: 2000 caractères

**Messages d'erreur:**
- "La description est obligatoire."
- "La description doit contenir au moins 10 caractères."
- "La description ne peut pas dépasser 2000 caractères."

### Champ: `etat`
- ✅ **NotBlank**: L'état est obligatoire
- ✅ **Choice**: Valeurs autorisées uniquement
  - Choix: `actif`, `inactif`, `brouillon`, `archive`

**Messages d'erreur:**
- "L'état du quiz est obligatoire."
- "L'état doit être: actif, inactif, brouillon ou archive."

---

## Entité Question

### Champ: `texteQuestion`
- ✅ **NotBlank**: Le texte est obligatoire
- ✅ **Length**: 
  - Minimum: 10 caractères
  - Maximum: 1000 caractères

**Messages d'erreur:**
- "Le texte de la question est obligatoire."
- "La question doit contenir au moins 10 caractères."
- "La question ne peut pas dépasser 1000 caractères."

### Champ: `point`
- ✅ **NotNull**: Le nombre de points est obligatoire
- ✅ **Positive**: Doit être un nombre positif
- ✅ **Range**: 
  - Minimum: 1
  - Maximum: 100
- ✅ **Type**: Doit être un entier (integer)

**Messages d'erreur:**
- "Le nombre de points est obligatoire."
- "Le nombre de points doit être positif."
- "Le nombre de points doit être entre 1 et 100."
- "Le nombre de points doit être un nombre entier."

### Champ: `quiz` (relation)
- ✅ **NotNull**: La question doit être associée à un quiz

**Messages d'erreur:**
- "La question doit être associée à un quiz."

---

## Entité Option

### Champ: `texteOption`
- ✅ **NotBlank**: Le texte est obligatoire
- ✅ **Length**: 
  - Minimum: 1 caractère
  - Maximum: 255 caractères

**Messages d'erreur:**
- "Le texte de l'option est obligatoire."
- "L'option doit contenir au moins 1 caractère."
- "L'option ne peut pas dépasser 255 caractères."

### Champ: `estCorrecte`
- ✅ **NotNull**: La valeur est obligatoire
- ✅ **Type**: Doit être un booléen (true/false)

**Messages d'erreur:**
- "Vous devez préciser si l'option est correcte ou non."
- "La valeur doit être un booléen (vrai ou faux)."

### Champ: `question` (relation)
- ✅ **NotNull**: L'option doit être associée à une question

**Messages d'erreur:**
- "L'option doit être associée à une question."

---

## Validateurs Personnalisés

### AtLeastOneCorrectOption (Question)
- ✅ Vérifie qu'une question a au moins une option correcte
- S'applique au niveau de la classe Question
- Validé uniquement si la question a des options

**Message d'erreur:**
- "Une question doit avoir au moins une option correcte."

---

## Configuration des Formulaires

### QuizType
```php
- titre: TextType avec maxlength=255
- description: TextareaType avec maxlength=2000
- etat: ChoiceType avec valeurs prédéfinies
```

### QuestionType
```php
- texteQuestion: TextareaType avec maxlength=1000
- point: IntegerType avec min=1, max=100
- quiz: EntityType (sélection d'un quiz existant)
```

### OptionType
```php
- texteOption: TextType avec maxlength=255
- estCorrecte: CheckboxType
- question: EntityType (sélection d'une question existante)
```

---

## Gestion des Erreurs dans les Contrôleurs

Les contrôleurs utilisent la validation automatique de Symfony:

```php
if ($form->isSubmitted() && $form->isValid()) {
    // Traitement uniquement si valide
    $entityManager->persist($entity);
    $entityManager->flush();
    return $this->redirectToRoute('...');
}
// Si non valide, le formulaire est réaffiché avec les erreurs
```

---

## Affichage des Erreurs

Les erreurs de validation sont automatiquement affichées dans les templates Twig via:
- `form_errors(form)` - Erreurs globales du formulaire
- `form_errors(form.field)` - Erreurs spécifiques à un champ
- Les messages sont en français et personnalisés

---

## Tests de Validation

### Pour tester les validations:

1. **Champs vides**: Essayez de soumettre un formulaire vide
2. **Longueurs invalides**: 
   - Titre avec 2 caractères
   - Description avec 5 caractères
   - Question avec 1000+ caractères
3. **Valeurs hors limites**:
   - Points: 0, -5, 101
4. **Caractères invalides**:
   - Titre avec des symboles spéciaux (@, #, $, etc.)
5. **Relations manquantes**:
   - Question sans quiz
   - Option sans question

---

## Sécurité

✅ **Aucune validation côté client** (HTML5/JavaScript désactivée)
✅ **Toutes les validations sont côté serveur**
✅ **Protection CSRF** activée sur tous les formulaires
✅ **Échappement automatique** des données dans Twig
✅ **Validation des types** (integer, boolean, string)
✅ **Validation des relations** (foreign keys)

---

## Notes Importantes

1. Les attributs HTML `maxlength`, `min`, `max` sont présents **uniquement pour l'UX** (aide visuelle), mais ne sont **pas utilisés pour la validation**
2. La validation réelle se fait **uniquement côté serveur** via les contraintes Symfony
3. Les messages d'erreur sont **tous en français**
4. Les validations sont **appliquées automatiquement** lors de `$form->isValid()`
