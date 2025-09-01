<?php
// Test direct de sortie
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Forcer l'arrêt de tout buffering
while (ob_get_level()) {
    ob_end_clean();
}

// Test 1: Sortie simple
echo "TEST 1: Sortie directe OK<br>\n";
flush();

// Test 2: Inclusion de config
echo "TEST 2: Config... ";
if (file_exists('config.php')) {
    include_once 'config.php';
    echo "OK<br>\n";
} else {
    echo "MANQUANT<br>\n";
}
flush();

// Test 3: Session
echo "TEST 3: Session... ";
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}
echo "OK<br>\n";
flush();

// Test 4: Sortie HTML basique
echo "TEST 4: HTML...<br>\n";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test</title>
</head>
<body>
    <h1 style="color: red;">Si vous voyez ce texte rouge, le HTML fonctionne</h1>
    
    <?php
    // Test 5: Vérifier ob_get_level
    echo "<p>OB Level: " . ob_get_level() . "</p>";
    
    // Test 6: Vérifier les buffers actifs
    $handlers = ob_list_handlers();
    echo "<p>Handlers actifs: " . implode(', ', $handlers) . "</p>";
    
    // Test 7: Tester l'index
    echo "<h2>Test index.php:</h2>";
    
    // Capturer l'output de index
    ob_start();
    $error = '';
    try {
        include 'index.php';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
    $output = ob_get_contents();
    ob_end_clean();
    
    if ($error) {
        echo "<p style='color: red;'>Erreur: $error</p>";
    } else {
        echo "<p style='color: green;'>Index généré: " . strlen($output) . " octets</p>";
        
        // Afficher les 500 premiers caractères
        echo "<h3>Début du HTML généré:</h3>";
        echo "<pre style='background: #f0f0f0; padding: 10px;'>";
        echo htmlspecialchars(substr($output, 0, 500));
        echo "</pre>";
        
        // Vérifier s'il y a des caractères invisibles
        echo "<h3>Analyse des caractères:</h3>";
        $first_chars = substr($output, 0, 10);
        for ($i = 0; $i < strlen($first_chars); $i++) {
            $char = $first_chars[$i];
            $ord = ord($char);
            echo "Position $i: '" . htmlspecialchars($char) . "' (ASCII: $ord)<br>";
        }
    }
    ?>
    
    <hr>
    <a href="index.php">Tester index.php directement</a>
</body>
</html>