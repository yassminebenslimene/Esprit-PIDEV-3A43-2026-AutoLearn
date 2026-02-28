@echo off
cd /d "%~dp0"

echo Checking which audit tables exist...
echo.

php bin/console doctrine:query:sql "SHOW TABLES LIKE '%%audit'"

echo.
echo Checking user_audit structure if it exists...
php bin/console doctrine:query:sql "DESCRIBE user_audit" 2>nul

echo.
echo Checking etudiant_audit structure if it exists...
php bin/console doctrine:query:sql "DESCRIBE etudiant_audit" 2>nul

echo.
echo Checking data in user_audit...
php bin/console doctrine:query:sql "SELECT COUNT(*) as count FROM user_audit" 2>nul

echo.
echo Checking data in etudiant_audit...
php bin/console doctrine:query:sql "SELECT COUNT(*) as count FROM etudiant_audit" 2>nul

echo.
pause
