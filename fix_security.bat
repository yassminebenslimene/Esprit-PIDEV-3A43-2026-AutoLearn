@echo off
echo ========================================
echo Security Fix Script for AutoLearn
echo ========================================
echo.

echo Step 1: Removing .env from Git tracking...
git rm --cached .env
if %errorlevel% neq 0 (
    echo Warning: .env might not be tracked or already removed
)
echo.

echo Step 2: Adding updated files...
git add .gitignore
git add .env.example
git add CONTACT_FORM_IMPLEMENTATION.md
git add SECURITY_FIX_INSTRUCTIONS.md
git add fix_security.bat
echo.

echo Step 3: Committing changes...
git commit -m "Security: Remove API keys from repository, add .env.example"
echo.

echo ========================================
echo IMPORTANT NEXT STEPS:
echo ========================================
echo.
echo 1. Go to Brevo dashboard: https://app.brevo.com/settings/keys/api
echo 2. DELETE your old API keys (they are now public!)
echo 3. GENERATE new API keys
echo 4. UPDATE your local .env file with the new keys
echo 5. Then run: git push origin ilef
echo.
echo Your local .env file will NOT be deleted - it stays on your computer
echo Only removed from Git tracking so it won't be pushed to GitHub
echo.
pause
