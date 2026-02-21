# 📦 GUIDE D'INSTALLATION DES PACKAGES - Pour l'Équipe

**IMPORTANT:** Après avoir pullé la branche Amira, vous DEVEZ installer les packages!

---

## ⚠️ POURQUOI ÇA NE FONCTIONNE PAS?

### Le dossier `vendor/` n'est PAS dans Git!

**Raison:**
- `vendor/` contient tous les packages PHP (très lourd: ~100-200 MB)
- Il est dans `.gitignore` → Jamais commité
- Chaque développeur doit installer les packages avec Composer

**Ce que vous avez après le pull:**
- ✅ `composer.json` (liste des packages)
- ✅ `composer.lock` (versions exactes)
- ❌ `vendor/` (packages installés) ← À installer!

---

## 📋 PACKAGES NÉCESSAIRES POUR LE MODULE ÉVÉNEMENT

### **1. symfony/http-client** - Pour Weather API
```json
"symfony/http-client": "6.4.*"
```
**Utilité:** Fait des requêtes HTTP vers OpenWeatherMap

### **2. symfony/sendgrid-mailer** - Pour SendGrid
```json
"symfony/sendgrid-mailer": "6.4.*"
```
**Utilité:** Envoie emails via SendGrid

### **3. symfony/mailer** - Système d'emails
```json
"symfony/mailer": "6.4.*"
```
**Utilité:** Gère l'envoi d'emails (base)

### **4. dompdf/dompdf** - Génération PDF
```json
"dompdf/dompdf": "^3.1"
```
**Utilité:** Génère certificats et badges PDF

### **5. endroid/qr-code** - QR Codes
```json
"endroid/qr-code": "^6.0"
```
**Utilité:** Génère QR codes pour participations

### **6. guzzlehttp/guzzle** - Client HTTP
```json
"guzzlehttp/guzzle": "^7.10"
```
**Utilité:** Utilisé par BrevoMailService

---

## 🚀 INSTALLATION COMPLÈTE (3 ÉTAPES)

### **Étape 1: Installer Composer (si pas déjà fait)**

#### **Vérifier si Composer est installé:**
```bash
composer --version
```

Si tu vois la version (ex: `Composer version 2.x.x`), passe à l'Étape 2.

#### **Si Composer n'est pas installé:**

**Windows:**
1. Télécharge: https://getcomposer.org/Composer-Setup.exe
2. Exécute l'installateur
3. Redémarre le terminal
4. Vérifie: `composer --version`

**Mac/Linux:**
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

---

### **Étape 2: Installer TOUS les packages**

**Dans le terminal, à la racine du projet:**

```bash
composer install
```

**Ce que cette commande fait:**
1. Lit `composer.json` et `composer.lock`
2. Télécharge tous les packages nécessaires
3. Les installe dans le dossier `vendor/`
4. Configure l'autoloading

**Durée:** 2-5 minutes (selon connexion internet)

**Résultat attendu:**
```
Loading composer repositories with package information
Installing dependencies from lock file
Package operations: 150 installs, 0 updates, 0 removals
  - Installing symfony/http-client (v6.4.x)
  - Installing symfony/sendgrid-mailer (v6.4.x)
  - Installing dompdf/dompdf (v3.1.x)
  - Installing endroid/qr-code (v6.0.x)
  ...
Generating autoload files
```

---

### **Étape 3: Configurer les clés API**

**Créer `.env.local` avec les clés:**

```bash
# Créer le fichier
copy .env.local.example .env.local
```

**Puis éditer `.env.local` et coller:**

```env
###> symfony/mailer - SENDGRID ###
MAILER_DSN=sendgrid+api://SG.VOTRE_CLE_SENDGRID_ICI@default
###< symfony/mailer ###

###> openweathermap ###
WEATHER_API_KEY=VOTRE_CLE_OPENWEATHERMAP_ICI
###< openweathermap ###
```

---

## ✅ VÉRIFICATION

### **1. Vérifier que vendor/ existe:**
```bash
dir vendor
# ou
ls vendor
```

Tu devrais voir plein de dossiers (symfony, dompdf, endroid, etc.)

### **2. Vérifier les packages spécifiques:**
```bash
composer show | findstr "http-client"
composer show | findstr "sendgrid"
composer show | findstr "dompdf"
composer show | findstr "qr-code"
```

Chaque commande doit afficher le package installé.

### **3. Vérifier la configuration Symfony:**
```bash
php bin/console debug:config framework http_client
```

Doit afficher la configuration du HTTP client.

### **4. Vérifier le mailer:**
```bash
php bin/console debug:config framework mailer
```

Doit afficher la configuration SendGrid.

---

## 🧪 TESTER LES FONCTIONNALITÉS

### **Test 1: Weather API**

1. Démarre le serveur:
   ```bash
   symfony server:start
   # ou
   php -S localhost:8000 -t public
   ```

2. Va sur: http://localhost:8000/events

3. **Résultat attendu:**
   - Tu vois la météo pour chaque événement
   - Température, description, icône

4. **Si erreur "Unable to fetch weather data":**
   - Vérifie que `symfony/http-client` est installé
   - Vérifie que `WEATHER_API_KEY` est dans `.env.local`
   - Regarde les logs: `tail -f var/log/dev.log`

