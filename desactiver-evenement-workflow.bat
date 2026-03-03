@echo off
echo ========================================
echo   DESACTIVATION MODULE EVENEMENT
echo ========================================
echo.
echo Le module Evenement necessite le composant Workflow
echo qui n'est pas installe. Desactivation en cours...
echo.

REM Désactiver EvenementController
if exist "src\Controller\EvenementController.php" (
    ren "src\Controller\EvenementController.php" "EvenementController.php.disabled"
    echo [OK] EvenementController desactive
) else (
    echo [--] EvenementController deja desactive
)

REM Désactiver UpdateEvenementWorkflowCommand
if exist "src\Command\UpdateEvenementWorkflowCommand.php" (
    ren "src\Command\UpdateEvenementWorkflowCommand.php" "UpdateEvenementWorkflowCommand.php.disabled"
    echo [OK] UpdateEvenementWorkflowCommand desactive
) else (
    echo [--] UpdateEvenementWorkflowCommand deja desactive
)

REM Désactiver UpdateEventStatusCommand
if exist "src\Command\UpdateEventStatusCommand.php" (
    ren "src\Command\UpdateEventStatusCommand.php" "UpdateEventStatusCommand.php.disabled"
    echo [OK] UpdateEventStatusCommand desactive
) else (
    echo [--] UpdateEventStatusCommand deja desactive
)

echo.
echo ========================================
echo   NETTOYAGE DU CACHE
echo ========================================
echo.
call php bin/console cache:clear

echo.
echo ========================================
echo   TERMINE !
echo ========================================
echo.
echo Le module Evenement a ete desactive.
echo Pour le reactiver, installez: composer require symfony/workflow
echo.
pause
