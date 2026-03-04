@echo off
echo ========================================
echo REPARATION MYSQL XAMPP
echo ========================================
echo.

echo Etape 1: Arreter MySQL...
taskkill /F /IM mysqld.exe 2>nul
timeout /t 2 >nul

echo Etape 2: Supprimer les fichiers de log corrompus...
del /F /Q "C:\xampp2\mysql\data\ib_logfile*" 2>nul
del /F /Q "C:\xampp2\mysql\data\ibdata1" 2>nul
del /F /Q "C:\xampp2\mysql\data\ibtmp1" 2>nul

echo Etape 3: Demarrer MySQL...
start "" "C:\xampp2\mysql_start.bat"
timeout /t 5 >nul

echo.
echo ========================================
echo TERMINE!
echo ========================================
echo.
echo Verifiez dans XAMPP si MySQL demarre maintenant.
echo Si le probleme persiste, restaurez depuis:
echo C:\xampp2\mysql\data_backup_*
echo.
pause
