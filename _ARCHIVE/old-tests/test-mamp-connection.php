<?php
/**
 * MAMP Connection Test
 * Tests different connection methods for MAMP on port 8889
 */

echo "=================================\n";
echo "   MAMP MySQL Connection Test    \n";
echo "=================================\n\n";

// Test configurations for MAMP
$configs = [
    [
        'name' => 'Using 127.0.0.1',
        'host' => '127.0.0.1',
        'port' => '8889',
        'user' => 'root',
        'pass' => 'root'
    ],
    [
        'name' => 'Using localhost',
        'host' => 'localhost',
        'port' => '8889',
        'user' => 'root',
        'pass' => 'root'
    ],
    [
        'name' => 'Using localhost with socket',
        'host' => 'localhost:/Applications/MAMP/tmp/mysql/mysql.sock',
        'port' => '8889',
        'user' => 'root',
        'pass' => 'root'
    ],
    [
        'name' => 'Using 127.0.0.1 with explicit charset',
        'host' => '127.0.0.1',
        'port' => '8889',
        'user' => 'root',
        'pass' => 'root',
        'charset' => 'utf8mb4'
    ]
];

$workingConfig = null;

foreach ($configs as $config) {
    echo "Testing: {$config['name']}\n";
    echo "  Host: {$config['host']}:{$config['port']}\n";
    
    try {
        // Try different DSN formats
        if (strpos($config['host'], '.sock') !== false) {
            // Socket connection
            $dsn = "mysql:unix_socket={$config['host']};charset=utf8mb4";
        } else {
            // TCP connection
            $dsn = "mysql:host={$config['host']};port={$config['port']};charset=utf8mb4";
        }
        
        echo "  DSN: $dsn\n";
        
        $pdo = new PDO($dsn, $config['user'], $config['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5
        ]);
        
        echo "  ✅ Connection successful!\n";
        
        // Test database operations
        $version = $pdo->query('SELECT VERSION()')->fetchColumn();
        echo "  MySQL Version: $version\n";
        
        // Check if culture_radar database exists
        $stmt = $pdo->query("SHOW DATABASES LIKE 'culture_radar'");
        $dbExists = $stmt->fetch();
        
        if (!$dbExists) {
            echo "  Creating database culture_radar...\n";
            $pdo->exec("CREATE DATABASE IF NOT EXISTS culture_radar CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            echo "  ✅ Database created!\n";
        } else {
            echo "  ✅ Database culture_radar exists\n";
        }
        
        // Now test connection to the specific database
        if (strpos($config['host'], '.sock') !== false) {
            $dsn = "mysql:unix_socket={$config['host']};dbname=culture_radar;charset=utf8mb4";
        } else {
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname=culture_radar;charset=utf8mb4";
        }
        
        $pdo2 = new PDO($dsn, $config['user'], $config['pass']);
        echo "  ✅ Connected to culture_radar database\n";
        
        $workingConfig = $config;
        break;
        
    } catch (PDOException $e) {
        echo "  ❌ Failed: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

if ($workingConfig) {
    echo "=================================\n";
    echo "✅ WORKING CONFIGURATION FOUND!\n";
    echo "=================================\n\n";
    
    echo "Update your .env file with:\n\n";
    echo "DB_HOST={$workingConfig['host']}\n";
    echo "DB_PORT={$workingConfig['port']}\n";
    echo "DB_NAME=culture_radar\n";
    echo "DB_USER=root\n";
    echo "DB_PASS=root\n";
    
    // Try to fix tables
    echo "\nChecking/Creating tables...\n";
    
    try {
        if (strpos($workingConfig['host'], '.sock') !== false) {
            $dsn = "mysql:unix_socket={$workingConfig['host']};dbname=culture_radar;charset=utf8mb4";
        } else {
            $dsn = "mysql:host={$workingConfig['host']};port={$workingConfig['port']};dbname=culture_radar;charset=utf8mb4";
        }
        
        $pdo = new PDO($dsn, $workingConfig['user'], $workingConfig['pass']);
        
        // Create users table if not exists
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            is_active BOOLEAN DEFAULT TRUE,
            accepts_newsletter BOOLEAN DEFAULT FALSE,
            onboarding_completed BOOLEAN DEFAULT FALSE,
            last_login DATETIME,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        $pdo->exec($sql);
        echo "✅ Users table ready\n";
        
        // Create user_profiles table if not exists
        $sql = "CREATE TABLE IF NOT EXISTS user_profiles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL UNIQUE,
            location VARCHAR(255),
            preferences JSON,
            budget_max DECIMAL(8, 2) DEFAULT 0,
            notification_enabled BOOLEAN DEFAULT TRUE,
            onboarding_completed BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        $pdo->exec($sql);
        echo "✅ User_profiles table ready\n";
        
    } catch (PDOException $e) {
        echo "❌ Table creation error: " . $e->getMessage() . "\n";
    }
    
} else {
    echo "=================================\n";
    echo "❌ Connection Failed\n";
    echo "=================================\n\n";
    
    echo "Troubleshooting for MAMP:\n\n";
    
    echo "1. Check MAMP is running:\n";
    echo "   - MAMP app should show 'Apache Server' and 'MySQL Server' as green\n\n";
    
    echo "2. Check MAMP ports:\n";
    echo "   - Open MAMP → Preferences → Ports\n";
    echo "   - MySQL Port should be: 8889\n";
    echo "   - If it's different, update your .env file\n\n";
    
    echo "3. Try MAMP's phpMyAdmin:\n";
    echo "   - Go to: http://localhost:8888/phpMyAdmin\n";
    echo "   - If this works, MySQL is running\n\n";
    
    echo "4. Check Windows Firewall:\n";
    echo "   - Windows Firewall might be blocking port 8889\n";
    echo "   - Add exception for MAMP MySQL\n\n";
    
    echo "5. Try restarting MAMP:\n";
    echo "   - Stop Servers → Start Servers\n\n";
}

// Additional diagnostic info
echo "\n=================================\n";
echo "System Information:\n";
echo "=================================\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "OS: " . PHP_OS . "\n";
echo "PDO Drivers: " . implode(', ', PDO::getAvailableDrivers()) . "\n";

if (PHP_OS === 'WINNT') {
    echo "\nWindows detected - Common issues:\n";
    echo "- Windows may block 'localhost', try '127.0.0.1' instead\n";
    echo "- Check Windows Defender Firewall settings\n";
    echo "- Make sure MAMP has permission to use port 8889\n";
}
?>