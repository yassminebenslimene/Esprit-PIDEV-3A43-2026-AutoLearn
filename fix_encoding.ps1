# Script pour corriger l'encodage UTF-8 du fichier HTML

$inputFile = "SPRINT_BACKLOG_COMPLET_PARTIE3.html"
$outputFile = "SPRINT_BACKLOG_COMPLET_PARTIE3_CORRECTED.html"

Write-Host "Lecture du fichier avec encodage par défaut..."
$content = Get-Content $inputFile -Raw -Encoding Default

Write-Host "Correction des caractères mal encodés..."
$replacements = @{
    'Ã©' = 'é'
    'Ã¨' = 'è'
    'Ã ' = 'à'
    'Ã§' = 'ç'
    'Ã´' = 'ô'
    'Ã®' = 'î'
    'Ã»' = 'û'
    'Ã‰' = 'É'
    'Ã€' = 'À'
    'Ãª' = 'ê'
    'Ã¢' = 'â'
    'Ã¹' = 'ù'
    'Ã¯' = 'ï'
    'Å"' = 'œ'
    'â€™' = "'"
    'â€œ' = '"'
    'â€' = '"'
    'â†'' = '→'
    'ðŸ"‹' = '📋'
    'ðŸ"Œ' = '📌'
    'âœ…' = '✅'
    'ðŸŽ¯' = '🎯'
    'âš¡' = '⚡'
    'ðŸ"Š' = '📊'
}

foreach ($key in $replacements.Keys) {
    $content = $content -replace [regex]::Escape($key), $replacements[$key]
}

Write-Host "Écriture du fichier corrigé avec encodage UTF-8..."
$utf8NoBom = New-Object System.Text.UTF8Encoding $false
[System.IO.File]::WriteAllText($outputFile, $content, $utf8NoBom)

Write-Host "✅ Fichier corrigé créé: $outputFile"
Write-Host ""
Write-Host "Pour remplacer l'original:"
Write-Host "Move-Item -Path $outputFile -Destination $inputFile -Force"
