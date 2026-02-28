@echo off
REM Configuration du planificateur de tâches Windows pour les rappels d'inactivité

echo ========================================
echo Configuration Rappel Automatique
echo ========================================
echo.

REM Créer une tâche planifiée qui s'exécute tous les jours à 9h00
schtasks /create /tn "AutoLearn_Rappel_Inactivite" /tr "php %~dp0bin\console app:send-inactivity-reminders" /sc daily /st 09:00 /f

if %errorlevel% equ 0 (
    echo.
    echo [OK] Tache planifiee creee avec succes!
    echo.
    echo La commande s'executera automatiquement tous les jours a 9h00
    echo.
    echo Pour modifier l'heure:
    echo 1. Ouvre "Planificateur de taches" Windows
    echo 2. Cherche "AutoLearn_Rappel_Inactivite"
    echo 3. Modifie l'heure selon tes besoins
    echo.
) else (
    echo.
    echo [ERREUR] Impossible de creer la tache planifiee
    echo Assure-toi d'executer ce fichier en tant qu'administrateur
    echo.
)

pause
