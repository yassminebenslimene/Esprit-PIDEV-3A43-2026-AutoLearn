@echo off
echo ========================================
echo Configuration du fichier .env
echo ========================================
echo.
echo IMPORTANT: Vous devez editer le fichier .env manuellement
echo et remplacer les valeurs suivantes:
echo.
echo 1. BREVO_API_KEY=your_brevo_api_key_here
echo    Remplacez par votre vraie cle API Brevo
echo.
echo 2. MAIL_FROM_EMAIL=your_email@example.com
echo    Remplacez par: autolearn66@gmail.com
echo.
echo 3. MAILER_DSN=smtp://apikey:your_brevo_smtp_key_here@smtp-relay.brevo.com:587
echo    Remplacez your_brevo_smtp_key_here par votre vraie cle SMTP
echo.
echo 4. APP_SECRET=your_app_secret_here
echo    Remplacez par une chaine aleatoire (ex: %RANDOM%%RANDOM%%RANDOM%)
echo.
echo ========================================
echo.
echo Voulez-vous ouvrir le fichier .env maintenant? (O/N)
set /p choice=
if /i "%choice%"=="O" notepad .env
echo.
echo Apres avoir configure .env, executez:
echo php bin/console cache:clear
echo.
pause
