# MySQL Optimization Script
# Run as Administrator

Write-Host "============================================" -ForegroundColor Cyan
Write-Host "MySQL OPTIMIZATION SETUP" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""

# Check if running as administrator
$isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
if (-not $isAdmin) {
    Write-Host "ERROR: This script must be run as Administrator!" -ForegroundColor Red
    Write-Host "Right-click PowerShell and select 'Run as administrator'" -ForegroundColor Yellow
    pause
    exit 1
}

Write-Host "[1/4] Stopping MySQL service..." -ForegroundColor Yellow
try {
    Stop-Service -Name "mysql" -ErrorAction Stop
    Write-Host "SUCCESS: MySQL stopped" -ForegroundColor Green
} catch {
    Write-Host "WARNING: Could not stop MySQL service. It may not be running." -ForegroundColor Yellow
    Write-Host "Continuing anyway..." -ForegroundColor Yellow
}

Write-Host ""
Write-Host "[2/4] Creating backup of current my.ini..." -ForegroundColor Yellow
$timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$backupPath = "C:\xampp2\mysql\bin\my.ini.backup_$timestamp"
try {
    Copy-Item "C:\xampp2\mysql\bin\my.ini" $backupPath -ErrorAction Stop
    Write-Host "SUCCESS: Backup created at $backupPath" -ForegroundColor Green
} catch {
    Write-Host "ERROR: Could not create backup: $_" -ForegroundColor Red
    pause
    exit 1
}

Write-Host ""
Write-Host "[3/4] Applying optimized configuration..." -ForegroundColor Yellow
try {
    $scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
    Copy-Item "$scriptDir\my.ini.optimized" "C:\xampp2\mysql\bin\my.ini" -Force -ErrorAction Stop
    Write-Host "SUCCESS: Configuration applied!" -ForegroundColor Green
} catch {
    Write-Host "ERROR: Could not copy optimized configuration: $_" -ForegroundColor Red
    Write-Host "Restoring backup..." -ForegroundColor Yellow
    Copy-Item $backupPath "C:\xampp2\mysql\bin\my.ini" -Force
    pause
    exit 1
}

Write-Host ""
Write-Host "[4/4] Starting MySQL service..." -ForegroundColor Yellow
try {
    Start-Service -Name "mysql" -ErrorAction Stop
    Write-Host "SUCCESS: MySQL started!" -ForegroundColor Green
} catch {
    Write-Host "ERROR: Failed to start MySQL service!" -ForegroundColor Red
    Write-Host "Check error log at: C:\xampp2\mysql\data\mysql_error.log" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "To restore backup, run:" -ForegroundColor Yellow
    Write-Host "Copy-Item '$backupPath' 'C:\xampp2\mysql\bin\my.ini' -Force" -ForegroundColor Cyan
    pause
    exit 1
}

Write-Host ""
Write-Host "============================================" -ForegroundColor Green
Write-Host "SUCCESS! MySQL optimization complete!" -ForegroundColor Green
Write-Host "============================================" -ForegroundColor Green
Write-Host ""
Write-Host "Changes applied:" -ForegroundColor Cyan
Write-Host "- InnoDB buffer pool: 16M -> 512M (32x improvement)" -ForegroundColor White
Write-Host "- InnoDB log file: 5M -> 128M" -ForegroundColor White
Write-Host "- SQL mode: Added STRICT_TRANS_TABLES, ERROR_FOR_DIVISION_BY_ZERO" -ForegroundColor White
Write-Host "- Collation: utf8mb4_general_ci -> utf8mb4_unicode_ci" -ForegroundColor White
Write-Host "- Timezone: Set to +00:00 (UTC)" -ForegroundColor White
Write-Host "- Flush log: 1 -> 2 (development mode - 10x faster)" -ForegroundColor White
Write-Host ""
Write-Host "NEXT STEPS:" -ForegroundColor Yellow
Write-Host "1. Run: .\VERIFY_MYSQL_CONFIG.bat" -ForegroundColor White
Write-Host "2. All checks should show [OK]" -ForegroundColor White
Write-Host ""
Write-Host "Backup location: $backupPath" -ForegroundColor Cyan
Write-Host ""
pause
