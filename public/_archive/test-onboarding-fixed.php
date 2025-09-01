<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Please login first. <a href='/login.php'>Go to Login</a>");
}

require_once __DIR__ . '/config.php';

echo "<!DOCTYPE html>";
echo "<html><head><title>Test Fixed Onboarding</title>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .info { color: blue; }
    button { background: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
    button:hover { background: #45a049; }
</style></head><body>";

echo "<div class='container'>";
echo "<h1>🔧 Test Fixed Onboarding Update</h1>";

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = Config::getPDO();
        
        // Sample data
        $preferences = ['music', 'theater', 'art', 'cinema'];
        $location = 'Paris';
        $budget = 50;
        $notificationEnabled = 1;
        
        // Prepare JSON
        $preferencesJson = json_encode($preferences);
        
        echo "<h2>Data to save:</h2>";
        echo "<ul>";
        echo "<li>Preferences: " . implode(', ', $preferences) . "</li>";
        echo "<li>Location: $location</li>";
        echo "<li>Budget: $budget €</li>";
        echo "<li>Notifications: " . ($notificationEnabled ? 'Enabled' : 'Disabled') . "</li>";
        echo "</ul>";
        
        // THE CORRECTED QUERY (without notification_settings)
        $stmt = $pdo->prepare("
            UPDATE user_profiles 
            SET preferences = ?, 
                location = ?, 
                budget_max = ?, 
                notification_enabled = ?, 
                onboarding_completed = 1, 
                updated_at = NOW()
            WHERE user_id = ?
        ");
        
        $result = $stmt->execute([
            $preferencesJson,
            $location,
            $budget,
            $notificationEnabled,
            $_SESSION['user_id']
        ]);
        
        if ($result) {
            $rowCount = $stmt->rowCount();
            if ($rowCount > 0) {
                $message = "<p class='success'>✅ Profile updated successfully!</p>";
                
                // Verify the update
                $stmt = $pdo->prepare("SELECT * FROM user_profiles WHERE user_id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $profile = $stmt->fetch(PDO::FETCH_ASSOC);
                
                echo "<h3>Updated Profile:</h3>";
                echo "<pre style='background: #f0f0f0; padding: 10px;'>";
                echo "Location: " . $profile['location'] . "\n";
                echo "Preferences: " . $profile['preferences'] . "\n";
                echo "Budget: " . $profile['budget_max'] . " €\n";
                echo "Notifications: " . ($profile['notification_enabled'] ? 'Yes' : 'No') . "\n";
                echo "Onboarding Completed: " . ($profile['onboarding_completed'] ? 'Yes' : 'No') . "\n";
                echo "</pre>";
                
                echo "<p class='success'>🎉 Everything works! You can now use the real onboarding page.</p>";
                echo "<p><a href='/onboarding.php'>➡️ Go to Onboarding</a></p>";
                echo "<p><a href='/dashboard.php'>➡️ Go to Dashboard</a></p>";
                
            } else {
                $message = "<p class='error'>❌ No profile found to update. Creating one...</p>";
                
                // Create profile if it doesn't exist
                $stmt = $pdo->prepare("
                    INSERT INTO user_profiles (user_id, preferences, location, budget_max, notification_enabled, onboarding_completed, created_at) 
                    VALUES (?, ?, ?, ?, ?, 1, NOW())
                ");
                $stmt->execute([
                    $_SESSION['user_id'],
                    $preferencesJson,
                    $location,
                    $budget,
                    $notificationEnabled
                ]);
                
                $message .= "<p class='success'>✅ Profile created successfully!</p>";
            }
        }
        
    } catch (PDOException $e) {
        $message = "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
    }
} else {
    // Check current profile status
    try {
        $pdo = Config::getPDO();
        $stmt = $pdo->prepare("SELECT * FROM user_profiles WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($profile) {
            echo "<h2>Current Profile Status:</h2>";
            echo "<ul>";
            echo "<li>Profile ID: " . $profile['id'] . "</li>";
            echo "<li>Location: " . ($profile['location'] ?: '(empty)') . "</li>";
            echo "<li>Preferences: " . ($profile['preferences'] ?: '{}') . "</li>";
            echo "<li>Budget: " . $profile['budget_max'] . " €</li>";
            echo "<li>Onboarding: " . ($profile['onboarding_completed'] ? 'Completed' : 'Not completed') . "</li>";
            echo "</ul>";
        } else {
            echo "<p class='warning'>No profile found for user ID: " . $_SESSION['user_id'] . "</p>";
        }
    } catch (PDOException $e) {
        echo "<p class='error'>Error checking profile: " . $e->getMessage() . "</p>";
    }
}

echo $message;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<h2>Test the corrected UPDATE query:</h2>";
    echo "<form method='POST'>";
    echo "<p>This will update your profile with sample data using the CORRECTED query (without notification_settings).</p>";
    echo "<button type='submit'>Run Test Update</button>";
    echo "</form>";
}

echo "</div></body></html>";
?>