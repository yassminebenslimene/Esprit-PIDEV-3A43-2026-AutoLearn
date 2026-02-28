@echo off
cd /d "%~dp0"

echo Checking Audit Bundle Services...
echo.

echo 1. Check if AuditManager service exists:
php bin/console debug:container audit --show-private 2>nul

echo.
echo 2. Check if audit listeners are registered:
php bin/console debug:event-dispatcher doctrine 2>nul | findstr /i "audit"

echo.
echo 3. List all doctrine listeners:
php bin/console debug:event-dispatcher doctrine.orm 2>nul

echo.
pause
