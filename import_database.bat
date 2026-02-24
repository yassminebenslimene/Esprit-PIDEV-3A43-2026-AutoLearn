@echo off
echo ========================================
echo Import de la Base de Donnees AutoLearn
echo ========================================
echo.

REM Configuration
set DB_NAME=autolearn_db
set DB_USER=root
set DB_PASS=

echo Fichiers SQL disponibles dans database_backups:
echo.
dir /b database_backups\*.sql
echo.

set /p SQL_FILE="Entrez le nom du fichier SQL a importer: "

if not exist "database_backups\%SQL_FILE%" (
    echo [ERREUR] Le fichier n'existe pas!
    pause
    exit /b 1
)

echo.
echo Importation en cours...
echo.

mysql -u %DB_USER% %DB_NAME% < database_backups\%SQL_FILE%

if %ERRORLEVEL% EQU 0 (
    echo [OK] Import reussi!
) else (
    echo [ERREUR] L'import a echoue!
    echo Verifiez que MySQL est demarre et que la base de donnees existe.
)

echo.
echo ========================================
echo Import termine!
echo ========================================
echo.
pause
