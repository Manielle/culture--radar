<?php
/**
 * Configuration Culture Radar pour Railway
 * Détecte automatiquement l'environnement et utilise les bonnes variables
 */

// Session sécurisée
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.use_only_cookies', 1);
    
    // Secure cookies en production
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        ini_set('session.cookie_secure', 1);
    }
}

// Détection de l'environnement Railway
$isRailway = getenv('RAILWAY_ENVIRONMENT') !== false || 
             getenv('RAILWAY_PUBLIC_DOMAIN') !== false ||
             getenv('MYSQLHOST') !== false;

// Configuration Base de données
if ($isRailway) {
    // Railway MySQL
    define('DB_HOST', getenv('MYSQLHOST') ?: 'centerbeam.proxy.rlwy.net');
    define('DB_PORT', getenv('MYSQLPORT') ?: '48330');
    define('DB_NAME', getenv('MYSQLDATABASE') ?: 'railway');
    define('DB_USER', getenv('MYSQLUSER') ?: 'root');
    define('DB_PASS', getenv('MYSQLPASSWORD') ?: 'tBixYXRKGkGAZuyxGHFZzaTxQAGXvJJH');
    
    // URL du site depuis Railway
    $railway_domain = getenv('RAILWAY_PUBLIC_DOMAIN') ?: 'ias-b3-g7-paris.up.railway.app';
    define('APP_URL', 'https://' . $railway_domain);
    define('APP_ENV', 'production');
    define('APP_DEBUG', false);
} else {
    // Local (MAMP)
    define('DB_HOST', '127.0.0.1');
    define('DB_PORT', '8889');
    define('DB_NAME', 'culture_radar');
    define('DB_USER', 'root');
    define('DB_PASS', 'root');
    
    define('APP_URL', 'http://localhost:8888');
    define('APP_ENV', 'development');
    define('APP_DEBUG', true);
}

// Configuration Application
define('APP_NAME', 'Culture Radar');
define('APP_VERSION', '1.0.0');
define('SITE_URL', APP_URL);
define('BASE_URL', APP_URL);

// Timezone
date_default_timezone_set('Europe/Paris');

// Gestion des erreurs selon l'environnement
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
    ini_set('display_errors', 0);
}

// Classe Config pour compatibilité
class Config {
    /**
     * Get configuration value
     */
    public static function get($key, $default = null) {
        // D'abord vérifier les constantes définies
        if (defined($key)) {
            return constant($key);
        }
        
        // Ensuite vérifier les variables d'environnement
        $value = getenv($key);
        if ($value !== false) {
            return self::parseValue($value);
        }
        
        // Retourner la valeur par défaut
        return $default;
    }
    
    /**
     * Parse configuration value
     */
    private static function parseValue($value) {
        if (strtolower($value) === 'true') return true;
        if (strtolower($value) === 'false') return false;
        if (strtolower($value) === 'null') return null;
        if (is_numeric($value)) {
            return strpos($value, '.') !== false ? (float)$value : (int)$value;
        }
        return $value;
    }
    
    /**
     * Get database configuration
     */
    public static function database() {
        return [
            'host' => DB_HOST,
            'name' => DB_NAME,
            'user' => DB_USER,
            'pass' => DB_PASS,
            'port' => DB_PORT,
            'charset' => 'utf8mb4'
        ];
    }
    
    /**
     * Get PDO DSN string
     */
    public static function getDSN() {
        return "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    }
    
    /**
     * Create and return a PDO instance
     */
    public static function getPDO() {
        try {
            $pdo = new PDO(
                self::getDSN(),
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            return $pdo;
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            
            if (APP_DEBUG) {
                die("Erreur de connexion : " . $e->getMessage());
            } else {
                // En production, continuer sans base de données
                return null;
            }
        }
    }
    
    /**
     * Check if in debug mode
     */
    public static function isDebug() {
        return APP_DEBUG;
    }
    
    /**
     * Check if in production
     */
    public static function isProduction() {
        return APP_ENV === 'production';
    }
}

// Pour compatibilité avec l'ancien code
if (!function_exists('config')) {
    function config($key, $default = null) {
        return Config::get($key, $default);
    }
}
?>