@echo off
cd /d "%~dp0"

echo Testing Audit Tracking...
echo.

echo Step 1: Check if tables exist
php bin/console doctrine:query:sql "SHOW TABLES LIKE '%%_audit'"

echo.
echo Step 2: Count revisions
php bin/console doctrine:query:sql "SELECT COUNT(*) as total_revisions FROM revisions"

echo.
echo Step 3: Check etudiant_audit data
php bin/console doctrine:query:sql "SELECT COUNT(*) as total_student_audits FROM etudiant_audit"

echo.
echo Step 4: Check recent revisions with usernames
php bin/console doctrine:query:sql "SELECT r.id, r.timestamp, r.username, (SELECT COUNT(*) FROM etudiant_audit ea WHERE ea.rev = r.id) as student_changes FROM revisions r ORDER BY r.timestamp DESC LIMIT 10"

echo.
echo Step 5: Check admin users
php bin/console doctrine:query:sql "SELECT userId, email, role FROM user WHERE role = 'ADMIN'"

echo.
pause
