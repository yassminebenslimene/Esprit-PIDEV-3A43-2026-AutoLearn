@echo off
REM ========================================
REM AutoLearn - Suspension Automatique Quotidienne
REM ========================================
REM Ce script doit être exécuté tous les jours
REM via le Planificateur de tâches Windows
REM ========================================

cd /d "%~dp0"

echo ========================================
echo AutoLearn - Suspension Automatique
echo Date: %date% %time%
echo ========================================
echo.

REM Exécuter la suspension automatique
php bin/console app:auto-suspend-inactive-users

echo.
echo ========================================
echo Terminé: %date% %time%
echo ========================================

REM Enregistrer dans un fichier log
echo [%date% %time%] Suspension automatique exécutée >> logs\auto_suspend.log
