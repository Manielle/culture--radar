<?php
/**
 * Fix user_profiles table structure
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';

echo "Fixing user_profiles table structure...\n\n";

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Check current columns
    $stmt = $pdo->query("SHOW COLUMNS FROM user_profiles");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Current columns: " . implode(', ', $columns) . "\n\n";
    
    // Add notification_enabled if it doesn't exist
    if (!in_array('notification_enabled', $columns)) {
        echo "Adding notification_enabled column...\n";
        $pdo->exec("ALTER TABLE user_profiles ADD COLUMN notification_enabled BOOLEAN DEFAULT TRUE AFTER budget_max");
        echo "✅ Added notification_enabled column\n";
    } else {
        echo "✅ notification_enabled column already exists\n";
    }
    
    // Check if notification_settings exists and should be removed
    if (in_array('notification_settings', $columns)) {
        echo "Removing old notification_settings column...\n";
        $pdo->exec("ALTER TABLE user_profiles DROP COLUMN notification_settings");
        echo "✅ Removed notification_settings column\n";
    }
    
    // Verify the structure
    echo "\n📋 Final table structure:\n";
    $stmt = $pdo->query("DESCRIBE user_profiles");
    $structure = $stmt->fetchAll();
    
    foreach ($structure as $col) {
        echo "  - {$col['Field']} ({$col['Type']})" . ($col['Null'] === 'NO' ? ' NOT NULL' : '') . "\n";
    }
    
    echo "\n✅ Database structure is ready!\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>