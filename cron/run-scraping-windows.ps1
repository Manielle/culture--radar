# Script PowerShell pour lancer le scraping sur Windows
# Culture Radar - Scraping Manuel

# Configuration - ADAPTEZ ces chemins selon votre installation
$phpPath = "C:\xampp\php\php.exe"  # Changez selon votre installation
$scriptPath = "C:\xampp\htdocs\culture-radar\cron\manual-scrape.php"

# Vérifier les chemins alternatifs courants
if (-not (Test-Path $phpPath)) {
    # Essayer WAMP
    $phpPath = "C:\wamp64\bin\php\php7.4.33\php.exe"
}
if (-not (Test-Path $phpPath)) {
    # Essayer Laragon
    $phpPath = "C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe"
}
if (-not (Test-Path $phpPath)) {
    # Essayer MAMP
    $phpPath = "C:\MAMP\bin\php\php7.4.1\php.exe"
}

# Vérifier que PHP existe
if (-not (Test-Path $phpPath)) {
    Write-Host "ERREUR: PHP non trouvé!" -ForegroundColor Red
    Write-Host "Editez ce script et mettez le bon chemin vers php.exe" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Chemins courants:" -ForegroundColor Cyan
    Write-Host "  XAMPP: C:\xampp\php\php.exe"
    Write-Host "  WAMP: C:\wamp64\bin\php\php7.4.33\php.exe"
    Write-Host "  Laragon: C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe"
    Read-Host "Appuyez sur Entrée pour fermer"
    exit
}

# Menu
Clear-Host
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "   CULTURE RADAR - SCRAPING WINDOWS" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "PHP trouvé: $phpPath" -ForegroundColor Green
Write-Host ""
Write-Host "Que voulez-vous faire?" -ForegroundColor Yellow
Write-Host "1. Tester le scraping (mode test)" -ForegroundColor White
Write-Host "2. Lancer le scraping réel pour toutes les villes" -ForegroundColor White
Write-Host "3. Scraper une ville spécifique" -ForegroundColor White
Write-Host "4. Voir l'aide" -ForegroundColor White
Write-Host "5. Configurer l'automatisation Windows" -ForegroundColor White
Write-Host "Q. Quitter" -ForegroundColor White
Write-Host ""

$choice = Read-Host "Votre choix"

switch ($choice) {
    "1" {
        Write-Host ""
        Write-Host "Test du scraping..." -ForegroundColor Green
        & $phpPath $scriptPath --test --verbose
    }
    "2" {
        Write-Host ""
        Write-Host "Lancement du scraping réel..." -ForegroundColor Green
        Write-Host "Cela peut prendre plusieurs minutes..." -ForegroundColor Yellow
        & $phpPath $scriptPath
    }
    "3" {
        Write-Host ""
        $city = Read-Host "Quelle ville? (Paris, Lyon, Marseille, etc.)"
        Write-Host "Scraping de $city..." -ForegroundColor Green
        & $phpPath $scriptPath --city=$city --verbose
    }
    "4" {
        & $phpPath $scriptPath --help
    }
    "5" {
        Write-Host ""
        Write-Host "AUTOMATISATION SUR WINDOWS" -ForegroundColor Cyan
        Write-Host "===========================" -ForegroundColor Cyan
        Write-Host ""
        Write-Host "Pour automatiser le scraping quotidien à 6h00:" -ForegroundColor Yellow
        Write-Host ""
        Write-Host "1. Ouvrez PowerShell en ADMINISTRATEUR" -ForegroundColor White
        Write-Host "2. Copiez et exécutez cette commande:" -ForegroundColor White
        Write-Host ""
        
        $taskCommand = @"
schtasks /create /tn "Culture Radar Scraping" /tr "powershell.exe -ExecutionPolicy Bypass -File $PSCommandPath" /sc daily /st 06:00 /ru SYSTEM /f
"@
        Write-Host $taskCommand -ForegroundColor Green
        Write-Host ""
        Write-Host "3. Pour vérifier que c'est configuré:" -ForegroundColor White
        Write-Host "   schtasks /query /tn 'Culture Radar Scraping'" -ForegroundColor Gray
        Write-Host ""
        
        $setup = Read-Host "Voulez-vous configurer maintenant? (O/N)"
        if ($setup -eq "O") {
            Start-Process powershell -Verb RunAs -ArgumentList "-Command", $taskCommand
            Write-Host "Configuration lancée!" -ForegroundColor Green
        }
    }
    "Q" {
        exit
    }
    default {
        Write-Host "Choix invalide" -ForegroundColor Red
    }
}

Write-Host ""
Read-Host "Appuyez sur Entrée pour fermer"