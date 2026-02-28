# Script de test complet pour le système de traduction
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "TEST COMPLET - Système de Traduction" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Test 1: Vérifier que le serveur répond
Write-Host "Test 1: Vérification du serveur..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8000" -TimeoutSec 5 -ErrorAction Stop
    Write-Host "✅ Serveur accessible" -ForegroundColor Green
} catch {
    Write-Host "❌ Serveur non accessible!" -ForegroundColor Red
    Write-Host "   Démarrer le serveur avec: symfony server:start" -ForegroundColor Yellow
    exit 1
}
Write-Host ""

# Test 2: Tester l'API des langues
Write-Host "Test 2: API des langues supportées..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8000/api/languages" -ErrorAction Stop
    $data = $response.Content | ConvertFrom-Json
    
    if ($data.status -eq "success") {
        Write-Host "✅ API des langues fonctionne" -ForegroundColor Green
        Write-Host "   Langues disponibles:" -ForegroundColor Cyan
        $data.languages.PSObject.Properties | ForEach-Object {
            Write-Host "   - $($_.Name): $($_.Value)" -ForegroundColor White
        }
    } else {
        Write-Host "❌ Erreur dans la réponse API" -ForegroundColor Red
    }
} catch {
    Write-Host "❌ Erreur lors de l'appel API: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""

# Test 3: Tester la traduction en anglais
Write-Host "Test 3: Traduction en anglais (chapitre 1)..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8000/api/chapitres/1/translate?lang=en" -ErrorAction Stop
    $data = $response.Content | ConvertFrom-Json
    
    if ($data.status -eq "success") {
        Write-Host "✅ Traduction réussie" -ForegroundColor Green
        Write-Host "   Titre: $($data.titre.Substring(0, [Math]::Min(50, $data.titre.Length)))..." -ForegroundColor Cyan
        Write-Host "   Contenu: $($data.contenu.Substring(0, [Math]::Min(100, $data.contenu.Length)))..." -ForegroundColor Cyan
        Write-Host "   Depuis cache: $($data.cached)" -ForegroundColor Cyan
    } else {
        Write-Host "❌ Erreur: $($data.message)" -ForegroundColor Red
    }
} catch {
    Write-Host "❌ Erreur lors de la traduction: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""

# Test 4: Tester la traduction en espagnol
Write-Host "Test 4: Traduction en espagnol (chapitre 1)..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8000/api/chapitres/1/translate?lang=es" -ErrorAction Stop
    $data = $response.Content | ConvertFrom-Json
    
    if ($data.status -eq "success") {
        Write-Host "✅ Traduction réussie" -ForegroundColor Green
        Write-Host "   Titre: $($data.titre.Substring(0, [Math]::Min(50, $data.titre.Length)))..." -ForegroundColor Cyan
    } else {
        Write-Host "❌ Erreur: $($data.message)" -ForegroundColor Red
    }
} catch {
    Write-Host "❌ Erreur lors de la traduction: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""

# Test 5: Tester avec un chapitre inexistant
Write-Host "Test 5: Gestion d'erreur (chapitre inexistant)..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8000/api/chapitres/99999/translate?lang=en" -ErrorAction Stop
    $data = $response.Content | ConvertFrom-Json
    
    if ($data.status -eq "error") {
        Write-Host "✅ Erreur correctement gérée: $($data.message)" -ForegroundColor Green
    }
} catch {
    if ($_.Exception.Response.StatusCode -eq 404) {
        Write-Host "✅ Erreur 404 correctement retournée" -ForegroundColor Green
    } else {
        Write-Host "⚠️  Erreur inattendue: $($_.Exception.Message)" -ForegroundColor Yellow
    }
}
Write-Host ""

# Test 6: Tester avec une langue invalide
Write-Host "Test 6: Gestion d'erreur (langue invalide)..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8000/api/chapitres/1/translate?lang=xx" -ErrorAction Stop
    $data = $response.Content | ConvertFrom-Json
    
    if ($data.status -eq "error") {
        Write-Host "✅ Erreur correctement gérée: $($data.message)" -ForegroundColor Green
    }
} catch {
    if ($_.Exception.Response.StatusCode -eq 400) {
        Write-Host "✅ Erreur 400 correctement retournée" -ForegroundColor Green
    } else {
        Write-Host "⚠️  Erreur inattendue: $($_.Exception.Message)" -ForegroundColor Yellow
    }
}
Write-Host ""

# Résumé
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "RÉSUMÉ DES TESTS" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "✅ Tests terminés!" -ForegroundColor Green
Write-Host ""
Write-Host "Pour tester dans le navigateur:" -ForegroundColor Yellow
Write-Host "1. Ouvrir: http://localhost:8000/test-traduction.html" -ForegroundColor White
Write-Host "2. Ou ouvrir un chapitre: http://localhost:8000/frontoffice/chapitre/1" -ForegroundColor White
Write-Host ""
