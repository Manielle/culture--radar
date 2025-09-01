<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><title>Test Erreur</title></head><body>";
echo "<h1>Test des erreurs PHP</h1>";

// Test 1: Includes
echo "<h2>1. Test des fichiers requis:</h2>";
$files = [
    'includes/performance.php',
    'config.php',
    'services/VenueLinksService.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file existe<br>";
        
        // Tester l'inclusion
        ob_start();
        $error = false;
        try {
            include_once $file;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
        $output = ob_get_clean();
        
        if ($error) {
            echo "❌ Erreur: $error<br>";
        } else {
            echo "✅ $file chargé sans erreur<br>";
        }
    } else {
        echo "❌ $file MANQUANT<br>";
    }
}

// Test 2: Session
echo "<h2>2. Test de session:</h2>";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    echo "✅ Session démarrée<br>";
} else {
    echo "⚠️ Session déjà active<br>";
}

// Test 3: Performance.php spécifiquement
echo "<h2>3. Test de performance.php:</h2>";
if (function_exists('enableQueryCache')) {
    echo "✅ Fonction enableQueryCache existe<br>";
    try {
        enableQueryCache();
        echo "✅ enableQueryCache() exécutée<br>";
    } catch (Exception $e) {
        echo "❌ Erreur enableQueryCache: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ Fonction enableQueryCache n'existe pas<br>";
}

echo "<hr>";
echo "<a href='index.php'>Retour à index.php</a>";
echo "</body></html>";
?>