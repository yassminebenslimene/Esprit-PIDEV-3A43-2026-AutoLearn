@echo off
echo ========================================
echo RESTAURATION TABLES SYSTEME MYSQL
echo ========================================
echo.

echo ATTENTION: Cette operation va reinitialiser les tables systeme MySQL
echo Vos bases de donnees (autolearn_db) seront preservees
echo.
pause

echo Etape 1: Arreter MySQL...
taskkill /F /IM mysqld.exe 2>nul
timeout /t 2 >nul

echo Etape 2: Sauvegarder votre base de donnees...
if not exist "C:\xampp2\mysql\backup_db" mkdir "C:\xampp2\mysql\backup_db"
xcopy "C:\xampp2\mysql\data\autolearn_db" "C:\xampp2\mysql\backup_db\autolearn_db\" /E /I /Y >nul 2>&1
echo Base de donnees sauvegardee dans: C:\xampp2\mysql\backup_db\

echo Etape 3: Supprimer les tables systeme corrompues...
del /F /Q "C:\xampp2\mysql\data\mysql\*.frm" 2>nul
del /F /Q "C:\xampp2\mysql\data\mysql\*.MYD" 2>nul
del /F /Q "C:\xampp2\mysql\data\mysql\*.MYI" 2>nul

echo Etape 4: Copier les tables systeme depuis le backup XAMPP...
xcopy "C:\xampp2\mysql\backup\mysql\*.*" "C:\xampp2\mysql\data\mysql\" /E /I /Y

echo Etape 5: Demarrer MySQL...
cd /d "C:\xampp2"
start mysql_start.bat

echo.
echo ========================================
echo TERMINE!
echo ========================================
echo.
echo Attendez 10 secondes puis verifiez XAMPP Control Panel
echo Si MySQL demarre, votre base autolearn_db est preservee
echo.
pause
