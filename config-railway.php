<?php
/**
 * Configuration Railway pour Culture Radar
 * Détection automatique de l'environnement Railway et configuration MySQL
 */

// Détection de l'environnement Railway
$isRailway = getenv('RAILWAY_ENVIRONMENT') !== false || 
             getenv('RAILWAY_PUBLIC_DOMAIN') !== false ||
             getenv('MYSQLHOST') !== false;

if ($isRailway) {
    // Configuration Railway MySQL
    define('DB_HOST', getenv('MYSQLHOST'));
    define('DB_NAME', getenv('MYSQLDATABASE'));
    define('DB_USER', getenv('MYSQLUSER'));
    define('DB_PASS', getenv('MYSQLPASSWORD'));
    define('DB_PORT', getenv('MYSQLPORT'));
    
    // URL de l'application
    $railwayDomain = getenv('RAILWAY_PUBLIC_DOMAIN');
    define('APP_URL', $railwayDomain ? "https://{$railwayDomain}" : 'http://localhost');
    define('APP_ENV', 'production');
    define('APP_DEBUG', false);
} else {
    // Configuration locale (MAMP/XAMPP)
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'culture_radar');
    define('DB_USER', 'root');
    define('DB_PASS', 'root');
    define('DB_PORT', '8889'); // Port MAMP par défaut
    
    define('APP_URL', 'http://localhost:8888');
    define('APP_ENV', 'development');
    define('APP_DEBUG', true);
}

// Configuration commune
define('APP_NAME', 'Culture Radar');
define('APP_VERSION', '1.0.0');

// Configuration des clés API
define('OPENAGENDA_API_KEY', getenv('OPENAGENDA_API_KEY') ?: '');
define('PARIS_OPEN_DATA_KEY', getenv('PARIS_OPEN_DATA_KEY') ?: '');
define('SERP_API_KEY', getenv('SERP_API_KEY') ?: 'b56aa6ec92f9f569f50f671e5133d46d5131c74c260086c37f5222bf489f2d4d');
define('OPENWEATHER_API_KEY', getenv('OPENWEATHER_API_KEY') ?: '');

// Configuration des sessions
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', $isRailway ? 1 : 0);
ini_set('session.use_strict_mode', 1);

// Configuration du fuseau horaire
date_default_timezone_set('Europe/Paris');

// Configuration des erreurs
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
    ini_set('display_errors', 0);
}
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error.log');

// Fonction de connexion PDO
function getDatabaseConnection() {
    try {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            DB_HOST,
            DB_PORT,
            DB_NAME
        );
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        
        // Configuration UTF8MB4
        $pdo->exec("SET NAMES utf8mb4");
        $pdo->exec("SET CHARACTER SET utf8mb4");
        $pdo->exec("SET COLLATION_CONNECTION = 'utf8mb4_unicode_ci'");
        
        return $pdo;
    } catch (PDOException $e) {
        if (APP_DEBUG) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        } else {
            error_log("Database connection failed: " . $e->getMessage());
            die("Erreur de connexion à la base de données. Veuillez réessayer plus tard.");
        }
    }
}

// Fonction pour tester la connexion
function testDatabaseConnection() {
    try {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->query("SELECT 1");
        return $stmt !== false;
    } catch (Exception $e) {
        return false;
    }
}

// Créer les répertoires nécessaires
$directories = [
    __DIR__ . '/logs',
    __DIR__ . '/uploads',
    __DIR__ . '/cache'
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Afficher les informations de debug si nécessaire
if (APP_DEBUG && isset($_GET['debug'])) {
    echo "<pre>";
    echo "=== Configuration Railway ===\n";
    echo "Is Railway: " . ($isRailway ? 'Yes' : 'No') . "\n";
    echo "DB_HOST: " . DB_HOST . "\n";
    echo "DB_NAME: " . DB_NAME . "\n";
    echo "DB_PORT: " . DB_PORT . "\n";
    echo "APP_URL: " . APP_URL . "\n";
    echo "Database Connection: " . (testDatabaseConnection() ? 'OK' : 'FAILED') . "\n";
    echo "</pre>";
}
?>