<?php
/**
 * Health check endpoint pour Railway
 * Vérifie que l'application et la base de données sont opérationnelles
 */

header('Content-Type: application/json');

require_once __DIR__ . '/config-railway.php';

$health = [
    'status' => 'healthy',
    'timestamp' => date('c'),
    'checks' => []
];

// Vérification de la connexion à la base de données
try {
    $pdo = getDatabaseConnection();
    $stmt = $pdo->query("SELECT 1");
    $health['checks']['database'] = [
        'status' => 'healthy',
        'message' => 'Database connection successful'
    ];
} catch (Exception $e) {
    $health['status'] = 'unhealthy';
    $health['checks']['database'] = [
        'status' => 'unhealthy',
        'message' => 'Database connection failed',
        'error' => APP_DEBUG ? $e->getMessage() : 'Connection error'
    ];
}

// Vérification des répertoires essentiels
$requiredDirs = ['logs', 'uploads', 'cache'];
foreach ($requiredDirs as $dir) {
    $path = __DIR__ . '/' . $dir;
    if (is_dir($path) && is_writable($path)) {
        $health['checks'][$dir . '_directory'] = [
            'status' => 'healthy',
            'message' => "Directory {$dir} is accessible"
        ];
    } else {
        $health['status'] = 'unhealthy';
        $health['checks'][$dir . '_directory'] = [
            'status' => 'unhealthy',
            'message' => "Directory {$dir} is not accessible or writable"
        ];
    }
}

// Vérification de la mémoire disponible
$memoryLimit = ini_get('memory_limit');
$health['checks']['memory'] = [
    'status' => 'healthy',
    'message' => "Memory limit: {$memoryLimit}",
    'usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB'
];

// Vérification de PHP
$health['checks']['php'] = [
    'status' => 'healthy',
    'version' => PHP_VERSION,
    'extensions' => [
        'pdo' => extension_loaded('pdo'),
        'pdo_mysql' => extension_loaded('pdo_mysql'),
        'json' => extension_loaded('json'),
        'curl' => extension_loaded('curl'),
        'mbstring' => extension_loaded('mbstring')
    ]
];

// Définir le code HTTP approprié
http_response_code($health['status'] === 'healthy' ? 200 : 503);

// Retourner la réponse JSON
echo json_encode($health, JSON_PRETTY_PRINT);
?>