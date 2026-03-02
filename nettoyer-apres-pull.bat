@echo off
echo ========================================
echo   NETTOYAGE APRES GIT PULL
echo ========================================
echo.

echo Etape 1: Suppression des fichiers de configuration problematiques...
echo.

REM Supprimer calendar.yaml
if exist "config\packages\calendar.yaml" (
    del "config\packages\calendar.yaml"
    echo [OK] calendar.yaml supprime
) else (
    echo [--] calendar.yaml deja supprime
)

REM Supprimer calendar routes
if exist "config\routes\calendar.yaml" (
    del "config\routes\calendar.yaml"
    echo [OK] calendar routes supprime
) else (
    echo [--] calendar routes deja supprime
)

REM Supprimer simple_things_entity_audit.yaml
if exist "config\packages\simple_things_entity_audit.yaml" (
    del "config\packages\simple_things_entity_audit.yaml"
    echo [OK] simple_things_entity_audit.yaml supprime
) else (
    echo [--] simple_things_entity_audit.yaml deja supprime
)

REM Supprimer workflow.yaml
if exist "config\packages\workflow.yaml" (
    del "config\packages\workflow.yaml"
    echo [OK] workflow.yaml supprime
) else (
    echo [--] workflow.yaml deja supprime
)

REM Desactiver EvenementWorkflowSubscriber
if exist "src\EventSubscriber\EvenementWorkflowSubscriber.php" (
    ren "src\EventSubscriber\EvenementWorkflowSubscriber.php" "EvenementWorkflowSubscriber.php.disabled"
    echo [OK] EvenementWorkflowSubscriber desactive
) else (
    echo [--] EvenementWorkflowSubscriber deja desactive
)

echo.
echo Etape 2: Installation des dependances...
echo.
call composer install

echo.
echo Etape 3: Vidage du cache...
echo.
call php bin/console cache:clear

echo.
echo ========================================
echo   NETTOYAGE TERMINE !
echo ========================================
echo.
echo Vous pouvez maintenant lancer: symfony serve
echo Ou si le serveur est deja lance: http://127.0.0.1:8000
echo.
pause
