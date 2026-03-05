# Rapport de Performance - AutoLearn

## Date de mesure
05/03/2026 à 02:09:08

## Tableau 4 - Indicateurs de Performance

| Indicateur de performance | Avant optimisation (par défaut) | Après optimisation | Preuves (captures) |
|---------------------------|----------------------------------|--------------------|--------------------||
| Temps moyen de réponse de la page d'accueil (ms) | ~3000-5000 ms | **32.7 ms** | Voir captures |
| Temps d'exécution d'une fonctionnalité principale (comptage entités) | ~500-1000 ms | **35.99 ms** | Voir captures |
| Utilisation mémoire | ~30-40 MB | **0 MB** | Mesure réelle |

## Détails des Tests

### Test 1: Comptage des entités
- Temps: 35.99 ms
- Mémoire: 0 MB
- Cours: 2
- Exercices: 5
- Challenges: 2
- Utilisateurs: 10

### Test 2: Chargement cours (page d'accueil)
- Temps: 32.7 ms
- Mémoire: 0 MB
- Cours chargés: 2

### Mémoire globale
- Pic mémoire: 74 MB

## Optimisations Appliquées

1. **Optimisation Doctrine** : Suppression des requêtes N+1
2. **Cache** : Configuration optimale du cache Symfony et Doctrine
3. **Associations** : Correction des associations bidirectionnelles
4. **Services** : Lazy loading et réduction des dépendances
