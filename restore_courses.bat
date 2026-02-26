@echo off
echo ========================================
echo Restore Courses for AutoLearn
echo ========================================
echo.

echo Step 1: Checking if Doctrine Fixtures is installed...
php bin/console list doctrine:fixtures:load >nul 2>&1
if %errorlevel% neq 0 (
    echo Doctrine Fixtures not found. Installing...
    composer require --dev doctrine/doctrine-fixtures-bundle
    echo.
) else (
    echo Doctrine Fixtures is already installed.
    echo.
)

echo Step 2: Loading course fixtures...
echo.
echo WARNING: This will add 10 sample courses to your database.
echo.
set /p confirm="Do you want to continue? (Y/N): "

if /i "%confirm%"=="Y" (
    echo.
    echo Loading fixtures...
    php bin/console doctrine:fixtures:load --append
    echo.
    echo ========================================
    echo SUCCESS!
    echo ========================================
    echo.
    echo 10 courses have been added to your database:
    echo - Python pour Debutants
    echo - JavaScript Moderne (ES6+)
    echo - Developpement Web avec React
    echo - Bases de Donnees SQL
    echo - PHP et Symfony Framework
    echo - Git et GitHub pour Developpeurs
    echo - Java et Programmation Orientee Objet
    echo - HTML5 et CSS3 Fondamentaux
    echo - Node.js et Express Backend
    echo - Introduction a l'Intelligence Artificielle
    echo.
    echo Go to http://localhost:8000/ to see them!
    echo.
) else (
    echo.
    echo Operation cancelled.
    echo.
)

pause
