@echo off
echo ========================================
echo REPARATION COMPLETE MYSQL
echo ========================================
echo.

echo Etape 1: Arreter tous les processus MySQL...
taskkill /F /IM mysqld.exe 2>nul
timeout /t 3 >nul

echo Etape 2: Sauvegarder les bases de donnees...
if not exist "C:\xampp2\mysql\backup" mkdir "C:\xampp2\mysql\backup"
xcopy "C:\xampp2\mysql\data\autolearn_db" "C:\xampp2\mysql\backup\autolearn_db\" /E /I /Y >nul 2>&1

echo Etape 3: Reparer les tables systeme MySQL...
cd /d "C:\xampp2\mysql\bin"

echo Demarrage de MySQL en mode reparation...
start /B mysqld --skip-grant-tables --skip-networking

timeout /t 5 >nul

echo Reparation des tables...
mysql -u root -e "USE mysql; REPAIR TABLE db, user, host, tables_priv, columns_priv, procs_priv, proxies_priv;"

echo Arret de MySQL...
mysqladmin -u root shutdown
timeout /t 3 >nul

echo Etape 4: Redemarrage normal de MySQL...
net start MySQL

echo.
echo ========================================
echo TERMINE!
echo ========================================
echo.
echo MySQL devrait maintenant fonctionner.
echo Si le probleme persiste, restaurez depuis:
echo C:\xampp2\mysql\backup\
echo.
pause
