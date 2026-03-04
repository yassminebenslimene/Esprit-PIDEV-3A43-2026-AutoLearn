@echo off
echo ============================================
echo MANUAL MYSQL FIX - Step by Step
echo ============================================
echo.
echo STEP 1: Stop MySQL
echo ----------------
echo 1. Open XAMPP Control Panel
echo 2. Click "Stop" next to MySQL
echo 3. Wait until it says "Stopped"
echo.
pause
echo.
echo STEP 2: Backup Current Configuration
echo ------------------------------------
echo Running backup command...
copy "C:\xampp2\mysql\bin\my.ini" "C:\xampp2\mysql\bin\my.ini.backup_%date:~-4,4%%date:~-10,2%%date:~-7,2%"
if %errorLevel% equ 0 (
    echo SUCCESS: Backup created!
) else (
    echo ERROR: Could not create backup. Check if path is correct.
    echo Your XAMPP path might be different.
    pause
    exit /b 1
)
echo.
pause
echo.
echo STEP 3: Apply Optimized Configuration
echo -------------------------------------
echo Copying optimized my.ini...
copy /Y "%~dp0my.ini.optimized" "C:\xampp2\mysql\bin\my.ini"
if %errorLevel% equ 0 (
    echo SUCCESS: Configuration applied!
) else (
    echo ERROR: Could not copy file.
    pause
    exit /b 1
)
echo.
pause
echo.
echo STEP 4: Start MySQL
echo -------------------
echo 1. Go back to XAMPP Control Panel
echo 2. Click "Start" next to MySQL
echo 3. Wait until it says "Running"
echo.
echo If MySQL fails to start:
echo - Check: C:\xampp2\mysql\data\mysql_error.log
echo - Restore backup: copy "C:\xampp2\mysql\bin\my.ini.backup_*" "C:\xampp2\mysql\bin\my.ini"
echo.
pause
echo.
echo STEP 5: Verify Configuration
echo ----------------------------
echo Run: VERIFY_MYSQL_CONFIG.bat
echo.
echo All values should show [OK]
echo.
pause
