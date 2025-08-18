<?php
/**
 * Railway-specific configuration
 * This file handles Railway's MySQL environment variables
 */

// Configure session settings ONLY if session hasn't started yet
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1); // HTTPS on Railway
    ini_set('session.use_strict_mode', 1);
}

class Config {
    private static $config = [];
    private static $loaded = false;
    
    /**
     * Load configuration from environment
     */
    public static function load() {
        if (self::$loaded) {
            return;
        }
        
        // Railway provides MYSQL_URL which contains all connection info
        if ($mysql_url = getenv('MYSQL_URL')) {
            $parsed = parse_url($mysql_url);
            
            self::$config['DB_HOST'] = $parsed['host'] ?? 'localhost';
            self::$config['DB_PORT'] = $parsed['port'] ?? 3306;
            self::$config['DB_USER'] = $parsed['user'] ?? 'root';
            self::$config['DB_PASS'] = $parsed['pass'] ?? '';
            self::$config['DB_NAME'] = ltrim($parsed['path'] ?? '', '/') ?: 'railway';
        }
        // Fall back to individual MySQL variables
        elseif (getenv('MYSQLHOST')) {
            self::$config['DB_HOST'] = getenv('MYSQLHOST');
            self::$config['DB_PORT'] = getenv('MYSQLPORT') ?: 3306;
            self::$config['DB_USER'] = getenv('MYSQLUSER');
            self::$config['DB_PASS'] = getenv('MYSQLPASSWORD');
            self::$config['DB_NAME'] = getenv('MYSQLDATABASE');
        }
        // Fall back to defaults for local development
        else {
            self::$config['DB_HOST'] = 'localhost';
            self::$config['DB_PORT'] = '3306';
            self::$config['DB_USER'] = 'root';
            self::$config['DB_PASS'] = '';
            self::$config['DB_NAME'] = 'culture_radar';
        }
        
        // Set other defaults
        self::setDefaults();
        self::$loaded = true;
    }
    
    /**
     * Get configuration value
     */
    public static function get($key, $default = null) {
        self::load();
        
        // Check environment variable first
        $envValue = getenv($key);
        if ($envValue !== false) {
            return self::parseValue($envValue);
        }
        
        // Check config array
        if (isset(self::$config[$key])) {
            return self::parseValue(self::$config[$key]);
        }
        
        return $default;
    }
    
    /**
     * Set configuration value
     */
    public static function set($key, $value) {
        self::$config[$key] = $value;
        $_ENV[$key] = $value;
        putenv("$key=$value");
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
     * Set default configuration values
     */
    private static function setDefaults() {
        $defaults = [
            'APP_NAME' => 'Culture Radar',
            'APP_ENV' => getenv('RAILWAY_ENVIRONMENT') ? 'production' : 'development',
            'APP_DEBUG' => getenv('RAILWAY_ENVIRONMENT') ? 'false' : 'true',
            'APP_URL' => getenv('RAILWAY_PUBLIC_DOMAIN') 
                ? 'https://' . getenv('RAILWAY_PUBLIC_DOMAIN')
                : 'http://localhost:8080',
            
            'CACHE_DRIVER' => 'file',
            'CACHE_TTL' => '3600',
            'UPLOAD_MAX_SIZE' => '10485760',
            'ALLOWED_IMAGE_TYPES' => 'jpg,jpeg,png,webp',
            'UPLOAD_PATH' => '/uploads',
            'RATE_LIMIT_ENABLED' => 'true',
            'RATE_LIMIT_REQUESTS' => '100',
            'RATE_LIMIT_WINDOW' => '3600',
            'AI_TRAINING_ENABLED' => 'true',
            'AI_MIN_INTERACTIONS' => '10',
            'ERROR_REPORTING' => getenv('RAILWAY_ENVIRONMENT') ? 'false' : 'true',
            'LOG_LEVEL' => 'warning'
        ];
        
        foreach ($defaults as $key => $value) {
            if (!isset(self::$config[$key]) && getenv($key) === false) {
                self::$config[$key] = $value;
            }
        }
    }
    
    /**
     * Get database configuration
     */
    public static function database() {
        self::load();
        return [
            'host' => self::$config['DB_HOST'],
            'name' => self::$config['DB_NAME'],
            'user' => self::$config['DB_USER'],
            'pass' => self::$config['DB_PASS'],
            'port' => self::$config['DB_PORT'],
            'charset' => 'utf8mb4'
        ];
    }
    
    /**
     * Get PDO DSN string
     */
    public static function getDSN() {
        $db = self::database();
        return "mysql:host={$db['host']};port={$db['port']};dbname={$db['name']};charset={$db['charset']}";
    }
    
    /**
     * Create and return a PDO instance
     */
    public static function getPDO() {
        try {
            $db = self::database();
            $dsn = self::getDSN();
            
            $pdo = new PDO($dsn, $db['user'], $db['pass']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            
            // Set UTF8MB4 charset
            $pdo->exec("SET NAMES utf8mb4");
            $pdo->exec("SET CHARACTER SET utf8mb4");
            $pdo->exec("SET COLLATION_CONNECTION = 'utf8mb4_unicode_ci'");
            
            return $pdo;
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            
            // Show error in development mode
            if (self::get('APP_DEBUG')) {
                throw new Exception("Database connection failed: " . $e->getMessage());
            }
            
            // Return null in production
            return null;
        }
    }
    
    /**
     * Check if app is in debug mode
     */
    public static function isDebug() {
        return self::get('APP_DEBUG') === true;
    }
    
    /**
     * Check if app is in production
     */
    public static function isProduction() {
        return self::get('APP_ENV') === 'production';
    }
    
    /**
     * Get all configuration values
     */
    public static function all() {
        self::load();
        return self::$config;
    }
}

// Auto-load configuration
Config::load();
?>