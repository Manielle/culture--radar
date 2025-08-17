<?php
// Secure session configuration - only set if session not started yet
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.regenerate_id', 1);
    
    // Enable secure cookies in production
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        ini_set('session.cookie_secure', 1);
        ini_set('session.cookie_samesite', 'Strict');
    }
}

/**
 * CultureRadar Configuration Manager
 * Handles environment variables and application configuration
 */

class Config {
    private static $config = [];
    private static $loaded = false;
    
    /**
     * Load configuration from .env file
     */
    public static function load() {
        if (self::$loaded) {
            return;
        }
        
        // Try multiple env file locations
        $envFiles = [
            __DIR__ . '/.env',
            __DIR__ . '/.env.local',
            __DIR__ . '/.env.development'
        ];
        
        $envFile = null;
        foreach ($envFiles as $file) {
            if (file_exists($file)) {
                $envFile = $file;
                break;
            }
        }
        
        if (!$envFile) {
            // Use safe defaults if no env file found
            self::setDefaults();
            self::$loaded = true;
            return;
        }
        
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            
            foreach ($lines as $line) {
                // Skip comments
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }
                
                // Parse key=value pairs
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    
                    // Remove quotes if present
                    if (($value[0] === '"' && $value[-1] === '"') || 
                        ($value[0] === "'" && $value[-1] === "'")) {
                        $value = substr($value, 1, -1);
                    }
                    
                    self::$config[$key] = $value;
                    
                    // Set as environment variable if not already set
                    if (!isset($_ENV[$key])) {
                        $_ENV[$key] = $value;
                        putenv("$key=$value");
                    }
                }
            }
        }
        
        // Set defaults for missing values
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
     * Alias for get() method - for compatibility
     */
    public static function env($key, $default = null) {
        return self::get($key, $default);
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
     * Parse configuration value (handle booleans, numbers, etc.)
     */
    private static function parseValue($value) {
        // Handle booleans
        if (strtolower($value) === 'true') return true;
        if (strtolower($value) === 'false') return false;
        
        // Handle null
        if (strtolower($value) === 'null') return null;
        
        // Handle numbers
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
            'APP_ENV' => 'development',
            'APP_DEBUG' => 'true',
            'APP_URL' => 'http://localhost:8888',
            'DB_HOST' => 'localhost:8889',
            'DB_NAME' => 'culture_radar',
            'DB_USER' => 'root',
            'DB_PASS' => 'root',
            'DB_PORT' => '8889',
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
            'ERROR_REPORTING' => 'true',
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
        return [
            'host' => self::get('DB_HOST'),
            'name' => self::get('DB_NAME'),
            'user' => self::get('DB_USER'),
            'pass' => self::get('DB_PASS'),
            'port' => self::get('DB_PORT'),
            'charset' => 'utf8mb4'
        ];
    }
    
    /**
     * Get email configuration
     */
    public static function mail() {
        return [
            'driver' => self::get('MAIL_DRIVER', 'smtp'),
            'host' => self::get('MAIL_HOST'),
            'port' => self::get('MAIL_PORT', 587),
            'username' => self::get('MAIL_USERNAME'),
            'password' => self::get('MAIL_PASSWORD'),
            'encryption' => self::get('MAIL_ENCRYPTION', 'tls'),
            'from_address' => self::get('MAIL_FROM_ADDRESS'),
            'from_name' => self::get('MAIL_FROM_NAME', self::get('APP_NAME'))
        ];
    }
    
    /**
     * Check if application is in debug mode
     */
    public static function isDebug() {
        return self::get('APP_DEBUG', false);
    }
    
    /**
     * Check if application is in production
     */
    public static function isProduction() {
        return self::get('APP_ENV') === 'production';
    }
    
    /**
     * Get all configuration as array
     */
    public static function all() {
        self::load();
        return array_merge($_ENV, self::$config);
    }
}

// Auto-load configuration
Config::load();

// Set PHP error reporting based on environment
if (Config::isDebug()) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
    ini_set('display_errors', 0);
}

// Set timezone
date_default_timezone_set('Europe/Paris');

// Define application constants
define('APP_NAME', Config::get('APP_NAME'));
define('APP_VERSION', '1.0.0');
define('APP_URL', Config::get('APP_URL'));

// Database constants (for backward compatibility)
if (!defined('DB_HOST')) {
    define('DB_HOST', Config::get('DB_HOST'));
    define('DB_NAME', Config::get('DB_NAME'));
    define('DB_USER', Config::get('DB_USER'));
    define('DB_PASS', Config::get('DB_PASS'));
}
?>