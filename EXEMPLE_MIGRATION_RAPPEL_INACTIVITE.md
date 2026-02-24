# 📝 Exemple de Migration - Système de Rappel d'Inactivité

## 🎯 Modifications de Base de Données Requises

### 1️⃣ Nouvelle Table : `notification`
### 2️⃣ Modifications Table : `user` (ajout 2 colonnes)

## 📋 Commandes pour Créer la Migration

```bash
# Générer automatiquement la migration
php bin/console make:migration

# Appliquer la migration
php bin/console doctrine:migrations:migrate

# Vérifier le schéma
php bin/console doctrine:schema:validate
```

## 📄 Exemple de Fichier de Migration Généré

```php
<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration pour le système de rappel d'inactivité
 * 
 * Modifications :
 * - Création table notification
 * - Ajout colonnes lastActivityAt et phoneNumber dans user
 */
final class Version20260222120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout du système de rappel d\'inactivité (notifications + SMS)';
    }

    public function up(Schema $schema): void
    {
        // 1️⃣ Création de la table notification
        $this->addSql('
            CREATE TABLE notification (
                id INT AUTO_INCREMENT NOT NULL,
                user_id INT NOT NULL,
                type VARCHAR(50) NOT NULL,
                title VARCHAR(255) NOT NULL,
                message LONGTEXT NOT NULL,
                isRead TINYINT(1) DEFAULT 0 NOT NULL,
                createdAt DATETIME NOT NULL,
                readAt DATETIME DEFAULT NULL,
                PRIMARY KEY(id),
                INDEX IDX_BF5476CAA76ED395 (user_id),
                CONSTRAINT FK_BF5476CAA76ED395 
                    FOREIGN KEY (user_id) 
                    REFERENCES user (userId) 
                    ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ');

        // 2️⃣ Ajout de la colonne lastActivityAt dans user
        $this->addSql('
            ALTER TABLE user 
            ADD lastActivityAt DATETIME DEFAULT NULL 
            COMMENT \'Date de dernière activité (validation chapitre)\'
        ');

        // 3️⃣ Ajout de la colonne phoneNumber dans user
        $this->addSql('
            ALTER TABLE user 
            ADD phoneNumber VARCHAR(20) DEFAULT NULL 
            COMMENT \'Numéro de téléphone pour SMS (format international)\'
        ');

        // 4️⃣ Initialiser lastActivityAt avec createdAt pour les utilisateurs existants
        $this->addSql('
            UPDATE user 
            SET lastActivityAt = createdAt 
            WHERE lastActivityAt IS NULL
        ');

        // 5️⃣ Créer un index sur lastActivityAt pour optimiser les requêtes
        $this->addSql('
            CREATE INDEX idx_user_last_activity 
            ON user (lastActivityAt, isSuspended, discr)
        ');
    }

    public function down(Schema $schema): void
    {
        // Rollback : Supprimer les modifications

        // 1️⃣ Supprimer l'index
        $this->addSql('DROP INDEX idx_user_last_activity ON user');

        // 2️⃣ Supprimer les colonnes de user
        $this->addSql('ALTER TABLE user DROP lastActivityAt');
        $this->addSql('ALTER TABLE user DROP phoneNumber');

        // 3️⃣ Supprimer la table notification
        $this->addSql('DROP TABLE notification');
    }
}
```

## 🔍 Vérification Après Migration

### 1. Vérifier que la Table `notification` Existe

```sql
DESCRIBE notification;
```

**Résultat attendu :**
```
+------------+--------------+------+-----+---------+----------------+
| Field      | Type         | Null | Key | Default | Extra          |
+------------+--------------+------+-----+---------+----------------+
| id         | int          | NO   | PRI | NULL    | auto_increment |
| user_id    | int          | NO   | MUL | NULL    |                |
| type       | varchar(50)  | NO   |     | NULL    |                |
| title      | varchar(255) | NO   |     | NULL    |                |
| message    | longtext     | NO   |     | NULL    |                |
| isRead     | tinyint(1)   | NO   |     | 0       |                |
| createdAt  | datetime     | NO   |     | NULL    |                |
| readAt     | datetime     | YES  |     | NULL    |                |
+------------+--------------+------+-----+---------+----------------+
```

