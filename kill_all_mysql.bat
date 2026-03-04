@echo off
echo Arret de tous les processus MySQL...
taskkill /F /IM mysqld.exe 2>nul
timeout /t 2 >nul
echo Tous les processus MySQL ont ete arretes.
echo.
echo Vous pouvez maintenant demarrer MySQL depuis XAMPP Control Panel
pause
