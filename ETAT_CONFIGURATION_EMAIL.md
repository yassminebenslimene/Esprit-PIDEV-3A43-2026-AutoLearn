# État de la Configuration Email

## ✅ Ce qui est configuré

### 1. Services Email
- ✅ `MailerService.php` - Service Symfony Mailer (SMTP)
- ✅ `BrevoMailService.php` - Service Brevo API (recommandé)
- ✅ `EmailService.php` - Service pour certificats

### 2. Configuration dans .env
```env
###> Brevo API ###
BREVO_API_KEY=your_brevo_api_key_here          # ⚠️ À CONFIGURER
MAIL_FROM_EMAIL=your_email@example.com         # ⚠️ À CONFIGURER
MAIL_FROM_NAME=AutoLearn                       # ✅ OK
###< Brevo API ###

###> Brevo SMTP ###
MAILER_DSN=smtp://apikey:your_brevo_smtp_key_here@smtp-relay.brevo.com:587  # ⚠️ À CONFIGURER
###< Brevo SMTP ###
```

### 3. Fonctionnalités Email Disponibles
- ✅ Email de bienvenue (avec mot de passe temporaire)
- ✅ Email de confirmation d'inscription
- ✅ Email de réinitialisation de mot de passe
- ✅ Email de suspension de compte
- ✅ Email de réactivation de compte
- ✅ Email de formulaire de contact
- ✅ Notification admin pour suspension automatique

### 4. Templates Email
Les templates sont dans `templates/emails/` :
- ✅ `welcome.html.twig` / `welcome.txt.twig`
- ✅ `registration_confirmation.html.twig` / `registration_confirmation.txt.twig`
- ✅ `suspension.html.twig` / `suspension.txt.twig`
- ✅ `reactivation.html.twig` / `reactivation.txt.twig`
- ✅ `admin_inactive_notification.html.twig` / `admin_inactive_notification.txt.twig`

## ⚠️ Ce qui MANQUE (API Keys)

### 1. Clé API Brevo
**Statut** : ❌ NON CONFIGURÉE

**Où l'obtenir** :
1. Créer un compte sur https://www.brevo.com (gratuit)
2. Aller dans : Settings → SMTP & API → API Keys
3. Créer une nouvelle clé API
4. Copier la clé dans `.env` :
   ```env
   BREVO_API_KEY=xkeysib-votre_cle_ici
   ```

### 2. Email d'expéditeur
**Statut** : ❌ NON CONFIGURÉ

**Configuration** :
1. Vérifier votre domaine ou email sur Brevo
2. Mettre à jour dans `.env` :
   ```env
   MAIL_FROM_EMAIL=noreply@votredomaine.com
   # OU
   MAIL_FROM_EMAIL=votre.email@gmail.com
   ```

### 3. SMTP Key (optionnel si vous utilisez l'API)
**Statut** : ❌ NON CONFIGURÉE

**Si vous préférez SMTP au lieu de l'API** :
```env
MAILER_DSN=smtp://apikey:votre_smtp_key@smtp-relay.brevo.com:587
```

## 📋 Checklist de Configuration

### Étape 1 : Créer un compte Brevo
- [ ] Aller sur https://www.brevo.com
- [ ] Créer un compte gratuit (300 emails/jour)
- [ ] Vérifier votre email

### Étape 2 : Obtenir la clé API
- [ ] Aller dans Settings → SMTP & API → API Keys
- [ ] Cliquer sur "Create a new API key"
- [ ] Copier la clé (commence par `xkeysib-`)

### Étape 3 : Configurer .env
- [ ] Ouvrir `.env`
- [ ] Remplacer `your_brevo_api_key_here` par votre vraie clé
- [ ] Remplacer `your_email@example.com` par votre email vérifié

### Étape 4 : Vérifier l'email expéditeur
- [ ] Dans Brevo : Settings → Senders & IP
- [ ] Ajouter et vérifier votre email
- [ ] Utiliser cet email dans `MAIL_FROM_EMAIL`

### Étape 5 : Tester
```bash
# Tester l'envoi d'email
php test_brevo_api.php

# OU via le contrôleur de test
# Aller sur : http://localhost:8000/test-mail
```

## 🔧 Services Disponibles

### BrevoMailService (Recommandé)
```php
// Injection dans un contrôleur
public function __construct(
    private BrevoMailService $brevoMailService
) {}

// Envoyer un email de bienvenue
$this->brevoMailService->sendWelcomeEmail(
    'etudiant@example.com',
    'Ahmed Ben Ali',
    'MotDePasse123',
    'https://autolearn.com/login'
);

// Envoyer une confirmation d'inscription
$this->brevoMailService->sendRegistrationConfirmation(
    'etudiant@example.com',
    'Ahmed Ben Ali',
    'https://autolearn.com/login'
);

// Envoyer un email de contact
$this->brevoMailService->sendContactEmail(
    'Visiteur',
    'visiteur@example.com',
    'Question sur les cours',
    'Bonjour, j\'ai une question...'
);
```

### MailerService (Alternative SMTP)
```php
// Injection dans un contrôleur
public function __construct(
    private MailerService $mailerService
) {}

// Utilisation identique à BrevoMailService
$this->mailerService->sendWelcomeEmail(...);
```

## 🎯 Recommandation

**Utilisez BrevoMailService** car :
- ✅ Plus fiable que SMTP
- ✅ Meilleure délivrabilité
- ✅ Statistiques d'envoi
- ✅ 300 emails/jour gratuits
- ✅ Pas de configuration SMTP complexe

## 📊 Limites Gratuites Brevo

- 📧 300 emails par jour
- 👥 Contacts illimités
- 📈 Statistiques basiques
- ✅ Parfait pour un projet étudiant

## ⚡ Action Immédiate Requise

Pour que les emails fonctionnent, vous DEVEZ :

1. **Créer un compte Brevo** (5 minutes)
2. **Obtenir la clé API** (2 minutes)
3. **Configurer .env** (1 minute)

**Total : ~8 minutes de configuration**

Sans ces clés, aucun email ne sera envoyé !
