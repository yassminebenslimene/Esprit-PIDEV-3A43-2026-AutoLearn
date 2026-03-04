@echo off
REM ============================================
REM MySQL Configuration Setup Script
REM ============================================
REM This script helps you configure MySQL for optimal performance
REM Run as Administrator
REM ============================================

echo.
echo ============================================
echo MySQL Configuration Setup
echo ============================================
echo.

REM Check if running as administrator
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo ERROR: This script must be run as Administrator!
    echo Right-click and select "Run as administrator"
    pause
    exit /b 1
)

echo [1/5] Checking XAMPP installation...
if not exist "C:\xampp2\mysql\bin\my.ini" (
    echo ERROR: MySQL configuration file not found at C:\xampp2\mysql\bin\my.ini
    echo Please verify your XAMPP installation path
    pause
    exit /b 1
)
echo Found: C:\xampp2\mysql\bin\my.ini

echo.
echo [2/5] Creating backup of current configuration...
copy "C:\xampp2\mysql\bin\my.ini" "C:\xampp2\mysql\bin\my.ini.backup_%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%" >nul 2>&1
if %errorLevel% equ 0 (
    echo Backup created successfully
) else (
    echo WARNING: Could not create backup
)

echo.
echo [3/5] Stopping MySQL service...
net stop mysql >nul 2>&1
if %errorLevel% equ 0 (
    echo MySQL stopped successfully
) else (
    echo MySQL was not running or could not be stopped
)

echo.
echo [4/5] Configuration file location:
echo C:\xampp2\mysql\bin\my.ini
echo.
echo You need to manually add these settings under [mysqld] section:
echo.
echo [mysqld]
echo default-time-zone = '+00:00'
echo sql_mode = STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION
echo innodb_buffer_pool_size = 512M
echo innodb_flush_log_at_trx_commit = 2
echo character-set-server = utf8mb4
echo collation-server = utf8mb4_unicode_ci
echo.
echo Opening configuration file in Notepad...
timeout /t 3 >nul
notepad "C:\xampp2\mysql\bin\my.ini"

echo.
echo [5/5] Starting MySQL service...
net start mysql
if %errorLevel% equ 0 (
    echo MySQL started successfully
) else (
    echo ERROR: MySQL failed to start!
    echo Check the error log at: C:\xampp2\mysql\data\mysql_error.log
    echo.
    echo Common issues:
    echo - Syntax error in my.ini
    echo - Buffer pool size too large for available RAM
    echo - Port 3306 already in use
    echo.
    echo Restoring backup...
    copy "C:\xampp2\mysql\bin\my.ini.backup_*" "C:\xampp2\mysql\bin\my.ini" >nul 2>&1
    net start mysql
    pause
    exit /b 1
)

echo.
echo ============================================
echo Configuration Complete!
echo ============================================
echo.
echo Next steps:
echo 1. Run apply_mysql_fixes.sql in phpMyAdmin to update database collation
echo 2. Verify settings by running mysql_timezone_setup.sql
echo 3. Check Doctrine Doctor to confirm all issues are resolved
echo.
echo Backup location: C:\xampp2\mysql\bin\my.ini.backup_*
echo.
pause
