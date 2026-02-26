# ⏰ Configuration Cron pour Automatisation Complète

## Vue d'Ensemble

Ce document explique comment configurer les tâches cron pour automatiser complètement la gestion des événements.

---

## 🎯 Tâches à Automatiser

### 1. Mise à Jour des Statuts d'Événements
**Commande:** `app:update-event-status`
**Fréquence recommandée:** Toutes les 5 minutes
**Fonction:** Démarre et termine automatiquement les événements selon leurs dates

### 2. Envoi des Rappels (3 jours avant)
**Commande:** `app:send-event-reminders`
**Fréquence recommandée:** Une fois par jour à 9h00
**Fonction:** Envoie des emails de rappel 3 jours avant chaque événement

### 3. Envoi des Certificats
**Commande:** `app:send-certificates`
**Fréquence recommandée:** Une fois par jour à 10h00
**Fonction:** Envoie les certificats aux participants des événements terminés

### 4. Nettoyage des Événements Annulés
**Commande:** `app:cleanup-cancelled-events`
**Fréquence recommandée:** Une fois par semaine (dimanche à 2h00)
**Fonction:** Archive ou nettoie les événements annulés

---

## 🔧 Configuration Cron

### Sur Linux/Unix

#### Ouvrir l'éditeur crontab
```bash
crontab -e
```

#### Ajouter les tâches suivantes

```cron
# ============================================
# AUTOLEARN PLATFORM - GESTION DES ÉVÉNEMENTS
# ============================================

# 1. Mise à jour des statuts d'événements (toutes les 5 minutes)
*/5 * * * * cd /var/www/autolearn && php bin/console app:update-event-status >> /var/log/autolearn/event-status.log 2>&1

# 2. Envoi des rappels 3 jours avant (tous les jours à 9h00)
0 9 * * * cd /var/www/autolearn && php bin/console app:send-event-reminders >> /var/log/autolearn/event-reminders.log 2>&1

# 3. Envoi des certificats (tous les jours à 10h00)
0 10 * * * cd /var/www/autolearn && php bin/console app:send-certificates >> /var/log/autolearn/certificates.log 2>&1

# 4. Nettoyage des événements annulés (dimanche à 2h00)
0 2 * * 0 cd /var/www/autolearn && php bin/console app:cleanup-cancelled-events >> /var/log/autolearn/cleanup.log 2>&1

# 5. Nettoyage du cache Symfony (tous les jours à 3h00)
0 3 * * * cd /var/www/autolearn && php bin/console cache:clear --env=prod >> /var/log/autolearn/cache-clear.log 2>&1
```

**Important:** Remplacer `/var/www/autolearn` par le chemin réel de votre projet!

---

## 📝 Explication de la Syntaxe Cron

```
* * * * * commande
│ │ │ │ │
│ │ │ │ └─── Jour de la semaine (0-7, 0 et 7 = dimanche)
│ │ │ └───── Mois (1-12)
│ │ └─────── Jour du mois (1-31)
│ └───────── Heure (0-23)
└─────────── Minute (0-59)
```

### Exemples

```cron
# Toutes les 5 minutes
*/5 * * * * commande

# Tous les jours à 9h00
0 9 * * * commande

# Tous les lundis à 8h30
30 8 * * 1 commande

# Le 1er de chaque mois à minuit
0 0 1 * * commande

# Tous les dimanches à 2h00
0 2 * * 0 commande
```

---

## 📂 Création des Dossiers de Logs

### Sur Linux/Unix

```bash
# Créer le dossier de logs
sudo mkdir -p /var/log/autolearn

# Donner les permissions appropriées
sudo chown -R www-data:www-data /var/log/autolearn
sudo chmod -R 755 /var/log/autolearn
```

### Sur Windows (XAMPP/WAMP)

```bash
# Créer le dossier dans le projet
mkdir logs
mkdir logs\cron
```

