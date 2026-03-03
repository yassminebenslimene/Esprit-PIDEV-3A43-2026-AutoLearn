@echo off
echo ========================================
echo SOLUTION: Nettoyer les secrets Git
echo ========================================
echo.

echo Etape 1: Annuler le dernier commit (sans perdre les modifications)
git reset --soft HEAD~1
echo.

echo Etape 2: Retirer .env.local du suivi Git
git rm --cached .env.local
echo.

echo Etape 3: Ajouter les fichiers corriges
git add .gitignore
git add .env.local.example
git add .
echo.

echo Etape 4: Creer un nouveau commit sans les secrets
git commit -m "fix: Remove API keys from repository and add .env.local.example"
echo.

echo Etape 5: Pousser sur GitHub
git push origin yasmine
echo.

echo ========================================
echo TERMINE!
echo ========================================
echo.
echo Votre ami devra:
echo 1. Faire: git pull origin yasmine
echo 2. Copier: copy .env.local.example .env.local
echo 3. Vous demander les vraies cles API
echo 4. Les mettre dans son .env.local
echo.
pause