### 2. Vérifier les Colonnes Ajoutées à `user`

```sql
DESCRIBE user;
```

**Vérifier la présence de :**
```
+------------------+--------------+------+-----+---------+-------+
| Field            | Type         | Null | Key | Default | Extra |
+------------------+--------------+------+-----+---------+-------+
| ...              | ...          | ...  | ... | ...     | ...   |
| lastActivityAt   | datetime     | YES  | MUL | NULL    |       |
| phoneNumber      | varchar(20)  | YES  |     | NULL    |       |
+------------------+--------------+------+-----+---------+-------+
```

### 3. Vérifier les Index

```sql
SHOW INDEX FROM user WHERE Key_name = 'idx_user_last_activity';
```

### 4. Vérifier les Contraintes de Clé Étrangère

```sql
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_NAME = 'notification'
  AND REFERENCED_TABLE_NAME IS NOT NULL;
```

**Résultat attendu :**
```
+-------------------------+--------------+-------------+-----------------------+------------------------+
| CONSTRAINT_NAME         | TABLE_NAME   | COLUMN_NAME | REFERENCED_TABLE_NAME | REFERENCED_COLUMN_NAME |
+-------------------------+--------------+-------------+-----------------------+------------------------+
| FK_BF5476CAA76ED395     | notification | user_id     | user                  | userId                 |
+-------------------------+--------------+-------------+-----------------------+------------------------+
```

## 🧪 Tests Après Migration

### Test 1 : Insérer une Notification

```sql
INSERT INTO notification (user_id, type, title, message, isRead, createdAt)
VALUES (1, 'inactivity_reminder', 'Test', 'Message de test', 0, NOW());

-- Vérifier
SELECT * FROM notification;
```

### Test 2 : Mettre à Jour lastActivityAt

```sql
UPDATE user 
SET lastActivityAt = NOW() 
WHERE userId = 1;

-- Vérifier
SELECT userId, nom, prenom, lastActivityAt FROM user WHERE userId = 1;
```

### Test 3 : Ajouter un Numéro de Téléphone

```sql
UPDATE user 
SET phoneNumber = '+21612345678' 
WHERE userId = 1;

-- Vérifier
SELECT userId, nom, prenom, phoneNumber FROM user WHERE userId = 1;
```

### Test 4 : Requête de Détection d'Inactivité

```sql
SELECT 
    userId,
    nom,
    prenom,
    email,
    lastActivityAt,
    DATEDIFF(NOW(), lastActivityAt) as jours_inactivite
FROM user
WHERE discr = 'etudiant'
  AND isSuspended = 0
  AND (lastActivityAt < DATE_SUB(NOW(), INTERVAL 3 DAY) 
       OR lastActivityAt IS NULL);
```

## 🔄 Rollback (Annuler la Migration)

Si vous devez annuler la migration :

```bash
# Voir les migrations appliquées
php bin/console doctrine:migrations:status

# Revenir à la migration précédente
php bin/console doctrine:migrations:migrate prev

# Ou revenir à une version spécifique
php bin/console doctrine:migrations:migrate Version20260222110000
```

## 📊 Données de Test

### Créer des Étudiants Inactifs pour Tests

