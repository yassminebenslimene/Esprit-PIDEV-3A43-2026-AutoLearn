# ✅ Ajout du Niveau de Difficulté au Générateur IA

## Nouvelle fonctionnalité

Le générateur IA de chapitres permet maintenant de choisir le niveau de difficulté du contenu généré.

## Niveaux disponibles

🟢 **Débutant**
- Explications simples et claires
- Exemples basiques
- Pas de jargon technique complexe
- Vocabulaire accessible

🟡 **Intermédiaire**
- Concepts plus avancés
- Exemples pratiques
- Terminologie technique appropriée
- Approfondissement des notions

🔴 **Avancé**
- Concepts complexes
- Cas d'usage avancés
- Optimisations et bonnes pratiques
- Niveau professionnel

## Comment utiliser

1. Cliquez sur **"🤖 Générer un Chapitre avec l'IA"**
2. Entrez le **titre du chapitre**
3. Choisissez le **niveau de difficulté** dans le menu déroulant
4. Cliquez sur **"Générer"**

## Exemple d'utilisation

### Titre: "Les jointures SQL"

**Niveau Débutant:**
- Explication simple de ce qu'est une jointure
- Exemples avec 2 tables simples
- Syntaxe de base

**Niveau Intermédiaire:**
- Types de jointures (INNER, LEFT, RIGHT, FULL)
- Exemples avec plusieurs tables
- Cas d'usage pratiques

**Niveau Avancé:**
- Optimisation des jointures
- Index et performance
- Jointures complexes avec sous-requêtes
- Bonnes pratiques en production

## Modifications techniques

### 1. Template (chapitres.html.twig)

Ajout d'un `<select>` dans le modal:
```html
<select id="chapterLevelSelect">
    <option value="debutant">🟢 Débutant</option>
    <option value="intermediaire">🟡 Intermédiaire</option>
    <option value="avance">🔴 Avancé</option>
</select>
```

### 2. JavaScript

Envoi du niveau dans la requête:
```javascript
body: JSON.stringify({
    chapterTitle: chapterTitle,
    chapterLevel: chapterLevel
})
```

### 3. Contrôleur (AIGeneratorController.php)

Récupération du niveau:
```php
$chapterLevel = $data['chapterLevel'] ?? 'debutant';
```

Passage au service:
```php
$result = $this->generatorService->generateChapter(
    $cours->getTitre(),
    $cours->getMatiere() ?? '',
    $cours->getNiveau() ?? '',
    $chapterTitle,
    $chapterLevel  // Nouveau paramètre
);
```

### 4. Service (CourseGeneratorService.php)

Nouveau paramètre dans la méthode:
```php
public function generateChapter(
    string $courseTitle, 
    string $subject = '', 
    string $level = '', 
    string $chapterTitle = '', 
    string $chapterLevel = 'debutant'  // Nouveau
): array
```

Adaptation du prompt selon le niveau:
```php
$levelMap = [
    'debutant' => 'Débutant',
    'intermediaire' => 'Intermédiaire',
    'avance' => 'Avancé'
];
$levelText = $levelMap[$chapterLevel] ?? 'Débutant';
```

Instructions spécifiques dans le prompt:
```
- Pour niveau Débutant: explications simples, exemples basiques
- Pour niveau Intermédiaire: concepts avancés, exemples pratiques
- Pour niveau Avancé: concepts complexes, optimisations, bonnes pratiques
```

## Fichiers modifiés

- ✅ `templates/backoffice/cours/chapitres.html.twig` - Ajout du select niveau
- ✅ `src/Controller/AIGeneratorController.php` - Récupération du niveau
- ✅ `src/Service/CourseGeneratorService.php` - Adaptation du prompt
- ✅ `public/test-ia-generation.html` - Page de test mise à jour

## Test

### Avec l'interface

1. Déconnectez-vous et reconnectez-vous (pour que le rôle ADMIN soit pris en compte)
2. Allez dans **Backoffice** → **Cours** → **Chapitres**
3. Testez avec différents niveaux:
   - "Introduction aux bases de données" → Débutant
   - "Les jointures SQL" → Intermédiaire
   - "Optimisation des requêtes complexes" → Avancé

### Avec la page de test

Ouvrez: `http://127.0.0.1:8000/test-ia-generation.html`

## Résultat attendu

L'IA génère maintenant du contenu adapté au niveau choisi:
- Vocabulaire approprié
- Complexité ajustée
- Exemples adaptés au niveau
- Profondeur d'explication variable

## Avantages

✅ Contenu personnalisé selon le public cible
✅ Progression pédagogique cohérente
✅ Flexibilité dans la création de cours
✅ Meilleure adaptation aux besoins des étudiants
