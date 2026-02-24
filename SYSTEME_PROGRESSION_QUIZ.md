# 📊 SYSTÈME DE PROGRESSION PAR QUIZ

## 🎯 PRINCIPE DE FONCTIONNEMENT

Le système de progression est basé sur la **validation des quiz**. Un chapitre n'est considéré comme complété que lorsque l'étudiant réussit le quiz associé.

### Règle de validation
- **Quiz réussi** = Score ≥ Seuil de réussite (défini par quiz, généralement 50%)
- **Chapitre validé** = Quiz réussi
- **Progression du cours** = (Chapitres validés / Total chapitres) × 100

---

## 📈 EXEMPLE CONCRET

### Cours: "Développement Web" (8 chapitres)

#### État Initial (0%)
```
Étudiant: Yasmine
Cours: Développement Web
Total chapitres: 8
Chapitres validés: 0
Progression: 0%

Chapitres:
├─ Chapitre 1: Introduction HTML ❌ (0%)
├─ Chapitre 2: CSS Basics ❌ (0%)
├─ Chapitre 3: JavaScript ❌ (0%)
├─ Chapitre 4: DOM Manipulation ❌ (0%)
├─ Chapitre 5: AJAX ❌ (0%)
├─ Chapitre 6: PHP Basics ❌ (0%)
├─ Chapitre 7: MySQL ❌ (0%)
└─ Chapitre 8: Projet Final ❌ (0%)
```

#### Après Quiz Chapitre 1 (12.5%)
```
Yasmine passe le quiz du Chapitre 1
Score obtenu: 85%
Seuil de réussite: 50%
Résultat: ✅ VALIDÉ

Progression: 1/8 = 12.5%
Couleur barre: 🔴 ROUGE (< 50%)

Chapitres:
├─ Chapitre 1: Introduction HTML ✅ (Score: 85%)
├─ Chapitre 2: CSS Basics ❌
├─ Chapitre 3: JavaScript ❌
├─ Chapitre 4: DOM Manipulation ❌
├─ Chapitre 5: AJAX ❌
├─ Chapitre 6: PHP Basics ❌
├─ Chapitre 7: MySQL ❌
└─ Chapitre 8: Projet Final ❌
```

#### Après Quiz Chapitres 1-4 (50%)
```
Chapitres validés: 4/8
Progression: 50%
Couleur barre: 🟠 ORANGE (50% - 80%)

Chapitres:
├─ Chapitre 1: Introduction HTML ✅ (85%)
├─ Chapitre 2: CSS Basics ✅ (92%)
├─ Chapitre 3: JavaScript ✅ (78%)
├─ Chapitre 4: DOM Manipulation ✅ (88%)
├─ Chapitre 5: AJAX ❌
├─ Chapitre 6: PHP Basics ❌
├─ Chapitre 7: MySQL ❌
└─ Chapitre 8: Projet Final ❌
```

#### Après Quiz Chapitres 1-7 (87.5%)
```
Chapitres validés: 7/8
Progression: 87.5%
Couleur barre: 🟢 VERT (> 80%)

Chapitres:
├─ Chapitre 1: Introduction HTML ✅ (85%)
├─ Chapitre 2: CSS Basics ✅ (92%)
├─ Chapitre 3: JavaScript ✅ (78%)
├─ Chapitre 4: DOM Manipulation ✅ (88%)
├─ Chapitre 5: AJAX ✅ (95%)
├─ Chapitre 6: PHP Basics ✅ (82%)
├─ Chapitre 7: MySQL ✅ (90%)
└─ Chapitre 8: Projet Final ❌
```

#### Cours Complété (100%)
```
Chapitres validés: 8/8
Progression: 100%
Couleur barre: 🟢 VERT
Statut: ✅ COURS TERMINÉ

Tous les chapitres validés!
```

---

## 🎨 SYSTÈME DE COULEURS

### Barres de Progression

| Pourcentage | Couleur | Signification | Gradient CSS |
|-------------|---------|---------------|--------------|
| **< 50%** | 🔴 Rouge | Début du cours | `#e74c3c → #c0392b` |
| **50% - 80%** | 🟠 Orange | Bon progrès | `#f39c12 → #e67e22` |
| **> 80%** | 🟢 Vert | Presque terminé | `#27ae60 → #229954` |

### Badges de Statut Chapitre

| Statut | Badge | Couleur |
|--------|-------|---------|
| **Complété** | ✅ Complété | Vert |
| **Non complété** | ⭕ À faire | Gris transparent |

---

## 🔧 ARCHITECTURE TECHNIQUE

### 1. Backend (PHP/Symfony)

