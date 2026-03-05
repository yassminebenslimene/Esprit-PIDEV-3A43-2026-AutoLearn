@echo off
cls
color 0A
echo ========================================
echo SOLUTION ULTIME - REINITIALISATION MYSQL
echo ========================================
echo.
echo Cette operation va:
echo 1. Exporter votre base autolearn_db
echo 2. Reinitialiser completement MySQL
echo 3. Reimporter votre base de donnees
echo.
echo IMPORTANT: Assurez-vous qu'Apache est demarre!
echo.
pause

echo.
echo [1/6] Arret de MySQL...
taskkill /F /IM mysqld.exe >nul 2>&1
timeout /t 2 >nul

echo [2/6] Sauvegarde complete du dossier data...
if not exist "C:\xampp2\mysql\DATA_BACKUP_COMPLETE" (
    echo     Creation de la sauvegarde...
    xcopy "C:\xampp2\mysql\data" "C:\xampp2\mysql\DATA_BACKUP_COMPLETE\" /E /I /Y /Q
    echo     Sauvegarde OK
) else (
    echo     Sauvegarde existante trouvee
)

echo [3/6] Suppression du dossier data actuel...
rmdir /S /Q "C:\xampp2\mysql\data"
timeout /t 1 >nul

echo [4/6] Restauration data depuis backup XAMPP...
xcopy "C:\xampp2\mysql\backup" "C:\xampp2\mysql\data\" /E /I /Y /Q
echo     Data restaure

echo [5/6] Copie de votre base autolearn_db...
if exist "C:\xampp2\mysql\DATA_BACKUP_COMPLETE\autolearn_db" (
    xcopy "C:\xampp2\mysql\DATA_BACKUP_COMPLETE\autolearn_db" "C:\xampp2\mysql\data\autolearn_db\" /E /I /Y /Q
    echo     Base autolearn_db restauree
) else (
    echo     ATTENTION: Base autolearn_db non trouvee dans la sauvegarde
)

echo [6/6] Demarrage de MySQL...
cd /d "C:\xampp2"
start mysql_start.bat
timeout /t 10 >nul

echo.
echo ========================================
echo TERMINE!
echo ========================================
echo.
echo Verifiez XAMPP Control Panel
echo MySQL devrait maintenant demarrer
echo.
echo Si MySQL demarre:
echo - Votre base autolearn_db est preservee
echo - Mot de passe root: (vide)
echo.
echo Sauvegarde complete dans:
echo C:\xampp2\mysql\DATA_BACKUP_COMPLETE\
echo.
pause
