# ✅ Database Issues Fixed - Complete

## Date: 2026-02-22

## Problem
After selective merge, Doctrine was generating queries with snake_case column names (`is_suspended`, `suspended_at`) but the database had camelCase columns (`isSuspended`, `suspendedAt`).

## Root Cause
The User entity didn't have explicit `name` attributes in the `#[ORM\Column]` annotations, so Doctrine's default naming strategy was converting camelCase property names to snake_case database columns.

## Solution Applied

### 1. Fixed User Entity
Added explicit column names to all suspension-related fields:

```php
#[ORM\Column(name: 'isSuspended', type: 'boolean', options: ['default' => false])]
private bool $isSuspended = false;

#[ORM\Column(name: 'suspendedAt', type: 'datetime', nullable: true)]
private ?\DateTimeInterface $suspendedAt = null;

#[ORM\Column(name: 'suspensionReason', type: 'string', length: 500, nullable: true)]
private ?string $suspensionReason = null;

#[ORM\Column(name: 'suspendedBy', type: 'integer', nullable: true)]
private ?int $suspendedBy = null;

#[ORM\Column(name: 'lastLoginAt', type: 'datetime', nullable: true)]
private ?\DateTimeInterface $lastLoginAt = null;
```

### 2. Fixed Database Columns
Renamed all snake_case columns to camelCase:

```sql
ALTER TABLE user CHANGE is_suspended isSuspended TINYINT(1) DEFAULT 0 NOT NULL;
ALTER TABLE user CHANGE suspended_at suspendedAt DATETIME DEFAULT NULL;
ALTER TABLE user CHANGE suspension_reason suspensionReason VARCHAR(500) DEFAULT NULL;
ALTER TABLE user CHANGE suspended_by suspendedBy INT DEFAULT NULL;
ALTER TABLE user CHANGE last_login_at lastLoginAt DATETIME DEFAULT NULL;
```

### 3. Recreated user_audit Table
```sql
CREATE TABLE user_audit (
    userId INT NOT NULL,
    rev INT NOT NULL,
    revtype VARCHAR(4) NOT NULL,
    nom VARCHAR(50) DEFAULT NULL,
    prenom VARCHAR(50) DEFAULT NULL,
    email VARCHAR(255) DEFAULT NULL,
    password VARCHAR(255) DEFAULT NULL,
    role VARCHAR(20) DEFAULT NULL,
    discr VARCHAR(255) DEFAULT NULL,
    niveau VARCHAR(20) DEFAULT NULL,
    createdAt DATETIME DEFAULT NULL,
    isSuspended TINYINT(1) DEFAULT 0,
    suspendedAt DATETIME DEFAULT NULL,
    suspensionReason VARCHAR(500) DEFAULT NULL,
    suspendedBy INT DEFAULT NULL,
    lastLoginAt DATETIME DEFAULT NULL,
    PRIMARY KEY(userId, rev),
    INDEX rev_idx (rev),
    CONSTRAINT FK_user_audit_rev FOREIGN KEY (rev) REFERENCES revisions (id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
```

### 4. Cleared All Caches
```bash
rm -r var/cache/*
php bin/console cache:clear
php bin/console doctrine:cache:clear-metadata
php bin/console doctrine:cache:clear-query
php bin/console doctrine:cache:clear-result
```

## Verification

✅ User table has camelCase columns
✅ User entity has explicit column names
✅ user_audit table matches entity structure
✅ All caches cleared
✅ Schema mapping correct

## Files Modified

- `src/Entity/User.php` - Added explicit column names
- Database: `user` table - Renamed columns to camelCase
- Database: `user_audit` table - Recreated with correct structure

## Status: COMPLETE ✅

The application should now work without any column name errors.
