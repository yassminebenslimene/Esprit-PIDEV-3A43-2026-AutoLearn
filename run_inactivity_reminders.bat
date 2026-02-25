@echo off
echo ========================================
echo  Systeme de Rappel d'Inactivite
echo  Autolearn Platform
echo ========================================
echo.

cd /d "%~dp0"

echo [1/3] Verification de l'environnement...
if not exist "bin\console" (
    echo ERREUR: Fichier bin\console introuvable
    echo Assurez-vous d'etre dans le repertoire racine du projet
    pause
    exit /b 1
)

echo [2/3] Execution de la commande...
echo.
php bin\console app:send-inactivity-reminders

echo.
echo [3/3] Termine!
echo.
echo Consultez les logs dans var\log\dev.log pour plus de details
echo.
pause
