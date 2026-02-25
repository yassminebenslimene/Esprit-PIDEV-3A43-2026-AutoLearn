@echo off
REM Script pour envoyer automatiquement les certificats
REM A executer quotidiennement via Planificateur de taches Windows

echo ========================================
echo Envoi automatique des certificats
echo Date: %date% %time%
echo ========================================

cd /d "%~dp0"

php bin/console app:send-certificates

echo.
echo ========================================
echo Termine!
echo ========================================
pause
