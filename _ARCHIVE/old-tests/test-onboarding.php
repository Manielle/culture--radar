<?php
/**
 * Test Onboarding Database Structure
 * This script checks if the database is properly configured for onboarding
 */

session_start();
require_once __DIR__ . '/config.php';

echo "<h1>Onboarding Database Test</h1>";
echo "<pre>";

try {
    // Connect to database
    $dbConfig = Config::database();
    $dsn = "mysql:host=" . $dbConfig['host'] . ";dbname=" . $dbConfig['name'] . ";charset=" . $dbConfig['charset'];
    $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Database connection successful\n\n";
    
    // Check if user_profiles table exists
    echo "Checking user_profiles table structure:\n";
    echo "----------------------------------------\n";
    
    $stmt = $pdo->prepare("SHOW COLUMNS FROM user_profiles");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($columns)) {
        echo "❌ Table 'user_profiles' does not exist!\n";
        echo "\nCreating table...\n";
        
        // Create the table
        $createTable = "
        CREATE TABLE IF NOT EXISTS user_profiles (
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
        
        $pdo->exec($createTable);
        echo "✅ Table created successfully!\n";
        
        // Re-check columns
        $stmt = $pdo->prepare("SHOW COLUMNS FROM user_profiles");
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    echo "Columns found:\n";
    foreach ($columns as $column) {
        echo "  - " . $column['Field'] . " (" . $column['Type'] . ")";
        if ($column['Null'] === 'YES') echo " [nullable]";
        if ($column['Key'] === 'PRI') echo " [PRIMARY KEY]";
        if ($column['Key'] === 'UNI') echo " [UNIQUE]";
        if ($column['Default'] !== null) echo " [default: " . $column['Default'] . "]";
        echo "\n";
    }
    
    // Check for notification_settings column (old name)
    $hasNotificationSettings = false;
    $hasNotificationEnabled = false;
    
    foreach ($columns as $column) {
        if ($column['Field'] === 'notification_settings') {
            $hasNotificationSettings = true;
        }
        if ($column['Field'] === 'notification_enabled') {
            $hasNotificationEnabled = true;
        }
    }
    
    if ($hasNotificationSettings && !$hasNotificationEnabled) {
        echo "\n⚠️ Found old column 'notification_settings' - updating to 'notification_enabled'...\n";
        try {
            $pdo->exec("ALTER TABLE user_profiles DROP COLUMN notification_settings");
            $pdo->exec("ALTER TABLE user_profiles ADD COLUMN notification_enabled BOOLEAN DEFAULT TRUE");
            echo "✅ Column updated successfully!\n";
        } catch (Exception $e) {
            echo "❌ Error updating column: " . $e->getMessage() . "\n";
        }
    }
    
    // Check session
    echo "\n\nSession Information:\n";
    echo "----------------------------------------\n";
    if (isset($_SESSION['user_id'])) {
        echo "✅ User is logged in\n";
        echo "  User ID: " . $_SESSION['user_id'] . "\n";
        echo "  User Name: " . ($_SESSION['user_name'] ?? 'Not set') . "\n";
        
        // Check if profile exists
        $stmt = $pdo->prepare("SELECT * FROM user_profiles WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($profile) {
            echo "\n✅ User profile exists:\n";
            echo "  Location: " . ($profile['location'] ?: 'Not set') . "\n";
            echo "  Budget Max: " . $profile['budget_max'] . "\n";
            echo "  Onboarding Completed: " . ($profile['onboarding_completed'] ? 'Yes' : 'No') . "\n";
            echo "  Preferences: " . ($profile['preferences'] ?: 'Not set') . "\n";
        } else {
            echo "\n⚠️ No user profile found for user ID " . $_SESSION['user_id'] . "\n";
            echo "  Profile will be created when onboarding is completed.\n";
        }
    } else {
        echo "❌ User is not logged in\n";
        echo "  Please login first to test onboarding.\n";
    }
    
    // Test data submission simulation
    echo "\n\nSimulated Onboarding Data Test:\n";
    echo "----------------------------------------\n";
    
    if (isset($_SESSION['user_id'])) {
        $testData = [
            'preferences' => ['musique', 'theatre', 'exposition'],
            'location' => 'Paris',
            'budget' => 'medium',
            'notifications' => ['email']
        ];
        
        echo "Test data:\n";
        echo "  Preferences: " . implode(', ', $testData['preferences']) . "\n";
        echo "  Location: " . $testData['location'] . "\n";
        echo "  Budget: " . $testData['budget'] . "\n";
        echo "  Notifications: " . implode(', ', $testData['notifications']) . "\n";
        
        echo "\nSQL that would be executed:\n";
        
        $preferencesJson = json_encode($testData['preferences']);
        $notificationEnabled = in_array('email', $testData['notifications']) ? 1 : 0;
        $budgetMap = ['free' => 0, 'low' => 15, 'medium' => 30, 'high' => 999];
        $budgetValue = $budgetMap[$testData['budget']] ?? 999;
        
        if ($profile) {
            echo "UPDATE user_profiles SET\n";
            echo "  preferences = '$preferencesJson',\n";
            echo "  location = '{$testData['location']}',\n";
            echo "  budget_max = $budgetValue,\n";
            echo "  notification_enabled = $notificationEnabled,\n";
            echo "  onboarding_completed = 1\n";
            echo "WHERE user_id = {$_SESSION['user_id']}\n";
        } else {
            echo "INSERT INTO user_profiles\n";
            echo "  (user_id, preferences, location, budget_max, notification_enabled, onboarding_completed)\n";
            echo "VALUES\n";
            echo "  ({$_SESSION['user_id']}, '$preferencesJson', '{$testData['location']}', $budgetValue, $notificationEnabled, 1)\n";
        }
    }
    
    echo "\n\n✅ All tests completed!\n";
    echo "\nIf onboarding still fails, check:\n";
    echo "1. That you're logged in before accessing onboarding\n";
    echo "2. That all form fields are properly filled\n";
    echo "3. The browser console for JavaScript errors\n";
    echo "4. PHP error logs for server-side issues\n";
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
    echo "Error code: " . $e->getCode() . "\n";
}

echo "</pre>";
?>