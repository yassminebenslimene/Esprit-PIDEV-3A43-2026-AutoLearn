@echo off
echo ============================================
echo MYSQL FIX - Simple Version
echo ============================================
echo.
echo This will:
echo 1. Stop MySQL
echo 2. Backup your current my.ini
echo 3. Apply optimized configuration
echo 4. Start MySQL
echo.
echo Press CTRL+C to cancel, or
pause
echo.

echo Stopping MySQL...
net stop mysql
echo.

echo Creating backup...
copy "C:\xampp2\mysql\bin\my.ini" "C:\xampp2\mysql\bin\my.ini.backup"
echo Backup created!
echo.

echo Applying optimized configuration...
copy /Y "my.ini.optimized" "C:\xampp2\mysql\bin\my.ini"
echo Configuration applied!
echo.

echo Starting MySQL...
net start mysql
echo.

echo ============================================
echo DONE! Now run VERIFY_MYSQL_CONFIG.bat
echo ============================================
pause
