@echo off
echo Reactivation du CalendarBundle...

REM Renommer les fichiers de configuration
if exist "config\packages\calendar.yaml.disabled" (
    ren "config\packages\calendar.yaml.disabled" "calendar.yaml"
    echo - config\packages\calendar.yaml reactive
)

if exist "config\routes\calendar.yaml.disabled" (
    ren "config\routes\calendar.yaml.disabled" "calendar.yaml"
    echo - config\routes\calendar.yaml reactive
)

echo.
echo CalendarBundle reactive!
echo N'oubliez pas d'installer le bundle: composer require tattali/calendar-bundle
echo.
pause