#### Service: `CourseProgressService`
```php
// Marquer un chapitre comme complété après quiz réussi
markChapterAsCompleted(User $user, Chapitre $chapitre, int $quizScore)

// Calculer la progression d'un cours
calculateCourseProgress(User $user, Cours $cours): float

// Vérifier si un chapitre est complété
isChapterCompleted(User $user, Chapitre $chapitre): bool

// Obtenir les statistiques de progression
getCourseProgressStats(User $user, Cours $cours): array
```

#### Controller: `QuizPassageController`
```php
// Après soumission du quiz
if ($statut === 'VALIDÉ' && $quiz->getChapitre()) {
    $this->progressService->markChapterAsCompleted(
        $etudiant,
        $quiz->getChapitre(),
        (int) $result['percentage']
    );
}
```

### 2. Base de Données

#### Table: `chapter_progress`
```sql
CREATE TABLE chapter_progress (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    chapitre_id INT NOT NULL,
    completed_at DATETIME NOT NULL,
    quiz_score INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES user(id),
    FOREIGN KEY (chapitre_id) REFERENCES chapitre(id)
);
```

### 3. Frontend (Twig)

#### Composant: `progress_bar.html.twig`
```twig
{% include 'components/progress_bar.html.twig' with {
    'percentage': 75.5,
    'show_label': true,
    'completed': 6,
    'total': 8
} %}
```

#### CSS: `progress-bar.css`
- Classes: `.progress-danger`, `.progress-warning`, `.progress-success`
- Animations: `fillProgress`
- Badges: `.chapter-status-badge`

---

## 📍 EMPLACEMENTS D'AFFICHAGE

### 1. Page d'accueil (`frontoffice/index.html.twig`)
- ✅ Barre de progression sur chaque carte de cours
- ✅ Affichage: "X/Y chapitres complétés"
- ✅ Couleur dynamique selon pourcentage

### 2. Liste des chapitres (`frontoffice/chapitre/index.html.twig`)
- ✅ Carte de progression globale du cours
- ✅ Statistiques: Complétés / Restants / Total
- ✅ Badge "Complété" ou "À faire" sur chaque chapitre

### 3. Page résultat du quiz (`frontoffice/quiz/result.html.twig`)
- ✅ Message de validation du chapitre
- ✅ Mise à jour automatique de la progression

---

## 🚀 FLUX UTILISATEUR

```
1. Étudiant consulte la page d'accueil
   └─> Voit la progression de chaque cours (0% au début)

2. Étudiant clique sur un cours
   └─> Voit la liste des chapitres avec badges "À faire"

3. Étudiant lit un chapitre
   └─> Clique sur "Passer le quiz"

4. Étudiant passe le quiz
   └─> Soumet ses réponses

5. Système calcule le score
   ├─> Si score ≥ seuil: ✅ VALIDÉ
   │   ├─> Chapitre marqué comme complété
   │   ├─> Progression du cours augmente
   │   └─> Badge devient "Complété" ✅
   └─> Si score < seuil: ❌ ÉCHEC
       └─> Chapitre reste "À faire"

6. Étudiant retourne à la liste des chapitres
   └─> Voit la barre de progression mise à jour
   └─> Voit le badge "Complété" sur le chapitre validé
```

---

## 📊 DONNÉES RETOURNÉES

### `getCourseProgressStats()`
```php
[
    'total_chapters' => 8,
    'completed_chapters' => 3,
    'remaining_chapters' => 5,
    'percentage' => 37.5,
    'is_completed' => false
]
```

### `getAllCoursesProgress()`
```php
[
    1 => ['percentage' => 37.5, 'completed_chapters' => 3, ...],
    2 => ['percentage' => 100, 'completed_chapters' => 10, ...],
    3 => ['percentage' => 0, 'completed_chapters' => 0, ...]
]
```

---

## ✅ FONCTIONNALITÉS IMPLÉMENTÉES

- ✅ Calcul automatique de la progression par cours
- ✅ Marquage des chapitres complétés après quiz réussi
- ✅ Barres de progression avec couleurs dynamiques (Rouge/Orange/Vert)
- ✅ Badges de statut sur chaque chapitre (Complété/À faire)
- ✅ Statistiques détaillées (Complétés/Restants/Total)
- ✅ Affichage sur page d'accueil (liste des cours)
- ✅ Affichage sur liste des chapitres
- ✅ Mise à jour automatique après validation de quiz
- ✅ Composant Twig réutilisable pour les barres
- ✅ CSS moderne avec animations

---

## 🎓 RÉSUMÉ

Le système de progression est **entièrement fonctionnel** et basé sur la validation des quiz:

1. **Étudiant commence**: Progression = 0%
2. **Étudiant réussit un quiz**: Chapitre validé → Progression augmente
3. **Barre de couleur change**: Rouge → Orange → Vert
4. **Badges mis à jour**: "À faire" → "Complété" ✅
5. **Cours terminé**: 100% = Tous les chapitres validés

Le système est **automatique**, **visuel** et **motivant** pour les étudiants!
