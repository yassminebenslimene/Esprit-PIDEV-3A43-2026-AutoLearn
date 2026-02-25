@echo off
echo ========================================
echo Fix Audit Tracking - Clear Cache
echo ========================================
echo.
echo Step 1: Clearing Symfony cache...
php bin/console cache:clear
echo.
echo Step 2: Warming up cache...
php bin/console cache:warmup
echo.
echo Step 3: Checking audit configuration...
php bin/console debug:config simple_things_entity_audit
echo.
echo ========================================
echo Done! Now try creating a course again.
echo ========================================
echo.
echo The audit should now track:
echo - Course creation
echo - Course updates
echo - Course deletions
echo - And all other entities!
echo.
pause
