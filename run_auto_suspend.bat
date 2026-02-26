@echo off
echo ========================================
echo Suspension Automatique - Utilisateurs Inactifs
echo ========================================
echo.

echo Cette commande va suspendre automatiquement les etudiants
echo qui n'ont pas ete actifs depuis 7 jours ou plus.
echo.

echo Options:
echo 1. Executer en mode SIMULATION (aucune modification)
echo 2. Executer en mode REEL (suspensions effectives)
echo 3. Personnaliser le nombre de jours
echo 4. Quitter
echo.

set /p choice="Votre choix (1-4): "

if "%choice%"=="1" (
    echo.
    echo Mode SIMULATION - Aucune modification ne sera effectuee
    php bin/console app:auto-suspend-inactive-users --dry-run
) else if "%choice%"=="2" (
    echo.
    echo Mode REEL - Les suspensions seront effectives
    echo Etes-vous sur? (O/N)
    set /p confirm=
    if /i "%confirm%"=="O" (
        php bin/console app:auto-suspend-inactive-users
    ) else (
        echo Operation annulee
    )
) else if "%choice%"=="3" (
    echo.
    set /p days="Nombre de jours d'inactivite: "
    echo Mode SIMULATION avec %days% jours
    php bin/console app:auto-suspend-inactive-users --days=%days% --dry-run
    echo.
    echo Voulez-vous executer en mode reel? (O/N)
    set /p confirm=
    if /i "%confirm%"=="O" (
        php bin/console app:auto-suspend-inactive-users --days=%days%
    )
) else (
    echo Au revoir!
)

echo.
pause
