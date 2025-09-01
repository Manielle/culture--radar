<?php
// Diagnostic complet de l'écran blanc
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><title>Diagnostic</title></head><body>";
echo "<h1>Diagnostic Culture Radar</h1>";

// Test 1: PHP fonctionne
echo "<h2>1. PHP fonctionne: ✅</h2>";

// Test 2: Session
echo "<h2>2. Test Session:</h2>";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    echo "✅ Session démarrée<br>";
} else {
    echo "⚠️ Session déjà active<br>";
}

// Test 3: Fichiers requis
echo "<h2>3. Fichiers requis:</h2>";
$files = [
    'config.php',
    'includes/performance.php',
    'services/VenueLinksService.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file existe<br>";
    } else {
        echo "❌ $file MANQUANT<br>";
    }
}

// Test 4: Inclusion de config.php
echo "<h2>4. Test config.php:</h2>";
try {
    require_once 'config.php';
    echo "✅ config.php chargé<br>";
} catch (Exception $e) {
    echo "❌ Erreur config.php: " . $e->getMessage() . "<br>";
}

// Test 5: Test performance.php
echo "<h2>5. Test performance.php:</h2>";
try {
    if (file_exists('includes/performance.php')) {
        ob_start();
        include_once 'includes/performance.php';
        ob_end_clean();
        echo "✅ performance.php chargé<br>";
    } else {
        echo "❌ performance.php introuvable<br>";
    }
} catch (Exception $e) {
    echo "❌ Erreur performance.php: " . $e->getMessage() . "<br>";
}

// Test 6: Test VenueLinksService
echo "<h2>6. Test VenueLinksService:</h2>";
try {
    if (file_exists('services/VenueLinksService.php')) {
        require_once 'services/VenueLinksService.php';
        echo "✅ VenueLinksService chargé<br>";
        if (class_exists('VenueLinksService')) {
            echo "✅ Classe VenueLinksService existe<br>";
        }
    } else {
        echo "❌ VenueLinksService.php introuvable<br>";
    }
} catch (Exception $e) {
    echo "❌ Erreur VenueLinksService: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h2>Actions:</h2>";
echo "<ul>";
echo "<li><a href='index-simple.php'>Tester index-simple.php (version simplifiée)</a></li>";
echo "<li><a href='index.php'>Tester index.php</a></li>";
echo "<li><a href='test-erreur.php'>Test erreurs détaillé</a></li>";
echo "</ul>";

echo "</body></html>";
?>