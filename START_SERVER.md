# 🚀 Démarrage du Serveur AutoLearn

## Méthode 1: Symfony CLI (Recommandé)

```bash
symfony serve
```

Ou avec un port spécifique:
```bash
symfony serve --port=8000
```

## Méthode 2: PHP Built-in Server

```bash
php -S 127.0.0.1:8000 -t public
```

## Méthode 3: Apache/XAMPP

Configurez un VirtualHost pointant vers le dossier `public/`

## ✅ Vérifications Avant Démarrage

1. **MySQL/MariaDB démarré** (via XAMPP)
2. **Cache vidé**: `php bin/console cache:clear`
3. **Migrations à jour**: `php bin/console doctrine:migrations:status`

## 🔧 En Cas de Problème

### Page bloquée en chargement

```bash
# 1. Arrêter tous les processus PHP
taskkill /F /IM php.exe

# 2. Vider le cache
php bin/console cache:clear

# 3. Redémarrer le serveur
symfony serve
```

### Erreur de connexion DB

```bash
# Vérifier la connexion
php bin/console doctrine:query:sql "SELECT 1"
```

### Performance lente

```bash
# Tester les composants
php bin/console app:test-homepage
```

## 📊 Monitoring

### Voir les logs en temps réel

```bash
tail -f var/log/dev.log
```

### Profiler Symfony

Accédez à: `http://127.0.0.1:8000/_profiler`

## 🎯 URLs Principales

- **Frontoffice**: http://127.0.0.1:8000/
- **Backoffice**: http://127.0.0.1:8000/backoffice
- **Login**: http://127.0.0.1:8000/backoffice/login
- **Profiler**: http://127.0.0.1:8000/_profiler
