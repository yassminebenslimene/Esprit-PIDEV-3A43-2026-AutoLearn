# 🔑 GUIDE DE CONFIGURATION DES APIs - Pour l'Équipe

**Date:** 19 Février 2026  
**Module:** Événement  
**Auteur:** Amira

---

## 🎯 OBJECTIF

Ce guide explique comment configurer les APIs (SendGrid et OpenWeatherMap) après avoir pullé la branche Amira.

**IMPORTANT:** Les clés API ne sont PAS dans Git pour des raisons de sécurité. Chaque membre de l'équipe doit configurer ses propres clés.

---

## ⚠️ POURQUOI LES APIs NE FONCTIONNENT PAS CHEZ VOUS?

### Le fichier `.env.local` n'est PAS dans Git

**Raison de sécurité:**
- `.env.local` contient des clés API secrètes
- Il est dans `.gitignore` → Jamais commité
- Chaque développeur doit créer son propre `.env.local`

**Ce que vous avez après le pull:**
- ✅ Le code des services (WeatherService, EmailService, etc.)
- ✅ Le fichier `.env.local.example` (template)
- ❌ PAS le fichier `.env.local` (clés API)

---

## 📋 ÉTAPES DE CONFIGURATION

### **Étape 1: Créer votre fichier `.env.local`**

**Option A: Copier le template (Recommandé)**

```bash
# Dans le terminal, à la racine du projet
copy .env.local.example .env.local
```

**Option B: Créer manuellement**

Créez un nouveau fichier nommé `.env.local` à la racine du projet.

---

### **Étape 2: Obtenir une clé SendGrid**

#### **2.1 Créer un compte SendGrid (Gratuit)**

1. Va sur: https://signup.sendgrid.com/
2. Clique "Start for Free"
3. Remplis le formulaire:
   - Email: Ton email personnel
   - Password: Choisis un mot de passe fort
   - Clique "Create Account"

4. Vérifie ton email et confirme le compte

#### **2.2 Créer une API Key**

1. Connecte-toi sur: https://app.sendgrid.com/
2. Va dans: **Settings** → **API Keys**
3. Clique "Create API Key"
4. Configuration:
   - **API Key Name:** `AutoLearn Development`
   - **API Key Permissions:** Sélectionne "Full Access"
   - Clique "Create & View"

5. **IMPORTANT:** Copie la clé immédiatement!
   - Format: `SG.xxxxxxxxxxxxxxxxxxxxxxx.yyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy`
   - Tu ne pourras plus la voir après avoir fermé la fenêtre

#### **2.3 Vérifier Sender Identity**

1. Va dans: **Settings** → **Sender Authentication**
2. Clique "Verify a Single Sender"
3. Remplis avec ton email personnel
4. Vérifie l'email de confirmation

**Alternative:** Utilise l'email du projet: `autolearnplateforme@gmail.com`

---

### **Étape 3: Obtenir une clé OpenWeatherMap**

#### **3.1 Créer un compte OpenWeatherMap (Gratuit)**

1. Va sur: https://home.openweathermap.org/users/sign_up
2. Remplis le formulaire:
   - Username: Ton pseudo
   - Email: Ton email
   - Password: Choisis un mot de passe
   - Accepte les conditions
   - Clique "Create Account"

3. Vérifie ton email et confirme le compte

#### **3.2 Obtenir l'API Key**

1. Connecte-toi sur: https://home.openweathermap.org/
2. Va dans: **API keys** (dans le menu)
3. Tu verras une clé par défaut déjà créée
4. Copie la clé (format: `xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx`)

**Note:** La clé peut prendre 10-15 minutes pour être activée après création du compte.

---

### **Étape 4: Configurer `.env.local`**

Ouvre ton fichier `.env.local` et remplace les valeurs:

```env
# ============================================
# CONFIGURATION LOCALE - NE PAS COMMITER
# ============================================

###> symfony/mailer - SENDGRID ###
# Remplace YOUR_REAL_SENDGRID_KEY par ta vraie clé
MAILER_DSN=sendgrid+api://SG.ta_cle_sendgrid_ici@default
###< symfony/mailer ###

###> openweathermap ###
# Remplace YOUR_REAL_OPENWEATHERMAP_KEY par ta vraie clé
WEATHER_API_KEY=ta_cle_openweathermap_ici
###< openweathermap ###
```

**Exemple avec de vraies clés (fictives):**

```env
###> symfony/mailer - SENDGRID ###
MAILER_DSN=sendgrid+api://SG.abc123xyz789.def456uvw012@default
###< symfony/mailer ###

###> openweathermap ###
WEATHER_API_KEY=5177b7da6160976397c624428cd12f3d
###< openweathermap ###
```

---

### **Étape 5: Vérifier la configuration**

#### **5.1 Vérifier que `.env.local` existe**

```bash
# Dans le terminal
dir .env.local
```

Tu devrais voir le fichier listé.

#### **5.2 Redémarrer le serveur Symfony**

```bash
# Arrête le serveur (Ctrl+C)
# Puis redémarre
symfony server:start
# ou
php -S localhost:8000 -t public
```

#### **5.3 Tester la météo**

1. Va sur: http://localhost:8000/events
2. Tu devrais voir la météo affichée pour chaque événement
3. Si tu vois "Unable to fetch weather data" → Vérifie ta clé OpenWeatherMap

#### **5.4 Tester l'envoi d'email**

**Option A: Via participation à un événement**
1. Crée un événement dans le backoffice
2. Participe depuis le frontoffice
3. Vérifie ton email (QR code, badge, .ics)

**Option B: Via commande de test**
```bash
php bin/console app:send-certificates
```

---

## 🔍 DÉPANNAGE

