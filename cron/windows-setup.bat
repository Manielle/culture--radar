@echo off
REM Script d'automatisation pour Windows
REM Configuration du scraping automatique sur Windows

echo ========================================
echo    CULTURE RADAR - Setup Windows
echo ========================================
echo.

REM Detecter le chemin PHP (adapter selon votre installation)
set PHP_PATH=C:\xampp\php\php.exe
if not exist "%PHP_PATH%" (
    set PHP_PATH=C:\wamp64\bin\php\php7.4.33\php.exe
)
if not exist "%PHP_PATH%" (
    set PHP_PATH=C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe
)
if not exist "%PHP_PATH%" (
    echo ERREUR: PHP non trouve! 
    echo Editez ce fichier et mettez le bon chemin vers php.exe
    pause
    exit /b 1
)

echo PHP trouve: %PHP_PATH%
echo.

REM Creer le script batch pour le scraping quotidien
echo Creation du script de scraping...
(
echo @echo off
echo echo [%%date%% %%time%%] Debut du scraping >> logs\scraping.log
echo "%PHP_PATH%" "%~dp0scrape-events.php" >> logs\scraping.log 2^>^&1
echo echo [%%date%% %%time%%] Fin du scraping >> logs\scraping.log
) > daily-scrape.bat

echo Script daily-scrape.bat cree
echo.

REM Creer une tache planifiee Windows
echo Configuration de la tache planifiee...
echo.
echo Pour automatiser le scraping quotidien a 6h00 :
echo.
echo 1. Ouvrez le Planificateur de taches Windows
echo    (Tapez "Planificateur" dans le menu Demarrer)
echo.
echo 2. Cliquez sur "Creer une tache de base..."
echo.
echo 3. Configurez :
echo    - Nom : Culture Radar Scraping
echo    - Declencheur : Tous les jours a 06:00
echo    - Action : Demarrer un programme
echo    - Programme : %~dp0daily-scrape.bat
echo.
echo OU executez cette commande PowerShell en admin :
echo.
echo schtasks /create /tn "Culture Radar Scraping" /tr "%~dp0daily-scrape.bat" /sc daily /st 06:00 /ru SYSTEM
echo.
pause

REM Tester le scraping maintenant
echo.
echo Voulez-vous tester le scraping maintenant? (O/N)
set /p test=
if /i "%test%"=="O" (
    echo.
    echo Test du scraping...
    "%PHP_PATH%" "%~dp0manual-scrape.php" --test --verbose
)

echo.
echo Configuration terminee!
pause