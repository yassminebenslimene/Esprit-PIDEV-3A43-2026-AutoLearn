@echo off
cls
echo ========================================
echo COPIE DU PROJET VERS HTDOCS
echo ========================================
echo.
echo Cette operation va copier le projet autolearn
echo vers C:\xampp2\htdocs\autolearn
echo.
pause

echo.
echo [1/3] Verification du dossier htdocs...
if not exist "C:\xampp2\htdocs" (
    echo ERREUR: Le dossier C:\xampp2\htdocs n'existe pas!
    pause
    exit /b 1
)

echo [2/3] Suppression de l'ancien projet (si existe)...
if exist "C:\xampp2\htdocs\autolearn" (
    rmdir /S /Q "C:\xampp2\htdocs\autolearn"
    echo     Ancien projet supprime
)

echo [3/3] Copie du projet...
xcopy "%~dp0*" "C:\xampp2\htdocs\autolearn\" /E /I /Y /Q /EXCLUDE:%~dp0exclude.txt
echo     Copie terminee

echo.
echo ========================================
echo TERMINE!
echo ========================================
echo.
echo Votre projet est maintenant accessible via:
echo http://localhost/autolearn/public/
echo.
echo OU utilisez le serveur Symfony:
echo http://localhost:8000
echo.
pause
