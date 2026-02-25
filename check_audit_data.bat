@echo off
echo ========================================
echo Checking Audit Data
echo ========================================
echo.
echo Checking revisions table...
php bin/console dbal:run-sql "SELECT COUNT(*) as total FROM revisions"
echo.
echo Checking cours_audit table...
php bin/console dbal:run-sql "SELECT COUNT(*) as total FROM cours_audit"
echo.
echo Last 5 revisions:
php bin/console dbal:run-sql "SELECT * FROM revisions ORDER BY timestamp DESC LIMIT 5"
echo.
echo Last 5 cours audits:
php bin/console dbal:run-sql "SELECT * FROM cours_audit ORDER BY rev DESC LIMIT 5"
echo.
pause
