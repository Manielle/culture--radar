<?php
// Configuration minimale pour Railway
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Base de données MySQL Railway
define('DB_HOST', getenv('MYSQLHOST') ?: 'localhost');
define('DB_NAME', getenv('MYSQLDATABASE') ?: 'culture_radar');
define('DB_USER', getenv('MYSQLUSER') ?: 'root');
define('DB_PASS', getenv('MYSQLPASSWORD') ?: '');
define('DB_PORT', getenv('MYSQLPORT') ?: '3306');

// URLs
define('SITE_URL', 'https://ias-b3-g7-paris.up.railway.app');
define('BASE_URL', 'https://ias-b3-g7-paris.up.railway.app');
define('APP_URL', 'https://ias-b3-g7-paris.up.railway.app');

// Configuration de base
define('DEBUG_MODE', false);
define('APP_NAME', 'Culture Radar');
define('APP_ENV', 'production');

// Clés API (vides pour l''instant)
define('OPENWEATHER_API_KEY', '');
define('GOOGLE_MAPS_API_KEY', '');
define('OPENAGENDA_API_KEY', '');
define('TICKETMASTER_API_KEY', '');

// Timezone
date_default_timezone_set('Europe/Paris');

// Erreurs
error_reporting(0);
ini_set('display_errors', 0);

// Classe Config pour compatibilité
class Config {
    public static function get($key, $default = null) {
        if (defined($key)) {
            return constant($key);
        }
        return getenv($key) ?: $default;
    }
    
    public static function getPDO() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            return new PDO($dsn, DB_USER, DB_PASS);
        } catch (PDOException $e) {
            return null;
        }
    }
}
?>