### **Problème 1: "Unable to fetch weather data"**

**Causes possibles:**
- Clé OpenWeatherMap invalide
- Clé pas encore activée (attends 10-15 min)
- Pas de connexion internet

**Solution:**
1. Vérifie que `WEATHER_API_KEY` est bien dans `.env.local`
2. Vérifie que la clé est correcte (32 caractères hexadécimaux)
3. Attends 15 minutes après création du compte
4. Teste la clé directement:
   ```
   https://api.openweathermap.org/data/2.5/weather?q=Tunis,TN&appid=TA_CLE_ICI
   ```

### **Problème 2: Emails non envoyés**

**Causes possibles:**
- Clé SendGrid invalide
- Sender identity non vérifiée
- Format DSN incorrect

**Solution:**
1. Vérifie le format: `sendgrid+api://SG.xxx@default`
2. Vérifie que la clé commence par `SG.`
3. Vérifie sender identity dans SendGrid
4. Regarde les logs Symfony:
   ```bash
   tail -f var/log/dev.log
   ```

### **Problème 3: `.env.local` ignoré**

**Cause:** Le fichier est peut-être mal nommé

**Solution:**
1. Vérifie le nom exact: `.env.local` (avec le point au début)
2. Pas d'espace, pas d'extension `.txt`
3. Sous Windows, active "Afficher les extensions de fichiers"

### **Problème 4: "Connection refused" SendGrid**

**Cause:** Firewall ou antivirus bloque la connexion

**Solution:**
1. Désactive temporairement l'antivirus
2. Vérifie les paramètres firewall
3. Essaie avec un autre réseau (WiFi différent)

---

## 📊 LIMITES DES PLANS GRATUITS

### **SendGrid Free Plan**
- ✅ 100 emails/jour
- ✅ Suffisant pour développement
- ❌ Pas assez pour production

### **OpenWeatherMap Free Plan**
- ✅ 1000 appels/jour
- ✅ 60 appels/minute
- ✅ Prévisions 5 jours
- ❌ Pas de prévisions au-delà de 5 jours

---

## 🔐 SÉCURITÉ - RÈGLES IMPORTANTES

### ❌ NE JAMAIS FAIRE:

1. **Commiter `.env.local` sur Git**
   ```bash
   # Vérifie que c'est dans .gitignore
   git status
   # .env.local ne doit PAS apparaître
   ```

2. **Partager tes clés API publiquement**
   - Pas sur Discord, Slack, WhatsApp
   - Pas dans des screenshots
   - Pas dans des documents partagés

3. **Utiliser les clés en production**
   - Les clés de dev sont différentes des clés de prod
   - Chaque environnement a ses propres clés

### ✅ BONNES PRATIQUES:

1. **Chaque développeur a ses propres clés**
   - Ne partage pas tes clés avec l'équipe
   - Chacun crée son compte gratuit

2. **Régénère les clés si compromises**
   - Si tu as accidentellement commité une clé
   - Régénère-la immédiatement dans SendGrid/OpenWeatherMap

3. **Utilise `.env.local.example` comme référence**
   - Garde-le à jour avec les nouvelles variables
   - Commite-le sur Git (sans les vraies valeurs)

---

## 📝 CHECKLIST DE CONFIGURATION

Coche chaque étape après l'avoir complétée:

- [ ] J'ai pullé la branche Amira
- [ ] J'ai créé mon fichier `.env.local`
- [ ] J'ai créé un compte SendGrid
- [ ] J'ai obtenu ma clé API SendGrid
- [ ] J'ai vérifié mon sender identity SendGrid
- [ ] J'ai créé un compte OpenWeatherMap
- [ ] J'ai obtenu ma clé API OpenWeatherMap
- [ ] J'ai configuré les deux clés dans `.env.local`
- [ ] J'ai redémarré le serveur Symfony
- [ ] J'ai testé la météo sur /events
- [ ] J'ai testé l'envoi d'email (participation)
- [ ] Tout fonctionne correctement ✅

---

## 🆘 BESOIN D'AIDE?

### **Si tu es bloqué:**

1. **Vérifie ce guide en entier**
2. **Regarde les logs Symfony:**
   ```bash
   tail -f var/log/dev.log
   ```
3. **Contacte Amira** (créatrice du module Événement)
4. **Partage l'erreur exacte** (pas juste "ça marche pas")

### **Informations utiles à partager:**

- Message d'erreur complet
- Contenu de `.env.local` (SANS les vraies clés!)
- Version PHP: `php -v`
- Version Symfony: `php bin/console --version`
- Système d'exploitation

---

## 🎓 RESSOURCES SUPPLÉMENTAIRES

### **Documentation officielle:**

- **SendGrid:** https://docs.sendgrid.com/
- **OpenWeatherMap:** https://openweathermap.org/api
- **Symfony Mailer:** https://symfony.com/doc/current/mailer.html
- **Symfony HTTP Client:** https://symfony.com/doc/current/http_client.html

### **Tutoriels vidéo:**

- SendGrid Setup: https://www.youtube.com/results?search_query=sendgrid+api+key
- OpenWeatherMap API: https://www.youtube.com/results?search_query=openweathermap+api+tutorial

---

## ✅ RÉSUMÉ RAPIDE

**En 5 minutes:**

1. Copie `.env.local.example` → `.env.local`
2. Crée compte SendGrid → Copie clé API
3. Crée compte OpenWeatherMap → Copie clé API
4. Colle les clés dans `.env.local`
5. Redémarre serveur → Teste!

**C'est tout!** 🎉

---

**Dernière mise à jour:** 19 Février 2026  
**Version:** 1.0  
**Auteur:** Amira - Module Événement
