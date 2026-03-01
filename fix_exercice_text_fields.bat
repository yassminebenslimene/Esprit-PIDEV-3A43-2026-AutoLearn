@echo off
echo ========================================
echo Fix: Augmenter taille colonnes exercice
echo ========================================
echo.
echo Ce script modifie les colonnes question et reponse
echo de VARCHAR(30) vers TEXT pour permettre des reponses longues
echo.
echo Appuyez sur une touche pour continuer...
pause > nul

cd /d "%~dp0"

echo.
echo Execution du script SQL...
echo.

php bin/console dbal:run-sql "ALTER TABLE exercice MODIFY COLUMN question TEXT NOT NULL"
php bin/console dbal:run-sql "ALTER TABLE exercice MODIFY COLUMN reponse TEXT NOT NULL"

echo.
echo ========================================
echo Verification de la structure:
echo ========================================
echo.

php bin/console dbal:run-sql "DESCRIBE exercice"

echo.
echo ========================================
echo Test avec les derniers exercices:
echo ========================================
echo.

php bin/console dbal:run-sql "SELECT id, LENGTH(question) as longueur_question, LENGTH(reponse) as longueur_reponse FROM exercice ORDER BY id DESC LIMIT 5"

echo.
echo Fix termine!
echo.
pause
