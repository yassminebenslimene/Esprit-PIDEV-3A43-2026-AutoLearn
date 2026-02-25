# Script PowerShell pour créer toutes les tables d'audit
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "CREATION TABLES AUDIT - Fix Final" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$tables = @(
    "user_audit",
    "etudiant_audit",
    "admin_audit",
    "cours_audit",
    "chapitre_audit",
    "ressource_audit",
    "quiz_audit",
    "exercice_audit",
    "challenge_audit",
    "evenement_audit",
    "communaute_audit",
    "post_audit",
    "commentaire_audit",
    "equipe_audit"
)

Write-Host "Creation de $($tables.Count) tables d'audit..." -ForegroundColor Yellow
Write-Host ""

# Utiliser schema:update pour créer toutes les tables manquantes
Write-Host "Execution de doctrine:schema:update..." -ForegroundColor Yellow
php bin/console doctrine:schema:update --force

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "VERIFICATION" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Vérifier les tables créées
Write-Host "Tables d'audit dans la base de données:" -ForegroundColor Yellow
php bin/console doctrine:query:sql "SHOW TABLES LIKE '%audit%'"

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "TERMINE !" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""

Write-Host "Vous pouvez maintenant:" -ForegroundColor Yellow
Write-Host "1. Demarrer le serveur: symfony server:start" -ForegroundColor White
Write-Host "2. Ouvrir le backoffice: http://localhost:8000/backoffice" -ForegroundColor White
Write-Host ""

Read-Host "Appuyez sur Entree pour continuer"
