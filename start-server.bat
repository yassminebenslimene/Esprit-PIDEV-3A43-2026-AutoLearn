@echo off
echo ========================================
echo   Demarrage du serveur Symfony
echo ========================================
echo.
echo Demarrage du serveur...
echo.
echo URLs disponibles:
echo   - Backoffice: http://127.0.0.1:8001/backoffice
echo   - Evenements: http://127.0.0.1:8001/backoffice/evenement/
echo   - Equipes:    http://127.0.0.1:8001/backoffice/equipe/
echo   - Participations: http://127.0.0.1:8001/backoffice/participation/
echo.
echo Le serveur demarre en arriere-plan.
echo Pour l'arreter, utilisez: symfony server:stop
echo ========================================
echo.
symfony server:start -d
echo.
echo Serveur demarre! Ouvrez votre navigateur.
echo.
pause