### **Test 2: Envoi d'emails**

1. Crée un événement dans le backoffice

2. Participe depuis le frontoffice

3. **Résultat attendu:**
   - Email reçu avec QR code
   - Badge PDF attaché
   - Fichier .ics attaché

4. **Si pas d'email reçu:**
   - Vérifie que `symfony/sendgrid-mailer` est installé
   - Vérifie que `MAILER_DSN` est dans `.env.local`
   - Vérifie les logs: `tail -f var/log/dev.log`
   - Vérifie spam/courrier indésirable

### **Test 3: Génération PDF**

1. Exécute la commande de test:
   ```bash
   php bin/console app:send-certificates
   ```

2. **Résultat attendu:**
   - Message "Terminé! X certificats envoyés"
   - Email avec certificat PDF

3. **Si erreur:**
   - Vérifie que `dompdf/dompdf` est installé
   - Regarde les logs: `tail -f var/log/dev.log`

---

## 🔧 DÉPANNAGE

### **Problème 1: "composer: command not found"**

**Cause:** Composer pas installé

**Solution:**
1. Installe Composer (voir Étape 1)
2. Redémarre le terminal
3. Réessaie `composer install`

### **Problème 2: "Your requirements could not be resolved"**

**Cause:** Conflit de versions PHP ou packages

**Solution:**
```bash
# Supprime le cache
composer clear-cache

# Réinstalle
composer install --no-cache
```

### **Problème 3: "Class 'Symfony\Component\HttpClient\HttpClient' not found"**

**Cause:** Package `symfony/http-client` pas installé

**Solution:**
```bash
composer require symfony/http-client
```

### **Problème 4: "Class 'Dompdf\Dompdf' not found"**

**Cause:** Package `dompdf/dompdf` pas installé

**Solution:**
```bash
composer require dompdf/dompdf
```

### **Problème 5: "Class 'Endroid\QrCode\QrCode' not found"**

**Cause:** Package `endroid/qr-code` pas installé

**Solution:**
```bash
composer require endroid/qr-code
```

### **Problème 6: Erreur "Failed to download"**

**Cause:** Problème de connexion internet ou proxy

**Solution:**
```bash
# Essaie avec timeout plus long
composer install --timeout=600

# Ou désactive SSL (temporaire)
composer config -g -- disable-tls false
composer config -g -- secure-http false
composer install
```

### **Problème 7: "Memory limit exceeded"**

**Cause:** Pas assez de mémoire pour Composer

**Solution:**
```bash
# Windows
set COMPOSER_MEMORY_LIMIT=-1
composer install

# Mac/Linux
COMPOSER_MEMORY_LIMIT=-1 composer install
```

---

## 📊 CHECKLIST COMPLÈTE

Coche chaque étape:

- [ ] J'ai pullé la branche Amira
- [ ] Composer est installé (`composer --version`)
- [ ] J'ai exécuté `composer install`
- [ ] Le dossier `vendor/` existe
- [ ] J'ai créé `.env.local`
- [ ] J'ai copié les clés API dans `.env.local`
- [ ] J'ai redémarré le serveur Symfony
- [ ] La météo s'affiche sur /events
- [ ] L'envoi d'email fonctionne
- [ ] Tout fonctionne correctement ✅

---

## 🎯 COMMANDES RÉCAPITULATIVES

**Installation complète en 3 commandes:**

```bash
# 1. Installer les packages
composer install

# 2. Créer .env.local
copy .env.local.example .env.local

# 3. Éditer .env.local et coller les clés API
# (voir contenu ci-dessus)

# 4. Redémarrer le serveur
symfony server:start
```

---

## 📝 POUR AMIRA (Créatrice du module)

**Packages que tu as installés pour le module Événement:**

```bash
# HTTP Client pour Weather API
composer require symfony/http-client

# SendGrid pour emails
composer require symfony/sendgrid-mailer

# Mailer de base
composer require symfony/mailer

# PDF pour certificats/badges
composer require dompdf/dompdf

# QR Codes
composer require endroid/qr-code

# Guzzle pour Brevo
composer require guzzlehttp/guzzle
```

**Ces packages sont listés dans `composer.json` et seront installés automatiquement avec `composer install`.**

---

## 🆘 BESOIN D'AIDE?

### **Si bloqué:**

1. **Vérifie les logs:**
   ```bash
   tail -f var/log/dev.log
   ```

2. **Vérifie la version PHP:**
   ```bash
   php -v
   ```
   Doit être >= 8.1

3. **Vérifie Symfony:**
   ```bash
   php bin/console --version
   ```

4. **Contacte Amira** avec:
   - Message d'erreur complet
   - Résultat de `composer --version`
   - Résultat de `php -v`
   - Contenu de `.env.local` (SANS les vraies clés!)

---

## ✅ RÉSUMÉ

**Pourquoi ça ne fonctionnait pas:**
- ❌ Packages pas installés (vendor/ manquant)
- ❌ Clés API pas configurées (.env.local manquant)

**Solution:**
1. ✅ `composer install` (installe packages)
2. ✅ Créer `.env.local` (configure clés)
3. ✅ Redémarrer serveur

**Durée totale:** 5-10 minutes

---

**Dernière mise à jour:** 19 Février 2026  
**Version:** 1.0  
**Auteur:** Amira - Module Événement
