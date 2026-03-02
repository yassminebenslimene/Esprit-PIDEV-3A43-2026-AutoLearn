@echo off
cls
echo ========================================
echo   MISE A JOUR COMPLETE APRES GIT PULL
echo ========================================
echo.
echo Ce script fait TOUT automatiquement:
echo - Nettoyage des bundles non installes
echo - Installation des dependances
echo - Mise a jour de la base de donnees
echo - Vidage du cache
echo.
pause

echo.
echo ========================================
echo   ETAPE 1/5 : NETTOYAGE
echo ========================================
echo.

REM Désactiver CalendarSubscriber
if exist "src\EventSubscriber\CalendarSubscriber.php" (
    ren "src\EventSubscriber\CalendarSubscriber.php" "CalendarSubscriber.php.disabled"
    echo [OK] CalendarSubscriber desactive
)

REM Désactiver EvenementWorkflowSubscriber
if exist "src\EventSubscriber\EvenementWorkflowSubscriber.php" (
    ren "src\EventSubscriber\EvenementWorkflowSubscriber.php" "EvenementWorkflowSubscriber.php.disabled"
    echo [OK] EvenementWorkflowSubscriber desactive
)

REM Désactiver EvenementController
if exist "src\Controller\EvenementController.php" (
    ren "src\Controller\EvenementController.php" "EvenementController.php.disabled"
    echo [OK] EvenementController desactive
)

REM Désactiver Commands
if exist "src\Command\UpdateEvenementWorkflowCommand.php" (
    ren "src\Command\UpdateEvenementWorkflowCommand.php" "UpdateEvenementWorkflowCommand.php.disabled"
    echo [OK] UpdateEvenementWorkflowCommand desactive
)

if exist "src\Command\UpdateEventStatusCommand.php" (
    ren "src\Command\UpdateEventStatusCommand.php" "UpdateEventStatusCommand.php.disabled"
    echo [OK] UpdateEventStatusCommand desactive
)

REM Supprimer fichiers de configuration
if exist "config\packages\calendar.yaml" del "config\packages\calendar.yaml"
if exist "config\routes\calendar.yaml" del "config\routes\calendar.yaml"
if exist "config\packages\simple_things_entity_audit.yaml" del "config\packages\simple_things_entity_audit.yaml"
if exist "config\packages\workflow.yaml" del "config\packages\workflow.yaml"

echo.
echo ========================================
echo   ETAPE 2/5 : COMPOSER INSTALL
echo ========================================
echo.
call composer install

echo.
echo ========================================
echo   ETAPE 3/5 : MISE A JOUR BASE DE DONNEES
echo ========================================
echo.
echo Verification des changements...
call php bin/console doctrine:schema:update --dump-sql

echo.
echo Application des changements...
call php bin/console doctrine:schema:update --force

echo.
echo ========================================
echo   ETAPE 4/5 : VIDAGE DU CACHE
echo ========================================
echo.
call php bin/console cache:clear --no-warmup
call php bin/console cache:warmup

echo.
echo ========================================
echo   ETAPE 5/5 : VERIFICATION
echo ========================================
echo.
call php bin/console about

echo.
echo ========================================
echo   MISE A JOUR TERMINEE !
echo ========================================
echo.
echo Votre application est prete !
echo.
echo Lancez: symfony serve
echo Ou ouvrez: http://127.0.0.1:8000
echo.
pause
