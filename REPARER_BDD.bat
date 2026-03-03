@echo off
cls
echo ========================================
echo   REPARATION BASE DE DONNEES
echo ========================================
echo.
echo Ce script va reparer la base de donnees
echo en ajoutant les colonnes manquantes
echo.
pause

echo.
echo ========================================
echo   METHODE 1 : Doctrine Schema Update
echo ========================================
echo.
echo Verification des changements necessaires...
call php bin/console doctrine:schema:update --dump-sql

echo.
echo Application des changements...
call php bin/console doctrine:schema:update --force

echo.
echo ========================================
echo   METHODE 2 : Validation du schema
echo ========================================
echo.
call php bin/console doctrine:schema:validate

echo.
echo ========================================
echo   METHODE 3 : SQL Manuel (si necessaire)
echo ========================================
echo.
echo Si l'erreur persiste, executez manuellement:
echo.
echo 1. Ouvrir phpMyAdmin
echo 2. Selectionner la base 'autolearn_db'
echo 3. Onglet SQL
echo 4. Copier-coller le contenu de 'ajouter-colonne-duree.sql'
echo 5. Executer
echo.
echo Ou via MySQL CLI:
echo mysql -u root -p autolearn_db ^< ajouter-colonne-duree.sql
echo.
pause

echo.
echo ========================================
echo   VIDAGE DU CACHE
echo ========================================
echo.
call php bin/console cache:clear

echo.
echo ========================================
echo   VERIFICATION FINALE
echo ========================================
echo.
call php bin/console doctrine:schema:validate

echo.
echo ========================================
echo   TERMINE !
echo ========================================
echo.
echo Si l'erreur persiste, consultez MISE_A_JOUR_BDD.md
echo.
pause
