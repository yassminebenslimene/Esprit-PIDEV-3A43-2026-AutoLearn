-- ============================================
-- MySQL Timezone Tables Setup
-- ============================================
-- This script helps set up timezone support in MySQL/MariaDB
-- 
-- OPTION 1: For Windows (Download timezone SQL file)
-- ============================================
-- 1. Download timezone SQL from: https://dev.mysql.com/downloads/timezones.html
-- 2. Import using phpMyAdmin or command line:
--    mysql -u root -p mysql < timezone_2024_*.sql
--
-- OPTION 2: Manual timezone setup (if download not available)
-- ============================================
-- Note: This is a simplified version. Full timezone support requires
-- downloading the official timezone SQL file from MySQL website.

-- ============================================
-- VERIFICATION QUERIES
-- ============================================

-- Check current timezone settings
SELECT @@global.time_zone AS global_tz, @@session.time_zone AS session_tz;

-- Check if timezone tables are loaded
SELECT COUNT(*) AS timezone_count FROM mysql.time_zone_name;
-- Should return > 0 (typically 500+) if loaded

-- Check InnoDB buffer pool size
SHOW VARIABLES LIKE 'innodb_buffer_pool_size';
-- Should show 536870912 (512MB) or higher

-- Check SQL mode
SELECT @@sql_mode;
-- Should include: STRICT_TRANS_TABLES, ERROR_FOR_DIVISION_BY_ZERO

-- Check innodb flush log setting
SHOW VARIABLES LIKE 'innodb_flush_log_at_trx_commit';
-- Should show 2 for development, 1 for production

-- Check character set and collation
SHOW VARIABLES LIKE 'character_set%';
SHOW VARIABLES LIKE 'collation%';
-- Should show utf8mb4

-- ============================================
-- DATABASE COLLATION UPDATE (Optional)
-- ============================================
-- Update database collation to utf8mb4_unicode_ci for better Unicode sorting
-- WARNING: This may take time on large databases

-- Check current database collation
SELECT DEFAULT_CHARACTER_SET_NAME, DEFAULT_COLLATION_NAME 
FROM information_schema.SCHEMATA 
WHERE SCHEMA_NAME = 'autolearn_db';

-- Update database collation (uncomment to execute)
-- ALTER DATABASE `autolearn_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ============================================
-- NOTES
-- ============================================
-- After configuration changes:
-- 1. Restart MySQL/MariaDB service
-- 2. Run these verification queries
-- 3. Check Doctrine Doctor again to verify fixes
-- ============================================
