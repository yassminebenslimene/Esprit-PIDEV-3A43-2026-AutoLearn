# 🔔 Système de Rappel Automatique d'Inactivité

## 📋 Vue d'Ensemble

Ce système détecte automatiquement les étudiants inactifs (aucun chapitre validé depuis 3 jours) et leur envoie des rappels via deux canaux :
- **Notification interne** : Visible dans le frontoffice de la plateforme
- **SMS externe** : Envoyé via l'API Twilio

## 🏗 Architecture Modulaire

### Principe de Séparation des Responsabilités

```
┌─────────────────────────────────────────────────────────────┐
│                    COMMANDE PLANIFIÉE                        │
│              SendInactivityRemindersCommand                  │
│                  (Orchestration)                             │
└──────────────────┬──────────────────────────────────────────┘
                   │
        ┌──────────┴──────────┐
        │                     │
        ▼                     ▼
┌───────────────────┐  ┌──────────────────────┐
│  LOGIQUE MÉTIER   │  │  LOGIQUE D'ENVOI     │
│                   │  │                      │
│ InactivityDetection│  │ NotificationService  │
│     Service       │  │                      │
│                   │  │  ┌────────────────┐  │
│ • Détecte 3 jours │  │  │ Notification   │  │
│   d'inactivité    │  │  │   Interne      │  │
│ • Calcule jours   │  │  │   (BDD)        │  │
│ • Règle métier    │  │  └────────────────┘  │
│                   │  │                      │
│                   │  │  ┌────────────────┐  │
│                   │  │  │ TwilioSms      │  │
│                   │  │  │   Service      │  │
│                   │  │  │   (API)        │  │
│                   │  │  └────────────────┘  │
└───────────────────┘  └──────────────────────┘
```

### 🧠 Pourquoi cette Architecture ?

