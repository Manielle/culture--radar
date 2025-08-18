<?php
/**
 * Configuration automatique pour Railway
 * Utilise les variables auto-injectées quand MySQL est lié au Web Service
 */

// Railway injecte automatiquement MYSQL_URL quand les services sont liés
if (getenv('MYSQL_URL')) {
    // On est sur Railway avec MySQL connecté !
    $mysql_url = getenv('MYSQL_URL');
    
    // Parser l'URL MySQL pour extraire les composants
    $url = parse_url($mysql_url);
    
    define('DB_HOST', $url['host']);
    define('DB_PORT', $url['port'] ?? 3306);
    define('DB_USER', $url['user']);
    define('DB_PASS', $url['pass']);
    define('DB_NAME', ltrim($url['path'], '/'));
    
    // Mode production
    define('APP_ENV', 'production');
    define('APP_DEBUG', false);
    
} else {
    // Configuration locale (MAMP/XAMPP)
    define('DB_HOST', 'localhost');
    define('DB_PORT', '8889');
    define('DB_USER', 'root');
    define('DB_PASS', 'root');
    define('DB_NAME', 'culture_radar');
    
    // Mode développement
    define('APP_ENV', 'development');
    define('APP_DEBUG', true);
}

// Fonction de connexion ultra simple
function getDB() {
    try {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            DB_HOST,
            DB_PORT,
            DB_NAME
        );
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
        
        return $pdo;
    } catch (PDOException $e) {
        if (APP_DEBUG) {
            die("Erreur de connexion : " . $e->getMessage());
        } else {
            die("Erreur de connexion à la base de données.");
        }
    }
}

// Test rapide de connexion
function testConnection() {
    try {
        $pdo = getDB();
        $result = $pdo->query("SELECT 1")->fetch();
        return $result !== false;
    } catch (Exception $e) {
        return false;
    }
}
?>