# 🧪 Test Rapide du Système de Rappel d'Inactivité

## 🎯 Test en 5 Étapes Simples (10 minutes)

### Étape 1 : Créer les Tables en Base de Données

Ouvrez **phpMyAdmin** et exécutez ce SQL :

```sql
-- Créer la table notification
CREATE TABLE IF NOT EXISTS notification (
    id INT AUTO_INCREMENT NOT NULL,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message LONGTEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0 NOT NULL,
    created_at DATETIME NOT NULL,
    read_at DATETIME DEFAULT NULL,
    PRIMARY KEY(id),
    INDEX IDX_BF5476CAA76ED395 (user_id),
    CONSTRAINT FK_BF5476CAA76ED395 
        FOREIGN KEY (user_id) 
        REFERENCES user (userId) 
        ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8mb4;

-- Ajouter les colonnes à user
ALTER TABLE user 
ADD COLUMN lastActivityAt DATETIME DEFAULT NULL,
ADD COLUMN phoneNumber VARCHAR(20) DEFAULT NULL;

-- Initialiser lastActivityAt
UPDATE user 
SET lastActivityAt = createdAt 
WHERE lastActivityAt IS NULL;
```

✅ **Vérification :** Rafraîchissez phpMyAdmin, vous devez voir la table `notification` et les nouvelles colonnes dans `user`.

---

### Étape 2 : Créer un Étudiant Inactif pour Test

Dans **phpMyAdmin**, exécutez :

```sql
-- Rendre un étudiant inactif (4 jours sans activité)
UPDATE user 
SET lastActivityAt = DATE_SUB(NOW(), INTERVAL 4 DAY),
    phoneNumber = '+21612345678'
WHERE userId = 1 AND discr = 'etudiant';
```

✅ **Vérification :** 
```sql
SELECT userId, nom, prenom, lastActivityAt, 
       DATEDIFF(NOW(), lastActivityAt) as jours_inactivite
FROM user 
WHERE userId = 1;
```

Vous devez voir `jours_inactivite = 4`.

---

### Étape 3 : Tester en Mode Simulation (Dry-Run)

Ouvrez **PowerShell** ou **CMD** dans le dossier du projet et exécutez :

```bash
php bin/console app:send-inactivity-reminders --dry-run
```

✅ **Résultat attendu :**
```
🔔 Envoi de rappels d'inactivité
================================

MODE SIMULATION - Aucune notification ne sera envoyée

📊 Détection des étudiants inactifs
------------------------------------
Trouvé 1 étudiant(s) inactif(s)

📤 Envoi des notifications
---------------------------
[SIMULATION] Ahmed Ben Ali (ahmed@example.com) - Inactif depuis 4 jours

📈 Résultats
------------
En mode réel, 1 notification(s) interne(s) et 1 SMS seraient envoyés

✓ Tous les rappels ont été envoyés avec succès
```

---

### Étape 4 : Tester l'Envoi Réel (Sans SMS)

Exécutez la commande **sans** `--dry-run` :

```bash
php bin/console app:send-inactivity-reminders
```

✅ **Résultat attendu :**
```
🔔 Envoi de rappels d'inactivité
================================

📊 Détection des étudiants inactifs
------------------------------------
Trouvé 1 étudiant(s) inactif(s)

📤 Envoi des notifications
---------------------------
 1/1 [============================] 100%

📈 Résultats
------------
┌──────────────────────────────────┬────────┐
│ Métrique                         │ Valeur │
├──────────────────────────────────┼────────┤
│ Étudiants inactifs détectés      │ 1      │
│ Notifications internes envoyées  │ 1      │
│ SMS envoyés                      │ 0      │
│ Erreurs                          │ 0      │
└──────────────────────────────────┴────────┘

✓ Tous les rappels ont été envoyés avec succès
```

**Note :** SMS = 0 car Twilio n'est pas encore configuré (c'est normal).

---

### Étape 5 : Vérifier la Notification en Base de Données

Dans **phpMyAdmin**, exécutez :

```sql
SELECT * FROM notification 
WHERE type = 'inactivity_reminder' 
ORDER BY created_at DESC 
LIMIT 1;
```

✅ **Résultat attendu :**

| id | user_id | type | title | message | is_read | created_at |
|----|---------|------|-------|---------|---------|------------|
| 1  | 1       | inactivity_reminder | ⏰ Rappel d'activité | Bonjour Ahmed, nous avons remarqué... | 0 | 2026-02-22 14:30:00 |

---

## 🎉 Félicitations !

Si vous voyez la notification en base de données, **le système fonctionne parfaitement** ! 🚀

---

## 📱 Test Avancé : Avec SMS (Optionnel)

Si vous voulez tester l'envoi de SMS :

### 1. Installer Twilio SDK

```bash
composer require twilio/sdk
```

### 2. Créer un Compte Twilio

1. Allez sur https://www.twilio.com/try-twilio
2. Créez un compte gratuit
3. Vérifiez votre email et numéro de téléphone

