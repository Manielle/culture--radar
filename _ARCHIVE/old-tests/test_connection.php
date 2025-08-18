<?php
/**
 * Database Connection Test Script
 * Run this script to verify database connectivity
 */

require_once __DIR__ . '/vendor/autoload.php';

use CultureRadar\Core\Database;

echo "Culture Radar - Database Connection Test\n";
echo "========================================\n\n";

try {
    // Test connection
    echo "Testing database connection...\n";
    $db = Database::getInstance();
    $connection = $db->getConnection();
    
    echo "✓ Connection successful!\n\n";
    
    // Test query execution
    echo "Testing query execution...\n";
    $result = $db->execute("SELECT DATABASE() as db_name");
    $row = $result->fetch();
    echo "✓ Connected to database: " . $row['db_name'] . "\n\n";
    
    // Count tables
    echo "Checking database structure...\n";
    $result = $db->execute("SHOW TABLES");
    $tables = $result->fetchAll(PDO::FETCH_COLUMN);
    echo "✓ Found " . count($tables) . " tables\n\n";
    
    // List tables
    if (count($tables) > 0) {
        echo "Tables in database:\n";
        foreach ($tables as $table) {
            echo "  - $table\n";
        }
    }
    
    echo "\n✓ All tests passed successfully!\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "\nPlease check:\n";
    echo "1. MAMP is running\n";
    echo "2. Database 'culture_radar' exists\n";
    echo "3. Database credentials are correct\n";
    exit(1);
}