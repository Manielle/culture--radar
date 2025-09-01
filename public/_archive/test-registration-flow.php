<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test Registration Flow</h1>";

// Check session variables
echo "<h2>Session Status:</h2>";
echo "<pre>";
echo "Session ID: " . session_id() . "\n";
echo "Session Variables:\n";
print_r($_SESSION);
echo "</pre>";

// Check if config loads properly
echo "<h2>Config Test:</h2>";
try {
    require_once __DIR__ . '/config.php';
    echo "<p style='color:green;'>✅ Config loaded successfully</p>";
    
    // Test database connection
    echo "<h2>Database Connection Test:</h2>";
    $dbConfig = Config::database();
    echo "<pre>";
    echo "Host: " . $dbConfig['host'] . "\n";
    echo "Database: " . $dbConfig['name'] . "\n";
    echo "</pre>";
    
    try {
        $dsn = "mysql:host=" . $dbConfig['host'] . ";dbname=" . $dbConfig['name'] . ";charset=" . $dbConfig['charset'];
        $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass']);
        echo "<p style='color:green;'>✅ Database connection successful</p>";
        
        // Check tables
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "<h3>Available Tables:</h3>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
        
        // Check if user_profiles table exists
        if (in_array('user_profiles', $tables)) {
            echo "<p style='color:green;'>✅ user_profiles table exists</p>";
            
            // Check table structure
            $stmt = $pdo->query("DESCRIBE user_profiles");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<h4>user_profiles structure:</h4>";
            echo "<pre>";
            print_r($columns);
            echo "</pre>";
        } else {
            echo "<p style='color:red;'>❌ user_profiles table does not exist!</p>";
        }
        
    } catch (PDOException $e) {
        echo "<p style='color:red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ Config error: " . $e->getMessage() . "</p>";
}

// Check file permissions
echo "<h2>File Permissions:</h2>";
$files = [
    'onboarding.php',
    'register.php',
    'login.php',
    'dashboard.php'
];

foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        echo "<p>$file: " . (is_readable($path) ? "✅ Readable" : "❌ Not readable") . "</p>";
    } else {
        echo "<p>$file: ❌ File does not exist</p>";
    }
}

// Test links
echo "<h2>Test Links:</h2>";
echo "<ul>";
echo "<li><a href='/register.php'>Register Page</a></li>";
echo "<li><a href='/onboarding.php'>Onboarding Page (requires login)</a></li>";
echo "<li><a href='/login.php'>Login Page</a></li>";
echo "<li><a href='/dashboard.php'>Dashboard (requires login)</a></li>";
echo "</ul>";

// Mock session to test onboarding access
echo "<h2>Test Onboarding Access:</h2>";
echo "<form method='post'>";
echo "<button type='submit' name='mock_login'>Mock Login (Set Session)</button>";
echo "<button type='submit' name='clear_session'>Clear Session</button>";
echo "</form>";

if (isset($_POST['mock_login'])) {
    $_SESSION['user_id'] = 999;
    $_SESSION['user_name'] = 'Test User';
    $_SESSION['user_email'] = 'test@example.com';
    echo "<p style='color:green;'>✅ Mock session created. <a href='/onboarding.php'>Go to Onboarding</a></p>";
}

if (isset($_POST['clear_session'])) {
    session_destroy();
    echo "<p style='color:blue;'>Session cleared. <a href='/test-registration-flow.php'>Refresh</a></p>";
}
?>