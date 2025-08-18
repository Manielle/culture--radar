<?php
// Script de test de connexion Railway

echo "🔍 Test de connexion à la base de données Railway\n";
echo "================================================\n\n";

// Configuration Railway
$configs = [
    'Railway Production' => [
        'host' => 'centerbeam.proxy.rlwy.net',
        'port' => '48330',
        'name' => 'railway',
        'user' => 'root',
        'pass' => 'tBixYXRKGkGAZuyxGHFZzaTxQAGXvJJH'
    ],
    'Local MAMP' => [
        'host' => '127.0.0.1',
        'port' => '8889',
        'name' => 'culture_radar',
        'user' => 'root',
        'pass' => 'root'
    ]
];

foreach ($configs as $env => $config) {
    echo "Testing: $env\n";
    echo "Host: {$config['host']}:{$config['port']}\n";
    
    try {
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['name']};charset=utf8mb4";
        $pdo = new PDO($dsn, $config['user'], $config['pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Test query
        $stmt = $pdo->query("SELECT VERSION() as version");
        $version = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "✅ Connection successful!\n";
        echo "MySQL Version: {$version['version']}\n";
        
        // Check tables
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "Tables found: " . count($tables) . "\n";
        if (count($tables) > 0) {
            echo "Tables: " . implode(", ", array_slice($tables, 0, 5)) . "...\n";
        }
        
    } catch (PDOException $e) {
        echo "❌ Connection failed!\n";
        echo "Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
}

// Test des variables d'environnement
echo "Variables d'environnement Railway:\n";
echo "RAILWAY_ENVIRONMENT: " . (getenv('RAILWAY_ENVIRONMENT') ?: 'Not set') . "\n";
echo "PORT: " . (getenv('PORT') ?: 'Not set') . "\n";
echo "MYSQLHOST: " . (getenv('MYSQLHOST') ?: 'Not set') . "\n";
echo "MYSQLPORT: " . (getenv('MYSQLPORT') ?: 'Not set') . "\n";
echo "MYSQLDATABASE: " . (getenv('MYSQLDATABASE') ?: 'Not set') . "\n";
?>