1. **Séparation des Préoccupations**
   - `InactivityDetectionService` = Règle métier pure (3 jours d'inactivité)
   - `NotificationService` = Gestion des envois (interne + SMS)
   - `TwilioSmsService` = Intégration API externe

2. **Testabilité**
   - Chaque service peut être testé indépendamment
   - Mock facile pour les tests unitaires

3. **Maintenabilité**
   - Changement de règle métier → Modifier uniquement `InactivityDetectionService`
   - Changement de provider SMS → Modifier uniquement `TwilioSmsService`

4. **Réutilisabilité**
   - `NotificationService` peut être utilisé pour d'autres types de notifications
   - `InactivityDetectionService` peut être utilisé dans d'autres contextes

## 📦 Composants du Système

### 1️⃣ Entités

#### `Notification` (src/Entity/Notification.php)
```php
- id: int
- user: User (ManyToOne)
- type: string (inactivity_reminder, course_update, etc.)
- title: string
- message: text
- isRead: boolean
- createdAt: datetime
- readAt: datetime (nullable)
```

#### Modification de `User` (src/Entity/User.php)
```php
+ lastActivityAt: datetime (nullable)
+ phoneNumber: string (nullable)
```

### 2️⃣ Services

#### `InactivityDetectionService` (src/Service/InactivityDetectionService.php)
**Responsabilité** : Logique métier de détection d'inactivité

**Méthodes** :
- `detectInactiveStudents()` : Retourne tous les étudiants inactifs
- `isStudentInactive(User $user)` : Vérifie si un étudiant est inactif
- `getInactivityDays(User $user)` : Calcule le nombre de jours d'inactivité
- `updateLastActivity(User $user)` : Met à jour la date de dernière activité
- `getInactivityThreshold()` : Retourne le seuil (3 jours)

**Règle Métier** :
```
Un étudiant est inactif SI :
- lastActivityAt < (maintenant - 3 jours) OU lastActivityAt IS NULL
- ET isSuspended = false
- ET role = ETUDIANT
```

#### `NotificationService` (src/Service/NotificationService.php)
**Responsabilité** : Gestion des envois multi-canaux

**Méthodes** :
- `sendInactivityReminder(User $user, int $days)` : Envoie rappel double canal
- `createInternalNotification(...)` : Crée notification en BDD
- `sendNotification(...)` : Envoi générique multi-canaux

**Workflow** :
1. Créer notification interne (BDD)
2. Si numéro de téléphone existe → Envoyer SMS
3. Logger les résultats
4. Retourner `['internal' => bool, 'sms' => bool]`

#### `TwilioSmsService` (src/Service/TwilioSmsService.php)
**Responsabilité** : Intégration API Twilio

**Méthodes** :
- `sendSms(string $to, string $message)` : Envoie un SMS
- `formatPhoneNumber(string $phone)` : Formate au format international
- `isConfigured()` : Vérifie si Twilio est configuré
- `sendTestSms(string $to)` : Envoie un SMS de test

### 3️⃣ Commande Planifiée

#### `SendInactivityRemindersCommand` (src/Command/SendInactivityRemindersCommand.php)

**Usage** :
```bash
# Envoi réel
php bin/console app:send-inactivity-reminders

# Simulation (dry-run)
php bin/console app:send-inactivity-reminders --dry-run
```

**Workflow** :
1. Détecte les étudiants inactifs (via `InactivityDetectionService`)
2. Pour chaque étudiant :
   - Calcule le nombre de jours d'inactivité
   - Envoie notification double canal (via `NotificationService`)
3. Affiche les statistiques

## ⚙️ Configuration

### 1️⃣ Variables d'Environnement (.env)

```env
###> TWILIO SMS CONFIGURATION ###
TWILIO_ACCOUNT_SID=your_account_sid_here
TWILIO_AUTH_TOKEN=your_auth_token_here
TWILIO_PHONE_NUMBER=+1234567890
###< TWILIO SMS CONFIGURATION ###
```

### 2️⃣ Installation de Twilio SDK

```bash
composer require twilio/sdk
```

### 3️⃣ Migration de Base de Données

```bash
# Créer la migration
php bin/console make:migration

# Appliquer la migration
php bin/console doctrine:migrations:migrate
```

**Modifications attendues** :
- Ajout table `notification`
- Ajout colonnes `lastActivityAt` et `phoneNumber` dans `user`

## 🚀 Mise en Production

### 1️⃣ Planification avec Cron (Linux/Mac)

```bash
# Éditer le crontab
crontab -e

# Ajouter cette ligne (exécution tous les jours à 9h00)
0 9 * * * cd /path/to/autolearn && php bin/console app:send-inactivity-reminders >> /var/log/inactivity-reminders.log 2>&1
```

### 2️⃣ Planification avec Task Scheduler (Windows)

1. Ouvrir "Planificateur de tâches"
2. Créer une tâche de base
3. Déclencheur : Quotidien à 9h00
4. Action : Démarrer un programme
   - Programme : `C:\php\php.exe`
   - Arguments : `bin/console app:send-inactivity-reminders`
   - Répertoire : `C:\path\to\autolearn`

### 3️⃣ Fichier Batch Windows (run_inactivity_reminders.bat)

```batch
@echo off
cd C:\path\to\autolearn
php bin/console app:send-inactivity-reminders
pause
```

## 🧪 Tests

### Test Manuel

```bash
# 1. Simulation sans envoi
php bin/console app:send-inactivity-reminders --dry-run

# 2. Test avec un étudiant inactif
# - Créer un étudiant
# - Modifier manuellement lastActivityAt à il y a 4 jours
# - Lancer la commande

# 3. Vérifier les logs
tail -f var/log/dev.log
```

### Test SMS Twilio

```php
// Dans un contrôleur de test
public function testTwilio(TwilioSmsService $twilioService): Response
{
    $result = $twilioService->sendTestSms('+21612345678');
    
    return new Response($result ? 'SMS envoyé' : 'Erreur');
}
```

## 📊 Monitoring

### Logs

Les logs sont enregistrés dans `var/log/dev.log` (ou `prod.log`) :

```
[info] Notification interne envoyée {"user_id":42,"type":"inactivity_reminder"}
[info] SMS envoyé {"user_id":42,"phone":"+21612345678"}
[error] Erreur envoi SMS {"user_id":43,"error":"Invalid phone number"}
```

### Statistiques

La commande affiche un tableau de statistiques :

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

## 🔄 Intégration avec le Système de Progression

### Mise à Jour Automatique de lastActivityAt

Le service `CourseProgressService` a été modifié pour mettre à jour automatiquement `lastActivityAt` quand un étudiant valide un chapitre :

```php
// Dans CourseProgressService::markChapterAsCompleted()
$user->setLastActivityAt(new \DateTime());
```

**Déclencheurs** :
- Validation d'un chapitre via quiz
- Toute autre action considérée comme "activité"

## 📱 Affichage des Notifications (FrontOffice)

### Contrôleur à Créer

```php
// src/Controller/NotificationController.php
#[Route('/notifications', name: 'app_notifications')]
public function index(NotificationRepository $repo): Response
{
    $user = $this->getUser();
    $notifications = $repo->findByUser($user);
    
    return $this->render('frontoffice/notifications/index.html.twig', [
        'notifications' => $notifications
    ]);
}
```

### Template Twig

```twig
{# templates/frontoffice/notifications/index.html.twig #}
{% for notification in notifications %}
<div class="notification {{ notification.isRead ? 'read' : 'unread' }}">
    <h4>{{ notification.title }}</h4>
    <p>{{ notification.message }}</p>
    <small>{{ notification.createdAt|date('d/m/Y H:i') }}</small>
</div>
{% endfor %}
```

## 🎯 Améliorations Futures

1. **Badge de notifications non lues** dans la navbar
2. **Notifications en temps réel** avec Mercure/WebSocket
3. **Préférences utilisateur** (activer/désactiver SMS)
4. **Statistiques admin** (taux d'ouverture, engagement)
5. **A/B Testing** sur les messages de rappel
6. **Notifications push** (PWA)

## 🔐 Sécurité

- Les numéros de téléphone sont validés avant envoi
- Les credentials Twilio sont stockés dans `.env` (jamais en dur)
- Les logs ne contiennent pas de données sensibles
- Rate limiting sur l'API Twilio (géré par Twilio)

## 💡 Bonnes Pratiques

1. **Toujours tester en dry-run** avant la production
2. **Monitorer les logs** régulièrement
3. **Vérifier le solde Twilio** (SMS payants)
4. **Limiter la fréquence** des rappels (éviter le spam)
5. **Respecter le RGPD** (consentement SMS)

## 📞 Support

En cas de problème :
1. Vérifier les logs : `var/log/dev.log`
2. Tester la configuration Twilio : `php bin/console app:send-inactivity-reminders --dry-run`
3. Vérifier les variables d'environnement : `.env`

---

**Auteur** : Système de Rappel Automatique  
**Version** : 1.0  
**Date** : 2026-02-22
