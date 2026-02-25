@echo off
echo ========================================
echo RESOLUTION DES CONFLITS - Branche Ilef
echo ========================================
echo.

echo Les conflits suivants ont ete resolus automatiquement:
echo - config/bundles.php (garde les deux bundles)
echo - config/services.yaml (garde les deux configurations)
echo.

echo Conflits restants a resoudre manuellement:
echo - .env.example
echo - composer.lock
echo - symfony.lock
echo - config/packages/vich_uploader.yaml
echo.

echo Fichiers de documentation d'Ilef a garder:
echo - COMMENT_IA_DETECTE_ACTIONS.md
echo - IA_ACCES_COMPLET_BD.md
echo - LIRE_EN_PREMIER.md
echo - SIDEBAR_FIX_COMPLETE.md
echo - TESTEZ_MAINTENANT.md
echo - config/packages/simple_things_entity_audit.yaml
echo.

echo Pour garder ces fichiers:
git add COMMENT_IA_DETECTE_ACTIONS.md
git add IA_ACCES_COMPLET_BD.md
git add LIRE_EN_PREMIER.md
git add SIDEBAR_FIX_COMPLETE.md
git add TESTEZ_MAINTENANT.md
git add config/packages/simple_things_entity_audit.yaml
echo.

echo Fichiers resolus:
git add config/bundles.php
git add config/services.yaml
echo.

echo ========================================
echo PROCHAINES ETAPES:
echo ========================================
echo 1. Resoudre manuellement les conflits dans:
echo    - .env.example
echo    - composer.lock
echo    - symfony.lock
echo    - config/packages/vich_uploader.yaml
echo.
echo 2. Puis executer:
echo    git add .
echo    git commit -m "merge: Resolution conflits branche ilef"
echo.

pause
