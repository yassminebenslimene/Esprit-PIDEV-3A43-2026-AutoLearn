@echo off
cd /d "%~dp0"
php bin/console app:test-audit-manual
pause