### 3. Obtenir les Credentials

1. Dans le Dashboard Twilio, copiez :
   - **Account SID** (commence par AC...)
   - **Auth Token**
2. Achetez un numéro Twilio (ou utilisez le numéro de test)

### 4. Configurer .env

Éditez le fichier `.env` et ajoutez :

```env
TWILIO_ACCOUNT_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_AUTH_TOKEN=votre_auth_token_ici
TWILIO_PHONE_NUMBER=+1234567890
```

### 5. Vider le Cache

```bash
php bin/console cache:clear
```

### 6. Tester l'Envoi SMS

```bash
php bin/console app:send-inactivity-reminders
```

✅ **Résultat attendu :**
```
📈 Résultats
------------
┌──────────────────────────────────┬────────┐
│ Métrique                         │ Valeur │
├──────────────────────────────────┼────────┤
│ Étudiants inactifs détectés      │ 1      │
│ Notifications internes envoyées  │ 1      │
│ SMS envoyés                      │ 1      │  ← Maintenant = 1 !
│ Erreurs                          │ 0      │
└──────────────────────────────────┴────────┘
```

Vous devriez recevoir un SMS sur votre téléphone ! 📱

---

## 🔍 Vérifier les Logs

Pour voir les détails d'exécution :

```bash
# Windows
type var\log\dev.log | findstr "inactivity"

# Ou ouvrir directement le fichier
notepad var\log\dev.log
```

✅ **Logs attendus :**
```
[info] Notification interne envoyée {"user_id":1,"type":"inactivity_reminder"}
[info] SMS envoyé {"user_id":1,"phone":"+21612345678"}
```

---

## 🧹 Nettoyer Après les Tests

Pour refaire les tests, supprimez les notifications créées :

```sql
DELETE FROM notification WHERE type = 'inactivity_reminder';
```

---

## ❓ Problèmes Courants

### Problème 1 : "Command not found"

**Solution :**
```bash
composer dump-autoload
php bin/console cache:clear
```

### Problème 2 : "Table notification doesn't exist"

**Solution :** Retournez à l'Étape 1 et exécutez le SQL dans phpMyAdmin.

### Problème 3 : "Aucun étudiant inactif détecté"

**Solution :** Vérifiez que vous avez bien exécuté l'Étape 2 (UPDATE user).

### Problème 4 : "Twilio non configuré"

**C'est normal !** Le système fonctionne sans Twilio. Les notifications internes sont créées quand même.

---

## 📊 Requêtes SQL Utiles

### Voir tous les étudiants inactifs
```sql
SELECT userId, nom, prenom, email, lastActivityAt,
       DATEDIFF(NOW(), lastActivityAt) as jours_inactivite
FROM user
WHERE discr = 'etudiant'
  AND isSuspended = 0
  AND lastActivityAt < DATE_SUB(NOW(), INTERVAL 3 DAY)
ORDER BY lastActivityAt ASC;
```

### Voir toutes les notifications
```sql
SELECT n.id, u.nom, u.prenom, n.title, n.message, n.created_at
FROM notification n
JOIN user u ON n.user_id = u.userId
ORDER BY n.created_at DESC;
```

### Créer plusieurs étudiants inactifs pour test
```sql
-- Rendre 5 étudiants inactifs avec différents niveaux
UPDATE user SET lastActivityAt = DATE_SUB(NOW(), INTERVAL 3 DAY) WHERE userId = 1;
UPDATE user SET lastActivityAt = DATE_SUB(NOW(), INTERVAL 5 DAY) WHERE userId = 2;
UPDATE user SET lastActivityAt = DATE_SUB(NOW(), INTERVAL 7 DAY) WHERE userId = 3;
UPDATE user SET lastActivityAt = DATE_SUB(NOW(), INTERVAL 10 DAY) WHERE userId = 4;
UPDATE user SET lastActivityAt = NULL WHERE userId = 5;
```

---

## ✅ Checklist de Test

- [ ] Tables créées en base de données
- [ ] Étudiant inactif créé (4 jours)
- [ ] Test dry-run réussi (affiche 1 étudiant)
- [ ] Test envoi réel réussi
- [ ] Notification visible en base de données
- [ ] Logs vérifiés (var/log/dev.log)
- [ ] (Optionnel) Twilio configuré
- [ ] (Optionnel) SMS reçu

---

## 🎯 Résumé

**Pour tester rapidement (sans SMS) :**
1. Exécuter le SQL (Étape 1)
2. Créer un étudiant inactif (Étape 2)
3. Lancer `php bin/console app:send-inactivity-reminders`
4. Vérifier la notification en BDD (Étape 5)

**Temps total : 5-10 minutes** ⏱️

---

**Le système fonctionne !** 🎉 Vous pouvez maintenant planifier l'exécution automatique avec Task Scheduler (Windows) ou cron (Linux).
