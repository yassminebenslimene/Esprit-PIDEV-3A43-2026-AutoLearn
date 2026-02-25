@echo off
echo ========================================
echo Testing Audit Bundle Configuration
echo ========================================
echo.
echo Checking if audit bundle is loaded...
php bin/console debug:container simple_things_entity_audit
echo.
echo Checking audited entities...
php bin/console debug:config simple_things_entity_audit audited_entities
echo.
echo Checking event listeners...
php bin/console debug:event-dispatcher doctrine
echo.
pause
