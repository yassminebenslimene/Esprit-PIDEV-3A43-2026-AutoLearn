@echo off
echo Test API Traduction - Verification rapide
echo.

echo Test 1: Liste des langues
curl -X GET http://localhost:8000/api/languages
echo.
echo.

echo Test 2: Traduction chapitre 1 en anglais
curl -X GET "http://localhost:8000/api/chapitres/1/translate?lang=en"
echo.
echo.

pause
