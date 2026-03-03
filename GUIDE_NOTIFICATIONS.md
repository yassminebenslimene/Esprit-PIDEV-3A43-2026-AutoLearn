# 🔔 Guide du Système de Notifications

## 📋 Vue d'ensemble

Le système de notifications permet d'envoyer des alertes aux utilisateurs (étudiants) pour :
- Rappels d'inactivité (pas de validation de chapitre depuis 3 jours)
- Notifications personnalisées
- Alertes système

---

## 🚀 Installation pour ton ami

### Étape 1 : Récupérer le code

```bash
git pull origin [ta-branche]
```

### Étape 2 : Installer les dépendances

```bash
composer install
```

### Étape 3 : Mettre à jour la base de données

```bash
php bin/console doctrine:schema:update --force
```

Cette commande va créer la table `notification` automatiquement.

### Étape 4 : Vider le cache

```bash
php bin/console cache:clear
```

### Étape 5 : Vérifier que ça fonctionne

```bash
# Tester la commande de rappels
php bin/console app:send-inactivity-reminders --dry-run
```

Si tu vois "✓ Aucun étudiant inactif détecté" ou une liste d'étudiants, c'est bon ! ✅

---

## 📁 Structure du système

```
src/
├── Entity/
│   └── Notification.php              # Entité base de données
├── Service/
│   ├── NotificationService.php       # Création et envoi de notifications
│   └── InactivityDetectionService.php # Détection des étudiants inactifs
├── Controller/
│   └── NotificationController.php    # Routes et API
├── Command/
│   └── SendInactivityRemindersCommand.php # Commande cron
└── Repository/
    └── NotificationRepository.php    # Requêtes base de données

templates/
└── frontoffice/
    └── notifications/
        └── index.html.twig           # Page des notifications
```

---

## 🔧 Comment utiliser le système

### 1. Créer une notification manuellement

Dans n'importe quel contrôleur ou service :

```php
use App\Service\NotificationService;

class MonController extends AbstractController
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function maMethode(User $user)
    {
        // Créer une notification
        $this->notificationService->createInternalNotification(
            $user,
            'info',                    // Type : info, warning, success, error
            'Nouveau cours disponible', // Titre
            'Le cours PHP avancé est maintenant disponible !' // Message
        );
    }
}
```

### 2. Envoyer un rappel d'inactivité

```php
use App\Service\NotificationService;
use App\Service\InactivityDetectionService;

// Détecter les étudiants inactifs
$inactiveStudents = $this->inactivityService->detectInactiveStudents();

// Envoyer les rappels
foreach ($inactiveStudents as $student) {
    $days = $this->inactivityService->getInactivityDays($student);
    
    $this->notificationService->sendInactivityReminder($student, $days);
}
```

### 3. Vérifier si un étudiant est inactif

```php
use App\Service\InactivityDetectionService;

if ($this->inactivityService->isStudentInactive($user)) {
    // L'étudiant est inactif depuis 3+ jours
    $days = $this->inactivityService->getInactivityDays($user);
    echo "Inactif depuis $days jours";
}
```

### 4. Mettre à jour la dernière activité

Quand un étudiant valide un chapitre :

```php
use App\Service\InactivityDetectionService;

// Après validation d'un chapitre
$this->inactivityService->updateLastActivity($user);
```

---

## 🌐 Routes disponibles

| Route | Méthode | Description |
|-------|---------|-------------|
| `/notifications/` | GET | Liste des notifications |
| `/notifications/{id}/mark-read` | POST | Marquer comme lu |
| `/notifications/mark-all-read` | POST | Tout marquer comme lu |
| `/notifications/{id}/delete` | POST | Supprimer |
| `/notifications/api/unread-count` | GET | Nombre non lus (JSON) |
| `/notifications/api/recent` | GET | 5 dernières (JSON) |

---

## 🤖 Automatisation avec Cron

### Commande disponible

```bash
php bin/console app:send-inactivity-reminders
```

**Options :**
- `--dry-run` : Simulation sans envoi réel

### Configuration du Cron (Windows)

Créer un fichier `rappels-inactivite.bat` :

```batch
@echo off
cd C:\chemin\vers\ton\projet
php bin/console app:send-inactivity-reminders
```

Puis configurer dans le Planificateur de tâches Windows :
- Déclencheur : Tous les jours à 9h00
- Action : Exécuter `rappels-inactivite.bat`

