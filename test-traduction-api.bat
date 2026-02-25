@echo off
echo ========================================
echo TEST API TRADUCTION - AutoLearn
echo ========================================
echo.

echo 1. Test de la liste des langues supportees...
echo.
powershell -Command "Invoke-WebRequest -Uri 'http://localhost:8000/api/languages' | Select-Object -ExpandProperty Content | ConvertFrom-Json | ConvertTo-Json -Depth 10"
echo.
echo.

echo 2. Test de traduction en anglais (chapitre 1)...
echo.
powershell -Command "Invoke-WebRequest -Uri 'http://localhost:8000/api/chapitres/1/translate?lang=en' | Select-Object -ExpandProperty Content | ConvertFrom-Json | ConvertTo-Json -Depth 10"
echo.
echo.

echo 3. Test de traduction en espagnol (chapitre 1)...
echo.
powershell -Command "Invoke-WebRequest -Uri 'http://localhost:8000/api/chapitres/1/translate?lang=es' | Select-Object -ExpandProperty Content | ConvertFrom-Json | ConvertTo-Json -Depth 10"
echo.
echo.

echo ========================================
echo Tests termines !
echo ========================================
pause
