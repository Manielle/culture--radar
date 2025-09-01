<?php
// Test Railway Database Connection
echo "Testing Railway Database Connection...\n";
echo "=====================================\n\n";

// Show environment variables (masked passwords)
echo "Environment Variables:\n";
echo "MYSQLHOST: " . getenv('MYSQLHOST') . "\n";
echo "MYSQLPORT: " . getenv('MYSQLPORT') . "\n";
echo "MYSQLDATABASE: " . getenv('MYSQLDATABASE') . "\n";
echo "MYSQLUSER: " . getenv('MYSQLUSER') . "\n";
echo "MYSQLPASSWORD: " . (getenv('MYSQLPASSWORD') ? '***hidden***' : 'NOT SET') . "\n";
echo "MYSQL_PUBLIC_URL: " . (getenv('MYSQL_PUBLIC_URL') ? 'SET' : 'NOT SET') . "\n\n";

// Try to connect using the public URL
$mysql_url = getenv('MYSQL_PUBLIC_URL');
if ($mysql_url) {
    echo "Parsing MYSQL_PUBLIC_URL...\n";
    $url_parts = parse_url($mysql_url);
    
    $host = $url_parts['host'] ?? '';
    $port = $url_parts['port'] ?? '3306';
    $user = $url_parts['user'] ?? '';
    $pass = $url_parts['pass'] ?? '';
    $db = ltrim($url_parts['path'] ?? '', '/');
    
    echo "Parsed connection details:\n";
    echo "Host: $host\n";
    echo "Port: $port\n";
    echo "Database: $db\n";
    echo "User: $user\n\n";
} else {
    // Fallback to individual variables
    $host = getenv('MYSQLHOST') ?: 'centerbeam.proxy.rlwy.net';
    $port = getenv('MYSQLPORT') ?: '48330';
    $user = getenv('MYSQLUSER') ?: 'root';
    $pass = getenv('MYSQLPASSWORD') ?: 'tBixYXRKGkGAZuyxGHFZzaTxQAGXvJJH';
    $db = getenv('MYSQLDATABASE') ?: 'railway';
}

// Test connection
echo "Testing connection to $host:$port...\n";

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
    
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 5
    ]);
    
    echo "✅ CONNECTION SUCCESSFUL!\n\n";
    
    // Test query
    $stmt = $pdo->query("SELECT VERSION() as version, DATABASE() as db");
    $result = $stmt->fetch();
    
    echo "MySQL Version: " . $result['version'] . "\n";
    echo "Current Database: " . $result['db'] . "\n\n";
    
    // List tables
    echo "Tables in database:\n";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo "⚠️  No tables found. Database needs initialization.\n";
        echo "Run setup-database.php to create tables.\n";
    } else {
        foreach ($tables as $table) {
            echo "  - $table\n";
        }
    }
    
} catch (PDOException $e) {
    echo "❌ CONNECTION FAILED!\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    
    echo "Troubleshooting:\n";
    echo "1. Check if the host is reachable\n";
    echo "2. Verify credentials are correct\n";
    echo "3. Ensure database exists\n";
    echo "4. Check firewall/network settings\n";
}
?>