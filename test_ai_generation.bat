@echo off
echo ========================================
echo Test de Generation d'Exercices par IA
echo ========================================
echo.
echo Ce test verifie que l'IA genere des reponses completes
echo (minimum 100 caracteres, idealement 150-300)
echo.
echo Appuyez sur une touche pour continuer...
pause > nul

cd /d "%~dp0"

echo.
echo Execution du test...
echo.

php bin/console dbal:run-sql "SELECT question, LENGTH(reponse) as longueur, LEFT(reponse, 150) as apercu FROM exercice ORDER BY id DESC LIMIT 5"

echo.
echo ========================================
echo Statistiques globales:
echo ========================================
echo.

php bin/console dbal:run-sql "SELECT COUNT(*) as total, AVG(LENGTH(reponse)) as longueur_moyenne, MIN(LENGTH(reponse)) as min_longueur, MAX(LENGTH(reponse)) as max_longueur FROM exercice"

echo.
echo ========================================
echo Exercices avec reponses trop courtes:
echo ========================================
echo.

php bin/console dbal:run-sql "SELECT COUNT(*) as nb_reponses_courtes FROM exercice WHERE LENGTH(reponse) < 100"

echo.
echo Test termine!
echo.
pause
