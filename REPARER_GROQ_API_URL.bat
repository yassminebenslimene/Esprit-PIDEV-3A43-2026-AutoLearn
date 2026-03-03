@echo off
echo ========================================
echo REPARATION: Variable GROQ_API_URL manquante
echo ========================================
echo.

echo Etape 1: Vider le cache Symfony
php bin/console cache:clear
echo.

echo Etape 2: Vider le cache de production aussi
php bin/console cache:clear --env=prod
echo.

echo Etape 3: Supprimer manuellement le dossier cache
rmdir /s /q var\cache
echo.

echo ========================================
echo TERMINE!
echo ========================================
echo.
echo Maintenant:
echo 1. Arretez le serveur Symfony (Ctrl+C)
echo 2. Relancez: symfony serve
echo 3. Rechargez la page
echo.
pause
