# Guide d'Intégration - Gestion des Chapitres

## Résumé des Changements

Ce projet intègre maintenant la gestion complète des chapitres dans le backoffice, avec une interface moderne pour le frontoffice.

## Étapes d'Intégration pour vos Collègues

### 1. Récupérer les Changements
```bash
git pull origin main
```

### 2. Installer les Dépendances
```bash
composer install
```

### 3. Configuration de la Base de Données

#### Option A: Nouvelle Installation (Recommandé)
```bash
# Supprimer la base de données existante
php bin/console doctrine:database:drop --force

# Créer une nouvelle base de données
php bin/console doctrine:database:create

# Créer le schéma
php bin/console doctrine:schema:create
```

#### Option B: Migration (Si vous avez des données importantes)
```bash
# Créer une nouvelle migration
php bin/console make:migration

# Exécuter la migration
php bin/console doctrine:migrations:migrate
```

### 4. Vider le Cache
```bash
php bin/console cache:clear
```

### 5. Démarrer le Serveur
```bash
symfony serve
```

## Fonctionnalités Ajoutées

### Backoffice
- **Gestion des Chapitres Intégrée dans Cours**
  - Accès: Cours → Bouton "Voir chapitres"
  - CRUD complet pour les chapitres
  - Chaque chapitre appartient à un cours spécifique
  - Menu "Chapitres" standalone supprimé

### Frontoffice
- **Affichage Moderne des Chapitres**
  - Design avec gradients et animations
  - Effets de parallaxe et scroll reveal
  - Barre de progression de lecture
  - Responsive design
  - Accessibilité (respect de prefers-reduced-motion)

## Structure des Routes

### Routes Backoffice (Cours)
- `GET /cours` - Liste des cours
- `GET /cours/{id}/chapitres` - Liste des chapitres d'un cours
- `GET /cours/{id}/chapitres/new` - Créer un chapitre
- `GET /cours/{coursId}/chapitres/{id}` - Voir un chapitre
- `GET /cours/{coursId}/chapitres/{id}/edit` - Modifier un chapitre
- `POST /cours/{coursId}/chapitres/{id}/delete` - Supprimer un chapitre

### Routes Frontoffice
- `GET /` - Page d'accueil (app_frontoffice)
- `GET /chapitres/` - Liste des chapitres
- `GET /chapitres/{id}` - Détail d'un chapitre

## Fichiers Modifiés

### Controllers
- `src/Controller/CoursController.php` - Gestion des chapitres intégrée
- `src/Controller/FrontOfficeController.php` - Routes frontoffice
- `src/Controller/ChapitreController.php` - Routes corrigées

### Templates
- `templates/backoffice/base.html.twig` - Menu mis à jour
- `templates/backoffice/cours/index.html.twig` - Bouton "Voir chapitres"
- `templates/backoffice/cours/chapitres.html.twig` - Liste des chapitres
- `templates/backoffice/cours/chapitre_*.html.twig` - CRUD chapitres

### Assets
- `public/frontoffice/css/chapitres-style.css` - Styles modernes
- `public/frontoffice/css/chapitres-animations.css` - Animations
- `public/frontoffice/js/chapitres-interactions.js` - Interactions JS

## Points Importants

### Base de Données
- La colonne primaire de `User` est `userId` (pas `id`)
- `Challenge.created_by` référence `User(userId)` explicitement
- Contrainte `Assert\NotNull` supprimée de `Chapitre.cours`

### Résolution de Conflits
Si vous rencontrez des conflits Git:
1. Résoudre les conflits dans les fichiers marqués
2. Vérifier particulièrement `templates/backoffice/base.html.twig` et `templates/frontoffice/index.html.twig`
3. Exécuter `php bin/console cache:clear`

### Conflits Résolus
- `templates/backoffice/base.html.twig` - Menu "Cours" ajouté dans la section "Gestion"
- `templates/frontoffice/index.html.twig` - Menu de navigation avec Login/Logout et Challenge
- `src/Controller/FrontOfficeController.php` - Ajout de la variable `challenges` dans les méthodes index() et home()

## Workflow Admin

1. Se connecter au backoffice
2. Aller dans "Cours"
3. Cliquer sur le bouton "Voir chapitres" à côté d'un cours
4. Gérer les chapitres (Créer, Modifier, Supprimer)

## Dépannage

### Erreur "Route does not exist"
```bash
php bin/console cache:clear
php bin/console debug:router
```

### Erreur de Base de Données
```bash
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:schema:create
```

### Erreur Composer
```bash
rm composer.lock
composer install
```

## Support

Pour toute question, vérifiez:
1. Les routes avec `php bin/console debug:router`
2. Les logs Symfony dans `var/log/dev.log`
3. La console du navigateur pour les erreurs JS/CSS

---

**Date de Mise à Jour:** 11 Février 2026
**Version:** 1.0
