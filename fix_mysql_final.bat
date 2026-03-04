@echo off
cls
echo ========================================
echo SOLUTION FINALE - REPARATION MYSQL
echo ========================================
echo.
echo Cette operation va:
echo 1. Sauvegarder votre base autolearn_db
echo 2. Restaurer les tables systeme MySQL
echo 3. Restaurer votre base de donnees
echo.
pause

echo.
echo [1/5] Arret de MySQL...
taskkill /F /IM mysqld.exe >nul 2>&1
timeout /t 2 >nul

echo [2/5] Sauvegarde de autolearn_db...
if not exist "C:\xampp2\mysql\BACKUP_AUTOLEARN" mkdir "C:\xampp2\mysql\BACKUP_AUTOLEARN"
xcopy "C:\xampp2\mysql\data\autolearn_db" "C:\xampp2\mysql\BACKUP_AUTOLEARN\autolearn_db\" /E /I /Y /Q
echo     Sauvegarde OK: C:\xampp2\mysql\BACKUP_AUTOLEARN\

echo [3/5] Restauration des tables systeme MySQL...
rmdir /S /Q "C:\xampp2\mysql\data\mysql" 2>nul
xcopy "C:\xampp2\mysql\backup\mysql" "C:\xampp2\mysql\data\mysql\" /E /I /Y /Q
echo     Tables systeme restaurees

echo [4/5] Nettoyage des fichiers InnoDB...
del /F /Q "C:\xampp2\mysql\data\ib_logfile*" 2>nul
del /F /Q "C:\xampp2\mysql\data\ibdata1" 2>nul
del /F /Q "C:\xampp2\mysql\data\ibtmp1" 2>nul
echo     Fichiers InnoDB nettoyes

echo [5/5] Demarrage de MySQL...
cd /d "C:\xampp2"
call mysql_start.bat
timeout /t 5 >nul

echo.
echo ========================================
echo TERMINE!
echo ========================================
echo.
echo Verifiez maintenant XAMPP Control Panel
echo MySQL devrait demarrer correctement
echo.
echo Votre base de donnees est sauvegardee dans:
echo C:\xampp2\mysql\BACKUP_AUTOLEARN\
echo.
pause
