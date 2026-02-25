# Guide du Système de Traduction Multilingue

## Vue d'ensemble

Le système de traduction permet aux utilisateurs de changer la langue de toute la plateforme (frontoffice et backoffice) via un sélecteur dans la navbar.

## Langues supportées

- 🇫🇷 **Français** (fr) - Langue par défaut
- 🇬🇧 **Anglais** (en)
- 🇸🇦 **Arabe** (ar)

## Architecture

### 1. Configuration Symfony

**Fichier**: `config/packages/translation.yaml`

```yaml
framework:
    default_locale: fr
    translator:
        default_path: '%kernel.project_dir%/translations'
        fallbacks:
            - fr
    enabled_locales: ['fr', 'en', 'ar']
```

### 2. Fichiers de traduction

Les traductions sont stockées dans le dossier `translations/`:

- `messages.fr.yaml` - Traductions françaises
- `messages.en.yaml` - Traductions anglaises
- `messages.ar.yaml` - Traductions arabes

### 3. Contrôleur de changement de langue

**Fichier**: `src/Controller/LanguageController.php`

Route: `/change-language/{locale}`

Fonctionnement:
1. Vérifie que la locale est valide (fr, en, ar)
2. Stocke la locale dans la session
3. Redirige vers la page précédente

### 4. Event Subscriber

**Fichier**: `src/EventSubscriber/LocaleSubscriber.php`

Rôle:
- Intercepte chaque requête HTTP
- Récupère la locale depuis la session
- Applique la locale à la requête

## Utilisation dans les templates

### Syntaxe de base

```twig
{{ 'clé.de.traduction'|trans }}
```

### Exemples

```twig
{# Navigation #}
<a href="{{ path('app_frontoffice') }}">{{ 'nav.home'|trans }}</a>

{# Titres #}
<h1>{{ 'courses.title'|trans }}</h1>

{# Boutons #}
<button>{{ 'actions.save'|trans }}</button>

{# Messages #}
{{ 'messages.success.created'|trans }}
```

## Sélecteur de langue

### Frontoffice

Le sélecteur est intégré dans la navbar principale avec un dropdown:

```twig
<li class="profile-dropdown-container">
    <a href="javascript:void(0);" onclick="toggleLanguageDropdown(event)">
        <i class="fa fa-globe"></i>
        {{ 'language.french'|trans }}
    </a>
    <ul class="profile-dropdown-menu" id="languageDropdown">
        <li><a href="{{ path('app_change_language', {'locale': 'fr'}) }}">🇫🇷 Français</a></li>
        <li><a href="{{ path('app_change_language', {'locale': 'en'}) }}">🇬🇧 English</a></li>
        <li><a href="{{ path('app_change_language', {'locale': 'ar'}) }}">🇸🇦 العربية</a></li>
    </ul>
</li>
```

### Backoffice

Le sélecteur est un bouton avec icône globe dans la navbar:

```twig
<button class="nav-btn" id="language-toggle" onclick="toggleLanguageMenu(event)">
    <svg><!-- Globe icon --></svg>
</button>
<div id="languageMenu" class="language-menu">
    <a href="{{ path('app_change_language', {'locale': 'fr'}) }}">🇫🇷 Français</a>
    <a href="{{ path('app_change_language', {'locale': 'en'}) }}">🇬🇧 English</a>
    <a href="{{ path('app_change_language', {'locale': 'ar'}) }}">🇸🇦 العربية</a>
</div>
```

## Structure des clés de traduction

### Navigation (nav)
- `nav.home` - Accueil
- `nav.events` - Événements
- `nav.courses` - Cours
- `nav.community` - Communauté
- `nav.login` - Connexion
- `nav.logout` - Déconnexion

### Cours (courses)
- `courses.title` - Cours
- `courses.new` - Nouveau cours
- `courses.edit` - Modifier le cours
- `courses.view` - Voir le cours
- `courses.chapters` - Chapitres
- `courses.quizzes` - Quiz

### Chapitres (chapters)
- `chapters.title` - Chapitres
- `chapters.new` - Nouveau chapitre
- `chapters.content` - Contenu
- `chapters.order` - Ordre

### Quiz (quiz)
- `quiz.title` - Quiz
- `quiz.new` - Nouveau quiz
- `quiz.management` - Gestion Quiz

