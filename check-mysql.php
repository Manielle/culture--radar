<?php
/**
 * Test rapide MySQL pour Railway
 */
header('Content-Type: text/plain');

echo "=== TEST MYSQL RAILWAY ===\n\n";

// Variables critiques manquantes
$critical = ['MYSQL_URL', 'MYSQLDATABASE'];
$missing = [];

foreach ($critical as $var) {
    if (!getenv($var)) {
        $missing[] = $var;
    }
}

if (!empty($missing)) {
    echo "❌ VARIABLES CRITIQUES MANQUANTES:\n";
    foreach ($missing as $var) {
        echo "   - $var\n";
    }
    echo "\nSOLUTION:\n";
    echo "1. Supprimez toutes les variables MySQL manuelles\n";
    echo "2. Cliquez sur 'Add Variable Reference'\n";
    echo "3. Sélectionnez votre service MySQL\n";
    echo "4. Railway ajoutera automatiquement TOUTES les variables\n";
} else {
    echo "✅ Variables MySQL détectées!\n\n";
    
    // Tester la connexion
    try {
        $pdo = new PDO(getenv('MYSQL_URL'));
        echo "✅ CONNEXION RÉUSSIE!\n";
        
        // Créer une table de test
        $pdo->exec("CREATE TABLE IF NOT EXISTS test_connection (id INT, test VARCHAR(50))");
        echo "✅ Table de test créée\n";
        
        // Insérer une donnée
        $pdo->exec("INSERT INTO test_connection VALUES (1, 'Railway OK!')");
        
        // Lire la donnée
        $result = $pdo->query("SELECT * FROM test_connection")->fetch();
        echo "✅ Test lecture/écriture: " . $result['test'] . "\n";
        
    } catch (Exception $e) {
        echo "❌ Erreur de connexion: " . $e->getMessage() . "\n";
    }
}

echo "\n=== VARIABLES ACTUELLES ===\n";
$vars = ['MYSQLHOST', 'MYSQLPORT', 'MYSQLUSER', 'MYSQLDATABASE', 'MYSQL_URL'];
foreach ($vars as $var) {
    $val = getenv($var);
    if ($var == 'MYSQL_URL' && $val) {
        // Masquer le mot de passe dans l'URL
        $parsed = parse_url($val);
        echo "$var: mysql://***:***@{$parsed['host']}:{$parsed['port']}{$parsed['path']}\n";
    } else {
        echo "$var: " . ($val ?: '[MANQUANT]') . "\n";
    }
}
?>