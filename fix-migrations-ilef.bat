@echo off
echo ========================================
echo FIX MIGRATIONS - Branche Ilef
echo ========================================
echo.

echo Option 1: Marquer toutes les migrations comme executees
echo (Recommande si les tables existent deja)
echo.
set /p choice1="Marquer les migrations comme executees? (o/n): "

if /i "%choice1%"=="o" (
    echo.
    echo Marquage des migrations comme executees...
    php bin/console doctrine:migrations:version --add --all --no-interaction
    echo.
    echo Fait !
    echo.
)

echo Option 2: Creer uniquement la table revisions manquante
echo.
set /p choice2="Creer la table revisions? (o/n): "

if /i "%choice2%"=="o" (
    echo.
    echo Creation de la table revisions...
    php bin/console doctrine:schema:update --force --em=default
    echo.
    echo Fait !
    echo.
)

echo ========================================
echo VERIFICATION
echo ========================================
echo.

echo Verification des migrations...
php bin/console doctrine:migrations:status
echo.

echo ========================================
echo TERMINE !
echo ========================================
echo.
echo Si tout est OK, vous pouvez maintenant:
echo 1. Demarrer le serveur: symfony server:start
echo 2. Tester le backoffice: http://localhost:8000/backoffice
echo.

pause
