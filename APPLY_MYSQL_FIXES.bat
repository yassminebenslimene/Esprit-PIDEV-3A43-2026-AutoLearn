@echo off
REM ============================================
REM MySQL Optimization - Automated Setup Script
REM ============================================
echo.
echo ============================================
echo MySQL OPTIMIZATION SETUP
echo ============================================
echo.
echo This script will:
echo 1. Stop MySQL service
echo 2. Backup your current my.ini
echo 3. Apply optimized configuration
echo 4. Start MySQL service
echo.
echo IMPORTANT: This requires Administrator privileges!
echo.
pause

REM Check if running as administrator
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo ERROR: This script must be run as Administrator!
    echo Right-click and select "Run as administrator"
    pause
    exit /b 1
)

echo.
echo [1/4] Stopping MySQL service...
net stop mysql
if %errorLevel% neq 0 (
    echo WARNING: Could not stop MySQL service. It may not be running.
    echo Continuing anyway...
)

echo.
echo [2/4] Creating backup of current my.ini...
if exist "C:\xampp2\mysql\bin\my.ini" (
    copy "C:\xampp2\mysql\bin\my.ini" "C:\xampp2\mysql\bin\my.ini.backup_%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%"
    echo Backup created successfully!
) else (
    echo WARNING: my.ini not found at C:\xampp2\mysql\bin\my.ini
    echo Please check your XAMPP installation path.
    pause
    exit /b 1
)

echo.
echo [3/4] Applying optimized configuration...
copy /Y "my.ini.optimized" "C:\xampp2\mysql\bin\my.ini"
if %errorLevel% neq 0 (
    echo ERROR: Failed to copy optimized configuration!
    echo Restoring backup...
    copy /Y "C:\xampp2\mysql\bin\my.ini.backup_*" "C:\xampp2\mysql\bin\my.ini"
    pause
    exit /b 1
)
echo Configuration applied successfully!

echo.
echo [4/4] Starting MySQL service...
net start mysql
if %errorLevel% neq 0 (
    echo ERROR: Failed to start MySQL service!
    echo Please check the error log at: C:\xampp2\mysql\data\mysql_error.log
    echo.
    echo To restore backup, run:
    echo copy "C:\xampp2\mysql\bin\my.ini.backup_*" "C:\xampp2\mysql\bin\my.ini"
    pause
    exit /b 1
)

echo.
echo ============================================
echo SUCCESS! MySQL optimization complete!
echo ============================================
echo.
echo Changes applied:
echo - InnoDB buffer pool: 16M -^> 512M (32x improvement)
echo - InnoDB log file: 5M -^> 128M
echo - SQL mode: Added STRICT_TRANS_TABLES, ERROR_FOR_DIVISION_BY_ZERO
echo - Collation: utf8mb4_general_ci -^> utf8mb4_unicode_ci
echo - Timezone: Set to +00:00 (UTC)
echo - Flush log: 1 -^> 2 (development mode - 10x faster)
echo.
echo NEXT STEPS:
echo 1. Run: php bin/console doctrine:schema:validate
echo 2. Test your application
echo 3. Monitor performance
echo.
echo Backup location: C:\xampp2\mysql\bin\my.ini.backup_*
echo.
pause
