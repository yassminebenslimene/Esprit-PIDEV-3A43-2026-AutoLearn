@echo off
cd /d "%~dp0"
echo Checking revisions table...
php bin/console doctrine:query:sql "SELECT COUNT(*) as count FROM revisions"

echo.
echo Checking etudiant_audit table...
php bin/console doctrine:query:sql "SELECT COUNT(*) as count FROM etudiant_audit"

echo.
echo Checking sample revisions...
php bin/console doctrine:query:sql "SELECT id, timestamp, username FROM revisions ORDER BY timestamp DESC LIMIT 5"

echo.
echo Checking sample etudiant_audit...
php bin/console doctrine:query:sql "SELECT rev, revtype, userId, nom, prenom FROM etudiant_audit LIMIT 5"

echo.
echo Checking user roles...
php bin/console doctrine:query:sql "SELECT email, role FROM user WHERE role = 'ADMIN' LIMIT 5"

pause
