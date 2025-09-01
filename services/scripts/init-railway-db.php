<?php
/**
 * Script d'initialisation de la base de données MySQL sur Railway
 * À exécuter une fois après avoir configuré MySQL sur Railway
 */

require_once __DIR__ . '/../config-railway.php';

echo "=== Initialisation de la base de données Culture Radar sur Railway ===\n\n";

// Test de connexion
echo "Test de connexion à MySQL...\n";
if (!testDatabaseConnection()) {
    die("❌ Impossible de se connecter à la base de données. Vérifiez vos variables d'environnement Railway.\n");
}
echo "✅ Connexion réussie!\n\n";

try {
    $pdo = getDatabaseConnection();
    
    // Création de la table users
    echo "Création de la table 'users'...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) UNIQUE NOT NULL,
            username VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            first_name VARCHAR(100),
            last_name VARCHAR(100),
            birth_date DATE,
            preferences JSON,
            role ENUM('user', 'admin', 'premium') DEFAULT 'user',
            subscription_type ENUM('free', 'premium') DEFAULT 'free',
            subscription_end_date DATE NULL,
            email_verified BOOLEAN DEFAULT FALSE,
            verification_token VARCHAR(255),
            reset_token VARCHAR(255),
            reset_token_expires DATETIME,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            last_login DATETIME,
            INDEX idx_email (email),
            INDEX idx_username (username)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Table 'users' créée\n\n";
    
    // Création de la table events
    echo "Création de la table 'events'...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS events (
            id INT AUTO_INCREMENT PRIMARY KEY,
            external_id VARCHAR(255) UNIQUE,
            source VARCHAR(50) NOT NULL,
            title VARCHAR(500) NOT NULL,
            description TEXT,
            short_description TEXT,
            category VARCHAR(100),
            subcategory VARCHAR(100),
            date_start DATETIME NOT NULL,
            date_end DATETIME,
            location_name VARCHAR(255),
            location_address TEXT,
            location_city VARCHAR(100),
            location_postal_code VARCHAR(20),
            location_lat DECIMAL(10, 8),
            location_lng DECIMAL(11, 8),
            price_min DECIMAL(10, 2),
            price_max DECIMAL(10, 2),
            price_info TEXT,
            image_url TEXT,
            thumbnail_url TEXT,
            booking_url TEXT,
            organizer_name VARCHAR(255),
            organizer_contact TEXT,
            tags JSON,
            accessibility JSON,
            audience VARCHAR(100),
            capacity INT,
            status ENUM('active', 'cancelled', 'postponed', 'sold_out') DEFAULT 'active',
            view_count INT DEFAULT 0,
            favorite_count INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_date_start (date_start),
            INDEX idx_category (category),
            INDEX idx_location_city (location_city),
            INDEX idx_status (status),
            FULLTEXT idx_search (title, description)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Table 'events' créée\n\n";
    
    // Création de la table user_favorites
    echo "Création de la table 'user_favorites'...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user_favorites (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            event_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
            UNIQUE KEY unique_favorite (user_id, event_id),
            INDEX idx_user_id (user_id),
            INDEX idx_event_id (event_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Table 'user_favorites' créée\n\n";
    
    // Création de la table user_events_history
    echo "Création de la table 'user_events_history'...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user_events_history (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            event_id INT NOT NULL,
            action ENUM('view', 'favorite', 'unfavorite', 'share', 'book') NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
            INDEX idx_user_action (user_id, action),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Table 'user_events_history' créée\n\n";
    
    // Création de la table recommendations
    echo "Création de la table 'recommendations'...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS recommendations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            event_id INT NOT NULL,
            score DECIMAL(5, 2) NOT NULL,
            reason JSON,
            shown BOOLEAN DEFAULT FALSE,
            clicked BOOLEAN DEFAULT FALSE,
            feedback ENUM('like', 'dislike') NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
            INDEX idx_user_score (user_id, score DESC),
            INDEX idx_shown (shown),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Table 'recommendations' créée\n\n";
    
    // Création de la table sessions (pour la gestion des sessions PHP)
    echo "Création de la table 'sessions'...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS sessions (
            id VARCHAR(128) PRIMARY KEY,
            user_id INT NULL,
            ip_address VARCHAR(45),
            user_agent TEXT,
            payload TEXT NOT NULL,
            last_activity INT NOT NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_last_activity (last_activity)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Table 'sessions' créée\n\n";
    
    // Création de la table api_logs
    echo "Création de la table 'api_logs'...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS api_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            source VARCHAR(50) NOT NULL,
            endpoint TEXT,
            method VARCHAR(10),
            status_code INT,
            response_time DECIMAL(10, 3),
            error_message TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_source (source),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Table 'api_logs' créée\n\n";
    
    // Création d'un utilisateur de démonstration
    echo "Création d'un utilisateur de démonstration...\n";
    $demoPassword = password_hash('demo123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("
        INSERT IGNORE INTO users (email, username, password, first_name, last_name, role, email_verified)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        'demo@culture-radar.fr',
        'demo',
        $demoPassword,
        'Demo',
        'User',
        'user',
        1
    ]);
    echo "✅ Utilisateur de démonstration créé (email: demo@culture-radar.fr, password: demo123)\n\n";
    
    // Statistiques
    echo "=== Statistiques de la base de données ===\n";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Nombre de tables créées: " . count($tables) . "\n";
    echo "Tables: " . implode(", ", $tables) . "\n\n";
    
    echo "🎉 Initialisation terminée avec succès!\n\n";
    echo "Variables d'environnement à configurer dans Railway:\n";
    echo "- OPENAGENDA_API_KEY\n";
    echo "- PARIS_OPEN_DATA_KEY\n";
    echo "- SERP_API_KEY (déjà configuré)\n";
    echo "- OPENWEATHER_API_KEY\n";
    
} catch (PDOException $e) {
    echo "❌ Erreur lors de la création des tables: " . $e->getMessage() . "\n";
    exit(1);
}
?>