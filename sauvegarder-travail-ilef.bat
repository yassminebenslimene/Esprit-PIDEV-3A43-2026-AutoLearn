@echo off
echo ========================================
echo SAUVEGARDE TRAVAIL BRANCHE ILEF
echo ========================================
echo.

echo Verification que tu es sur la branche ilef...
git branch
echo.

echo Fichiers modifies:
git status
echo.

set /p message="Entre le message de commit (ex: Amelioration Navbar): "
echo.

echo Ajout des fichiers modifies...
git add .
echo.

echo Creation du commit...
git commit -m "feat: %message%"
echo.

echo Push vers la branche ilef...
git push origin ilef
echo.

echo ========================================
echo SAUVEGARDE TERMINEE !
echo ========================================
echo.
echo Ton travail a ete sauvegarde sur la branche ilef.
echo Les autres branches ne sont pas affectees.
echo.

pause
