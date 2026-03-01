@echo off
echo ========================================
echo Migration Challenge: date -> duree
echo ========================================
echo.

echo Execution de la migration...
php bin/console doctrine:migrations:migrate --no-interaction

echo.
echo Migration terminee!
echo.
pause
