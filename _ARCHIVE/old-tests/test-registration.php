<?php
/**
 * Test Registration Script
 * This script tests the registration functionality directly
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Culture Radar Registration Test ===\n\n";

// Test 1: Check if required files exist
echo "1. Checking required files...\n";
$requiredFiles = [
    'config.php',
    'includes/db.php',
    'classes/Auth.php'
];

foreach ($requiredFiles as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "   ✓ $file exists\n";
    } else {
        echo "   ✗ $file missing\n";
        exit(1);
    }
}

// Test 2: Try to load configuration
echo "\n2. Loading configuration...\n";
try {
    require_once __DIR__ . '/config.php';
    echo "   ✓ Configuration loaded\n";
    
    // Show database config (without password)
    $dbConfig = Config::database();
    echo "   Database config:\n";
    echo "   - Host: " . $dbConfig['host'] . "\n";
    echo "   - Database: " . $dbConfig['name'] . "\n";
    echo "   - User: " . $dbConfig['user'] . "\n";
    echo "   - Port: " . ($dbConfig['port'] ?? '3306') . "\n";
} catch (Exception $e) {
    echo "   ✗ Error loading config: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 3: Test database connection
echo "\n3. Testing database connection...\n";
try {
    require_once __DIR__ . '/includes/db.php';
    $db = Database::getInstance();
    $connection = $db->getConnection();
    echo "   ✓ Database connection successful\n";
    
    // Check if tables exist
    $stmt = $connection->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "   Found " . count($tables) . " tables\n";
    
    // Check specifically for users table
    if (in_array('users', $tables)) {
        echo "   ✓ 'users' table exists\n";
    } else {
        echo "   ✗ 'users' table not found - creating tables...\n";
        // Tables should be created automatically by Database class
    }
    
    if (in_array('user_profiles', $tables)) {
        echo "   ✓ 'user_profiles' table exists\n";
    } else {
        echo "   ✗ 'user_profiles' table not found\n";
    }
    
} catch (Exception $e) {
    echo "   ✗ Database error: " . $e->getMessage() . "\n";
    echo "\nPossible issues:\n";
    echo "1. MySQL/MariaDB is not running\n";
    echo "2. Database credentials are incorrect\n";
    echo "3. Database 'culture_radar' doesn't exist\n";
    echo "\nTrying to create database and tables...\n";
    
    // Try to create database
    try {
        $dbConfig = Config::database();
        $host = $dbConfig['host'];
        $port = $dbConfig['port'] ?? '3306';
        
        if (strpos($host, ':') !== false) {
            list($host, $port) = explode(':', $host);
        }
        
        // Connect without database
        $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
        $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create database
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbConfig['name']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "   ✓ Database created successfully\n";
        
        // Now reconnect with database
        require_once __DIR__ . '/includes/db.php';
        $db = Database::getInstance();
        echo "   ✓ Connected to new database\n";
        
    } catch (PDOException $e) {
        echo "   ✗ Failed to create database: " . $e->getMessage() . "\n";
        exit(1);
    }
}

// Test 4: Test Auth class
echo "\n4. Testing Auth class...\n";
try {
    require_once __DIR__ . '/classes/Auth.php';
    $auth = new Auth();
    echo "   ✓ Auth class loaded\n";
} catch (Exception $e) {
    echo "   ✗ Error loading Auth: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 5: Attempt test registration
echo "\n5. Testing registration function...\n";
$testUser = [
    'name' => 'Test User',
    'email' => 'test_' . time() . '@example.com',
    'password' => 'TestPassword123!',
    'password_confirm' => 'TestPassword123!',
    'location' => 'Paris',
    'newsletter' => false
];

echo "   Attempting to register user: " . $testUser['email'] . "\n";

try {
    $result = $auth->register($testUser);
    
    if ($result['success']) {
        echo "   ✓ Registration successful!\n";
        echo "   User ID: " . $result['user_id'] . "\n";
        
        // Clean up test user
        $connection = $db->getConnection();
        $stmt = $connection->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$result['user_id']]);
        echo "   ✓ Test user cleaned up\n";
    } else {
        echo "   ✗ Registration failed: " . ($result['message'] ?? 'Unknown error') . "\n";
        if (isset($result['errors'])) {
            foreach ($result['errors'] as $error) {
                echo "     - $error\n";
            }
        }
    }
} catch (Exception $e) {
    echo "   ✗ Registration error: " . $e->getMessage() . "\n";
    echo "   Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n=== Test Complete ===\n";
echo "\nIf all tests passed, registration should work.\n";
echo "If not, check the errors above for guidance.\n";