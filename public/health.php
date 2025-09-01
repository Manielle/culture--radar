<?php
/**
 * Health check endpoint for Railway
 * Simple endpoint to verify the application is running
 */

header('Content-Type: application/json');

// Basic health check
$health = [
    'status' => 'healthy',
    'timestamp' => date('c'),
    'environment' => [
        'php_version' => PHP_VERSION,
        'server_port' => $_SERVER['SERVER_PORT'] ?? 'unknown',
        'railway_port' => getenv('PORT') ?: 'not set',
        'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'unknown',
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
        'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown'
    ]
];

// Check database connection if config exists
try {
    if (file_exists(__DIR__ . '/config.php')) {
        require_once __DIR__ . '/config.php';
        $pdo = Config::getPDO();
        $health['database'] = 'connected';
        
        // Get database info
        $stmt = $pdo->query("SELECT VERSION() as version");
        $health['database_version'] = $stmt->fetch()['version'];
    } else {
        $health['database'] = 'config not found';
    }
} catch (Exception $e) {
    $health['database'] = 'error';
    $health['database_error'] = $e->getMessage();
}

// Check if we can write to cache directory
$cacheDir = __DIR__ . '/cache';
if (is_dir($cacheDir) && is_writable($cacheDir)) {
    $health['cache'] = 'writable';
} else {
    $health['cache'] = 'not writable';
}

// Railway-specific checks
if (getenv('RAILWAY_ENVIRONMENT')) {
    $health['railway'] = [
        'environment' => getenv('RAILWAY_ENVIRONMENT'),
        'static_url' => getenv('RAILWAY_STATIC_URL'),
        'replica_id' => getenv('RAILWAY_REPLICA_ID'),
        'deployment_id' => getenv('RAILWAY_DEPLOYMENT_ID')
    ];
}

echo json_encode($health, JSON_PRETTY_PRINT);
?>