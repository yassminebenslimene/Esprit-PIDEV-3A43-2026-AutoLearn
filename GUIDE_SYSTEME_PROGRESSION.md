# 📊 Guide du Système de Progression des Étudiants

## 🎯 Vue d'ensemble

Le système de progression permet de suivre automatiquement l'avancement des étudiants dans chaque cours en fonction des chapitres validés via quiz.

## 🏗 Architecture

### Entités créées

1. **ChapterProgress** (`src/Entity/ChapterProgress.php`)
   - Stocke la validation d'un chapitre par un étudiant
   - Champs: `user`, `chapitre`, `completedAt`, `quizScore`
   - Contrainte unique: un étudiant ne peut valider un chapitre qu'une fois

2. **ChapterProgressRepository** (`src/Repository/ChapterProgressRepository.php`)
   - Méthodes pour interroger les progressions
   - Compte les chapitres complétés par cours
   - Vérifie si un chapitre est complété

3. **CourseProgressService** (`src/Service/CourseProgressService.php`)
   - Service métier principal
   - Calcule les pourcentages de progression
   - Marque les chapitres comme complétés

4. **ProgressExtension** (`src/Twig/ProgressExtension.php`)
   - Fonctions Twig pour afficher la progression
   - Facilite l'intégration dans les templates

## 📐 Formule de calcul

```
Progression (%) = (Chapitres validés / Total chapitres) × 100
```

## 💻 Utilisation dans le code

### 1. Marquer un chapitre comme complété

Après qu'un étudiant réussit un quiz:

```php
use App\Service\CourseProgressService;

class QuizController extends AbstractController
{
    public function __construct(
        private CourseProgressService $progressService
    ) {}

    #[Route('/quiz/{id}/submit', name: 'quiz_submit')]
    public function submitQuiz(Quiz $quiz, Request $request): Response
    {
        $user = $this->getUser();
        $score = $this->calculateScore($request); // Votre logique
        
        // Si le score est suffisant (ex: >= 60%)
        if ($score >= 60) {
            $chapitre = $quiz->getChapitre();
            $this->progressService->markChapterAsCompleted($user, $chapitre, $score);
            
            $this->addFlash('success', 'Chapitre validé ! 🎉');
        }
        
        return $this->redirectToRoute('cours_show', ['id' => $cours->getId()]);
    }
}
```

### 2. Afficher la progression dans un contrôleur

```php
use App\Service\CourseProgressService;

class CoursController extends AbstractController
{
    public function __construct(
        private CourseProgressService $progressService
    ) {}

    #[Route('/cours/{id}', name: 'cours_show')]
    public function show(Cours $cours): Response
    {
        $user = $this->getUser();
        
        // Récupérer les statistiques de progression
        $stats = $this->progressService->getCourseProgressStats($user, $cours);
        
        return $this->render('frontoffice/cours/show.html.twig', [
            'cours' => $cours,
            'progress_stats' => $stats,
        ]);
    }
}
```

### 3. Utiliser dans les templates Twig

#### Afficher le pourcentage simple

```twig
{% if app.user %}
    <div class="progress-info">
        Progression: {{ course_progress(app.user, cours) }}%
    </div>
{% endif %}
```

#### Afficher une barre de progression

```twig
{% if app.user %}
    {% set stats = course_progress_stats(app.user, cours) %}
    
    <div class="course-progress">
        <h4>Votre progression</h4>
        <div class="progress" style="height: 25px;">
            <div class="progress-bar bg-success" 
                 role="progressbar" 
                 style="width: {{ stats.percentage }}%"
                 aria-valuenow="{{ stats.percentage }}" 
                 aria-valuemin="0" 
                 aria-valuemax="100">
                {{ stats.percentage }}%
            </div>
        </div>
        <p class="mt-2">
            {{ stats.completed_chapters }} / {{ stats.total_chapters }} chapitres complétés
        </p>
    </div>
{% endif %}
```

#### Marquer les chapitres complétés

```twig
{% if app.user %}
    <ul class="chapter-list">
        {% for chapitre in cours.chapitres %}
            <li class="chapter-item {% if is_chapter_completed(app.user, chapitre) %}completed{% endif %}">
                <a href="{{ path('chapitre_show', {id: chapitre.id}) }}">
                    {% if is_chapter_completed(app.user, chapitre) %}
                        ✅
                    {% else %}
                        ⭕
                    {% endif %}
                    {{ chapitre.titre }}
                </a>
            </li>
        {% endfor %}
    </ul>
{% endif %}
```

## 🎨 Exemple d'interface complète

