<?php
/**
 * Configuration fixée pour Railway
 * Résout les problèmes de connexion MySQL sur Railway
 */

// Détection de l'environnement Railway
$isRailway = getenv('RAILWAY_ENVIRONMENT') !== false || 
             getenv('RAILWAY_PUBLIC_DOMAIN') !== false ||
             getenv('MYSQLHOST') !== false ||
             getenv('MYSQL_URL') !== false;

if ($isRailway) {
    // Sur Railway, utiliser les variables d'environnement
    
    // Option 1: Si MYSQL_URL est disponible (services liés)
    if (getenv('MYSQL_URL')) {
        $mysql_url = getenv('MYSQL_URL');
        $url_parts = parse_url($mysql_url);
        
        define('DB_HOST', $url_parts['host']);
        define('DB_NAME', ltrim($url_parts['path'], '/'));
        define('DB_USER', $url_parts['user']);
        define('DB_PASS', $url_parts['pass']);
        define('DB_PORT', $url_parts['port'] ?? '3306');
    } 
    // Option 2: Variables individuelles
    else if (getenv('MYSQLHOST')) {
        define('DB_HOST', getenv('MYSQLHOST'));
        define('DB_NAME', getenv('MYSQLDATABASE'));
        define('DB_USER', getenv('MYSQLUSER'));
        define('DB_PASS', getenv('MYSQLPASSWORD'));
        define('DB_PORT', getenv('MYSQLPORT') ?: '3306');
    }
    // Option 3: Fallback
    else {
        // Configuration par défaut Railway
        define('DB_HOST', '127.0.0.1');
        define('DB_NAME', 'railway');
        define('DB_USER', 'root');
        define('DB_PASS', '');
        define('DB_PORT', '3306');
    }
    
    define('APP_ENV', 'production');
    define('APP_DEBUG', false);
    
} else {
    // Configuration locale (MAMP/XAMPP)
    // Utiliser 127.0.0.1 au lieu de localhost pour éviter les sockets Unix
    define('DB_HOST', '127.0.0.1');
    define('DB_NAME', 'culture_radar');
    define('DB_USER', 'root');
    define('DB_PASS', 'root');
    define('DB_PORT', '8889'); // Port MAMP par défaut
    
    define('APP_ENV', 'development');
    define('APP_DEBUG', true);
}

// Configuration commune
define('APP_NAME', 'Culture Radar');
define('APP_VERSION', '1.0.0');

// Désactiver l'affichage des erreurs en production
if (!APP_DEBUG) {
    error_reporting(0);
    ini_set('display_errors', 0);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Toujours logger les erreurs
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error.log');

// Fonction de connexion PDO améliorée
function getDatabaseConnection() {
    static $pdo = null;
    
    if ($pdo !== null) {
        return $pdo;
    }
    
    try {
        // Construction du DSN avec gestion des cas spéciaux
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            DB_HOST,
            DB_PORT,
            DB_NAME
        );
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            // Timeout de connexion
            PDO::ATTR_TIMEOUT => 5,
            // Persistent connections pour Railway
            PDO::ATTR_PERSISTENT => defined('RAILWAY_ENVIRONMENT')
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        
        // Configuration UTF8MB4
        $pdo->exec("SET NAMES utf8mb4");
        $pdo->exec("SET CHARACTER SET utf8mb4");
        $pdo->exec("SET COLLATION_CONNECTION = 'utf8mb4_unicode_ci'");
        
        return $pdo;
        
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        
        // En développement, afficher l'erreur
        if (APP_DEBUG) {
            // Vérifier si les headers ont déjà été envoyés
            if (!headers_sent()) {
                header('HTTP/1.1 503 Service Unavailable');
                header('Content-Type: text/plain');
            }
            echo "Database Connection Error:\n";
            echo "DSN: mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . "\n";
            echo "Error: " . $e->getMessage() . "\n\n";
            echo "Troubleshooting:\n";
            echo "1. Check if MySQL service is running\n";
            echo "2. Verify database credentials\n";
            echo "3. On Railway: Check if services are linked\n";
            exit(1);
        } else {
            // En production, page d'erreur générique
            if (!headers_sent()) {
                header('HTTP/1.1 503 Service Unavailable');
                header('Retry-After: 60');
            }
            die("Service temporarily unavailable. Please try again later.");
        }
    }
}

// Fonction de test de connexion
function testDatabaseConnection() {
    try {
        $pdo = getDatabaseConnection();
        if ($pdo) {
            $stmt = $pdo->query("SELECT 1");
            return $stmt !== false;
        }
        return false;
    } catch (Exception $e) {
        return false;
    }
}

// Fonction helper pour obtenir la connexion PDO (alias)
function getDB() {
    return getDatabaseConnection();
}

// Créer les répertoires nécessaires
$requiredDirs = [
    __DIR__ . '/logs',
    __DIR__ . '/uploads',
    __DIR__ . '/cache',
    __DIR__ . '/sessions'
];

foreach ($requiredDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Configuration des sessions
if (session_status() === PHP_SESSION_NONE) {
    // Utiliser un répertoire de sessions personnalisé
    $sessionPath = __DIR__ . '/sessions';
    if (is_dir($sessionPath) && is_writable($sessionPath)) {
        session_save_path($sessionPath);
    }
    
    // Configuration sécurisée des sessions
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', $isRailway ? 1 : 0);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.use_only_cookies', 1);
    
    // Démarrer la session seulement si nécessaire
    // (sera démarré par les pages qui en ont besoin)
}

// Timezone
date_default_timezone_set('Europe/Paris');
?>