# Module Quiz - Frontoffice

## Description
Ce module permet aux utilisateurs du frontoffice de consulter et de répondre aux quiz créés par les administrateurs dans le backoffice.

## Fonctionnalités

### 1. Liste des Quiz par Chapitre
- **Route**: `/chapitre/{chapitreId}/quiz`
- **Méthode**: GET
- **Description**: Affiche tous les quiz actifs associés à un chapitre spécifique
- **Template**: `templates/frontoffice/quiz/list.html.twig`

### 2. Affichage d'un Quiz
- **Route**: `/chapitre/{chapitreId}/quiz/{id}`
- **Méthode**: GET
- **Description**: Affiche les questions et options d'un quiz spécifique
- **Template**: `templates/frontoffice/quiz/show.html.twig`

### 3. Soumission des Réponses
- **Route**: `/chapitre/{chapitreId}/quiz/{id}/submit`
- **Méthode**: POST
- **Description**: Traite les réponses de l'utilisateur et calcule le score
- **Template**: `templates/frontoffice/quiz/result.html.twig`

## Structure des Fichiers

```
src/Controller/FrontOffice/
└── QuizController.php          # Contrôleur gérant les quiz

templates/frontoffice/quiz/
├── list.html.twig              # Liste des quiz d'un chapitre
├── show.html.twig              # Affichage d'un quiz avec questions
└── result.html.twig            # Résultats après soumission
```

## Utilisation

### Pour l'Utilisateur

1. **Accéder aux quiz**:
   - Depuis la page des chapitres, cliquer sur le bouton "Quiz" d'un chapitre
   - Vous serez redirigé vers la liste des quiz disponibles pour ce chapitre

2. **Passer un quiz**:
   - Cliquer sur "Commencer le quiz"
   - Répondre à toutes les questions en sélectionnant une option
   - Cliquer sur "Soumettre mes réponses"

3. **Consulter les résultats**:
   - Voir votre score total et pourcentage
   - Consulter les détails de chaque question
   - Les bonnes réponses sont affichées en vert
   - Vos réponses incorrectes sont affichées en rouge

### Pour l'Administrateur

1. **Créer un quiz dans le backoffice**:
   - Créer un quiz et l'associer à un chapitre
   - Définir l'état du quiz comme "actif"
   - Ajouter des questions avec leurs points
   - Ajouter des options pour chaque question
   - Marquer la bonne réponse pour chaque question

2. **Seuls les quiz avec l'état "actif" sont visibles dans le frontoffice**

## Calcul du Score

- Chaque question a un nombre de points défini
- Si l'utilisateur sélectionne la bonne option, il obtient les points de la question
- Le score total est la somme des points obtenus
- Le pourcentage est calculé: (score obtenu / total des points) × 100

## Validation

- L'utilisateur doit répondre à toutes les questions avant de soumettre
- Une seule option peut être sélectionnée par question
- Les quiz inactifs ne sont pas affichés dans le frontoffice

## Améliorations Futures Possibles

- Enregistrer les résultats des utilisateurs en base de données
- Afficher l'historique des tentatives
- Ajouter un timer pour limiter le temps de réponse
- Permettre plusieurs tentatives avec suivi du meilleur score
- Ajouter des statistiques pour les administrateurs
