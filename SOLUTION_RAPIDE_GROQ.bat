@echo off
echo ========================================
echo SOLUTION RAPIDE: Recharger les variables
echo ========================================
echo.

echo Etape 1: Supprimer TOUT le cache
rmdir /s /q var\cache
echo Cache supprime!
echo.

echo Etape 2: Recreer le cache
php bin/console cache:warmup
echo Cache recree!
echo.

echo ========================================
echo IMPORTANT!
echo ========================================
echo.
echo Maintenant vous DEVEZ:
echo.
echo 1. Aller dans le terminal ou Symfony tourne
echo 2. Appuyer sur Ctrl+C pour arreter le serveur
echo 3. Taper: symfony serve
echo 4. Recharger la page dans le navigateur
echo.
echo ========================================
pause
