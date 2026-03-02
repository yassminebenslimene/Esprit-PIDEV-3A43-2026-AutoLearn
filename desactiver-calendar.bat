@echo off
echo Desactivation du CalendarBundle...

REM Renommer les fichiers de configuration
if exist "config\packages\calendar.yaml" (
    ren "config\packages\calendar.yaml" "calendar.yaml.disabled"
    echo - config\packages\calendar.yaml desactive
)

if exist "config\routes\calendar.yaml" (
    ren "config\routes\calendar.yaml" "calendar.yaml.disabled"
    echo - config\routes\calendar.yaml desactive
)

echo.
echo CalendarBundle desactive avec succes!
echo Vous pouvez maintenant lancer: symfony serve
echo.
pause