Puis modifier les chemins dans le cron:
```cron
*/5 * * * * cd C:\xampp\htdocs\autolearn && php bin/console app:update-event-status >> logs\cron\event-status.log 2>&1
```

---

## 🪟 Configuration sur Windows

### Option 1: Utiliser le Planificateur de Tâches Windows

#### 1. Ouvrir le Planificateur de Tâches
- Appuyer sur `Win + R`
- Taper `taskschd.msc`
- Appuyer sur Entrée

#### 2. Créer une Nouvelle Tâche
- Cliquer sur "Créer une tâche de base..."
- Nom: "Autolearn - Mise à jour événements"
- Description: "Met à jour automatiquement les statuts des événements"

#### 3. Déclencheur
- Sélectionner "Quotidien" ou "À l'ouverture de session"
- Répéter la tâche toutes les 5 minutes pendant 24 heures

#### 4. Action
- Sélectionner "Démarrer un programme"
- Programme: `C:\xampp\php\php.exe`
- Arguments: `bin/console app:update-event-status`
- Dossier de départ: `C:\xampp\htdocs\autolearn`

#### 5. Répéter pour chaque commande

### Option 2: Utiliser un Script Batch

Créer un fichier `cron-tasks.bat`:

```batch
@echo off
REM Autolearn Platform - Tâches Automatiques

REM Chemin du projet
set PROJECT_PATH=C:\xampp\htdocs\autolearn
set PHP_PATH=C:\xampp\php\php.exe

REM Mise à jour des statuts
cd %PROJECT_PATH%
%PHP_PATH% bin/console app:update-event-status >> logs\cron\event-status.log 2>&1

REM Attendre 5 minutes et recommencer
timeout /t 300 /nobreak
goto :loop
```

Puis créer une tâche planifiée qui exécute ce script au démarrage.

---

## 🔍 Vérification du Fonctionnement

### Vérifier que les tâches cron sont actives

```bash
# Lister les tâches cron
crontab -l

# Vérifier les logs en temps réel
tail -f /var/log/autolearn/event-status.log

# Vérifier les dernières lignes
tail -n 50 /var/log/autolearn/event-status.log
```

### Tester manuellement les commandes

```bash
# Tester la mise à jour des statuts
php bin/console app:update-event-status

# Tester l'envoi des rappels
php bin/console app:send-event-reminders

# Tester l'envoi des certificats
php bin/console app:send-certificates
```

---

## 📊 Monitoring des Logs

### Script de Monitoring (Linux)

Créer un fichier `monitor-cron.sh`:

```bash
#!/bin/bash

echo "==================================="
echo "AUTOLEARN - MONITORING CRON TASKS"
echo "==================================="
echo ""

echo "📊 Dernières mises à jour de statuts:"
tail -n 5 /var/log/autolearn/event-status.log
echo ""

echo "📧 Derniers rappels envoyés:"
tail -n 5 /var/log/autolearn/event-reminders.log
echo ""

echo "🎓 Derniers certificats envoyés:"
tail -n 5 /var/log/autolearn/certificates.log
echo ""

echo "🧹 Dernier nettoyage:"
tail -n 5 /var/log/autolearn/cleanup.log
echo ""

echo "==================================="
```

Rendre le script exécutable:
```bash
chmod +x monitor-cron.sh
```

Exécuter:
```bash
./monitor-cron.sh
```

---

## 🚨 Gestion des Erreurs

### Recevoir des Notifications par Email

Modifier le crontab pour envoyer des emails en cas d'erreur:

```cron
# Définir l'email de notification
MAILTO=admin@autolearn.com

# Les erreurs seront envoyées à cet email
*/5 * * * * cd /var/www/autolearn && php bin/console app:update-event-status >> /var/log/autolearn/event-status.log 2>&1 || echo "Erreur dans app:update-event-status" | mail -s "Erreur Cron Autolearn" admin@autolearn.com
```

### Rotation des Logs

Créer un fichier `/etc/logrotate.d/autolearn`:

