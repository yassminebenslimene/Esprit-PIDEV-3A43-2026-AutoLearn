@echo off
echo ========================================
echo PULL BRANCHE ILEF - Mode Isole
echo ========================================
echo.

echo Etape 1: Verification de l'etat actuel...
git status
echo.

echo Etape 2: Recuperation des dernieres modifications du depot distant...
git fetch origin
echo.

echo Etape 3: Basculement vers la branche ilef...
git checkout ilef
echo.

echo Etape 4: Mise a jour de la branche ilef avec les dernieres modifications...
git pull origin ilef
echo.

echo ========================================
echo TERMINE !
echo ========================================
echo.
echo Tu es maintenant sur la branche ilef.
echo Tu peux travailler sur la Navbar et Sidebar sans impacter les autres branches.
echo.
echo Pour revenir a ta branche yasmine plus tard:
echo   git checkout yasmine
echo.

pause
