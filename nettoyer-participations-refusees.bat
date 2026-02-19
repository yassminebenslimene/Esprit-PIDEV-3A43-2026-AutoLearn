@echo off
echo ========================================
echo NETTOYAGE DES PARTICIPATIONS REFUSEES
echo ========================================
echo.
echo Ce script va supprimer toutes les participations avec le statut "Refusé" de la base de données.
echo.
pause

php bin/console doctrine:query:sql "DELETE FROM participation WHERE statut = 'Refusé'"

echo.
echo ========================================
echo NETTOYAGE TERMINE
echo ========================================
echo.
echo Affichage des participations restantes:
echo.

php bin/console doctrine:query:sql "SELECT id, statut FROM participation"

echo.
pause
