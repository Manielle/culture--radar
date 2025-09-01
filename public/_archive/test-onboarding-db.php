<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html><head><title>Test Onboarding DB</title>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .info { color: blue; }
    pre { background: #f0f0f0; padding: 10px; border-radius: 5px; }
    table { border-collapse: collapse; width: 100%; margin: 10px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background: #4CAF50; color: white; }
</style></head><body>";

echo "<h1>🔍 Test Onboarding Database Connection</h1>";

// Check session
echo "<h2>1. Session Check:</h2>";
if (isset($_SESSION['user_id'])) {
    echo "<p class='success'>✅ User ID in session: " . $_SESSION['user_id'] . "</p>";
    echo "<p>User Name: " . ($_SESSION['user_name'] ?? 'Not set') . "</p>";
    echo "<p>User Email: " . ($_SESSION['user_email'] ?? 'Not set') . "</p>";
} else {
    echo "<p class='error'>❌ No user in session - Please login first</p>";
    echo "<p><a href='/login.php'>Go to Login</a></p>";
}

// Load config
echo "<h2>2. Configuration:</h2>";
require_once __DIR__ . '/config.php';

$dbConfig = Config::database();
echo "<table>";
echo "<tr><th>Parameter</th><th>Value</th></tr>";
echo "<tr><td>Host</td><td>{$dbConfig['host']}</td></tr>";
echo "<tr><td>Port</td><td class='" . ($dbConfig['port'] == '8889' ? 'success' : 'error') . "'>{$dbConfig['port']}</td></tr>";
echo "<tr><td>Database</td><td>{$dbConfig['name']}</td></tr>";
echo "<tr><td>User</td><td>{$dbConfig['user']}</td></tr>";
echo "<tr><td>Charset</td><td>{$dbConfig['charset']}</td></tr>";
echo "</table>";

// Test connection
echo "<h2>3. Database Connection Test:</h2>";
try {
    // Method 1: Manual DSN
    $dsn = "mysql:host=" . $dbConfig['host'] . ";port=" . $dbConfig['port'] . ";dbname=" . $dbConfig['name'] . ";charset=" . $dbConfig['charset'];
    echo "<p>DSN: <code>$dsn</code></p>";
    
    $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p class='success'>✅ Connection successful with manual DSN</p>";
    
    // Method 2: Config::getPDO()
    $pdo2 = Config::getPDO();
    echo "<p class='success'>✅ Connection successful with Config::getPDO()</p>";
    
} catch (PDOException $e) {
    echo "<p class='error'>❌ Connection failed: " . $e->getMessage() . "</p>";
    echo "<pre>Error Code: " . $e->getCode() . "</pre>";
    die("</body></html>");
}

// Check user_profiles table
echo "<h2>4. User Profiles Table Check:</h2>";
try {
    // Check table structure
    $stmt = $pdo->query("DESCRIBE user_profiles");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p class='success'>✅ Table 'user_profiles' exists with " . count($columns) . " columns</p>";
    
    echo "<table>";
    echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check if user has profile
    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM user_profiles WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($profile) {
            echo "<p class='success'>✅ User profile found (ID: {$profile['id']})</p>";
            echo "<pre>";
            print_r($profile);
            echo "</pre>";
        } else {
            echo "<p class='error'>❌ No profile found for user ID: {$_SESSION['user_id']}</p>";
            
            // Try to create one
            echo "<p class='info'>Attempting to create profile...</p>";
            try {
                $stmt = $pdo->prepare("INSERT INTO user_profiles (user_id, preferences, location, budget_max, created_at) VALUES (?, '{}', '', 0, NOW())");
                $stmt->execute([$_SESSION['user_id']]);
                echo "<p class='success'>✅ Profile created successfully!</p>";
            } catch (PDOException $e) {
                echo "<p class='error'>❌ Could not create profile: " . $e->getMessage() . "</p>";
            }
        }
    }
    
} catch (PDOException $e) {
    echo "<p class='error'>❌ Table check failed: " . $e->getMessage() . "</p>";
}

// Test UPDATE query
echo "<h2>5. Test UPDATE Query (Simulation):</h2>";
if (isset($_SESSION['user_id'])) {
    $testData = [
        'preferences' => json_encode(['music', 'theater', 'art']),
        'location' => 'Paris',
        'budget_max' => 50,
        'notification_settings' => json_encode(['email' => true]),
        'user_id' => $_SESSION['user_id']
    ];
    
    $sql = "UPDATE user_profiles 
            SET preferences = ?, location = ?, budget_max = ?, notification_settings = ?, onboarding_completed = 1, updated_at = NOW()
            WHERE user_id = ?";
    
    echo "<p>Query to execute:</p>";
    echo "<pre>$sql</pre>";
    echo "<p>With values:</p>";
    echo "<pre>";
    print_r($testData);
    echo "</pre>";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $testData['preferences'],
            $testData['location'],
            $testData['budget_max'],
            $testData['notification_settings'],
            $testData['user_id']
        ]);
        
        $rowCount = $stmt->rowCount();
        echo "<p class='success'>✅ UPDATE successful! Rows affected: $rowCount</p>";
        
        if ($rowCount === 0) {
            echo "<p class='error'>⚠️ Warning: No rows were updated. Profile might not exist.</p>";
        }
        
    } catch (PDOException $e) {
        echo "<p class='error'>❌ UPDATE failed: " . $e->getMessage() . "</p>";
        echo "<pre>SQL State: " . $e->getCode() . "</pre>";
    }
}

echo "<hr>";
echo "<h2>Actions:</h2>";
echo "<p><a href='/onboarding.php'>➡️ Return to Onboarding</a></p>";
echo "<p><a href='/dashboard.php'>➡️ Go to Dashboard</a></p>";
echo "<p><a href='/login.php'>➡️ Login Page</a></p>";

echo "</body></html>";
?>