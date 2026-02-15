# Guide d'accès au Frontoffice

## Routes disponibles

Le frontoffice est accessible via deux routes :

### 1. Route principale (recommandée)
```
http://127.0.0.1:8000/
```
ou
```
http://localhost:8000/
```

### 2. Route alternative
```
http://127.0.0.1:8000/home
```
ou
```
http://localhost:8000/home
```

## Comportement selon le type d'utilisateur

### Si vous n'êtes PAS connecté
- Vous verrez la page d'accueil du frontoffice avec :
  - Les challenges disponibles
  - Les événements créés
  - Les équipes formées

### Si vous êtes connecté en tant qu'ADMIN
- Vous serez automatiquement redirigé vers `/backoffice`
- C'est un comportement normal pour les administrateurs

### Si vous êtes connecté en tant qu'ÉTUDIANT
- Vous verrez le frontoffice normalement
- Vous pourrez consulter les événements et équipes

## Contenu affiché dans le frontoffice

### Section "Events"
- Affiche tous les événements créés
- Pour chaque événement, vous verrez :
  - Titre
  - Date de début
  - Places disponibles (calculées automatiquement : nbMax - participations acceptées)
  - Bouton "Voir détails"

### Section "Team"
- Affiche toutes les équipes créées
- Pour chaque équipe, vous verrez :
  - Nom de l'équipe uniquement (sans les membres)

## Vérification rapide

Pour tester si le frontoffice fonctionne :

1. Assurez-vous que le serveur est démarré sur le port 8000
2. Ouvrez votre navigateur
3. Accédez à `http://127.0.0.1:8000/`
4. Vous devriez voir la page d'accueil avec le template Scholar

## En cas de problème

### Erreur "Variable does not exist"
- Vérifiez que le FrontofficeController passe bien les variables `evenements` et `equipes`
- Ces variables sont maintenant correctement configurées

### Redirection automatique vers /backoffice
- C'est normal si vous êtes connecté en tant qu'Admin
- Déconnectez-vous ou utilisez un compte étudiant pour voir le frontoffice

### Page blanche ou erreur 404
- Vérifiez que le serveur est bien démarré
- Vérifiez l'URL (doit être exactement `http://127.0.0.1:8000/`)
- Vérifiez les logs du serveur pour voir les erreurs

## Routes complètes du projet

- `/` → Frontoffice (page d'accueil)
- `/home` → Frontoffice (alternative)
- `/backoffice` → Backoffice (admin uniquement)
- `/backoffice/evenement` → Gestion des événements
- `/backoffice/equipe` → Gestion des équipes
- `/backoffice/participation` → Gestion des participations
- `/challenges` → Liste des challenges
