@echo off
echo ========================================
echo Export de la Base de Donnees AutoLearn
echo ========================================
echo.

REM Configuration
set DB_NAME=autolearn_db
set DB_USER=root
set DB_PASS=
set BACKUP_DIR=database_backups
set TIMESTAMP=%date:~-4%%date:~3,2%%date:~0,2%_%time:~0,2%%time:~3,2%%time:~6,2%
set TIMESTAMP=%TIMESTAMP: =0%

REM Créer le dossier de backup s'il n'existe pas
if not exist %BACKUP_DIR% mkdir %BACKUP_DIR%

echo Exportation en cours...
echo.

REM Export complet
mysqldump -u %DB_USER% %DB_NAME% > %BACKUP_DIR%\backup_complet_%TIMESTAMP%.sql

if %ERRORLEVEL% EQU 0 (
    echo [OK] Export complet reussi!
    echo Fichier: %BACKUP_DIR%\backup_complet_%TIMESTAMP%.sql
) else (
    echo [ERREUR] L'export a echoue!
    echo Verifiez que MySQL est demarre et que les identifiants sont corrects.
)

echo.
echo ========================================
echo Export termine!
echo ========================================
echo.
echo Vous pouvez maintenant partager le fichier SQL avec vos camarades via:
echo - Google Drive
echo - Dropbox
echo - WeTransfer
echo - Discord/Slack
echo.
pause