### Actions (actions)
- `actions.create` - Créer
- `actions.edit` - Modifier
- `actions.delete` - Supprimer
- `actions.save` - Enregistrer
- `actions.cancel` - Annuler
- `actions.search` - Rechercher

### Messages (messages)
- `messages.success.created` - Créé avec succès
- `messages.success.updated` - Modifié avec succès
- `messages.error.general` - Une erreur s'est produite

## Ajouter de nouvelles traductions

### Étape 1: Ajouter la clé dans les 3 fichiers

**messages.fr.yaml**
```yaml
mon_module:
  titre: Mon Titre
  description: Ma Description
```

**messages.en.yaml**
```yaml
mon_module:
  titre: My Title
  description: My Description
```

**messages.ar.yaml**
```yaml
mon_module:
  titre: عنواني
  description: وصفي
```

### Étape 2: Utiliser dans le template

```twig
<h1>{{ 'mon_module.titre'|trans }}</h1>
<p>{{ 'mon_module.description'|trans }}</p>
```

### Étape 3: Vider le cache

```bash
php bin/console cache:clear
```

## Traduction avec paramètres

### Dans le fichier de traduction

```yaml
messages:
  welcome: Bienvenue %name%
  items_count: Vous avez %count% éléments
```

### Dans le template

```twig
{{ 'messages.welcome'|trans({'%name%': user.prenom}) }}
{{ 'messages.items_count'|trans({'%count%': items|length}) }}
```

## Support RTL pour l'arabe

Pour supporter correctement l'arabe (langue RTL), ajoutez dans votre layout:

```twig
<html lang="{{ app.request.locale }}" dir="{% if app.request.locale == 'ar' %}rtl{% else %}ltr{% endif %}">
```

CSS pour RTL:

```css
[dir="rtl"] {
    text-align: right;
}

[dir="rtl"] .navbar {
    flex-direction: row-reverse;
}
```

## Tester le système

1. Démarrer le serveur: `symfony serve`
2. Accéder à la page d'accueil
3. Cliquer sur le sélecteur de langue (icône globe)
4. Choisir une langue
5. Vérifier que tous les textes sont traduits

## Dépannage

### Les traductions ne s'affichent pas

1. Vérifier que le fichier de traduction existe
2. Vérifier la syntaxe YAML (indentation)
3. Vider le cache: `php bin/console cache:clear`
4. Vérifier que la clé existe dans tous les fichiers

### La langue ne change pas

1. Vérifier que la session est activée
2. Vérifier que le LocaleSubscriber est enregistré
3. Vérifier les logs: `var/log/dev.log`

### Erreur "Translation not found"

1. Ajouter la clé manquante dans les fichiers de traduction
2. Utiliser le fallback français si la traduction n'existe pas

## Bonnes pratiques

1. **Nommer les clés de manière cohérente**: `module.action.element`
2. **Grouper par fonctionnalité**: nav, courses, quiz, etc.
3. **Éviter les textes hardcodés**: Toujours utiliser `|trans`
4. **Traduire tous les éléments**: menus, boutons, messages, placeholders
5. **Tester dans toutes les langues**: Vérifier que rien n'est cassé
6. **Documenter les nouvelles clés**: Ajouter des commentaires dans les fichiers YAML

## Fichiers modifiés

- ✅ `config/packages/translation.yaml` - Configuration
- ✅ `src/Controller/LanguageController.php` - Contrôleur
- ✅ `src/EventSubscriber/LocaleSubscriber.php` - Event subscriber
- ✅ `translations/messages.fr.yaml` - Traductions françaises
- ✅ `translations/messages.en.yaml` - Traductions anglaises
- ✅ `translations/messages.ar.yaml` - Traductions arabes
- ✅ `templates/frontoffice/base.html.twig` - Navbar frontoffice
- ✅ `templates/backoffice/base.html.twig` - Navbar backoffice

## Prochaines étapes

Pour compléter la traduction de toute la plateforme:

1. Traduire tous les templates restants
2. Traduire les messages flash
3. Traduire les formulaires
4. Traduire les messages d'erreur de validation
5. Ajouter le support RTL complet pour l'arabe
6. Traduire les emails
7. Traduire les notifications