```
/var/log/autolearn/*.log {
    daily
    rotate 30
    compress
    delaycompress
    notifempty
    create 0644 www-data www-data
    sharedscripts
    postrotate
        # Optionnel: redémarrer un service si nécessaire
    endscript
}
```

---

## 🔐 Sécurité

### Permissions des Fichiers

```bash
# Logs accessibles uniquement par www-data
chmod 644 /var/log/autolearn/*.log
chown www-data:www-data /var/log/autolearn/*.log

# Commandes Symfony accessibles uniquement par www-data
chmod 755 bin/console
chown www-data:www-data bin/console
```

### Isolation des Tâches

Créer un utilisateur dédié pour les tâches cron:

```bash
# Créer un utilisateur
sudo useradd -r -s /bin/bash autolearn-cron

# Donner les permissions nécessaires
sudo chown -R autolearn-cron:autolearn-cron /var/www/autolearn

# Configurer le crontab pour cet utilisateur
sudo crontab -u autolearn-cron -e
```

---

## 📈 Optimisation

### Éviter les Exécutions Simultanées

Utiliser `flock` pour éviter que deux instances de la même commande s'exécutent en même temps:

```cron
*/5 * * * * flock -n /tmp/event-status.lock -c "cd /var/www/autolearn && php bin/console app:update-event-status >> /var/log/autolearn/event-status.log 2>&1"
```

### Limiter la Charge Serveur

Utiliser `nice` pour réduire la priorité des tâches:

```cron
*/5 * * * * nice -n 10 cd /var/www/autolearn && php bin/console app:update-event-status >> /var/log/autolearn/event-status.log 2>&1
```

---

## 🧪 Tests

### Tester le Cron Localement

```bash
# Simuler l'exécution du cron
cd /var/www/autolearn && php bin/console app:update-event-status

# Vérifier le code de sortie
echo $?
# 0 = succès, autre = erreur
```

### Tester avec des Événements de Test

1. Créer un événement qui démarre dans 1 minute
2. Attendre 5 minutes (prochain cycle du cron)
3. Vérifier les logs
4. Vérifier que l'événement a changé de statut
5. Vérifier que les emails ont été envoyés

---

## 📋 Checklist de Configuration

- [ ] Crontab configuré avec les 4 tâches principales
- [ ] Dossiers de logs créés avec les bonnes permissions
- [ ] Chemins du projet corrects dans le crontab
- [ ] Commandes testées manuellement (toutes fonctionnent)
- [ ] Logs vérifiés après 10 minutes (au moins 2 exécutions)
- [ ] Emails de test reçus
- [ ] Rotation des logs configurée
- [ ] Monitoring en place
- [ ] Documentation partagée avec l'équipe

---

## 🆘 Dépannage

### Problème: Les tâches ne s'exécutent pas

**Solutions:**
1. Vérifier que le service cron est actif: `sudo service cron status`
2. Vérifier les permissions: `ls -la /var/log/autolearn/`
3. Vérifier les chemins dans le crontab: `crontab -l`
4. Tester manuellement: `cd /var/www/autolearn && php bin/console app:update-event-status`

### Problème: Erreurs dans les logs

**Solutions:**
1. Lire les logs: `cat /var/log/autolearn/event-status.log`
2. Vérifier les permissions de la base de données
3. Vérifier les variables d'environnement: `.env.local`
4. Vérifier que Symfony est en mode production: `APP_ENV=prod`

### Problème: Emails non envoyés

**Solutions:**
1. Vérifier `MAILER_DSN` dans `.env.local`
2. Tester manuellement: `php bin/console app:send-event-reminders`
3. Vérifier les logs SendGrid
4. Vérifier que les événements ont des participations acceptées

---

## 📞 Support

En cas de problème persistant:
1. Consulter les logs: `/var/log/autolearn/`
2. Vérifier la documentation Symfony: https://symfony.com/doc/current/console.html
3. Vérifier la documentation cron: `man crontab`

---

**Configuration terminée! Les événements sont maintenant gérés automatiquement. 🎉**
