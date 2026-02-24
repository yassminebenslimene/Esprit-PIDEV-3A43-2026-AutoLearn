# 🔍 Diagnostic Twilio - Pourquoi les SMS ne sont pas envoyés

## ❌ Problème Identifié

Ton fichier `.env` contient des valeurs de configuration par défaut:
```
TWILIO_ACCOUNT_SID=your_account_sid_here
TWILIO_AUTH_TOKEN=your_auth_token_here
TWILIO_PHONE_NUMBER=+1234567890
```

Ces valeurs ne sont pas réelles, donc Twilio ne peut pas envoyer de SMS.

---

## ✅ Solution: Configurer Twilio

### Étape 1: Créer un compte Twilio (si pas déjà fait)

1. Va sur https://www.twilio.com/try-twilio
2. Inscris-toi gratuitement
3. Vérifie ton email et ton numéro de téléphone

### Étape 2: Récupérer tes identifiants

1. Connecte-toi à https://console.twilio.com
2. Sur le Dashboard, tu verras:
   - **Account SID** (commence par AC...)
   - **Auth Token** (clique sur "Show" pour le voir)

### Étape 3: Obtenir un numéro Twilio

**Option A: Compte Trial (Gratuit)**
- Tu reçois un numéro de test gratuit
- Limitations: 
  - Tu ne peux envoyer qu'aux numéros vérifiés
  - Les SMS contiennent "Sent from your Twilio trial account"

**Option B: Compte Payant**
- Achète un numéro Twilio (~1€/mois)
- Pas de limitations

Pour obtenir ton numéro:
1. Va dans **Phone Numbers** → **Manage** → **Buy a number**
2. Choisis un numéro avec capacité SMS
3. Achète-le (gratuit en trial)

### Étape 4: Vérifier ton numéro de téléphone (Trial uniquement)

Si tu es en mode Trial:
1. Va dans **Phone Numbers** → **Manage** → **Verified Caller IDs**
2. Clique sur **Add a new Caller ID**
3. Entre ton numéro de téléphone (format international: +216XXXXXXXX)
4. Tu recevras un code de vérification par SMS
5. Entre le code pour valider

### Étape 5: Mettre à jour ton fichier .env

Remplace dans `.env`:
```env
###> Twilio SMS Configuration ###
TWILIO_ACCOUNT_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_AUTH_TOKEN=your_real_auth_token_here
TWILIO_PHONE_NUMBER=+15551234567
###< Twilio SMS Configuration ###
```

**Important:**
- `TWILIO_ACCOUNT_SID`: Commence par "AC" suivi de 32 caractères
- `TWILIO_AUTH_TOKEN`: 32 caractères alphanumériques
- `TWILIO_PHONE_NUMBER`: Le numéro Twilio que tu as obtenu (format +1...)

---

## 🧪 Test Rapide

### Test 1: Vérifier la configuration

Crée un fichier de test:

```php
// public/test-twilio.php
<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Twilio\Rest\Client;

$accountSid = 'ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'; // Ton SID
$authToken = 'your_auth_token';                      // Ton Token
$twilioNumber = '+15551234567';                      // Ton numéro Twilio
$toNumber = '+216XXXXXXXX';                          // Ton numéro vérifié

try {
    $client = new Client($accountSid, $authToken);
    
    $message = $client->messages->create(
        $toNumber,
        [
            'from' => $twilioNumber,
            'body' => 'Test Twilio depuis AutoLearn ✓'
        ]
    );
    
    echo "✅ SMS envoyé avec succès!\n";
    echo "SID: " . $message->sid . "\n";
    echo "Status: " . $message->status . "\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
```

Exécute:
```cmd
php public/test-twilio.php
```

### Test 2: Via la commande Symfony

```cmd
php bin/console app:send-inactivity-reminders
```

Vérifie les logs:
```cmd
type var\log\dev.log | findstr Twilio
```

---

## 🔍 Erreurs Courantes

### Erreur 1: "Unable to create record: The 'To' number is not a valid phone number"
**Solution:** Le numéro destinataire doit être au format international (+216...)

### Erreur 2: "The number +216XXXXXXXX is unverified"
**Solution:** En mode Trial, tu dois vérifier le numéro dans la console Twilio

### Erreur 3: "Authenticate"
**Solution:** Vérifie que ton Account SID et Auth Token sont corrects

### Erreur 4: "The 'From' number +1234567890 is not a valid phone number"
**Solution:** Utilise le vrai numéro Twilio que tu as obtenu

---

## 📊 Vérifier l'envoi dans Twilio Console

1. Va sur https://console.twilio.com/us1/monitor/logs/sms
2. Tu verras tous les SMS envoyés avec leur statut:
   - **Queued**: En attente
   - **Sent**: Envoyé
   - **Delivered**: Livré ✓
   - **Failed**: Échec ❌

---

## 🆓 Alternative Gratuite: Mode Simulation

Si tu ne veux pas utiliser Twilio pour l'instant, tu peux simuler l'envoi:

Modifie `TwilioSmsService.php`:

```php
public function sendSms(string $toNumber, string $message): bool
{
    // MODE SIMULATION - Retirer en production
    $this->logger->info('📱 [SIMULATION] SMS envoyé', [
        'to' => $toNumber,
        'message' => $message
    ]);
    return true;
    
    // Code Twilio réel ci-dessous...
}
```

---

## 📝 Checklist de Configuration

- [ ] Compte Twilio créé
- [ ] Account SID récupéré
- [ ] Auth Token récupéré
- [ ] Numéro Twilio obtenu
- [ ] Numéro destinataire vérifié (si Trial)
- [ ] Fichier .env mis à jour
- [ ] Cache Symfony vidé: `php bin/console cache:clear`
- [ ] Test effectué

---

## 💰 Coûts Twilio

**Mode Trial (Gratuit):**
- Crédit gratuit: $15
- ~0.0075€ par SMS
- Environ 2000 SMS gratuits

**Mode Production:**
- ~0.0075€ par SMS
- Location numéro: ~1€/mois

---

## 🚀 Prochaines Étapes

1. Configure tes vraies credentials Twilio
2. Teste avec `test-twilio.php`
3. Si ça marche, teste la commande complète
4. Vérifie les logs pour confirmer l'envoi

Besoin d'aide pour une étape spécifique?
