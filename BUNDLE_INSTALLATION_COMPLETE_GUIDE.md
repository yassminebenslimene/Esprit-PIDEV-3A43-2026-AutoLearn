# Complete Guide: Symfony Entity Audit Bundle Installation

## 📚 What Your Professor Asked For

Your professor wants you to demonstrate:
1. ✅ **Installing an existing Symfony bundle** via `composer require`
2. ✅ **Configuring the bundle** via YAML file
3. ✅ **Integrating it** into your user management module
4. ✅ **Using it** to track user activities

## ✅ Bundle Installation Steps (What We Did)

### Step 1: Install Bundle via Composer
```bash
composer require sonata-project/entity-audit-bundle
```

**What this does**:
- Downloads the bundle from Packagist (Symfony's package repository)
- Adds it to `composer.json` dependencies
- Registers it automatically in `config/bundles.php`
- Creates initial configuration files

**Result**: Bundle installed at version `1.22.0`

### Step 2: Configure Bundle via YAML
**File Created**: `config/packages/simple_things_entity_audit.yaml`

```yaml
simple_things_entity_audit:
    # Entities to audit - tracking only Etudiant (students) for user management
    audited_entities:
        - App\Entity\Etudiant
    
    # Global ignore columns (don't track these fields)
    global_ignore_columns: 
        - createdAt
        - updatedAt
    
    # Table naming configuration
    table_prefix: ''
    table_suffix: '_audit'
    
    # Revision table name
    revision_table_name: 'revisions'
    
    # Revision field names
    revision_field_name: 'rev'
    revision_type_field_name: 'revtype'
    revision_id_field_type: 'integer'
```

**What this configures**:
- Which entities to track (only Etudiant - students)
- Which fields to ignore (createdAt, updatedAt)
- How to name audit tables
- Revision tracking settings

### Step 3: Add Audit Annotation to Entity
**File Modified**: `src/Entity/User.php`

```php
use SimpleThings\EntityAudit\Mapping\Annotation as Audit;

#[Audit\Auditable]
abstract class User implements UserInterface, PasswordAuthenticatedUserInterface
```

**What this does**:
- Marks the User entity as auditable
- Tells the bundle to track all changes to this entity
- Enables automatic audit logging

### Step 4: Update Database Schema
```bash
php bin/console doctrine:schema:update --force
```

**What this creates**:
- `revisions` table: Stores revision metadata
- `user_audit` table: Stores complete user entity history

## 🗄️ Database Tables Created

### Table 1: `revisions`
```sql
CREATE TABLE revisions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    timestamp DATETIME NOT NULL,
    username VARCHAR(255) DEFAULT NULL
);
```

**Purpose**: Tracks when changes happened and who made them

**Fields**:
- `id`: Unique revision identifier
- `timestamp`: When the change occurred
- `username`: Who made the change (if available)

### Table 2: `etudiant_audit`
```sql
CREATE TABLE etudiant_audit (
    userId INT NOT NULL,
    nom VARCHAR(50),
    prenom VARCHAR(50),
    email VARCHAR(255),
    password VARCHAR(255),
    role VARCHAR(20),
    niveau VARCHAR(20),
    is_suspended TINYINT(1),
    suspended_at DATETIME,
    suspension_reason VARCHAR(500),
    suspended_by INT,
    last_login_at DATETIME,
    discr VARCHAR(255),
    rev INT NOT NULL,              -- Links to revisions.id
    revtype VARCHAR(4) NOT NULL,   -- 'INS', 'UPD', 'DEL'
    PRIMARY KEY(userId, rev),
    FOREIGN KEY (rev) REFERENCES revisions(id)
);
```

**Purpose**: Stores complete student (Etudiant) entity state at each revision

**Fields**:
- All User entity fields (nom, prenom, email, etc.)
- `rev`: Links to revision table
- `revtype`: Type of change (INSERT/UPDATE/DELETE)

## 🎯 How the Bundle Works

### Automatic Tracking
The bundle automatically tracks these operations:

1. **INSERT (INS)**: When a new user is created
```php
$user = new Etudiant();
$user->setNom('Dupont');
$user->setEmail('student@example.com');
$entityManager->persist($user);
$entityManager->flush(); // → Creates INS revision
```

2. **UPDATE (UPD)**: When user data is modified
```php
$user->setNiveau('INTERMEDIAIRE');
$entityManager->flush(); // → Creates UPD revision
```

3. **DELETE (DEL)**: When a user is deleted
```php
$entityManager->remove($user);
$entityManager->flush(); // → Creates DEL revision
```

### What Gets Tracked
- ✅ Student (Etudiant) creation (registration, admin creation)
- ✅ Student profile updates (name, email, level changes)
- ✅ Student suspension/reactivation
- ✅ Password changes
- ✅ Role modifications
- ❌ Admin users (not tracked)
- ❌ createdAt/updatedAt (ignored via configuration)

## 📊 Bundle Features

### 1. Complete Audit Trail
- Every change to User entities is recorded
- Complete entity state stored at each revision
- Timestamp and user attribution for each change

### 2. Time Travel Queries
```php
// Get entity state at specific revision
$auditReader = $container->get('simplethings_entityaudit.reader');
$user = $auditReader->find(User::class, $userId, $revisionId);

// Find all revisions for a user
$revisions = $auditReader->findRevisions(User::class, $userId);
```

### 3. Change Detection
- See what changed between revisions
- Track who made changes
- Identify when changes occurred

## 🎓 What to Tell Your Professor

### 1. Bundle Installation
"I installed the **Sonata Entity Audit Bundle** using Composer:"
```bash
composer require sonata-project/entity-audit-bundle
```

### 2. Configuration
"I configured it via YAML file at `config/packages/simple_things_entity_audit.yaml` to track only Etudiant (student) entities."

### 3. Integration
"I added the `#[Audit\Auditable]` annotation to the User entity to enable automatic tracking."

### 4. Database Schema
"The bundle automatically created two tables:
- `revisions`: Tracks when and who made changes
- `etudiant_audit`: Stores complete student history"

### 5. Functionality
"The bundle now automatically tracks:
- Student creation (INSERT)
- Student profile updates (UPDATE)
- Student deletion (DELETE)
- All changes are stored with timestamps and user attribution"

## 📝 Configuration File Explanation

### audited_entities
```yaml
audited_entities:
    - App\Entity\Etudiant
```
**Purpose**: Specifies which entities to track. We track only Etudiant (student) entities, not Admin users.

### global_ignore_columns
```yaml
global_ignore_columns: 
    - createdAt
    - updatedAt
```
**Purpose**: Fields to exclude from tracking (timestamps that change automatically).

### table_suffix
```yaml
table_suffix: '_audit'
```
**Purpose**: Adds `_audit` suffix to audit tables (e.g., `user_audit`).

### revision_table_name
```yaml
revision_table_name: 'revisions'
```
**Purpose**: Name of the main revision tracking table.

## 🔍 Verification Commands

### Check Bundle Installation
```bash
composer show sonata-project/entity-audit-bundle
```

### Check Configuration
```bash
php bin/console debug:config simple_things_entity_audit
```

### Check Database Tables
```bash
php bin/console dbal:run-sql "SHOW TABLES LIKE '%audit%'"
php bin/console dbal:run-sql "SHOW TABLES LIKE '%revision%'"
```

### Test Audit Functionality
```bash
# Make a change to a user
# Then check the audit tables:
php bin/console dbal:run-sql "SELECT * FROM revisions ORDER BY id DESC LIMIT 5"
php bin/console dbal:run-sql "SELECT * FROM user_audit ORDER BY rev DESC LIMIT 5"
```

## 🎯 Key Points for Your Report

1. **Bundle Name**: Sonata Entity Audit Bundle (sonata-project/entity-audit-bundle)
2. **Installation Method**: Composer (`composer require`)
3. **Configuration**: YAML file (`simple_things_entity_audit.yaml`)
4. **Integration**: Annotation (`#[Audit\Auditable]`)
5. **Database**: Automatic table creation via Doctrine
6. **Functionality**: Automatic tracking of INSERT/UPDATE/DELETE operations

## 📚 Official Documentation

- **Bundle Repository**: https://github.com/sonata-project/entity-audit-bundle
- **Symfony Bundles**: https://symfony.com/bundles
- **Packagist**: https://packagist.org/packages/sonata-project/entity-audit-bundle

## ✅ Summary

You have successfully:
1. ✅ Installed an existing Symfony bundle via Composer
2. ✅ Configured it via YAML file
3. ✅ Integrated it with your User entity
4. ✅ Created necessary database tables
5. ✅ Enabled automatic user activity tracking

This demonstrates proper use of Symfony's bundle ecosystem and follows best practices for integrating third-party packages into a Symfony application.

---

**Date**: February 22, 2026  
**Bundle**: sonata-project/entity-audit-bundle v1.22.0  
**Status**: ✅ Installed and Configured  
**Purpose**: User Management Activity Tracking