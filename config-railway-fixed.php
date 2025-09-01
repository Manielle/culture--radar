<?php
// Railway configuration with proper database handling
class Config {
    private static $pdo = null;
    
    public static function getPDO() {
        if (self::$pdo === null) {
            self::$pdo = self::createConnection();
        }
        return self::$pdo;
    }
    
    private static function createConnection() {
        // Railway provides MYSQL_PUBLIC_URL for external connections
        $mysql_url = getenv('MYSQL_PUBLIC_URL');
        
        if ($mysql_url) {
            // Parse the MYSQL_PUBLIC_URL
            // Format: mysql://root:password@host:port/database
            $url_parts = parse_url($mysql_url);
            
            $host = $url_parts['host'] ?? 'centerbeam.proxy.rlwy.net';
            $port = $url_parts['port'] ?? '48330';
            $user = $url_parts['user'] ?? 'root';
            $pass = $url_parts['pass'] ?? 'tBixYXRKGkGAZuyxGHFZzaTxQAGXvJJH';
            $db = ltrim($url_parts['path'] ?? '/railway', '/');
        } else {
            // Fallback to individual environment variables
            $host = getenv('MYSQLHOST') ?: 'centerbeam.proxy.rlwy.net';
            $port = getenv('MYSQLPORT') ?: '48330';
            $user = getenv('MYSQLUSER') ?: 'root';
            $pass = getenv('MYSQLPASSWORD') ?: getenv('MYSQL_ROOT_PASSWORD') ?: 'tBixYXRKGkGAZuyxGHFZzaTxQAGXvJJH';
            $db = getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE') ?: 'railway';
        }
        
        try {
            $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
            
            error_log("Attempting database connection to: $host:$port");
            
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                PDO::ATTR_TIMEOUT => 5,
                PDO::ATTR_PERSISTENT => false
            ]);
            
            error_log("Database connection successful!");
            return $pdo;
            
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            error_log("Connection details - Host: $host, Port: $port, Database: $db, User: $user");
            
            // Return null instead of throwing to allow graceful degradation
            return null;
        }
    }
    
    public static function get($key, $default = null) {
        $configs = [
            'app.env' => getenv('APP_ENV') ?: 'production',
            'app.debug' => getenv('APP_DEBUG') === 'true',
            'app.url' => getenv('APP_URL') ?: 'https://ias-b3-g7-paris.up.railway.app',
            
            // API Keys
            'api.openagenda' => getenv('OPENAGENDA_API_KEY') ?: 'b6cea4ca5dcf4054ae4dd58482b389a1',
            'api.weather' => getenv('OPENWEATHERMAP_API_KEY') ?: '4f70ce6daf82c0e77d6128bc7fadf972',
            'api.google_maps' => getenv('GOOGLE_MAPS_API_KEY') ?: 'AIzaSyClDB39stO1egB2L1P3aRymydGjPZ72uNo',
            'api.serpapi' => getenv('SERPAPI_KEY') ?: 'b56aa6ec92f9f569f50f671e5133d46d5131c74c260086c37f5222bf489f2d4d',
            'api.ratp' => getenv('RATP_PRIM_API_KEY') ?: 'CNcmVauFV8dbo3sMWmemifQah7aopdsf',
        ];
        
        return $configs[$key] ?? $default;
    }
    
    public static function isRailway() {
        return getenv('RAILWAY_ENVIRONMENT') !== false || 
               getenv('MYSQL_PUBLIC_URL') !== false;
    }
    
    public static function checkDatabaseConnection() {
        try {
            $pdo = self::getPDO();
            if ($pdo) {
                $stmt = $pdo->query("SELECT 1");
                return true;
            }
        } catch (Exception $e) {
            error_log("Database check failed: " . $e->getMessage());
        }
        return false;
    }
}

// Initialize database connection on Railway
if (Config::isRailway()) {
    error_log("Running on Railway environment");
    
    // Check if database is accessible
    if (!Config::checkDatabaseConnection()) {
        error_log("Warning: Database connection not available");
    }
}