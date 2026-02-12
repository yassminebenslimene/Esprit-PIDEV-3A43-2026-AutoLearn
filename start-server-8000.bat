@echo off
echo ========================================
echo   Demarrage du serveur sur le port 8000
echo ========================================
echo.
echo Arret des serveurs existants...
call symfony server:stop 2>nul
echo.
echo Demarrage du serveur PHP sur le port 8000...
echo.
echo IMPORTANT: Laissez cette fenetre ouverte!
echo.
echo URLs disponibles:
echo   - Backoffice: http://localhost:8000/backoffice
echo   - Evenements: http://localhost:8000/backoffice/evenement/
echo   - Equipes:    http://localhost:8000/backoffice/equipe/
echo   - Participations: http://localhost:8000/backoffice/participation/
echo.
echo Appuyez sur Ctrl+C pour arreter le serveur
echo ========================================
echo.
php -S localhost:8000 -t public