### Configuration du Cron (Linux/Mac)

```bash
crontab -e
```

Ajouter :
```
0 9 * * * cd /chemin/vers/projet && php bin/console app:send-inactivity-reminders
```

---

## 🎨 Personnalisation

### Changer le seuil d'inactivité

Dans `src/Service/InactivityDetectionService.php` :

```php
// Ligne 14
private const INACTIVITY_THRESHOLD_DAYS = 3; // Changer ici (ex: 7 pour 7 jours)
```

### Personnaliser le message de rappel

Dans `src/Service/NotificationService.php` :

```php
// Ligne 28-33
sprintf(
    'Bonjour %s, nous avons remarqué que vous n\'avez pas validé de chapitre depuis %d jours. ' .
    'Continuez votre apprentissage pour progresser ! 🚀',
    $user->getPrenom(),
    $inactivityDays
)
```

### Ajouter un badge de notifications

Dans `templates/frontoffice/base.html.twig` :

```twig
<a href="{{ path('app_notifications_index') }}" class="notification-bell">
    🔔
    <span class="badge" id="notification-count">0</span>
</a>

<script>
// Mettre à jour le badge toutes les 30 secondes
setInterval(async () => {
    const response = await fetch('/notifications/api/unread-count');
    const data = await response.json();
    document.getElementById('notification-count').textContent = data.count;
}, 30000);
</script>
```

---

## 🗄️ Structure de la table `notification`

```sql
CREATE TABLE notification (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,           -- Type de notification
    title VARCHAR(255) NOT NULL,         -- Titre
    message TEXT NOT NULL,               -- Contenu
    is_read BOOLEAN DEFAULT FALSE,       -- Lu/Non lu
    created_at DATETIME NOT NULL,        -- Date de création
    read_at DATETIME DEFAULT NULL,       -- Date de lecture
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
);
```

---

## 🧪 Tests

### Test 1 : Créer une notification de test

```bash
php bin/console doctrine:query:sql "INSERT INTO notification (user_id, type, title, message, is_read, created_at) VALUES (1, 'test', 'Test notification', 'Ceci est un test', 0, NOW())"
```

### Test 2 : Vérifier les notifications

```bash
php bin/console doctrine:query:sql "SELECT * FROM notification"
```

### Test 3 : Tester la commande

```bash
php bin/console app:send-inactivity-reminders --dry-run
```

### Test 4 : Tester l'API

```bash
# Nombre de notifications non lues
curl http://localhost:8000/notifications/api/unread-count

# Dernières notifications
curl http://localhost:8000/notifications/api/recent
```

---

## 🚨 Dépannage

### Erreur : "Table 'notification' doesn't exist"

```bash
php bin/console doctrine:schema:update --force
```

### Erreur : "Service not found"

```bash
php bin/console cache:clear
composer dump-autoload
```

### Les notifications ne s'affichent pas

1. Vérifier que l'utilisateur est connecté
2. Vérifier la table : `SELECT * FROM notification WHERE user_id = [id]`
3. Vider le cache : `php bin/console cache:clear`

### La commande cron ne fonctionne pas

```bash
# Tester en mode debug
php bin/console app:send-inactivity-reminders --dry-run -vvv
```

---

## 📊 Statistiques

Pour voir les statistiques des notifications :

```sql
-- Nombre total de notifications
SELECT COUNT(*) FROM notification;

-- Notifications par utilisateur
SELECT user_id, COUNT(*) as total 
FROM notification 
GROUP BY user_id;

-- Notifications non lues
SELECT COUNT(*) FROM notification WHERE is_read = 0;

-- Notifications par type
SELECT type, COUNT(*) as total 
FROM notification 
GROUP BY type;
```

---

## 🎯 Prochaines améliorations possibles

- [ ] Notifications push (navigateur)
- [ ] Notifications par email
- [ ] Notifications par SMS (Twilio)
- [ ] Notifications en temps réel (Mercure/WebSocket)
- [ ] Préférences de notification par utilisateur
- [ ] Groupement de notifications
- [ ] Notifications avec actions (boutons)

---

## 📞 Support

Si ton ami a des problèmes :

1. Vérifier qu'il a bien fait `composer install`
2. Vérifier qu'il a bien fait `doctrine:schema:update --force`
3. Vérifier qu'il a vidé le cache
4. Tester la commande en mode `--dry-run`

**Tout devrait fonctionner après ces étapes !** ✅
