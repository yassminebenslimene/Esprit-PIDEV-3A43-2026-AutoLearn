@echo off
echo ========================================
echo Creating Audit Tables for All Entities
echo ========================================
echo.
echo This will create audit tables for:
echo - Courses (cours_audit)
echo - Challenges (challenge_audit)
echo - Events (evenement_audit)
echo - Communities (communaute_audit)
echo - And more...
echo.
pause

php bin/console doctrine:schema:update --force

echo.
echo ========================================
echo Done! Audit tables created.
echo ========================================
echo.
echo Now the Audit Bundle will automatically track:
echo - Course creation/updates/deletions
echo - Challenge creation/updates/deletions
echo - Event creation/updates/deletions
echo - Community creation/updates/deletions
echo - And all other admin actions!
echo.
pause
