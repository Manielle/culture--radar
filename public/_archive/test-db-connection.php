<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html><head><title>Test connexion DB</title>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    .success { color: green; }
    .error { color: red; }
    pre { background: #f0f0f0; padding: 10px; }
</style></head><body>";

echo "<h1>Test de connexion à la base de données MySQL</h1>";

// Configuration MAMP
$configs = [
    'Port 8889' => [
        'host' => 'localhost',
        'port' => '8889',
        'db' => 'culture_radar',
        'user' => 'root',
        'pass' => 'root'
    ],
    'Port 3306' => [
        'host' => 'localhost', 
        'port' => '3306',
        'db' => 'culture_radar',
        'user' => 'root',
        'pass' => 'root'
    ],
    'Socket' => [
        'host' => 'localhost:8889',
        'port' => '',
        'db' => 'culture_radar',
        'user' => 'root',
        'pass' => 'root'
    ],
    '127.0.0.1:8889' => [
        'host' => '127.0.0.1',
        'port' => '8889',
        'db' => 'culture_radar',
        'user' => 'root',
        'pass' => 'root'
    ]
];

foreach ($configs as $name => $config) {
    echo "<h2>Test: $name</h2>";
    
    try {
        // Construction DSN
        if ($config['port']) {
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['db']};charset=utf8mb4";
        } else {
            $dsn = "mysql:host={$config['host']};dbname={$config['db']};charset=utf8mb4";
        }
        
        echo "<pre>DSN: $dsn</pre>";
        
        $pdo = new PDO($dsn, $config['user'], $config['pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<p class='success'>✅ CONNEXION RÉUSSIE!</p>";
        
        // Test query
        $stmt = $pdo->query("SELECT DATABASE() as db, NOW() as time");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<pre>Base: {$result['db']}\nHeure serveur: {$result['time']}</pre>";
        
        // Show tables
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "<p>Tables trouvées: " . implode(", ", $tables) . "</p>";
        
        echo "<hr>";
        
        // Save working config
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px;'>";
        echo "<h3>👍 Cette configuration fonctionne!</h3>";
        echo "<p>Utilisez ces paramètres dans votre config.php:</p>";
        echo "<pre>";
        echo "define('DB_HOST', '{$config['host']}');\n";
        if ($config['port']) echo "define('DB_PORT', '{$config['port']}');\n";
        echo "define('DB_NAME', '{$config['db']}');\n";
        echo "define('DB_USER', '{$config['user']}');\n";
        echo "define('DB_PASS', '{$config['pass']}');\n";
        echo "</pre>";
        echo "</div>";
        
        break; // Stop after first successful connection
        
    } catch (PDOException $e) {
        echo "<p class='error'>❌ Échec: " . $e->getMessage() . "</p>";
        echo "<hr>";
    }
}

echo "<h2>Info PHP MySQL:</h2>";
echo "<pre>";
if (extension_loaded('pdo_mysql')) {
    echo "✅ Extension PDO MySQL: Activée\n";
} else {
    echo "❌ Extension PDO MySQL: Non activée\n";
}

if (extension_loaded('mysqli')) {
    echo "✅ Extension MySQLi: Activée\n";
} else {
    echo "❌ Extension MySQLi: Non activée\n";
}

echo "\nPHP Version: " . PHP_VERSION . "\n";
echo "OS: " . PHP_OS . "\n";
echo "</pre>";

echo "</body></html>";
?>