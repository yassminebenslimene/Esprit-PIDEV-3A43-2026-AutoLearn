# 📥 Installation après Git Pull - Guide pour l'équipe

## 🎯 Quand utiliser ce guide

Quand un membre de l'équipe fait `git pull` de ta branche et récupère ton travail.

## ⚠️ Commandes OBLIGATOIRES

### 1. Installer les dépendances Composer

```bash
composer install
```

**Pourquoi ?** De nouveaux services PHP ont été ajoutés.

### 2. Vider le cache Symfony

```bash
php bin/console cache:clear
```

**Pourquoi ?** Pour que Symfony reconnaisse les nouveaux fichiers.

### 3. Mettre à jour la base de données

```bash
php bin/console doctrine:schema:update --force
```

**Pourquoi ?** Pour créer les tables manquantes (revisions, etc.).

### 4. Copier le fichier .env.local (si nécessaire)

Si ton ami n'a pas de fichier `.env.local`, il doit le créer avec :

```env
# .env.local
GROQ_API_KEY=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
MAILER_DSN=sendgrid+api://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@default
WEATHER_API_KEY=5177b7da6160976397c624428cd12f3d
```

**Pourquoi ?** Pour que les services IA et email fonctionnent.

## 📋 Commandes complètes (copier-coller)

```bash
# 1. Installer les dépendances
composer install

# 2. Vider le cache
php bin/console cache:clear

# 3. Mettre à jour la base de données
php bin/console doctrine:schema:update --force

# 4. Démarrer le serveur
symfony server:start
```

## ✅ Vérification

Après ces commandes, ton ami peut vérifier que tout fonctionne :

```bash
# Ouvrir le backoffice
http://localhost:8000/backoffice

# Vérifier que ces pages fonctionnent :
# - Gestion des cours → Chapitres → Bouton "🤖 Générer un Chapitre avec l'IA"
# - Traduction de chapitres (dans la page chapitre)
# - Assistant IA explainer (dans la page chapitre)
# - Notifications (http://localhost:8000/notifications)
```

### Test du générateur IA de chapitres

1. Aller dans **Backoffice → Gestion des cours**
2. Cliquer sur un cours
3. Cliquer sur **"🤖 Générer un Chapitre avec l'IA"**
4. Attendre 5-10 secondes
5. Le nouveau chapitre apparaît automatiquement

### Test des notifications

1. Aller sur **http://localhost:8000/notifications**
2. Vérifier l'affichage des notifications
3. Tester "Marquer comme lu"

### Test de la commande cron (optionnel)

```bash
php bin/console app:send-inactivity-reminders --dry-run
```

## 🚨 Si problèmes

### Erreur: "Class not found"

```bash
composer dump-autoload
php bin/console cache:clear
```

### Erreur: "Table doesn't exist"

```bash
php bin/console doctrine:schema:update --force
```

### Erreur: "Service not found"

```bash
php bin/console cache:clear
php bin/console debug:container | findstr Translation
```

## 📦 Nouveaux fichiers ajoutés

Ton ami recevra automatiquement :

### Services IA
- `src/Service/TranslationService.php` - Traduction de chapitres (8 langues)
- `src/Service/CourseGeneratorService.php` - ✅ **NOUVEAU** Génération de chapitres IA
- `src/Service/ChapterExplainerService.php` - Explications avec synthèse vocale

### Services Notifications
- `src/Service/NotificationService.php` - Gestion des notifications
- `src/Service/InactivityDetectionService.php` - Détection d'inactivité

### Contrôleurs
- `src/Controller/TranslationController.php` - API traduction
- `src/Controller/AIGeneratorController.php` - ✅ **NOUVEAU** API génération IA
- `src/Controller/NotificationController.php` - Gestion notifications

### Commandes (Cron)
- `src/Command/SendInactivityRemindersCommand.php` - Rappels automatiques

### Templates
- `templates/backoffice/cours/chapitres.html.twig` - ✅ **MODIFIÉ** Bouton IA ajouté
- `templates/frontoffice/chapter_explainer/index.html.twig` - Assistant IA
- `templates/frontoffice/notifications/index.html.twig` - Page notifications

### CSS/JS
- `public/Backoffice/css/navbar-sidebar-improvements.css`
- `public/Backoffice/js/navbar-sidebar-improvements.js`
- `public/frontoffice/css/chapter-explainer.css`

### Configuration
- `config/services.yaml` - ✅ **MODIFIÉ** CourseGeneratorService ajouté
- `config/bundles.php` (modifié)

## 🔑 Clés API nécessaires

Ton ami doit avoir dans son `.env.local` :

```env
# API Groq pour l'IA
GROQ_API_KEY=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

# Email (optionnel)
MAILER_DSN=sendgrid+api://...

# Météo (optionnel)
WEATHER_API_KEY=...
```

## 📝 Résumé pour ton ami

**Après `git pull`, exécuter dans l'ordre :**

1. `composer install`
2. `php bin/console cache:clear`
3. `php bin/console doctrine:schema:update --force`
4. Créer `.env.local` avec les clés API
5. `symfony server:start`

**C'est tout !** ✅

---

**Important:** Le fichier `.env.local` n'est PAS dans Git (il est dans `.gitignore`), donc chaque membre de l'équipe doit le créer avec ses propres clés API.
