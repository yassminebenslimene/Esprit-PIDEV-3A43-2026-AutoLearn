@echo off
cls
echo ========================================
echo   DEMARRAGE DE MYSQL
echo ========================================
echo.
echo Tentative de demarrage de MySQL...
echo.

REM Démarrer MySQL via XAMPP
echo [1/3] Tentative via XAMPP...
if exist "C:\xampp\mysql_start.bat" (
    echo MySQL trouve dans XAMPP
    cd C:\xampp
    call mysql_start.bat
    echo.
    echo MySQL demarre via XAMPP !
    goto :success
)

REM Démarrer MySQL via service Windows
echo.
echo [2/3] Tentative via Service Windows...
net start MySQL
if %ERRORLEVEL% EQU 0 (
    echo.
    echo MySQL demarre via Service Windows !
    goto :success
)

REM Démarrer MySQL80 (version 8.0)
echo.
echo [3/3] Tentative MySQL80...
net start MySQL80
if %ERRORLEVEL% EQU 0 (
    echo.
    echo MySQL80 demarre !
    goto :success
)

REM Si aucune méthode ne fonctionne
echo.
echo ========================================
echo   ERREUR : MySQL non trouve
echo ========================================
echo.
echo MySQL n'a pas pu etre demarre automatiquement.
echo.
echo SOLUTIONS MANUELLES:
echo.
echo 1. Si vous utilisez XAMPP:
echo    - Ouvrir XAMPP Control Panel
echo    - Cliquer sur "Start" pour MySQL
echo.
echo 2. Si vous utilisez WAMP:
echo    - Ouvrir WAMP
echo    - Cliquer sur l'icone
echo    - Start All Services
echo.
echo 3. Via Services Windows:
echo    - Appuyer sur Win + R
echo    - Taper: services.msc
echo    - Chercher "MySQL" ou "MySQL80"
echo    - Clic droit ^> Demarrer
echo.
pause
exit /b 1

:success
echo.
echo ========================================
echo   VERIFICATION
echo ========================================
echo.
echo Attente de 3 secondes...
timeout /t 3 /nobreak >nul

echo.
echo Test de connexion MySQL...
php -r "try { new PDO('mysql:host=127.0.0.1;port=3306', 'root', ''); echo 'OK - MySQL est accessible !'; } catch(Exception $e) { echo 'ERREUR - MySQL non accessible: ' . $e->getMessage(); }"

echo.
echo.
echo ========================================
echo   MYSQL DEMARRE !
echo ========================================
echo.
echo Vous pouvez maintenant lancer:
echo   php bin/console doctrine:schema:update --force
echo   symfony serve
echo.
pause
