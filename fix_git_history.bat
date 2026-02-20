@echo off
echo ========================================
echo Nettoyage de l'historique Git
echo ========================================
echo.

echo ATTENTION: Cette operation va réécrire l'historique Git!
echo Assurez-vous d'avoir sauvegardé votre travail.
echo.
pause

echo.
echo Etape 1: Suppression du .env de l'historique...
git filter-branch --force --index-filter "git rm --cached --ignore-unmatch .env" --prune-empty --tag-name-filter cat -- --all

echo.
echo Etape 2: Nettoyage des références...
git for-each-ref --format="delete %(refname)" refs/original | git update-ref --stdin

echo.
echo Etape 3: Garbage collection...
git reflog expire --expire=now --all
git gc --prune=now --aggressive

echo.
echo ========================================
echo Nettoyage terminé!
echo ========================================
echo.
echo Maintenant vous pouvez pousser avec:
echo git push origin web --force
echo.
pause
