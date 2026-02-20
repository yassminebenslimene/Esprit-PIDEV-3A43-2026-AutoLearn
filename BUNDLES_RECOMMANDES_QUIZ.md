# 📦 Bundles Recommandés pour le Module Quiz

## 🎯 Top 3 Bundles à Installer Immédiatement

### 1. KnpPaginatorBundle - Pagination

**Installation:**
```bash
composer require knplabs/knp-paginator-bundle
```

**Configuration:** `config/packages/knp_paginator.yaml`
```yaml
knp_paginator:
    page_range: 5
    default_options:
        page_name: page
        sort_field_name: sort
        sort_direction_name: direction
        distinct: true
    template:
        pagination: '@KnpPaginator/Pagination/sliding.html.twig'
        sortable: '@KnpPaginator/Pagination/sortable_link.html.twig'
```

**Utilisation dans le contrôleur:**
```php
use Knp\Component\Pager\PaginatorInterface;

public function list(PaginatorInterface $paginator, Request $request): Response
{
    $query = $this->quizRepository->createQueryBuilder('q')
        ->orderBy('q.createdAt', 'DESC')
        ->getQuery();
    
    $pagination = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1),
        10 // Items par page
    );
    
    return $this->render('quiz/list.html.twig', [
        'pagination' => $pagination,
    ]);
}
```

**Dans le template:**
```twig
{% for quiz in pagination %}
    {# Afficher le quiz #}
{% endfor %}

{{ knp_pagination_render(pagination) }}
```

---

### 2. StofDoctrineExtensionsBundle - Extensions Doctrine

**Installation:**
```bash
composer require stof/doctrine-extensions-bundle
```

**Configuration:** `config/packages/stof_doctrine_extensions.yaml`
```yaml
stof_doctrine_extensions:
    default_locale: fr_FR
    orm:
        default:
            timestampable: true
            sluggable: true
            sortable: true
```

**Utilisation dans l'entité Quiz:**
```php
use Gedmo\Mapping\Annotation as Gedmo;

class Quiz
{
    /**
     * @Gedmo\Timestampable(on="create")
     */
    private ?\DateTimeInterface $createdAt = null;

    /**
     * @Gedmo\Timestampable(on="update")
     */
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * @Gedmo\Slug(fields={"titre"})
     */
    private ?string $slug = null;
}
```

**Utilisation dans l'entité Question:**
```php
use Gedmo\Mapping\Annotation as Gedmo;

class Question
{
    /**
     * @Gedmo\SortablePosition
     */
    private ?int $position = null;

    /**
     * @Gedmo\SortableGroup
     */
    private ?Quiz $quiz = null;
}
```

**Avantages:**
- Timestamps automatiques (plus besoin de les gérer manuellement)
- URLs SEO-friendly avec slugs
- Tri automatique des questions dans un quiz

---

### 3. Symfony UX Chartjs - Graphiques

**Installation:**
```bash
composer require symfony/ux-chartjs
```

**Utilisation dans le contrôleur:**
```php
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

public function statistics(ChartBuilderInterface $chartBuilder): Response
{
    // Statistiques de réussite par quiz
    $chart = $chartBuilder->createChart(Chart::TYPE_BAR);
    
    $chart->setData([
        'labels' => ['Quiz 1', 'Quiz 2', 'Quiz 3', 'Quiz 4'],
        'datasets' => [
            [
                'label' => 'Taux de réussite (%)',
                'backgroundColor' => ['#2ecc71', '#3498db', '#f39c12', '#e74c3c'],
                'data' => [85, 72, 65, 45],
            ],
        ],
    ]);

    $chart->setOptions([
        'scales' => [
            'y' => [
                'beginAtZero' => true,
                'max' => 100,
            ],
        ],
    ]);

    return $this->render('quiz/statistics.html.twig', [
        'chart' => $chart,
    ]);
}
```

**Dans le template:**
```twig
<div class="chart-container">
    {{ render_chart(chart) }}
</div>
```

---

## 🚀 Bundles Complémentaires (Phase 2)

### 4. VichUploaderBundle - Upload d'images

**Installation:**
```bash
composer require vich/uploader-bundle
```

**Cas d'usage:**
- Images dans les questions
- Illustrations pour les options
- Médias dans les explications

### 5. EasyAdminBundle - Interface d'administration

**Installation:**
```bash
composer require easycorp/easyadmin-bundle
```

**Cas d'usage:**
- Dashboard moderne pour gérer les quiz
- CRUD automatique amélioré
- Statistiques visuelles

### 6. PhpSpreadsheet - Import/Export Excel

**Installation:**
```bash
composer require phpoffice/phpspreadsheet
```

**Cas d'usage:**
- Import massif de questions depuis Excel
- Export des résultats en Excel
- Modèles de quiz Excel

---

## 📊 Bundles pour Fonctionnalités Avancées (Phase 3)

### 7. API Platform - API REST

**Installation:**
```bash
composer require api-platform/core
```

**Cas d'usage:**
- API pour application mobile
- Intégrations tierces
- Documentation automatique

### 8. KnpSnappyBundle - Génération PDF

**Installation:**
```bash
composer require knplabs/knp-snappy-bundle
```

**Cas d'usage:**
- Certificats de réussite PDF
- Rapports de résultats PDF
- Export de quiz en PDF

### 9. Symfony Notifier - Notifications

**Installation:**
```bash
composer require symfony/notifier
```

**Cas d'usage:**
- Email de résultats
- SMS pour résultats importants
- Notifications Slack/Discord

---

## 🎯 Plan d'Implémentation Recommandé

### Semaine 1 - Fondations
1. ✅ Installer KnpPaginatorBundle
2. ✅ Installer StofDoctrineExtensionsBundle
3. ✅ Ajouter timestamps et slugs aux entités

### Semaine 2 - Visualisation
4. ✅ Installer UX Chartjs
5. ✅ Créer page de statistiques
6. ✅ Graphiques de performance

### Semaine 3 - Médias
7. ✅ Installer VichUploaderBundle
8. ✅ Ajouter support images dans questions
9. ✅ Interface d'upload

### Semaine 4 - Administration
10. ✅ Installer EasyAdminBundle
11. ✅ Configurer dashboard
12. ✅ CRUD amélioré

---

## 💡 Conseils d'Utilisation

### Performance
- Utiliser le cache Symfony pour les quiz fréquemment consultés
- Paginer toutes les listes (quiz, résultats, utilisateurs)
- Optimiser les requêtes Doctrine avec QueryBuilder

### Sécurité
- Valider toutes les entrées utilisateur
- Utiliser les Voters pour les permissions
- Limiter le nombre de tentatives par IP

### UX
- Ajouter des loaders pendant les calculs
- Feedback visuel pour chaque action
- Messages d'erreur clairs et utiles

### Maintenance
- Documenter chaque bundle installé
- Tester après chaque installation
- Garder les bundles à jour

---

## 📚 Ressources

- [KnpPaginatorBundle](https://github.com/KnpLabs/KnpPaginatorBundle)
- [StofDoctrineExtensionsBundle](https://github.com/stof/StofDoctrineExtensionsBundle)
- [Symfony UX](https://ux.symfony.com/)
- [EasyAdmin](https://symfony.com/bundles/EasyAdminBundle/current/index.html)
- [API Platform](https://api-platform.com/)
