# 🚀 Installation du Système de Rappel d'Inactivité - État Actuel

## ✅ Ce qui a été Fait

### 1. Code Créé (100%)
- ✅ `src/Entity/Notification.php` - Entité notifications
- ✅ `src/Entity/User.php` - Modifié (lastActivityAt + phoneNumber)
- ✅ `src/Repository/NotificationRepository.php` - Requêtes
- ✅ `src/Service/InactivityDetectionService.php` - Détection inactivité
- ✅ `src/Service/NotificationService.php` - Gestion envois
- ✅ `src/Service/TwilioSmsService.php` - Intégration Twilio
- ✅ `src/Command/SendInactivityRemindersCommand.php` - Commande
- ✅ `src/Service/CourseProgressService.php` - Modifié
- ✅ `config/services.yaml` - Configuration Twilio
- ✅ `.env.example` - Variables Twilio

### 2. Documentation Créée (100%)
- ✅ 7 fichiers de documentation complète
- ✅ Guide d'installation
- ✅ Architecture détaillée
- ✅ Commandes de test
- ✅ Exemples de migration

### 3. Migration Créée (100%)
- ✅ `migrations/Version20260222123211.php` - Migration générée
- ✅ Inclut table `notification`
- ✅ Inclut colonnes `lastActivityAt` et `phoneNumber` dans `user`

## ⚠️ Problème Actuel

**Erreur lors de l'application de la migration:**
```
Table 'commentaire' already exists
```

**Cause:** Une migration précédente (Version20260209083209) essaie de créer une table qui existe déjà.

## 🔧 Solutions Possibles

### Solution 1 : Marquer les Migrations Problématiques comme Exécutées

```bash
# Voir l'état des migrations
php bin/console doctrine:migrations:status

# Marquer la migration problématique comme exécutée sans l'appliquer
php bin/console doctrine:migrations:version Version20260209083209 --add --no-interaction

# Réessayer d'appliquer toutes les migrations
php bin/console doctrine:migrations:migrate --no-interaction
```

### Solution 2 : Appliquer Uniquement la Nouvelle Migration

```bash
# Appliquer directement la migration du système de rappel
php bin/console doctrine:migrations:execute --up Version20260222123211 --no-interaction
```

### Solution 3 : Créer les Tables Manuellement (SQL Direct)

Si les migrations ne fonctionnent pas, exécutez ce SQL dans phpMyAdmin:

```sql
-- 1. Créer la table notification
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
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

-- 2. Ajouter les colonnes à la table user (si elles n'existent pas)
ALTER TABLE user 
ADD COLUMN IF NOT EXISTS lastActivityAt DATETIME DEFAULT NULL,
ADD COLUMN IF NOT EXISTS phoneNumber VARCHAR(20) DEFAULT NULL;

-- 3. Initialiser lastActivityAt pour les utilisateurs existants
UPDATE user 
SET lastActivityAt = createdAt 
WHERE lastActivityAt IS NULL;

-- 4. Créer un index pour optimiser les requêtes
CREATE INDEX IF NOT EXISTS idx_user_last_activity 
ON user (lastActivityAt, isSuspended, discr);
```

## 📋 Prochaines Étapes

### Étape 1 : Résoudre le Problème de Migration

Choisissez une des 3 solutions ci-dessus.

### Étape 2 : Installer Twilio SDK

```bash
composer require twilio/sdk
```

### Étape 3 : Configurer .env

Éditez le fichier `.env` et ajoutez:

```env
###> Twilio SMS Configuration ###
TWILIO_ACCOUNT_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_AUTH_TOKEN=your_auth_token_here
TWILIO_PHONE_NUMBER=+1234567890
###< Twilio SMS Configuration ###
```

**Pour obtenir les credentials Twilio:**
1. Créer un compte sur https://www.twilio.com/
2. Aller dans Console → Account Info
3. Copier Account SID et Auth Token
4. Acheter un numéro de téléphone Twilio

### Étape 4 : Vider le Cache

```bash
php bin/console cache:clear
```

### Étape 5 : Tester en Simulation

```bash
php bin/console app:send-inactivity-reminders --dry-run
```

### Étape 6 : Créer un Étudiant Inactif de Test

Dans phpMyAdmin, exécutez:

```sql
-- Créer un étudiant inactif pour test
UPDATE user 
SET lastActivityAt = DATE_SUB(NOW(), INTERVAL 4 DAY),
    phoneNumber = '+21612345678'
WHERE userId = 1 AND discr = 'etudiant';
```

### Étape 7 : Tester l'Envoi Réel

```bash
php bin/console app:send-inactivity-reminders
```

### Étape 8 : Vérifier les Résultats

```sql
-- Vérifier les notifications créées
SELECT * FROM notification 
WHERE type = 'inactivity_reminder' 
ORDER BY created_at DESC;

-- Vérifier les logs
type var\log\dev.log | findstr "inactivity"
```

### Étape 9 : Planifier l'Exécution Automatique

**Windows (Task Scheduler):**
1. Ouvrir "Planificateur de tâches"
2. Créer une tâche de base
3. Nom : "Rappel Inactivité Autolearn"
4. Déclencheur : Quotidien à 9h00
5. Action : Démarrer un programme
   - Programme : `C:\php\php.exe`
   - Arguments : `bin/console app:send-inactivity-reminders`
   - Répertoire : `C:\Users\yassm\OneDrive\Desktop\PI - Copie (2)\autolearn`

**Ou utiliser le fichier batch:**
```bash
run_inactivity_reminders.bat
```

## 📊 Vérification Finale

### Checklist

- [ ] Migration appliquée (table `notification` créée)
- [ ] Colonnes `lastActivityAt` et `phoneNumber` ajoutées à `user`
- [ ] Twilio SDK installé
- [ ] Fichier `.env` configuré avec credentials Twilio
- [ ] Cache vidé
- [ ] Test en dry-run réussi
- [ ] Étudiant inactif créé pour test
- [ ] Test d'envoi réel réussi
- [ ] Notification créée en BDD
- [ ] SMS reçu (si numéro valide)
- [ ] Logs vérifiés
- [ ] Tâche planifiée créée

## 🎯 Résumé

**État actuel:**
- ✅ Code 100% terminé
- ✅ Documentation 100% terminée
- ✅ Migration créée
- ⚠️ Migration non appliquée (problème avec migration précédente)

**Action immédiate requise:**
1. Résoudre le problème de migration (Solution 1, 2 ou 3)
2. Installer Twilio SDK
3. Configurer .env
4. Tester

**Temps estimé pour finaliser:** 15-30 minutes

## 📞 Support

Si vous rencontrez des problèmes:

1. **Problème de migration:** Utilisez la Solution 3 (SQL direct)
2. **Twilio non configuré:** Vérifiez `.env` et videz le cache
3. **Commande introuvable:** Exécutez `composer dump-autoload`
4. **Erreur SMS:** Vérifiez le format du numéro (+21612345678)

## 📚 Documentation Complète

Consultez ces fichiers pour plus de détails:

- `RAPPEL_INACTIVITE_ULTRA_COURT.md` - Vue rapide
- `GUIDE_SYSTEME_RAPPEL_INACTIVITE.md` - Guide complet
- `COMMANDES_RAPPEL_INACTIVITE.md` - Toutes les commandes
- `EXEMPLE_MIGRATION_RAPPEL_INACTIVITE.md` - Détails migration
- `INDEX_DOCUMENTATION_RAPPEL_INACTIVITE.md` - Navigation

---

**Le système est prêt à 95%** - Il ne reste que l'application de la migration et la configuration Twilio! 🚀
