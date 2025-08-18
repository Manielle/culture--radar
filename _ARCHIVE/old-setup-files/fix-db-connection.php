<?php
/**
 * Database Connection Fixer
 * This script will help identify and fix your database connection issues
 */

echo "====================================\n";
echo "  Culture Radar Database Fix Tool  \n";
echo "====================================\n\n";

// Common MySQL configurations
$configurations = [
    'WAMP' => [
        'host' => 'localhost',
        'port' => '3306',
        'user' => 'root',
        'pass' => ''
    ],
    'XAMPP' => [
        'host' => 'localhost',
        'port' => '3306',
        'user' => 'root',
        'pass' => ''
    ],
    'MAMP' => [
        'host' => 'localhost',
        'port' => '8889',
        'user' => 'root',
        'pass' => 'root'
    ],
    'MAMP_ALT' => [
        'host' => 'localhost',
        'port' => '3306',
        'user' => 'root',
        'pass' => 'root'
    ],
    'Local' => [
        'host' => '127.0.0.1',
        'port' => '3306',
        'user' => 'root',
        'pass' => ''
    ]
];

echo "Testing database connections...\n\n";

$workingConfig = null;

foreach ($configurations as $name => $config) {
    echo "Testing $name configuration:\n";
    echo "  Host: {$config['host']}:{$config['port']}\n";
    echo "  User: {$config['user']}\n";
    echo "  Pass: " . (empty($config['pass']) ? '(empty)' : '****') . "\n";
    
    try {
        $dsn = "mysql:host={$config['host']};port={$config['port']};charset=utf8mb4";
        $pdo = new PDO($dsn, $config['user'], $config['pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "  ✅ Connection successful!\n";
        
        // Check if database exists
        $stmt = $pdo->query("SHOW DATABASES LIKE 'culture_radar'");
        $dbExists = $stmt->fetch();
        
        if (!$dbExists) {
            echo "  ⚠️  Database 'culture_radar' not found. Creating...\n";
            $pdo->exec("CREATE DATABASE IF NOT EXISTS culture_radar CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            echo "  ✅ Database created!\n";
        } else {
            echo "  ✅ Database 'culture_radar' exists\n";
        }
        
        // Test connection to the database
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname=culture_radar;charset=utf8mb4";
        $pdo = new PDO($dsn, $config['user'], $config['pass']);
        
        // Check tables
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "  ✅ Found " . count($tables) . " tables\n";
        
        $workingConfig = $config;
        $workingConfig['name'] = $name;
        break;
        
    } catch (PDOException $e) {
        echo "  ❌ Connection failed: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

if ($workingConfig) {
    echo "====================================\n";
    echo "✅ WORKING CONFIGURATION FOUND!\n";
    echo "====================================\n\n";
    
    echo "Update your .env file with these settings:\n\n";
    echo "DB_HOST={$workingConfig['host']}\n";
    echo "DB_PORT={$workingConfig['port']}\n";
    echo "DB_NAME=culture_radar\n";
    echo "DB_USER={$workingConfig['user']}\n";
    echo "DB_PASS={$workingConfig['pass']}\n";
    
    // Try to update .env automatically
    $envFile = __DIR__ . '/.env';
    if (file_exists($envFile)) {
        echo "\nUpdating .env file automatically...\n";
        
        $envContent = file_get_contents($envFile);
        $envContent = preg_replace('/DB_HOST=.*/', "DB_HOST={$workingConfig['host']}", $envContent);
        $envContent = preg_replace('/DB_PORT=.*/', "DB_PORT={$workingConfig['port']}", $envContent);
        $envContent = preg_replace('/DB_USER=.*/', "DB_USER={$workingConfig['user']}", $envContent);
        $envContent = preg_replace('/DB_PASS=.*/', "DB_PASS={$workingConfig['pass']}", $envContent);
        
        file_put_contents($envFile, $envContent);
        echo "✅ .env file updated!\n";
    }
    
    // Create/check tables
    echo "\nChecking database structure...\n";
    
    try {
        $dsn = "mysql:host={$workingConfig['host']};port={$workingConfig['port']};dbname=culture_radar;charset=utf8mb4";
        $pdo = new PDO($dsn, $workingConfig['user'], $workingConfig['pass']);
        
        // Check if user_profiles table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'user_profiles'");
        if (!$stmt->fetch()) {
            echo "Creating user_profiles table...\n";
            
            $sql = "CREATE TABLE IF NOT EXISTS user_profiles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL UNIQUE,
                location VARCHAR(255),
                latitude DECIMAL(10, 8),
                longitude DECIMAL(11, 8),
                preferences JSON,
                budget_max DECIMAL(8, 2) DEFAULT 0,
                transport_mode ENUM('walking', 'transit', 'driving', 'cycling', 'all') DEFAULT 'all',
                max_distance INT DEFAULT 10,
                accessibility_required BOOLEAN DEFAULT FALSE,
                notification_enabled BOOLEAN DEFAULT TRUE,
                onboarding_completed BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_location (location),
                INDEX idx_user_location (user_id, latitude, longitude)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            $pdo->exec($sql);
            echo "✅ Table created!\n";
        }
        
        // Check if users table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
        if (!$stmt->fetch()) {
            echo "Creating users table...\n";
            
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
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_email (email),
                INDEX idx_active_users (is_active, created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            $pdo->exec($sql);
            echo "✅ Table created!\n";
        }
        
        echo "\n✅ Database is ready!\n";
        echo "\nYou can now try the onboarding again.\n";
        
    } catch (PDOException $e) {
        echo "❌ Error setting up database: " . $e->getMessage() . "\n";
    }
    
} else {
    echo "====================================\n";
    echo "❌ NO WORKING CONFIGURATION FOUND\n";
    echo "====================================\n\n";
    
    echo "Please check:\n";
    echo "1. Is your MySQL/WAMP/XAMPP/MAMP server running?\n";
    echo "2. Check the correct port in your server control panel\n";
    echo "3. Try these common solutions:\n\n";
    
    echo "For WAMP (Windows):\n";
    echo "  - Click WAMP icon → MySQL → Service → Start/Resume\n";
    echo "  - Default port is usually 3306\n";
    echo "  - Password is usually empty\n\n";
    
    echo "For XAMPP:\n";
    echo "  - Open XAMPP Control Panel\n";
    echo "  - Click 'Start' next to MySQL\n";
    echo "  - Default port is 3306\n\n";
    
    echo "For MAMP:\n";
    echo "  - Open MAMP\n";
    echo "  - Click 'Start Servers'\n";
    echo "  - Check Preferences → Ports (usually 8889 or 3306)\n\n";
    
    echo "Once MySQL is running, run this script again.\n";
}

echo "\n";
?>