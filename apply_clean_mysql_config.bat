@echo off
echo ========================================
echo APPLICATION CONFIG MYSQL PROPRE
echo ========================================
echo.

echo Arret de MySQL...
taskkill /F /IM mysqld.exe >nul 2>&1
timeout /t 2 >nul

echo Copie du fichier my.ini propre...
copy /Y "%~dp0my.ini.clean" "C:\xampp2\mysql\bin\my.ini"

echo Demarrage de MySQL...
cd /d "C:\xampp2"
call mysql_start.bat

echo.
echo ========================================
echo TERMINE!
echo ========================================
echo.
echo Verifiez XAMPP Control Panel
echo MySQL devrait demarrer sans erreur
echo.
pause
