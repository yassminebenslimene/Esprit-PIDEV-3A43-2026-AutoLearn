@echo off
echo ========================================
echo Configuration Automatique - Branche ilef
echo ========================================
echo.

echo Etape 1: Verification du fichier .env...
if not exist .env (
    echo .env n'existe pas, creation depuis .env.example...
    copy .env.example .env
    echo.
    echo IMPORTANT: Vous devez editer .env et configurer:
    echo - BREVO_API_KEY
    echo - MAIL_FROM_EMAIL
    echo - MAILER_DSN
    echo - APP_SECRET
    echo.
    echo Voulez-vous ouvrir .env maintenant? (O/N)
    set /p choice=
    if /i "%choice%"=="O" notepad .env
    echo.
    echo Appuyez sur une touche apres avoir configure .env...
    pause
) else (
    echo .env existe deja
)

echo.
echo Etape 2: Verification des migrations...
php bin/console doctrine:migrations:status

echo.
echo Voulez-vous marquer toutes les migrations comme executees? (O/N)
set /p migrate=
if /i "%migrate%"=="O" (
    php bin/console doctrine:migrations:version --add --all --no-interaction
    echo Migrations marquees comme executees
)

echo.
echo Etape 3: Verification des colonnes de suspension...
php bin/console dbal:run-sql "DESCRIBE user" | findstr "suspended"

echo.
echo Si les colonnes suspended n'apparaissent pas, voulez-vous les creer? (O/N)
set /p createcols=
if /i "%createcols%"=="O" (
    php bin/console dbal:run-sql "ALTER TABLE user ADD is_suspended TINYINT(1) DEFAULT 0 NOT NULL, ADD suspended_at DATETIME DEFAULT NULL, ADD suspension_reason VARCHAR(500) DEFAULT NULL, ADD suspended_by INT DEFAULT NULL"
    echo Colonnes creees
)

echo.
echo Etape 4: Nettoyage du cache...
php bin/console cache:clear

echo.
echo Etape 5: Verification des routes...
php bin/console debug:router | findstr suspend

echo.
echo ========================================
echo Configuration terminee!
echo ========================================
echo.
echo Prochaines etapes:
echo 1. Testez l'application: symfony server:start
echo 2. Allez sur http://localhost:8000/backoffice/users
echo 3. Testez la suspension/reactivation
echo 4. Push vers GitHub: git push origin ilef
echo.
pause
