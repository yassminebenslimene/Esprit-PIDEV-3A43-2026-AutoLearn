# 🔧 Troubleshooting: Audit Not Tracking

## Problem
Created a course but it's not showing in the audit trail.

## ✅ Checklist

### 1. Clear Symfony Cache
**MOST COMMON FIX!**

```bash
php bin/console cache:clear
php bin/console cache:warmup
```

After changing `simple_things_entity_audit.yaml`, you MUST clear the cache.

### 2. Verify Tables Exist

```sql
SHOW TABLES LIKE '%_audit';
```

Should show:
- `cours_audit` ✓
- `challenge_audit` ✓
- `evenement_audit` ✓
- etc.

### 3. Check if Data Was Inserted

```sql
-- Check revisions table
SELECT * FROM revisions ORDER BY timestamp DESC LIMIT 10;

-- Check cours_audit table
SELECT * FROM cours_audit ORDER BY rev DESC LIMIT 10;
```

### 4. Verify Configuration is Loaded

```bash
php bin/console debug:config simple_things_entity_audit
```

Should show all entities listed in `audited_entities`.

### 5. Check if You're Logged In as Admin

The audit only tracks actions by users with `ROLE_ADMIN`. Make sure:
- You're logged in
- Your user has `role = 'ADMIN'` in the database
- Your email is being captured in `revisions.username`

### 6. Verify Entity Namespace

The configuration uses:
```yaml
- App\Entity\GestionDeCours\Cours
```

Make sure this matches your actual entity namespace.

### 7. Check Doctrine Events

```bash
php bin/console debug:event-dispatcher doctrine
```

Should show `SimpleThingsEntityAudit` listeners.

## 🐛 Common Issues

### Issue 1: Cache Not Cleared
**Symptom:** Configuration changed but nothing happens
**Fix:** `php bin/console cache:clear`

### Issue 2: Wrong Entity Namespace
**Symptom:** Tables created but no data inserted
**Fix:** Check entity namespace in config matches actual class

### Issue 3: Not Logged In as Admin
**Symptom:** Data inserted in audit tables but not showing in UI
**Fix:** Make sure you're logged in as admin when creating courses

### Issue 4: Doctrine Not Flushing
**Symptom:** Entity created but audit not triggered
**Fix:** Make sure `$entityManager->flush()` is called after persist

## 🧪 Test Audit Manually

Create a simple test:

```php
// In a controller
$cours = new Cours();
$cours->setTitre('Test Audit');
$cours->setDescription('Testing audit tracking');
$cours->setNiveau('DEBUTANT');

$entityManager->persist($cours);
$entityManager->flush(); // This should trigger audit

// Check database
// SELECT * FROM revisions ORDER BY id DESC LIMIT 1;
// SELECT * FROM cours_audit ORDER BY rev DESC LIMIT 1;
```

## 📊 Expected Behavior

When you create a course:

1. **revisions table** gets a new row:
   - `id`: auto-increment
   - `timestamp`: current time
   - `username`: your admin email

2. **cours_audit table** gets a new row:
   - `id`: course ID
   - `rev`: revision ID (foreign key to revisions)
   - `revtype`: 'INS' (insert)
   - `titre`: course title
   - All other course fields

3. **Audit UI** shows:
   - 📚 Course name
   - ➕ Created action
   - Timestamp
   - Eye icon to view details

## 🔍 Debug SQL Queries

Enable SQL logging to see if audit queries are being executed:

```yaml
# config/packages/dev/doctrine.yaml
doctrine:
    dbal:
        logging: true
        profiling: true
```

Then check `var/log/dev.log` for audit-related SQL queries.

## ✅ Verification Steps

1. Clear cache: `php bin/console cache:clear`
2. Create a course as admin
3. Check revisions: `SELECT * FROM revisions ORDER BY id DESC LIMIT 1;`
4. Check cours_audit: `SELECT * FROM cours_audit ORDER BY rev DESC LIMIT 1;`
5. Refresh audit page: `/backoffice/audit`

If steps 3-4 show data but step 5 doesn't, the issue is in the controller/template.
If steps 3-4 show no data, the issue is in the audit bundle configuration.

## 🆘 Still Not Working?

Check:
1. Is the bundle installed? `composer show simplethings/entity-audit-bundle`
2. Is it registered? Check `config/bundles.php` for `SimpleThingsEntityAuditBundle`
3. Are there any errors in logs? Check `var/log/dev.log`
