<?php
// Diagnostic écran blanc
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Diagnostic Écran Blanc</h1>";

// Test 1: Session
echo "<h2>1. Test Session:</h2>";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    echo "✅ Session OK<br>";
} else {
    echo "⚠️ Session déjà démarrée<br>";
}

// Test 2: Headers
echo "<h2>2. Headers envoyés?</h2>";
if (headers_sent($file, $line)) {
    echo "❌ Headers déjà envoyés dans $file à la ligne $line<br>";
} else {
    echo "✅ Headers pas encore envoyés<br>";
}

// Test 3: Fichiers critiques
echo "<h2>3. Fichiers requis:</h2>";
$files = [
    'config.php',
    'includes/performance.php',
    'services/VenueLinksService.php',
    'includes/auto-refresh-events.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file existe<br>";
        
        // Tester l'inclusion
        ob_start();
        $error = '';
        try {
            include_once $file;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
        $output = ob_get_clean();
        
        if ($error) {
            echo "  ❌ Erreur: $error<br>";
        }
    } else {
        echo "⚠️ $file manquant (optionnel)<br>";
    }
}

// Test 4: Mémoire
echo "<h2>4. Mémoire:</h2>";
echo "Utilisée: " . round(memory_get_usage()/1024/1024, 2) . " MB<br>";
echo "Limite: " . ini_get('memory_limit') . "<br>";

// Test 5: Erreurs PHP
echo "<h2>5. Dernières erreurs PHP:</h2>";
$lastError = error_get_last();
if ($lastError) {
    echo "<pre>" . print_r($lastError, true) . "</pre>";
} else {
    echo "✅ Aucune erreur détectée<br>";
}

// Test 6: Test index.php
echo "<h2>6. Test index.php:</h2>";
ob_start();
$indexError = '';
try {
    // Simuler l'environnement web
    $_SERVER['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '/';
    
    include 'index.php';
    $indexOutput = ob_get_contents();
} catch (Exception $e) {
    $indexError = $e->getMessage();
}
ob_end_clean();

if ($indexError) {
    echo "❌ Erreur index.php: $indexError<br>";
} elseif (strpos($indexOutput, '<!DOCTYPE') !== false) {
    echo "✅ index.php génère du HTML<br>";
    echo "Taille output: " . strlen($indexOutput) . " octets<br>";
} else {
    echo "⚠️ index.php ne génère pas de HTML valide<br>";
}

echo "<hr>";
echo "<a href='index.php'>Tester index.php</a>";
?>