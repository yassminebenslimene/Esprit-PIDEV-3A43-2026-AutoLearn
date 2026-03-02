@echo off
cls
echo ========================================
echo   NETTOYAGE FINAL COMPLET
echo ========================================
echo.
echo Ce script desactive tous les modules
echo qui necessitent des composants non installes
echo.
pause

echo.
echo [1/4] Desactivation des EventSubscribers...
echo.

if exist "src\EventSubscriber\CalendarSubscriber.php" (
    ren "src\EventSubscriber\CalendarSubscriber.php" "CalendarSubscriber.php.disabled"
    echo [OK] CalendarSubscriber desactive
)

if exist "src\EventSubscriber\EvenementWorkflowSubscriber.php" (
    ren "src\EventSubscriber\EvenementWorkflowSubscriber.php" "EvenementWorkflowSubscriber.php.disabled"
    echo [OK] EvenementWorkflowSubscriber desactive
)

echo.
echo [2/4] Desactivation des Controllers...
echo.

if exist "src\Controller\EvenementController.php" (
    ren "src\Controller\EvenementController.php" "EvenementController.php.disabled"
    echo [OK] EvenementController desactive
)

echo.
echo [3/4] Desactivation des Commands...
echo.

if exist "src\Command\UpdateEvenementWorkflowCommand.php" (
    ren "src\Command\UpdateEvenementWorkflowCommand.php" "UpdateEvenementWorkflowCommand.php.disabled"
    echo [OK] UpdateEvenementWorkflowCommand desactive
)

if exist "src\Command\UpdateEventStatusCommand.php" (
    ren "src\Command\UpdateEventStatusCommand.php" "UpdateEventStatusCommand.php.disabled"
    echo [OK] UpdateEventStatusCommand desactive
)

echo.
echo [4/4] Suppression des fichiers de configuration...
echo.

if exist "config\packages\calendar.yaml" (
    del "config\packages\calendar.yaml"
    echo [OK] calendar.yaml supprime
)

if exist "config\routes\calendar.yaml" (
    del "config\routes\calendar.yaml"
    echo [OK] calendar routes supprime
)

if exist "config\packages\simple_things_entity_audit.yaml" (
    del "config\packages\simple_things_entity_audit.yaml"
    echo [OK] simple_things_entity_audit.yaml supprime
)

if exist "config\packages\workflow.yaml" (
    del "config\packages\workflow.yaml"
    echo [OK] workflow.yaml supprime
)

echo.
echo ========================================
echo   VIDAGE DU CACHE
echo ========================================
echo.
call php bin/console cache:clear --no-warmup
call php bin/console cache:warmup

echo.
echo ========================================
echo   NETTOYAGE TERMINE !
echo ========================================
echo.
echo Tous les modules problematiques ont ete desactives.
echo.
echo Vous pouvez maintenant lancer:
echo   symfony serve
echo.
echo Ou ouvrir: http://127.0.0.1:8000
echo.
pause
