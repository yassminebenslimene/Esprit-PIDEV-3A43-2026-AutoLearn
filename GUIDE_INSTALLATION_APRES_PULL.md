# 📋 GUIDE D'INSTALLATION APRÈS PULL (Branche Amira)

## ⚠️ IMPORTANT - À LIRE AVANT DE COMMENCER

Ce guide est pour les camarades qui vont pull la branche `Amira` après le merge avec `ilef`.

## 🔄 Étapes d'installation (DANS L'ORDRE)

### 1️⃣ Pull de la branche

```bash
git checkout Amira
git pull origin Amira
```

### 2️⃣ Installer les dépendances

```bash
composer install
```

### 3️⃣ Mettre à jour la base de données

**OPTION A - Recommandée (Mise à jour automatique):**
```bash
php bin/console doctrine:schema:update --force
```

**OPTION B - Si vous préférez les migrations:**
```bash
php bin/console doctrine:migrations:migrate --no-interaction
```

⚠️ **Note:** Si vous avez des erreurs avec les migrations, utilisez l'OPTION A.

### 4️⃣ Vider le cache

```bash
php bin/console cache:clear
```

### 5️⃣ Configurer votre fichier .env.local

Créez un fichier `.env.local` avec vos clés API personnelles:

```env
###> Brevo Email Configuration ###
MAILER_DSN=brevo+api://VOTRE_CLE_BREVO_ICI@default
###< Brevo ###

###> Groq API (Optionnel) ###
GROQ_API_KEY=votre_cle_groq_ici
###< Groq API ###

###> Hugging Face API (Optionnel) ###
HUGGINGFACE_API_KEY=votre_cle_huggingface_ici
###< Hugging Face API ###
```

## ✅ Vérification

Testez que tout fonctionne:

```bash
php bin/console about
```

## 🆘 En cas de problème

### Erreur: "Column not found"
```bash
php bin/console doctrine:schema:update --force
php bin/console cache:clear
```

### Erreur: "Table already exists"
```bash
# Utilisez l'OPTION A ci-dessus au lieu des migrations
php bin/console doctrine:schema:update --force
```

### Erreur avec les migrations
```bash
# Synchroniser les métadonnées
php bin/console doctrine:migrations:sync-metadata-storage
```

## 📧 Module Événement

Toutes les fonctionnalités du module Événement sont intactes:
- ✅ Création/modification/suppression d'événements
- ✅ Gestion des équipes et participations
- ✅ Envoi d'emails (confirmation, annulation, rappels, certificats)
- ✅ Workflow (planifié → en cours → terminé → annulé)
- ✅ Calendrier intégré
- ✅ Rapports AI avec Hugging Face
- ✅ Analytics et feedbacks

## 🔑 Clés API nécessaires

### Brevo (Email) - OBLIGATOIRE
1. Créer un compte sur https://www.brevo.com
2. Aller dans "SMTP & API" → "API Keys"
3. Créer une nouvelle clé API
4. Vérifier votre email expéditeur dans "Senders"

### Groq (AI) - OPTIONNEL
1. Créer un compte sur https://console.groq.com
2. Générer une clé API

### Hugging Face (AI Rapports) - OPTIONNEL
1. Créer un compte sur https://huggingface.co
2. Aller dans Settings → Access Tokens
3. Créer un token avec permission "Make calls to Inference Providers"

## 📞 Support

En cas de problème, contactez Amira ou consultez les fichiers de documentation dans le projet.
