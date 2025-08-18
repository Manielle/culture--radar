<?php
/**
 * Configuration spéciale pour Railway
 * Ce fichier détecte automatiquement l'environnement Railway
 */

// Détection de l'environnement Railway
$isRailway = isset($_ENV['RAILWAY_ENVIRONMENT']) || 
             isset($_SERVER['RAILWAY_ENVIRONMENT']) ||
             getenv('RAILWAY_ENVIRONMENT');

if ($isRailway) {
    // Configuration Railway - utilise les variables d'environnement
    define('DB_HOST', getenv('MYSQLHOST') ?: getenv('DB_HOST') ?: 'centerbeam.proxy.rlwy.net');
    define('DB_PORT', getenv('MYSQLPORT') ?: getenv('DB_PORT') ?: '48330');
    define('DB_NAME', getenv('MYSQLDATABASE') ?: getenv('DB_NAME') ?: 'railway');
    define('DB_USER', getenv('MYSQLUSER') ?: getenv('DB_USER') ?: 'root');
    define('DB_PASS', getenv('MYSQLPASSWORD') ?: getenv('DB_PASSWORD') ?: 'tBixYXRKGkGAZuyxGHFZzaTxQAGXvJJH');
    
    // Force production mode
    $_ENV['APP_ENV'] = 'production';
    $_ENV['APP_DEBUG'] = 'false';
    
    error_log("Railway environment detected - using Railway MySQL");
} else {
    // Configuration locale (MAMP ou autre)
    define('DB_HOST', '127.0.0.1');
    define('DB_PORT', '8889');
    define('DB_NAME', 'culture_radar');
    define('DB_USER', 'root');
    define('DB_PASS', 'root');
    
    error_log("Local environment detected - using local database");
}

// Créer le DSN pour PDO
function getDatabaseDSN() {
    $host = DB_HOST;
    $port = DB_PORT;
    
    // Si le port est déjà dans l'host, ne pas le dupliquer
    if (strpos($host, ':') !== false) {
        return "mysql:host=$host;dbname=" . DB_NAME . ";charset=utf8mb4";
    } else {
        return "mysql:host=$host;port=$port;dbname=" . DB_NAME . ";charset=utf8mb4";
    }
}

// Fonction pour obtenir la connexion PDO
function getDatabaseConnection() {
    try {
        $dsn = getDatabaseDSN();
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        error_log("Database connection successful to: " . DB_HOST);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        error_log("Attempted DSN: " . getDatabaseDSN());
        
        // En production, ne pas exposer les détails
        if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'production') {
            die("Service temporairement indisponible. Veuillez réessayer plus tard.");
        } else {
            die("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }
}

// Test de connexion au chargement
if (php_sapi_name() === 'cli') {
    echo "Testing database connection...\n";
    echo "Environment: " . ($isRailway ? "Railway" : "Local") . "\n";
    echo "Host: " . DB_HOST . "\n";
    echo "Port: " . DB_PORT . "\n";
    echo "Database: " . DB_NAME . "\n";
    
    try {
        $pdo = getDatabaseConnection();
        echo "✅ Connection successful!\n";
    } catch (Exception $e) {
        echo "❌ Connection failed: " . $e->getMessage() . "\n";
    }
}
?>