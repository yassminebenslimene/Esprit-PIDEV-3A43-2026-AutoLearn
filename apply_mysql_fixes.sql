-- ============================================
-- MySQL Configuration Fixes - SQL Commands
-- ============================================
-- Run these commands in phpMyAdmin or MySQL command line
-- These are temporary fixes until you update my.ini
-- ============================================

-- 1. SET SQL STRICT MODE (Session - temporary)
-- ============================================
SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- 2. SET SQL STRICT MODE (Global - until restart)
-- ============================================
SET GLOBAL sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- 3. SET INNODB FLUSH LOG (Development only - until restart)
-- ============================================
-- WARNING: Only for development! Use 1 in production
SET GLOBAL innodb_flush_log_at_trx_commit = 2;

-- 4. UPDATE DATABASE COLLATION
-- ============================================
ALTER DATABASE `autolearn_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 5. VERIFY CHANGES
-- ============================================
SELECT @@sql_mode AS current_sql_mode;
SELECT @@innodb_flush_log_at_trx_commit AS flush_log_setting;
SELECT DEFAULT_COLLATION_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = 'autolearn_db';

-- ============================================
-- NOTES
-- ============================================
-- These changes are TEMPORARY and will be lost on MySQL restart
-- To make them PERMANENT, you must edit my.ini file
-- See MYSQL_OPTIMIZATION_GUIDE.md for permanent configuration
-- ============================================

-- Buffer pool size CANNOT be changed dynamically in MariaDB 10.4
-- You MUST edit my.ini and restart MySQL to change it
-- Add this line under [mysqld] in C:\xampp2\mysql\bin\my.ini:
-- innodb_buffer_pool_size = 512M
-- ============================================
