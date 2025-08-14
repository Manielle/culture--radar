<?php
/**
 * Configuration Culture Radar pour Railway
 * Utilise automatiquement les variables d'environnement Railway
 */

// Session sécurisée
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    ini_set('session.use_strict_mode', 1);
}

// Configuration Base de données - Railway MySQL
define('DB_HOST', getenv('MYSQLHOST') ?: 'localhost');
define('DB_NAME', getenv('MYSQLDATABASE') ?: 'culture_radar');
define('DB_USER', getenv('MYSQLUSER') ?: 'root');
define('DB_PASS', getenv('MYSQLPASSWORD') ?: '');
define('DB_PORT', getenv('MYSQLPORT') ?: '3306');

// URL du site depuis Railway
$railway_domain = getenv('RAILWAY_PUBLIC_DOMAIN') ?: 'localhost';
define('APP_URL', 'https://' . $railway_domain);
define('SITE_URL', 'https://' . $railway_domain);
define('BASE_URL', 'https://' . $railway_domain);

// Configuration Application
define('APP_NAME', 'Culture Radar');
define('APP_VERSION', '1.0.0');
define('APP_ENV', getenv('APP_ENV') ?: 'production');
define('APP_DEBUG', getenv('APP_DEBUG') === 'true' ? true : false);
define('DEBUG_MODE', getenv('DEBUG_MODE') === 'true' ? true : false);

// Clés API (optionnelles - à ajouter dans Railway Variables si nécessaire)
define('OPENWEATHER_API_KEY', getenv('OPENWEATHER_API_KEY') ?: '');
define('GOOGLE_MAPS_API_KEY', getenv('GOOGLE_MAPS_API_KEY') ?: '');
define('OPENAGENDA_API_KEY', getenv('OPENAGENDA_API_KEY') ?: '');
define('TICKETMASTER_API_KEY', getenv('TICKETMASTER_API_KEY') ?: '');

// Chemins
define('ROOT_PATH', dirname(__FILE__));
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('LOG_PATH', ROOT_PATH . '/logs');
define('CACHE_PATH', ROOT_PATH . '/cache');

// Configuration Cache et Upload
define('CACHE_DRIVER', 'file');
define('CACHE_TTL', 3600);
define('UPLOAD_MAX_SIZE', 10485760); // 10MB
define('ALLOWED_IMAGE_TYPES', 'jpg,jpeg,png,webp,gif');

// Configuration Sécurité
define('RATE_LIMIT_ENABLED', true);
define('RATE_LIMIT_REQUESTS', 100);
define('RATE_LIMIT_WINDOW', 3600);

// Timezone
date_default_timezone_set('Europe/Paris');

// Gestion des erreurs selon l'environnement
if (APP_DEBUG || APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
} else {
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

// Créer les dossiers nécessaires s'ils n'existent pas
$directories = [UPLOAD_PATH, LOG_PATH, CACHE_PATH];
foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Classe Config pour compatibilité avec l'ancien code
class Config {
    private static $config = [];
    private static $loaded = false;
    
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
        
        // Enfin retourner la valeur par défaut
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
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ]
            );
            return $pdo;
        } catch (PDOException $e) {
            if (APP_DEBUG) {
                // En debug, afficher l'erreur
                die("Erreur de connexion à la base de données : " . $e->getMessage());
            } else {
                // En production, logger l'erreur et afficher un message générique
                error_log("Database connection failed: " . $e->getMessage());
                die("Une erreur est survenue. Veuillez réessayer plus tard.");
            }
        }
    }
    
    /**
     * Test database connection
     */
    public static function testDatabaseConnection() {
        try {
            $pdo = self::getPDO();
            $stmt = $pdo->query("SELECT 1");
            return $stmt !== false;
        } catch (Exception $e) {
            return false;
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
    
    /**
     * Check if in development
     */
    public static function isDevelopment() {
        return APP_ENV === 'development';
    }
    
    /**
     * Get all configuration as array
     */
    public static function all() {
        $allConfig = [];
        
        // Récupérer toutes les constantes définies
        $constants = get_defined_constants(true);
        if (isset($constants['user'])) {
            $allConfig = $constants['user'];
        }
        
        // Ajouter les variables d'environnement
        $allConfig = array_merge($allConfig, $_ENV);
        
        return $allConfig;
    }
}

// Pour compatibilité avec l'ancien code qui pourrait utiliser ces fonctions
if (!function_exists('config')) {
    function config($key, $default = null) {
        return Config::get($key, $default);
    }
}

// Initialisation de la connexion à la base de données (optionnel)
// Décommentez si vous voulez tester la connexion au démarrage
// try {
//     $db = Config::getPDO();
// } catch (Exception $e) {
//     // La connexion a échoué mais on continue
//     error_log("Initial database connection failed: " . $e->getMessage());
// }
?>
