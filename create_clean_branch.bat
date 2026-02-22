@echo off
echo ========================================
echo Creation d'une branche propre
echo ========================================
echo.

echo Etape 1: Sauvegarde de la branche actuelle...
git branch web-backup

echo.
echo Etape 2: Creation d'une nouvelle branche depuis origin/web...
git checkout -b web-temp origin/web

echo.
echo Etape 3: Cherry-pick des commits propres...
git cherry-pick 5655cf0
git cherry-pick a424b88
git cherry-pick c53d4ea

echo.
echo Etape 4: Suppression de l'ancienne branche web...
git branch -D web

echo.
echo Etape 5: Renommage de web-temp en web...
git branch -m web

echo.
echo ========================================
echo Branche propre créée!
echo ========================================
echo.
echo Maintenant vous pouvez pousser avec:
echo git push origin web --force
echo.
pause