```sql
-- Créer 5 étudiants inactifs avec différents niveaux d'inactivité
UPDATE user 
SET 
    lastActivityAt = DATE_SUB(NOW(), INTERVAL 3 DAY),
    phoneNumber = '+21612345001'
WHERE userId = 1 AND discr = 'etudiant';

UPDATE user 
SET 
    lastActivityAt = DATE_SUB(NOW(), INTERVAL 5 DAY),
    phoneNumber = '+21612345002'
WHERE userId = 2 AND discr = 'etudiant';

UPDATE user 
SET 
    lastActivityAt = DATE_SUB(NOW(), INTERVAL 7 DAY),
    phoneNumber = '+21612345003'
WHERE userId = 3 AND discr = 'etudiant';

UPDATE user 
SET 
    lastActivityAt = DATE_SUB(NOW(), INTERVAL 10 DAY),
    phoneNumber = '+21612345004'
WHERE userId = 4 AND discr = 'etudiant';

UPDATE user 
SET 
    lastActivityAt = NULL,
    phoneNumber = '+21612345005'
WHERE userId = 5 AND discr = 'etudiant';
```

### Créer des Notifications de Test

```sql
INSERT INTO notification (user_id, type, title, message, isRead, createdAt)
VALUES 
(1, 'inactivity_reminder', '⏰ Rappel d\'activité', 'Bonjour, vous n\'avez pas validé de chapitre depuis 3 jours.', 0, NOW()),
(2, 'inactivity_reminder', '⏰ Rappel d\'activité', 'Bonjour, vous n\'avez pas validé de chapitre depuis 5 jours.', 0, NOW()),
(3, 'course_update', '📚 Nouveau contenu', 'Un nouveau chapitre a été ajouté au cours Java.', 0, NOW());
```

## 🛠 Dépannage

### Erreur : "Syntax error or access violation"

**Cause** : Problème de syntaxe SQL ou permissions insuffisantes

**Solution** :
```bash
# Vérifier les permissions
SHOW GRANTS FOR CURRENT_USER;

# Vérifier la syntaxe de la migration
php bin/console doctrine:migrations:status
```

### Erreur : "Foreign key constraint fails"

**Cause** : Tentative de créer une notification pour un user_id inexistant

**Solution** :
```sql
-- Vérifier que l'utilisateur existe
SELECT userId FROM user WHERE userId = 1;

-- Utiliser un userId valide
```

### Erreur : "Duplicate column name"

**Cause** : Les colonnes existent déjà (migration déjà appliquée)

**Solution** :
```bash
# Vérifier l'état des migrations
php bin/console doctrine:migrations:status

# Si nécessaire, marquer comme exécutée sans l'appliquer
php bin/console doctrine:migrations:version Version20260222120000 --add
```

## 📈 Optimisation des Performances

### Index Recommandés

```sql
-- Index sur type de notification (pour filtrer rapidement)
CREATE INDEX idx_notification_type ON notification(type);

-- Index sur isRead (pour compter les non lues)
CREATE INDEX idx_notification_read ON notification(isRead);

-- Index composite sur user_id et isRead
CREATE INDEX idx_notification_user_read ON notification(user_id, isRead);

-- Index sur createdAt (pour trier par date)
CREATE INDEX idx_notification_created ON notification(createdAt DESC);
```

### Statistiques de Table

```sql
-- Analyser la table pour optimiser les requêtes
ANALYZE TABLE notification;
ANALYZE TABLE user;

-- Voir les statistiques
SHOW TABLE STATUS LIKE 'notification';
```

## ✅ Checklist Post-Migration

- [ ] Table `notification` créée
- [ ] Colonne `lastActivityAt` ajoutée à `user`
- [ ] Colonne `phoneNumber` ajoutée à `user`
- [ ] Index `idx_user_last_activity` créé
- [ ] Contrainte de clé étrangère créée
- [ ] `lastActivityAt` initialisé pour utilisateurs existants
- [ ] Tests d'insertion réussis
- [ ] Requête de détection fonctionne
- [ ] Commande Symfony fonctionne
- [ ] Logs vérifiés

---

**Migration prête** : Suivez les étapes ci-dessus ✓  
**Tests inclus** : Vérification complète ✓  
**Rollback documenté** : Annulation possible ✓
