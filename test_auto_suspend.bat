@echo off
echo ========================================
echo Test du Systeme de Suspension Automatique
echo ========================================
echo.

echo Etape 1: Simulation d'inactivite pour un etudiant
echo.
echo Cette commande va mettre a jour la date de derniere connexion
echo d'un etudiant pour simuler 10 jours d'inactivite.
echo.

php bin/console dbal:run-sql "UPDATE user SET last_login_at = DATE_SUB(NOW(), INTERVAL 10 DAY) WHERE role = 'ETUDIANT' LIMIT 1"

echo.
echo Etape 2: Verification des utilisateurs inactifs (MODE SIMULATION)
echo.
php bin/console app:auto-suspend-inactive-users --dry-run

echo.
echo Etape 3: Voulez-vous executer la suspension en mode REEL? (O/N)
set /p confirm=
if /i "%confirm%"=="O" (
    echo.
    echo Execution de la suspension automatique...
    php bin/console app:auto-suspend-inactive-users
    
    echo.
    echo Etape 4: Verification de la suspension en base de donnees
    php bin/console dbal:run-sql "SELECT userId, nom, prenom, email, is_suspended, suspended_at, suspension_reason FROM user WHERE role = 'ETUDIANT' AND is_suspended = 1"
    
    echo.
    echo Test termine! Verifiez vos emails.
) else (
    echo Test annule.
)

echo.
pause
