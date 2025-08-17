@echo off
echo ========================================
echo    CULTURE RADAR - SCRAPING MANUEL
echo ========================================
echo.
echo ATTENTION: PHP doit etre installe !
echo.

REM Chercher PHP automatiquement
set PHP_FOUND=0

REM Essayer XAMPP
if exist "C:\xampp\php\php.exe" (
    set PHP_PATH=C:\xampp\php\php.exe
    set PHP_FOUND=1
    echo PHP trouve dans XAMPP
    goto :found
)

REM Essayer WAMP
if exist "C:\wamp64\bin\php\php8.2.0\php.exe" (
    set PHP_PATH=C:\wamp64\bin\php\php8.2.0\php.exe
    set PHP_FOUND=1
    echo PHP trouve dans WAMP
    goto :found
)

if exist "C:\wamp64\bin\php\php8.1.0\php.exe" (
    set PHP_PATH=C:\wamp64\bin\php\php8.1.0\php.exe
    set PHP_FOUND=1
    echo PHP trouve dans WAMP
    goto :found
)

REM Essayer Laragon
if exist "C:\laragon\bin\php\php-8.2.0-Win32-vs16-x64\php.exe" (
    set PHP_PATH=C:\laragon\bin\php\php-8.2.0-Win32-vs16-x64\php.exe
    set PHP_FOUND=1
    echo PHP trouve dans Laragon
    goto :found
)

:notfound
echo.
echo ====== PHP NON TROUVE ! ======
echo.
echo Vous devez installer un serveur PHP comme :
echo   - XAMPP : https://www.apachefriends.org/
echo   - WAMP : https://www.wampserver.com/
echo   - Laragon : https://laragon.org/
echo.
echo Apres installation, relancez ce script.
echo.
pause
exit

:found
echo PHP trouve : %PHP_PATH%
echo.
echo Que voulez-vous faire ?
echo 1. Tester le scraping (sans sauvegarder)
echo 2. Lancer le vrai scraping
echo 3. Scraper Paris seulement
echo 4. Quitter
echo.
set /p choice="Votre choix (1-4): "

if "%choice%"=="1" (
    echo.
    echo Test du scraping...
    "%PHP_PATH%" "%~dp0cron\manual-scrape.php" --test --verbose
    pause
)

if "%choice%"=="2" (
    echo.
    echo Lancement du scraping reel...
    echo Cela peut prendre quelques minutes...
    "%PHP_PATH%" "%~dp0cron\manual-scrape.php"
    pause
)

if "%choice%"=="3" (
    echo.
    echo Scraping de Paris...
    "%PHP_PATH%" "%~dp0cron\manual-scrape.php" --city=Paris --verbose
    pause
)

if "%choice%"=="4" (
    exit
)