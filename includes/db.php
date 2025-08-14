<?php
/**
 * Database connection helper for Culture Radar
 * Provides a reusable PDO connection
 */

require_once dirname(__DIR__) . '/config.php';

class Database {
    private static $instance = null;
    private $pdo = null;
    
    private function __construct() {
        try {
            $dbConfig = Config::database();
            
            // Parse host and port
            $host = $dbConfig['host'];
            $port = $dbConfig['port'];
            
            if (strpos($host, ':') !== false) {
                list($host, $port) = explode(':', $host);
            }
            
            $dsn = "mysql:host=$host;port=$port;dbname={$dbConfig['name']};charset={$dbConfig['charset']}";
            
            $this->pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            // If database doesn't exist, try to create it
            if (strpos($e->getMessage(), 'Unknown database') !== false) {
                $this->createDatabase($dbConfig, $host, $port);
            } else {
                throw $e;
            }
        }
    }
    
    private function createDatabase($dbConfig, $host, $port) {
        try {
            // Connect without database
            $dsn = "mysql:host=$host;port=$port;charset={$dbConfig['charset']}";
            $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create database
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbConfig['name']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // Reconnect with database
            $dsn = "mysql:host=$host;port=$port;dbname={$dbConfig['name']};charset={$dbConfig['charset']}";
            $this->pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // Run setup script
            $this->initializeTables();
            
        } catch(PDOException $e) {
            error_log("Database creation failed: " . $e->getMessage());
            throw $e;
        }
    }
    
    private function initializeTables() {
        // Create essential tables if they don't exist
        $queries = [
            // Users table
            "CREATE TABLE IF NOT EXISTS users (
                id INT PRIMARY KEY AUTO_INCREMENT,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                accepts_newsletter BOOLEAN DEFAULT FALSE,
                is_active BOOLEAN DEFAULT TRUE,
                onboarding_completed BOOLEAN DEFAULT FALSE,
                last_login TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_email (email),
                INDEX idx_active (is_active)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            // User profiles table
            "CREATE TABLE IF NOT EXISTS user_profiles (
                id INT PRIMARY KEY AUTO_INCREMENT,
                user_id INT NOT NULL,
                preferences JSON DEFAULT NULL,
                location VARCHAR(255) DEFAULT '',
                budget_max DECIMAL(8,2) DEFAULT 0.00,
                notification_settings JSON DEFAULT NULL,
                onboarding_completed BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_user_id (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            // Events table
            "CREATE TABLE IF NOT EXISTS events (
                id INT PRIMARY KEY AUTO_INCREMENT,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                category VARCHAR(100) NOT NULL,
                venue_name VARCHAR(255),
                address TEXT,
                city VARCHAR(100),
                postal_code VARCHAR(20),
                latitude DECIMAL(10, 8) NULL,
                longitude DECIMAL(11, 8) NULL,
                start_date DATETIME NOT NULL,
                end_date DATETIME NULL,
                price DECIMAL(8,2) DEFAULT 0.00,
                is_free BOOLEAN DEFAULT FALSE,
                image_url VARCHAR(500),
                external_url VARCHAR(500),
                organizer_id INT NULL,
                is_active BOOLEAN DEFAULT TRUE,
                featured BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_category (category),
                INDEX idx_city (city),
                INDEX idx_start_date (start_date),
                INDEX idx_active (is_active)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            // Venues table
            "CREATE TABLE IF NOT EXISTS venues (
                id INT PRIMARY KEY AUTO_INCREMENT,
                name VARCHAR(255) NOT NULL,
                description TEXT,
                address TEXT,
                city VARCHAR(100),
                postal_code VARCHAR(20),
                latitude DECIMAL(10, 8) NULL,
                longitude DECIMAL(11, 8) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_city (city)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        ];
        
        foreach ($queries as $query) {
            try {
                $this->pdo->exec($query);
            } catch(PDOException $e) {
                error_log("Table creation error: " . $e->getMessage());
            }
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->pdo;
    }
    
    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch(PDOException $e) {
            error_log("Query error: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    public function execute($sql, $params = []) {
        return $this->query($sql, $params);
    }
    
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
}

// Create global database function for easy access
function db() {
    return Database::getInstance()->getConnection();
}

// Helper function to get database instance
function getDb() {
    return Database::getInstance();
}