@echo off
echo ========================================
echo Mise a jour de la table challenge
echo ========================================
echo.

echo Execution du script SQL...
mysql -u root autolearn_db < update_challenge_table.sql

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ✓ Table challenge mise a jour avec succes!
    echo   - Colonne 'duree' ajoutee
    echo   - Colonnes 'date_debut' et 'date_fin' supprimees
) else (
    echo.
    echo ✗ Erreur lors de la mise a jour
)

echo.
pause
