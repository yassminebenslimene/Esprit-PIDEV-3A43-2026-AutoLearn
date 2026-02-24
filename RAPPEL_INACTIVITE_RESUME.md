# 🔔 Système de Rappel d'Inactivité - Résumé Rapide

## 🎯 Objectif
Envoyer automatiquement des rappels aux étudiants inactifs (3 jours sans validation de chapitre) via :
- ✅ Notification interne (visible dans la plateforme)
- ✅ SMS externe (via Twilio)

## 🏗 Architecture Propre

### Séparation des Responsabilités

```
📋 Commande Planifiée
    ↓
🧠 InactivityDetectionService (Règle métier : détecte 3 jours)
    ↓
📤 NotificationService (Gère les 2 envois)
    ├─→ Notification Interne (BDD)
    └─→ TwilioSmsService (API externe)
```

### Pourquoi cette Architecture ?

✅ **Logique métier séparée** : Changement de règle (3→5 jours) = 1 seul fichier  
✅ **API séparée** : Changement de provider SMS = 1 seul fichier  
✅ **Testable** : Chaque service peut être testé indépendamment  
✅ **Réutilisable** : NotificationService utilisable pour d'autres notifications

## 📦 Fichiers Créés

### Entités
- `src/Entity/Notification.php` - Notifications internes
- `src/Entity/User.php` - Ajout `lastActivityAt` + `phoneNumber`

### Services
- `src/Service/InactivityDetectionService.php` - **Logique métier** (détection 3 jours)
- `src/Service/NotificationService.php` - **Gestion envois** (interne + SMS)
- `src/Service/TwilioSmsService.php` - **Intégration API** Twilio

### Commande
- `src/Command/SendInactivityRemindersCommand.php` - Commande planifiée

### Repositories
- `src/Repository/NotificationRepository.php` - Requêtes notifications

### Documentation
- `GUIDE_SYSTEME_RAPPEL_INACTIVITE.md` - Guide complet
- `run_inactivity_reminders.bat` - Script Windows

## ⚙️ Installation

### 1️⃣ Installer Twilio SDK
```bash
composer require twilio/sdk
```

### 2️⃣ Configurer .env
```env
TWILIO_ACCOUNT_SID=your_account_sid
TWILIO_AUTH_TOKEN=your_auth_token
TWILIO_PHONE_NUMBER=+1234567890
```

### 3️⃣ Créer les Tables
```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

## 🚀 Utilisation

### Test en Simulation
```bash
php bin/console app:send-inactivity-reminders --dry-run
```

### Envoi Réel
```bash
php bin/console app:send-inactivity-reminders
```

### Planification Automatique (Cron)
```bash
# Tous les jours à 9h00
0 9 * * * cd /path/to/autolearn && php bin/console app:send-inactivity-reminders
```

### Windows (Task Scheduler)
Utiliser le fichier `run_inactivity_reminders.bat`

## 🔄 Intégration Automatique

Le système met à jour automatiquement `lastActivityAt` quand un étudiant :
- ✅ Valide un chapitre (quiz réussi)
- ✅ Toute autre activité considérée

**Fichier modifié** : `src/Service/CourseProgressService.php`

```php
// Lors de la validation d'un chapitre
$user->setLastActivityAt(new \DateTime());
```

## 📊 Workflow Complet

1. **Étudiant valide un chapitre** → `lastActivityAt` mis à jour
2. **Commande planifiée s'exécute** (tous les jours à 9h)
3. **InactivityDetectionService** détecte les étudiants avec `lastActivityAt < (maintenant - 3 jours)`
4. **NotificationService** envoie :
   - Notification interne (table `notification`)
   - SMS via Twilio (si numéro de téléphone existe)
5. **Logs enregistrés** dans `var/log/dev.log`

## 📱 Exemple de Notification

### Notification Interne
```
Titre : ⏰ Rappel d'activité
Message : Bonjour Ahmed, nous avons remarqué que vous n'avez pas 
          validé de chapitre depuis 3 jours. Continuez votre 
          apprentissage pour progresser ! 🚀
```

### SMS
```
Bonjour Ahmed, vous n'avez pas validé de chapitre depuis 3 jours 
sur Autolearn. Continuez votre apprentissage ! 🎓
```

## 🧪 Tests

### 1. Test Simulation
```bash
php bin/console app:send-inactivity-reminders --dry-run
```

### 2. Test avec Données Réelles
```sql
-- Créer un étudiant inactif (modifier lastActivityAt)
UPDATE user 
SET lastActivityAt = DATE_SUB(NOW(), INTERVAL 4 DAY) 
WHERE userId = 1;
```

```bash
php bin/console app:send-inactivity-reminders
```

### 3. Vérifier les Résultats
```bash
# Logs
tail -f var/log/dev.log

# Base de données
SELECT * FROM notification WHERE type = 'inactivity_reminder';
```

## 📈 Statistiques Affichées

```
┌──────────────────────────────────┬────────┐
│ Métrique                         │ Valeur │
├──────────────────────────────────┼────────┤
│ Étudiants inactifs détectés      │ 15     │
│ Notifications internes envoyées  │ 15     │
│ SMS envoyés                      │ 12     │
│ Erreurs                          │ 0      │
└──────────────────────────────────┴────────┘
```

## 🔐 Sécurité

✅ Credentials Twilio dans `.env` (jamais en dur)  
✅ Validation des numéros de téléphone  
✅ Logs sans données sensibles  
✅ Rate limiting géré par Twilio

## 💡 Points Clés

### ✅ Ce qui est FAIT
- Entité Notification créée
- Service de détection d'inactivité (règle métier)
- Service de notification multi-canaux
- Service Twilio SMS
- Commande planifiée
- Intégration automatique avec progression
- Documentation complète

### ⚠️ À FAIRE
1. Créer la migration : `php bin/console make:migration`
2. Appliquer la migration : `php bin/console doctrine:migrations:migrate`
3. Installer Twilio : `composer require twilio/sdk`
4. Configurer `.env` avec credentials Twilio
5. Tester en dry-run
6. Planifier avec cron/Task Scheduler

### 🎯 Prochaines Étapes (Optionnel)
- Affichage des notifications dans le frontoffice
- Badge de notifications non lues
- Préférences utilisateur (activer/désactiver SMS)
- Statistiques admin

## 📞 Obtenir les Credentials Twilio

1. Créer un compte sur https://www.twilio.com/
2. Aller dans Console → Account Info
3. Copier :
   - Account SID
   - Auth Token
4. Acheter un numéro de téléphone Twilio
5. Ajouter dans `.env`

## 🎓 Avantages Métier

✅ **Améliore l'engagement** : Rappels automatiques  
✅ **Réduit l'abandon** : Relance les étudiants inactifs  
✅ **Double canal** : Notification + SMS = meilleur taux de lecture  
✅ **Automatique** : Aucune intervention manuelle  
✅ **Scalable** : Fonctionne pour 10 ou 10 000 étudiants

---

**Architecture** : Modulaire et propre ✓  
**Logique métier** : Séparée de l'API ✓  
**Prêt pour production** : Oui ✓
