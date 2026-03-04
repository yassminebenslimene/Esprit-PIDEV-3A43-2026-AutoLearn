@echo off
REM Script pour analyser le code avec PHPStan
REM Usage: phpstan-check.bat [options]

echo ========================================
echo   PHPStan - Analyse Statique du Code
echo ========================================
echo.

if "%1"=="--help" (
    echo Usage:
    echo   phpstan-check.bat              Analyse complete
    echo   phpstan-check.bat controller   Analyse des controleurs
    echo   phpstan-check.bat service      Analyse des services
    echo   phpstan-check.bat entity       Analyse des entites
    echo   phpstan-check.bat --clear      Nettoyer le cache
    echo.
    exit /b 0
)

if "%1"=="--clear" (
    echo Nettoyage du cache PHPStan...
    vendor\bin\phpstan clear-result-cache
    echo Cache nettoye!
    exit /b 0
)

if "%1"=="controller" (
    echo Analyse des controleurs...
    vendor\bin\phpstan analyse src/Controller --level=8
    exit /b %ERRORLEVEL%
)

if "%1"=="service" (
    echo Analyse des services...
    vendor\bin\phpstan analyse src/Service --level=8
    exit /b %ERRORLEVEL%
)

if "%1"=="entity" (
    echo Analyse des entites...
    vendor\bin\phpstan analyse src/Entity --level=8
    exit /b %ERRORLEVEL%
)

echo Analyse complete du projet...
vendor\bin\phpstan analyse src --level=8

exit /b %ERRORLEVEL%
