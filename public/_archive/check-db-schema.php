<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config.php';

echo "<!DOCTYPE html>";
echo "<html><head><title>Check DB Schema</title>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background: #4CAF50; color: white; }
</style></head><body>";

echo "<h1>🔍 Database Schema Check</h1>";

try {
    $pdo = Config::getPDO();
    
    // Check users table
    echo "<h2>Users Table:</h2>";
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table>";
    echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Default</th></tr>";
    
    $hasOnboardingCompleted = false;
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "</tr>";
        
        if ($col['Field'] === 'onboarding_completed') {
            $hasOnboardingCompleted = true;
        }
    }
    echo "</table>";
    
    if (!$hasOnboardingCompleted) {
        echo "<p class='warning'>⚠️ Column 'onboarding_completed' missing in users table</p>";
        echo "<p>Adding column...</p>";
        
        try {
            $pdo->exec("ALTER TABLE users ADD COLUMN onboarding_completed TINYINT(1) DEFAULT 0");
            echo "<p class='success'>✅ Column added successfully!</p>";
        } catch (PDOException $e) {
            echo "<p class='error'>❌ Could not add column: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p class='success'>✅ Column 'onboarding_completed' exists</p>";
    }
    
    // Check user_profiles table
    echo "<h2>User Profiles Table:</h2>";
    $stmt = $pdo->query("DESCRIBE user_profiles");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table>";
    echo "<tr><th>Column</th><th>Type</th><th>Status</th></tr>";
    
    $expectedColumns = [
        'preferences' => 'JSON for storing preferences',
        'location' => 'Text location',
        'budget_max' => 'Maximum budget',
        'notification_enabled' => 'Notification preference',
        'onboarding_completed' => 'Onboarding status'
    ];
    
    foreach ($columns as $col) {
        $status = '';
        if (isset($expectedColumns[$col['Field']])) {
            $status = "✅ " . $expectedColumns[$col['Field']];
        } elseif ($col['Field'] === 'notification_settings') {
            $status = "❌ Should be removed (use notification_enabled instead)";
        }
        
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>$status</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (PDOException $e) {
    echo "<p class='error'>❌ Database error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='/onboarding.php'>➡️ Test Onboarding Now</a></p>";
echo "<p><a href='/dashboard.php'>➡️ Go to Dashboard</a></p>";

echo "</body></html>";
?>