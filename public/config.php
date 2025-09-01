<?php
/**
 * Configuration file for Culture Radar
 * Handles both local and Railway deployment environments
 */

// Error reporting (disable in production)
if (getenv('ENVIRONMENT') === 'production' || getenv('RAILWAY_ENVIRONMENT')) {
    error_reporting(0);
    ini_set('display_errors', 0);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Load environment variables from .env file if it exists
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Database configuration
class Config {
    // Railway provides MYSQL variables
    public static function getDatabaseConfig() {
        // Check for Railway MySQL variables first
        if (getenv('MYSQL_URL')) {
            $url = parse_url(getenv('MYSQL_URL'));
            return [
                'host' => $url['host'] ?? 'localhost',
                'port' => $url['port'] ?? 3306,
                'dbname' => ltrim($url['path'] ?? '', '/') ?: 'culture_radar',
                'username' => $url['user'] ?? 'root',
                'password' => $url['pass'] ?? '',
                'charset' => 'utf8mb4'
            ];
        }
        
        // Railway individual variables
        if (getenv('MYSQLHOST')) {
            return [
                'host' => getenv('MYSQLHOST'),
                'port' => getenv('MYSQLPORT') ?: 3306,
                'dbname' => getenv('MYSQLDATABASE') ?: 'culture_radar',
                'username' => getenv('MYSQLUSER') ?: 'root',
                'password' => getenv('MYSQLPASSWORD') ?: '',
                'charset' => 'utf8mb4'
            ];
        }
        
        // Fallback to standard environment variables
        return [
            'host' => getenv('DB_HOST') ?: 'localhost',
            'port' => getenv('DB_PORT') ?: 3306,
            'dbname' => getenv('DB_NAME') ?: 'culture_radar',
            'username' => getenv('DB_USER') ?: 'root',
            'password' => getenv('DB_PASSWORD') ?: '',
            'charset' => 'utf8mb4'
        ];
    }
    
    public static function getPDO() {
        $config = self::getDatabaseConfig();
        
        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $config['host'],
                $config['port'],
                $config['dbname'],
                $config['charset']
            );
            
            $pdo = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]
            );
            
            return $pdo;
        } catch (PDOException $e) {
            // Log error but don't expose database details
            error_log('Database connection error: ' . $e->getMessage());
            
            // In production, show generic error
            if (getenv('ENVIRONMENT') === 'production' || getenv('RAILWAY_ENVIRONMENT')) {
                die('Service temporarily unavailable. Please try again later.');
            } else {
                die('Database connection failed: ' . $e->getMessage());
            }
        }
    }
    
    // API Keys
    public static function getApiKey($service) {
        $keys = [
            'openweather' => getenv('OPENWEATHER_API_KEY') ?: '4f70ce6daf82c0e77d6128bc7fadf972',
            'mapbox' => getenv('MAPBOX_API_KEY') ?: '',
            'google_maps' => getenv('GOOGLE_MAPS_API_KEY') ?: '',
            'ticketmaster' => getenv('TICKETMASTER_API_KEY') ?: '',
            'eventbrite' => getenv('EVENTBRITE_API_KEY') ?: '',
            'paris_api' => getenv('PARIS_API_KEY') ?: ''
        ];
        
        return $keys[$service] ?? '';
    }
    
    // Base URL configuration
    public static function getBaseUrl() {
        if (getenv('RAILWAY_STATIC_URL')) {
            return 'https://' . getenv('RAILWAY_STATIC_URL');
        }
        
        if (getenv('BASE_URL')) {
            return getenv('BASE_URL');
        }
        
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $protocol . '://' . $host;
    }
    
    // Cache directory
    public static function getCacheDir() {
        $cacheDir = __DIR__ . '/cache';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }
        return $cacheDir;
    }
    
    // Session configuration
    public static function initSession() {
        if (session_status() === PHP_SESSION_NONE) {
            // Set session cookie parameters
            session_set_cookie_params([
                'lifetime' => 86400, // 24 hours
                'path' => '/',
                'domain' => '',
                'secure' => (getenv('RAILWAY_ENVIRONMENT') || getenv('ENVIRONMENT') === 'production'),
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            
            session_start();
        }
    }
}

// Auto-initialize session
Config::initSession();

// Set timezone
date_default_timezone_set('Europe/Paris');

// Set locale
setlocale(LC_ALL, 'fr_FR.UTF-8', 'fr_FR', 'french');
?>