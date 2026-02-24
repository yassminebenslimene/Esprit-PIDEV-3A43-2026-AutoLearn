# 🏗 Architecture du Système de Rappel d'Inactivité

## 📐 Diagramme d'Architecture Complète

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                          COUCHE PRÉSENTATION                                 │
│                                                                              │
│  ┌──────────────────────────────────────────────────────────────────────┐  │
│  │                    COMMANDE SYMFONY (CLI)                             │  │
│  │         SendInactivityRemindersCommand.php                            │  │
│  │                                                                        │  │
│  │  • Orchestration du workflow                                          │  │
│  │  • Affichage des statistiques                                         │  │
│  │  • Mode dry-run pour tests                                            │  │
│  └────────────────────────┬─────────────────────────────────────────────┘  │
└────────────────────────────┼──────────────────────────────────────────────┘
                             │
                             │ Appelle
                             │
┌────────────────────────────┼──────────────────────────────────────────────┐
│                            ▼         COUCHE MÉTIER                         │
│                                                                             │
│  ┌─────────────────────────────────────────────────────────────────────┐  │
│  │              InactivityDetectionService.php                          │  │
│  │                    (LOGIQUE MÉTIER PURE)                             │  │
│  │                                                                       │  │
│  │  📊 Responsabilités :                                                │  │
│  │  • detectInactiveStudents() → array<Etudiant>                       │  │
│  │  • isStudentInactive(User) → bool                                   │  │
│  │  • getInactivityDays(User) → int                                    │  │
│  │  • updateLastActivity(User) → void                                  │  │
│  │                                                                       │  │
│  │  🧠 Règle Métier :                                                   │  │
│  │     lastActivityAt < (NOW - 3 DAYS) OR lastActivityAt IS NULL       │  │
│  │     AND isSuspended = false                                          │  │
│  │     AND role = ETUDIANT                                              │  │
│  └───────────────────────────────┬─────────────────────────────────────┘  │
│                                   │                                         │
│                                   │ Retourne liste étudiants inactifs       │
│                                   │                                         │
│  ┌────────────────────────────────▼────────────────────────────────────┐  │
│  │              NotificationService.php                                 │  │
│  │              (GESTION DES ENVOIS)                                    │  │
│  │                                                                       │  │
│  │  📤 Responsabilités :                                                │  │
│  │  • sendInactivityReminder(User, days) → array                       │  │
│  │  • createInternalNotification(...) → Notification                   │  │
│  │  • sendNotification(...) → array                                    │  │
│  │                                                                       │  │
│  │  🔀 Workflow :                                                       │  │
│  │     1. Créer notification interne (BDD)                              │  │
│  │     2. Si phoneNumber existe → Envoyer SMS                           │  │
│  │     3. Logger les résultats                                          │  │
│  │     4. Retourner ['internal' => bool, 'sms' => bool]                │  │
│  └───────────────────┬──────────────────────┬──────────────────────────┘  │
└────────────────────────┼──────────────────────┼─────────────────────────┘
                         │                      │
                         │ Appelle              │ Appelle
                         │                      │
┌────────────────────────┼──────────────────────┼─────────────────────────┐
│                        ▼                      ▼    COUCHE INFRASTRUCTURE │
│                                                                           │
│  ┌──────────────────────────────┐  ┌──────────────────────────────────┐ │
│  │  EntityManager (Doctrine)    │  │   TwilioSmsService.php           │ │
│  │                              │  │   (INTÉGRATION API)              │ │
│  │  💾 Responsabilités :        │  │                                  │ │
│  │  • Persister Notification    │  │  📱 Responsabilités :            │ │
│  │  • Flush en base             │  │  • sendSms(to, message) → bool  │ │
│  │  • Gérer transactions        │  │  • formatPhoneNumber() → string │ │
│  │                              │  │  • isConfigured() → bool        │ │
│  │  ┌────────────────────────┐  │  │  • sendTestSms() → bool         │ │
│  │  │  Table: notification   │  │  │                                  │ │
│  │  │  ─────────────────────  │  │  │  🔌 API Externe :               │ │
│  │  │  • id                  │  │  │     Twilio REST API             │ │
│  │  │  • user_id             │  │  │     https://api.twilio.com      │ │
│  │  │  • type                │  │  │                                  │ │
│  │  │  • title               │  │  │  ⚙️ Configuration :              │ │
│  │  │  • message             │  │  │     TWILIO_ACCOUNT_SID          │ │
│  │  │  • isRead              │  │  │     TWILIO_AUTH_TOKEN           │ │
│  │  │  • createdAt           │  │  │     TWILIO_PHONE_NUMBER         │ │
│  │  │  • readAt              │  │  │                                  │ │
│  │  └────────────────────────┘  │  └──────────────────────────────────┘ │
│  └──────────────────────────────┘                                        │
└───────────────────────────────────────────────────────────────────────────┘
```

## 🔄 Flux de Données Complet

```
┌─────────────────────────────────────────────────────────────────────────┐
│                        DÉCLENCHEMENT                                     │
│                                                                          │
│  Cron Job (9h00 tous les jours)                                         │
│  OU                                                                      │
│  Exécution manuelle : php bin/console app:send-inactivity-reminders     │
└────────────────────────────────┬────────────────────────────────────────┘
                                 │
                                 ▼
