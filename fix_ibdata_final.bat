@echo off
cls
echo ========================================
echo SOLUTION FINALE - IBDATA1
echo ========================================
echo.
echo ATTENTION: Ceci va reinitialiser InnoDB
echo Vos donnees seront preservees
echo.
pause

echo [1/4] Arret MySQL...
taskkill /F /IM mysqld.exe >nul 2>&1
timeout /t 3 >nul

echo [2/4] Suppression fichiers InnoDB corrompus...
del /F /Q "C:\xampp2\mysql\data\ibdata1"
del /F /Q "C:\xampp2\mysql\data\ib_logfile0"
del /F /Q "C:\xampp2\mysql\data\ib_logfile1"
del /F /Q "C:\xampp2\mysql\data\ibtmp1"
echo     Fichiers supprimes

echo [3/4] Modification my.ini pour compatibilite...
powershell -Command "(Get-Content 'C:\xampp2\mysql\bin\my.ini') -replace 'innodb_buffer_pool_size=512M', 'innodb_buffer_pool_size=128M' | Set-Content 'C:\xampp2\mysql\bin\my.ini'"
powershell -Command "(Get-Content 'C:\xampp2\mysql\bin\my.ini') -replace 'innodb_log_file_size=128M', 'innodb_log_file_size=48M' | Set-Content 'C:\xampp2\mysql\bin\my.ini'"
echo     Configuration ajustee

echo [4/4] Demarrage MySQL...
cd /d "C:\xampp2"
start mysql_start.bat
timeout /t 8 >nul

echo.
echo ========================================
echo TERMINE!
echo ========================================
echo.
echo MySQL devrait maintenant demarrer
echo Verifiez XAMPP Control Panel
echo.
pause
