@echo off
echo ========================================
echo Test du Systeme de Suspension
echo ========================================
echo.

echo 1. Verification des routes...
php bin/console debug:router | findstr suspend
php bin/console debug:router | findstr reactivate
echo.

echo 2. Verification de la base de donnees...
php bin/console dbal:run-sql "SELECT is_suspended, suspended_at, suspension_reason, suspended_by FROM user LIMIT 1"
echo.

echo 3. Verification du cache...
php bin/console cache:clear
echo.

echo ========================================
echo Tests termines!
echo ========================================
pause