┌─────────────────────────────────────────────────────────────────────────┐
│  ÉTAPE 1 : DÉTECTION DES ÉTUDIANTS INACTIFS                             │
│                                                                          │
│  InactivityDetectionService::detectInactiveStudents()                   │
│                                                                          │
│  SELECT * FROM user                                                      │
│  WHERE discr = 'etudiant'                                                │
│    AND isSuspended = false                                               │
│    AND (lastActivityAt < NOW() - INTERVAL 3 DAY                          │
│         OR lastActivityAt IS NULL)                                       │
│                                                                          │
│  Résultat : [Etudiant1, Etudiant2, ..., EtudiantN]                      │
└────────────────────────────────┬────────────────────────────────────────┘
                                 │
                                 ▼
┌─────────────────────────────────────────────────────────────────────────┐
│  ÉTAPE 2 : CALCUL DES JOURS D'INACTIVITÉ                                │
│                                                                          │
│  Pour chaque étudiant :                                                  │
│    InactivityDetectionService::getInactivityDays(etudiant)              │
│                                                                          │
│    Calcul : NOW() - lastActivityAt = X jours                            │
│                                                                          │
│  Résultat : [3 jours, 5 jours, 7 jours, ...]                            │
└────────────────────────────────┬────────────────────────────────────────┘
                                 │
                                 ▼
┌─────────────────────────────────────────────────────────────────────────┐
│  ÉTAPE 3 : ENVOI NOTIFICATION INTERNE                                   │
│                                                                          │
│  NotificationService::createInternalNotification(                        │
│    user: Etudiant,                                                       │
│    type: 'inactivity_reminder',                                          │
│    title: '⏰ Rappel d\'activité',                                       │
│    message: 'Bonjour Ahmed, vous n\'avez pas validé...'                 │
│  )                                                                       │
│                                                                          │
│  INSERT INTO notification (user_id, type, title, message, ...)          │
│  VALUES (42, 'inactivity_reminder', '⏰ Rappel...', ...)                 │
│                                                                          │
│  Résultat : Notification créée en BDD ✓                                 │
└────────────────────────────────┬────────────────────────────────────────┘
                                 │
                                 ▼
┌─────────────────────────────────────────────────────────────────────────┐
│  ÉTAPE 4 : ENVOI SMS (SI NUMÉRO EXISTE)                                 │
│                                                                          │
│  IF (etudiant.phoneNumber !== null) {                                   │
│                                                                          │
│    TwilioSmsService::sendSms(                                            │
│      to: '+21612345678',                                                 │
│      message: 'Bonjour Ahmed, vous n\'avez pas validé...'               │
│    )                                                                     │
│                                                                          │
│    ┌─────────────────────────────────────────────────────────────┐     │
│    │  API Twilio                                                  │     │
│    │  POST https://api.twilio.com/2010-04-01/Accounts/{SID}/...  │     │
│    │                                                              │     │
│    │  Body: {                                                     │     │
│    │    "To": "+21612345678",                                     │     │
│    │    "From": "+1234567890",                                    │     │
│    │    "Body": "Bonjour Ahmed..."                                │     │
│    │  }                                                           │     │
│    │                                                              │     │
│    │  Response: { "sid": "SM...", "status": "queued" }           │     │
│    └─────────────────────────────────────────────────────────────┘     │
│                                                                          │
│    Résultat : SMS envoyé ✓                                              │
│  }                                                                       │
└────────────────────────────────┬────────────────────────────────────────┘
                                 │
                                 ▼