```twig
{# templates/frontoffice/cours/show.html.twig #}

{% extends 'frontoffice/base.html.twig' %}

{% block body %}
<div class="container mt-4">
    <h1>{{ cours.titre }}</h1>
    
    {% if app.user %}
        {% set stats = course_progress_stats(app.user, cours) %}
        
        {# Barre de progression #}
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">📊 Votre progression</h5>
                
                <div class="progress mb-3" style="height: 30px;">
                    <div class="progress-bar 
                                {% if stats.percentage >= 100 %}bg-success
                                {% elseif stats.percentage >= 50 %}bg-info
                                {% else %}bg-warning{% endif %}" 
                         role="progressbar" 
                         style="width: {{ stats.percentage }}%">
                        <strong>{{ stats.percentage }}%</strong>
                    </div>
                </div>
                
                <div class="row text-center">
                    <div class="col-md-4">
                        <h3>{{ stats.completed_chapters }}</h3>
                        <p class="text-muted">Complétés</p>
                    </div>
                    <div class="col-md-4">
                        <h3>{{ stats.remaining_chapters }}</h3>
                        <p class="text-muted">Restants</p>
                    </div>
                    <div class="col-md-4">
                        <h3>{{ stats.total_chapters }}</h3>
                        <p class="text-muted">Total</p>
                    </div>
                </div>
                
                {% if stats.is_completed %}
                    <div class="alert alert-success mt-3">
                        🎉 Félicitations ! Vous avez terminé ce cours !
                    </div>
                {% endif %}
            </div>
        </div>
        
        {# Liste des chapitres #}
        <div class="card">
            <div class="card-header">
                <h5>📚 Chapitres</h5>
            </div>
            <ul class="list-group list-group-flush">
                {% for chapitre in cours.chapitres|sort((a, b) => a.ordre <=> b.ordre) %}
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            {% if is_chapter_completed(app.user, chapitre) %}
                                <span class="badge bg-success me-2">✓</span>
                            {% else %}
                                <span class="badge bg-secondary me-2">○</span>
                            {% endif %}
                            <a href="{{ path('chapitre_show', {id: chapitre.id}) }}">
                                {{ chapitre.ordre }}. {{ chapitre.titre }}
                            </a>
                        </div>
                        {% if is_chapter_completed(app.user, chapitre) %}
                            <span class="badge bg-success rounded-pill">Validé</span>
                        {% endif %}
                    </li>
                {% endfor %}
            </ul>
        </div>
    {% else %}
        <div class="alert alert-info">
            Connectez-vous pour suivre votre progression dans ce cours.
        </div>
    {% endif %}
</div>
{% endblock %}
```

## 🎮 Workflow complet

1. **Étudiant consulte un chapitre**
   - Affichage du contenu
   - Bouton "Passer le quiz"

2. **Étudiant passe le quiz**
   - Soumission des réponses
   - Calcul du score

3. **Si score >= seuil (ex: 60%)**
   - Appel à `markChapterAsCompleted()`
   - Enregistrement dans `chapter_progress`
   - Message de félicitations

4. **Affichage du cours**
   - Calcul automatique du pourcentage
   - Mise à jour de la barre de progression
   - Marquage visuel des chapitres complétés

## 📊 Statistiques disponibles

Le service retourne un tableau avec:

```php
[
    'total_chapters' => 10,           // Nombre total de chapitres
    'completed_chapters' => 4,        // Chapitres validés
    'remaining_chapters' => 6,        // Chapitres restants
    'percentage' => 40.0,             // Pourcentage (float)
    'is_completed' => false           // Cours terminé ?
]
```

## 🔧 Méthodes du service

### CourseProgressService

- `calculateCourseProgress(User, Cours): float` - Calcule le %
- `markChapterAsCompleted(User, Chapitre, int): ChapterProgress` - Valide un chapitre
- `isChapterCompleted(User, Chapitre): bool` - Vérifie si complété
- `getCompletedChapters(User, Cours): array` - Liste des chapitres validés
- `getCourseProgressStats(User, Cours): array` - Statistiques complètes
- `getAllCoursesProgress(User, array): array` - Progression de tous les cours

## 🎨 CSS suggéré

```css
/* Barre de progression personnalisée */
.course-progress .progress {
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.course-progress .progress-bar {
    font-weight: bold;
    transition: width 0.6s ease;
}

/* Liste des chapitres */
.chapter-item.completed {
    background-color: #d4edda;
}

.chapter-item.completed a {
    color: #155724;
    font-weight: 500;
}

/* Badge de validation */
.badge.bg-success {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}
```

## 🚀 Prochaines étapes possibles

1. **Gamification**
   - Badges pour 25%, 50%, 75%, 100%
   - Système de points
   - Classement des étudiants

2. **Streak system**
   - Jours consécutifs d'apprentissage
   - Bonus de motivation

3. **Certificats**
   - Génération automatique à 100%
   - PDF téléchargeable

4. **Analytics**
   - Temps moyen par chapitre
   - Taux de réussite aux quiz
   - Chapitres les plus difficiles

## ✅ Checklist d'intégration

- [x] Entité ChapterProgress créée
- [x] Repository créé
- [x] Service métier créé
- [x] Extension Twig créée
- [x] Migration exécutée
- [ ] Intégrer dans QuizController
- [ ] Mettre à jour les templates de cours
- [ ] Mettre à jour les templates de chapitres
- [ ] Ajouter le CSS
- [ ] Tester avec un utilisateur

## 📝 Notes importantes

- La progression est calculée **dynamiquement** (pas stockée en base)
- Un chapitre ne peut être validé qu'**une seule fois** par étudiant
- Le score du quiz est **conservé** pour référence
- Le système fonctionne uniquement pour les **utilisateurs connectés**
