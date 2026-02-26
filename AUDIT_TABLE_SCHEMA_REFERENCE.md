# Audit Table Schema Reference

## IMPORTANT: Column Names Must Match Entity Exactly

The `user_audit` table is managed by SimpleThings EntityAudit bundle and must have column names that EXACTLY match the User entity properties (camelCase).

## Correct user_audit Table Schema

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
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
```

## Key Points

1. **Column names are camelCase** (isSuspended, suspendedAt, etc.) - NOT snake_case
2. **Must include ALL fields from User entity** including inherited fields
3. **Must include `discr` column** for Single Table Inheritance
4. **Must include `niveau` column** from Etudiant entity
5. **createdAt is included** even though it's in global_ignore_columns (the bundle still needs it in the audit table)

## If You Get Column Not Found Errors

### Error: "Column not found: 1054 Unknown column 'is_suspended'"
**Solution:** The column name should be `isSuspended` (camelCase), not `is_suspended`

### Error: "Column not found: 1054 Unknown column 'createdAt'"
**Solution:** Add the `createdAt` column to user_audit table

### How to Fix
```bash
# Drop and recreate the table
php bin/console dbal:run-sql "DROP TABLE IF EXISTS user_audit"

# Recreate with correct schema (see above)
php bin/console dbal:run-sql "CREATE TABLE user_audit (...)"

# Clear all caches
php bin/console cache:clear
php bin/console doctrine:cache:clear-metadata
php bin/console doctrine:cache:clear-result
php bin/console doctrine:cache:clear-query
```

## Verification

```bash
# Check table structure
php bin/console dbal:run-sql "DESCRIBE user_audit"

# Test a query
php bin/console dbal:run-sql "SELECT * FROM user_audit LIMIT 1"
```

## Why This Happens

The audit bundle uses reflection to read entity properties and expects the database columns to match exactly. When you restore old code or merge branches, the table structure might not match the current entity definition.

## Prevention

Always run these commands after:
- Merging branches
- Restoring old code
- Changing entity properties
- Switching between branches with different entity structures

```bash
# 1. Check if audit tables need recreation
php bin/console doctrine:schema:validate

# 2. If needed, drop and recreate user_audit
# (see commands above)

# 3. Clear all caches
php bin/console cache:clear
php bin/console doctrine:cache:clear-metadata
php bin/console doctrine:cache:clear-result
php bin/console doctrine:cache:clear-query
```


---

## CRITICAL: Main User Table Must Also Use camelCase

The `user` table itself must have camelCase column names to match the User entity!

### Correct user Table Column Names

```sql
-- Fix snake_case to camelCase
ALTER TABLE user CHANGE is_suspended isSuspended TINYINT(1) DEFAULT 0;
ALTER TABLE user CHANGE suspended_at suspendedAt DATETIME DEFAULT NULL;
ALTER TABLE user CHANGE suspension_reason suspensionReason VARCHAR(500) DEFAULT NULL;
ALTER TABLE user CHANGE suspended_by suspendedBy INT DEFAULT NULL;
ALTER TABLE user CHANGE last_login_at lastLoginAt DATETIME DEFAULT NULL;
```

### Why This Matters

Doctrine ORM expects database column names to match entity property names EXACTLY. When you have:
- Entity: `private bool $isSuspended`
- Database: `is_suspended`

Doctrine will throw: `Column not found: 1054 Unknown column 'is_suspended'`

### Complete Fix After Branch Merge/Restore

```bash
# 1. Fix user table columns
php bin/console dbal:run-sql "ALTER TABLE user CHANGE is_suspended isSuspended TINYINT(1) DEFAULT 0"
php bin/console dbal:run-sql "ALTER TABLE user CHANGE suspended_at suspendedAt DATETIME DEFAULT NULL"
php bin/console dbal:run-sql "ALTER TABLE user CHANGE suspension_reason suspensionReason VARCHAR(500) DEFAULT NULL"
php bin/console dbal:run-sql "ALTER TABLE user CHANGE suspended_by suspendedBy INT DEFAULT NULL"
php bin/console dbal:run-sql "ALTER TABLE user CHANGE last_login_at lastLoginAt DATETIME DEFAULT NULL"

# 2. Recreate user_audit table (see schema above)
php bin/console dbal:run-sql "DROP TABLE IF EXISTS user_audit"
php bin/console dbal:run-sql "CREATE TABLE user_audit (...)"

# 3. Clear ALL caches
php bin/console cache:clear
php bin/console doctrine:cache:clear-metadata
php bin/console doctrine:cache:clear-result
php bin/console doctrine:cache:clear-query
```

### Verification

```bash
# Check user table structure
php bin/console dbal:run-sql "SELECT userId, isSuspended, suspendedAt, lastLoginAt FROM user LIMIT 1"

# Should work without errors
```