┌─────────────────────────────────────────────────────────────────────────┐
│  ÉTAPE 5 : LOGGING ET STATISTIQUES                                      │
│                                                                          │
│  Logger::info('Notification interne envoyée', [                         │
│    'user_id' => 42,                                                      │
│    'type' => 'inactivity_reminder'                                       │
│  ])                                                                      │
│                                                                          │
│  Logger::info('SMS envoyé', [                                            │
│    'user_id' => 42,                                                      │
│    'phone' => '+21612345678'                                             │
│  ])                                                                      │
│                                                                          │
│  Statistiques :                                                          │
│  • Total étudiants inactifs : 15                                         │
│  • Notifications internes : 15                                           │
│  • SMS envoyés : 12                                                      │
│  • Erreurs : 0                                                           │
└─────────────────────────────────────────────────────────────────────────┘
```

## 🎯 Séparation des Responsabilités (SOLID)

### Single Responsibility Principle (SRP)

```
InactivityDetectionService
└─→ UNE SEULE responsabilité : Détecter l'inactivité (règle métier)

NotificationService
└─→ UNE SEULE responsabilité : Gérer les envois de notifications

TwilioSmsService
└─→ UNE SEULE responsabilité : Intégrer l'API Twilio

SendInactivityRemindersCommand
└─→ UNE SEULE responsabilité : Orchestrer le workflow
```

### Dependency Inversion Principle (DIP)

```
SendInactivityRemindersCommand
    ↓ dépend de (interface)
InactivityDetectionService
NotificationService
    ↓ dépend de (interface)
TwilioSmsService
```

## 🔌 Points d'Extension

### Ajouter un Nouveau Canal de Notification

```php
// 1. Créer un nouveau service
class WhatsAppService {
    public function sendMessage(string $to, string $message): bool {
        // Intégration WhatsApp Business API
    }
}

// 2. Modifier NotificationService
public function sendInactivityReminder(User $user, int $days): array {
    $results = [
        'internal' => $this->createInternalNotification(...),
        'sms' => $this->twilioService->sendSms(...),
        'whatsapp' => $this->whatsAppService->sendMessage(...) // ✅ Nouveau
    ];
    return $results;
}
```

### Changer la Règle Métier (3 jours → 5 jours)

```php
// Modifier UNIQUEMENT InactivityDetectionService
private const INACTIVITY_THRESHOLD_DAYS = 5; // ✅ Un seul endroit
```

### Ajouter un Nouveau Type de Rappel

```php
// Créer une nouvelle méthode dans NotificationService
public function sendCourseUpdateReminder(User $user, Cours $cours): array {
    return $this->sendNotification(
        $user,
        'course_update',
        '📚 Nouveau contenu disponible',
        sprintf('Le cours %s a été mis à jour !', $cours->getTitre()),
        true // Envoyer aussi par SMS
    );
}
```

## 📊 Métriques et Monitoring

### Logs Structurés

```json
{
  "level": "info",
  "message": "Notification interne envoyée",
  "context": {
    "user_id": 42,
    "type": "inactivity_reminder",
    "timestamp": "2026-02-22T09:00:00+00:00"
  }
}

{
  "level": "info",
  "message": "SMS envoyé",
  "context": {
    "user_id": 42,
    "phone": "+21612345678",
    "sid": "SM1234567890abcdef",
    "status": "queued"
  }
}
```

### Statistiques en Temps Réel

```
┌──────────────────────────────────┬────────┐
│ Métrique                         │ Valeur │
├──────────────────────────────────┼────────┤
│ Étudiants inactifs détectés      │ 15     │
│ Notifications internes envoyées  │ 15     │
│ SMS envoyés                      │ 12     │
│ Erreurs                          │ 0      │
│ Taux de succès                   │ 100%   │
│ Durée d'exécution                │ 2.3s   │
└──────────────────────────────────┴────────┘
```

---

**Architecture** : Clean, Modulaire, SOLID ✓  
**Testable** : Chaque composant isolé ✓  
**Maintenable** : Séparation claire des responsabilités ✓  
**Scalable** : Prêt pour des milliers d'étudiants ✓
