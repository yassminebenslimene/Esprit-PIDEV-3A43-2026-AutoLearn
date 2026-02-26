@echo off
echo ========================================
echo Testing StofDoctrineExtensionsBundle
echo ========================================
echo.

echo Running test command...
php bin/console app:test-stof-extensions

echo.
echo ========================================
echo Test completed!
echo ========================================
echo.
echo What was tested:
echo - Timestampable: Auto-updates createdAt and updatedAt
echo - The updatedAt field was automatically set to current time
echo.
echo To see it in action:
echo 1. Create a new community in the backoffice
echo 2. Check the database - created_at will be set automatically
echo 3. Edit the community - updated_at will be updated automatically
echo.
pause
