<?php
/**
 * Comprehensive Database Connection and Structure Test
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';

header('Content-Type: text/plain; charset=utf-8');

echo "=== Culture Radar Database Test ===\n\n";

try {
    // Test 1: Basic Connection
    echo "1. Testing database connection...\n";
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    echo "✅ Database connection successful\n";
    
    // Display connection info
    $dbConfig = Config::database();
    echo "   - Host: " . $dbConfig['host'] . "\n";
    echo "   - Database: " . $dbConfig['name'] . "\n";
    echo "   - User: " . $dbConfig['user'] . "\n";
    echo "   - Charset: " . $dbConfig['charset'] . "\n\n";
    
    // Test 2: Check database exists
    echo "2. Verifying database exists...\n";
    $stmt = $pdo->query("SELECT DATABASE() as current_db");
    $currentDb = $stmt->fetch()['current_db'];
    echo "✅ Current database: " . $currentDb . "\n\n";
    
    // Test 3: Check required tables
    echo "3. Checking required tables...\n";
    $requiredTables = ['users', 'user_profiles', 'events', 'venues'];
    $stmt = $pdo->query("SHOW TABLES");
    $existingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $tableStatus = [];
    foreach ($requiredTables as $table) {
        $exists = in_array($table, $existingTables);
        $tableStatus[$table] = $exists;
        echo ($exists ? "✅" : "❌") . " Table '$table': " . ($exists ? "EXISTS" : "MISSING") . "\n";
        
        if ($exists) {
            // Count records
            $countStmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
            $count = $countStmt->fetch()['count'];
            echo "   - Records: $count\n";
        }
    }
    echo "\n";
    
    // Test 4: Test table structures
    echo "4. Verifying table structures...\n";
    foreach ($requiredTables as $table) {
        if ($tableStatus[$table]) {
            echo "📋 Structure of '$table':\n";
            $stmt = $pdo->query("DESCRIBE `$table`");
            $columns = $stmt->fetchAll();
            foreach ($columns as $column) {
                echo "   - " . $column['Field'] . " (" . $column['Type'] . ")" . 
                     ($column['Key'] ? " [" . $column['Key'] . "]" : "") . "\n";
            }
            echo "\n";
        }
    }
    
    // Test 5: Test basic CRUD operations
    echo "5. Testing basic database operations...\n";
    
    // Test INSERT
    $testData = [
        'name' => 'Test User ' . date('Y-m-d H:i:s'),
        'email' => 'test_' . time() . '@example.com',
        'password' => password_hash('test123', PASSWORD_DEFAULT),
        'accepts_newsletter' => false
    ];
    
    $insertStmt = $pdo->prepare("INSERT INTO users (name, email, password, accepts_newsletter) VALUES (?, ?, ?, ?)");
    $insertResult = $insertStmt->execute([$testData['name'], $testData['email'], $testData['password'], $testData['accepts_newsletter']]);
    
    if ($insertResult) {
        $testUserId = $pdo->lastInsertId();
        echo "✅ INSERT test successful (User ID: $testUserId)\n";
        
        // Test SELECT
        $selectStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $selectStmt->execute([$testUserId]);
        $user = $selectStmt->fetch();
        
        if ($user) {
            echo "✅ SELECT test successful (Found user: " . $user['name'] . ")\n";
            
            // Test UPDATE
            $newName = $user['name'] . ' - Updated';
            $updateStmt = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
            $updateResult = $updateStmt->execute([$newName, $testUserId]);
            
            if ($updateResult) {
                echo "✅ UPDATE test successful\n";
                
                // Test DELETE
                $deleteStmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $deleteResult = $deleteStmt->execute([$testUserId]);
                
                if ($deleteResult) {
                    echo "✅ DELETE test successful\n";
                } else {
                    echo "❌ DELETE test failed\n";
                }
            } else {
                echo "❌ UPDATE test failed\n";
            }
        } else {
            echo "❌ SELECT test failed\n";
        }
    } else {
        echo "❌ INSERT test failed\n";
    }
    echo "\n";
    
    // Test 6: Test Database class methods
    echo "6. Testing Database class methods...\n";
    
    // Test fetchAll method
    $users = $db->fetchAll("SELECT id, name, email FROM users LIMIT 5");
    echo "✅ fetchAll() method works - Retrieved " . count($users) . " users\n";
    
    // Test fetchOne method
    $firstUser = $db->fetchOne("SELECT id, name, email FROM users LIMIT 1");
    if ($firstUser) {
        echo "✅ fetchOne() method works - Retrieved user: " . $firstUser['name'] . "\n";
    } else {
        echo "ℹ️  fetchOne() method works but no users found\n";
    }
    echo "\n";
    
    // Test 7: Check foreign key constraints
    echo "7. Testing foreign key relationships...\n";
    
    if ($tableStatus['users'] && $tableStatus['user_profiles']) {
        // Check if foreign key relationship works
        $stmt = $pdo->query("
            SELECT 
                u.id as user_id,
                u.name,
                up.id as profile_id
            FROM users u 
            LEFT JOIN user_profiles up ON u.id = up.user_id 
            LIMIT 3
        ");
        $userProfiles = $stmt->fetchAll();
        echo "✅ Foreign key relationship test completed\n";
        echo "   - Found " . count($userProfiles) . " user-profile relationships\n";
    }
    echo "\n";
    
    // Summary
    echo "=== DATABASE TEST SUMMARY ===\n";
    $missingTables = array_keys(array_filter($tableStatus, function($exists) { return !$exists; }));
    $existingTables = array_keys(array_filter($tableStatus, function($exists) { return $exists; }));
    
    echo "✅ Existing tables (" . count($existingTables) . "): " . implode(', ', $existingTables) . "\n";
    if (!empty($missingTables)) {
        echo "❌ Missing tables (" . count($missingTables) . "): " . implode(', ', $missingTables) . "\n";
        echo "   💡 Run setup-database.php to create missing tables\n";
    }
    
    echo "\n🎯 Database Status: " . (empty($missingTables) ? "READY ✅" : "NEEDS SETUP ⚠️") . "\n";
    
} catch (Exception $e) {
    echo "❌ Database test failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    
    echo "\n💡 Troubleshooting suggestions:\n";
    echo "1. Make sure MySQL/MAMP is running\n";
    echo "2. Check database credentials in .env file\n";
    echo "3. Run setup-database.php to create database and tables\n";
    echo "4. Verify the database port (MAMP usually uses 8889)\n";
}
?>