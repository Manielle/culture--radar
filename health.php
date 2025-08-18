<?php
// Simple health check endpoint for Railway
header('Content-Type: application/json');

$response = [
    'status' => 'healthy',
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => PHP_VERSION,
    'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'port' => $_SERVER['SERVER_PORT'] ?? 'Unknown'
];

// Check database connection if variables are set
$db_status = 'not_configured';
if (getenv('MYSQL_URL') || getenv('MYSQLHOST')) {
    try {
        // Try Railway's MYSQL_URL first
        if ($mysql_url = getenv('MYSQL_URL')) {
            $parsed = parse_url($mysql_url);
            $host = $parsed['host'] ?? '';
            $port = $parsed['port'] ?? 3306;
            $user = $parsed['user'] ?? '';
            $pass = $parsed['pass'] ?? '';
            $dbname = ltrim($parsed['path'] ?? '', '/');
            
            $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass);
        } 
        // Fall back to individual variables
        else {
            $host = getenv('MYSQLHOST');
            $port = getenv('MYSQLPORT') ?: 3306;
            $user = getenv('MYSQLUSER');
            $pass = getenv('MYSQLPASSWORD');
            $dbname = getenv('MYSQLDATABASE');
            
            $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass);
        }
        
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db_status = 'connected';
    } catch (Exception $e) {
        $db_status = 'error: ' . $e->getMessage();
    }
} else {
    // List what MySQL variables are available
    $mysql_vars = [];
    foreach ($_ENV as $key => $value) {
        if (strpos($key, 'MYSQL') !== false || strpos($key, 'DATABASE') !== false) {
            $mysql_vars[$key] = substr($value, 0, 10) . '...'; // Show only first 10 chars for security
        }
    }
    $response['available_db_vars'] = $mysql_vars;
}

$response['database'] = $db_status;

// Output response
echo json_encode($response, JSON_PRETTY_PRINT);
?>