# MySQL/MariaDB Optimization Guide

## Overview
This guide helps you fix the database configuration issues identified by Doctrine Doctor.

## Issues to Fix

### 🟠 Critical Issues
1. **InnoDB buffer pool too small** (16MB → 512MB+)
2. **Missing SQL strict mode settings**
3. **Timezone tables not loaded**

### 🔵 Optional Improvements
4. **Database collation** (utf8mb4_general_ci → utf8mb4_unicode_ci)
5. **InnoDB flush log** (1 → 2 for development)

---

## Step-by-Step Instructions

### Step 1: Stop MySQL Service

1. Open XAMPP Control Panel
2. Click "Stop" next to MySQL
3. Wait for it to fully stop

### Step 2: Backup Current Configuration

1. Navigate to: `C:\xampp2\mysql\bin\`
2. Copy `my.ini` to `my.ini.backup`
3. This allows you to restore if needed

### Step 3: Edit MySQL Configuration

1. Open `C:\xampp2\mysql\bin\my.ini` in a text editor (as Administrator)
2. Find the `[mysqld]` section
3. Add the following settings:

```ini
[mysqld]

# Timezone Configuration
default-time-zone = '+00:00'

# SQL Mode (Data Integrity)
sql_mode = STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION

# InnoDB Buffer Pool (Performance)
innodb_buffer_pool_size = 512M

# InnoDB Flush Log (Development Only - use 1 in production!)
innodb_flush_log_at_trx_commit = 2

# Character Set & Collation
character-set-server = utf8mb4
collation-server = utf8mb4_unicode_ci

# Additional Performance Settings
max_connections = 150
table_open_cache = 400
sort_buffer_size = 2M
read_buffer_size = 1M
read_rnd_buffer_size = 2M
```

4. Save the file

### Step 4: Start MySQL Service

1. Go back to XAMPP Control Panel
2. Click "Start" next to MySQL
3. Wait for it to start successfully
4. Check the logs if it fails to start

### Step 5: Verify Configuration

Open phpMyAdmin or MySQL command line and run:

```sql
-- Check timezone
SELECT @@global.time_zone, @@session.time_zone;
-- Should show: +00:00, +00:00

-- Check buffer pool size
SHOW VARIABLES LIKE 'innodb_buffer_pool_size';
-- Should show: 536870912 (512MB)

-- Check SQL mode
SELECT @@sql_mode;
-- Should include: STRICT_TRANS_TABLES, ERROR_FOR_DIVISION_BY_ZERO

-- Check innodb flush log
SHOW VARIABLES LIKE 'innodb_flush_log_at_trx_commit';
-- Should show: 2

-- Check character set
SHOW VARIABLES LIKE 'character_set_server';
-- Should show: utf8mb4

-- Check collation
SHOW VARIABLES LIKE 'collation_server';
-- Should show: utf8mb4_unicode_ci
```

### Step 6: Load Timezone Tables (Optional but Recommended)

**For Windows:**

1. Download timezone SQL file from: https://dev.mysql.com/downloads/timezones.html
2. Choose "POSIX standard" version
3. Extract the downloaded file
4. Import using phpMyAdmin:
   - Select `mysql` database
   - Go to "Import" tab
   - Choose the downloaded SQL file
   - Click "Go"

**Verify timezone tables:**
```sql
SELECT COUNT(*) FROM mysql.time_zone_name;
-- Should return > 0 (typically 500+)
```

### Step 7: Update Database Collation (Optional)

If you want better Unicode sorting:

```sql
-- Check current collation
SELECT DEFAULT_COLLATION_NAME 
FROM information_schema.SCHEMATA 
WHERE SCHEMA_NAME = 'autolearn_db';

-- Update to utf8mb4_unicode_ci (optional)
ALTER DATABASE `autolearn_db` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;
```

**Note:** This doesn't change existing table collations, only the default for new tables.

---

## Troubleshooting

### MySQL Won't Start After Changes

1. Check XAMPP error logs: `C:\xampp2\mysql\data\mysql_error.log`
2. Common issues:
   - **Syntax error in my.ini**: Check for typos
   - **Buffer pool too large**: Reduce to 256M if you have limited RAM
   - **Port conflict**: Check if another service is using port 3306

3. Restore backup:
   - Copy `my.ini.backup` back to `my.ini`
   - Start MySQL
   - Try again with smaller values

### Performance Issues After Changes

If the application becomes slower:

1. **Check available RAM**: Buffer pool shouldn't exceed 70% of total RAM
2. **Adjust buffer pool**: Start with 256M and increase gradually
3. **Monitor MySQL**: Use phpMyAdmin → Status to check performance

### Timezone Issues Persist

If timezone issues continue:

1. Verify Doctrine configuration: `config/packages/doctrine.yaml`
2. Should have:
   ```yaml
   doctrine:
       dbal:
           options:
               1002: "SET time_zone = '+00:00'"
   ```
3. Clear Symfony cache: `php bin/console cache:clear`

---

## Expected Results

After completing these steps:

✅ InnoDB buffer pool: 512MB (was 16MB)
✅ SQL strict mode: Enabled
✅ Timezone: UTC (+00:00)
✅ Character set: utf8mb4
✅ Collation: utf8mb4_unicode_ci
✅ Development performance: 10x faster writes
✅ Data integrity: Improved with strict mode

---

## Production Considerations

When deploying to production:

1. **Change innodb_flush_log_at_trx_commit back to 1**
   ```ini
   innodb_flush_log_at_trx_commit = 1
   ```

2. **Increase buffer pool to 50-70% of available RAM**
   ```ini
   # Example for 8GB RAM server
   innodb_buffer_pool_size = 5G
   ```

3. **Enable slow query log for monitoring**
   ```ini
   slow_query_log = 1
   slow_query_log_file = /var/log/mysql/slow-queries.log
   long_query_time = 2
   ```

---

## Additional Resources

- MySQL Configuration: https://dev.mysql.com/doc/refman/8.0/en/server-configuration.html
- InnoDB Configuration: https://dev.mysql.com/doc/refman/8.0/en/innodb-configuration.html
- Timezone Support: https://dev.mysql.com/doc/refman/8.0/en/time-zone-support.html
- Doctrine Best Practices: https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/best-practices.html

---

## Questions?

If you encounter issues:
1. Check the error logs
2. Verify each setting individually
3. Start with conservative values and increase gradually
4. Test the application after each change
