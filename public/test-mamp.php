<?php
// Test rapide pour MAMP
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test Configuration MAMP pour Culture Radar</h1>";

// Configurations MAMP possibles
$configs = [
    ['host' => 'localhost', 'port' => '8889', 'user' => 'root', 'pass' => 'root'],
    ['host' => 'localhost', 'port' => '3306', 'user' => 'root', 'pass' => 'root'],
    ['host' => '127.0.0.1', 'port' => '8889', 'user' => 'root', 'pass' => 'root'],
    ['host' => 'localhost', 'port' => '3306', 'user' => 'root', 'pass' => '']
];

$working_config = null;

echo "<h2>Test des configurations:</h2>";
foreach ($configs as $config) {
    echo "<p>Test {$config['host']}:{$config['port']} ... ";
    try {
        $dsn = "mysql:host={$config['host']};port={$config['port']}";
        $pdo = new PDO($dsn, $config['user'], $config['pass']);
        echo "<span style='color:green'>✓ FONCTIONNE!</span></p>";
        $working_config = $config;
        break;
    } catch (PDOException $e) {
        echo "<span style='color:red'>✗ Échec</span></p>";
    }
}

if ($working_config) {
    echo "<h2 style='color:green'>Configuration trouvée!</h2>";
    echo "<pre>";
    echo "Host: {$working_config['host']}\n";
    echo "Port: {$working_config['port']}\n";
    echo "User: {$working_config['user']}\n";
    echo "Pass: {$working_config['pass']}\n";
    echo "</pre>";
    
    // Test création/accès base culture_radar
    try {
        $dsn = "mysql:host={$working_config['host']};port={$working_config['port']}";
        $pdo = new PDO($dsn, $working_config['user'], $working_config['pass']);
        
        // Créer la base si elle n'existe pas
        $pdo->exec("CREATE DATABASE IF NOT EXISTS culture_radar CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "<p style='color:green'>✓ Base de données 'culture_radar' créée/vérifiée</p>";
        
        // Se connecter à la base
        $dsn = "mysql:host={$working_config['host']};port={$working_config['port']};dbname=culture_radar";
        $pdo = new PDO($dsn, $working_config['user'], $working_config['pass']);
        
        // Créer les tables essentielles si elles n'existent pas
        $tables = [
            "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                role ENUM('user', 'organizer', 'admin') DEFAULT 'user',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )",
            "CREATE TABLE IF NOT EXISTS events (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                category VARCHAR(50),
                city VARCHAR(100),
                start_date DATETIME,
                end_date DATETIME,
                price DECIMAL(10,2) DEFAULT 0,
                is_free BOOLEAN DEFAULT FALSE,
                venue_name VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )",
            "CREATE TABLE IF NOT EXISTS user_profiles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT UNIQUE,
                location VARCHAR(100),
                preferences JSON,
                onboarding_completed BOOLEAN DEFAULT FALSE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )"
        ];
        
        foreach ($tables as $sql) {
            $pdo->exec($sql);
        }
        echo "<p style='color:green'>✓ Tables essentielles créées/vérifiées</p>";
        
        // Ajouter un utilisateur de test si aucun n'existe
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        $count = $stmt->fetchColumn();
        
        if ($count == 0) {
            $pdo->exec("INSERT INTO users (name, email, password, role) VALUES 
                ('Test User', 'test@culture-radar.com', '" . password_hash('test123', PASSWORD_DEFAULT) . "', 'user'),
                ('Admin', 'admin@culture-radar.com', '" . password_hash('admin123', PASSWORD_DEFAULT) . "', 'admin')");
            echo "<p style='color:green'>✓ Utilisateurs de test créés</p>";
            echo "<p>Connexion test: test@culture-radar.com / test123</p>";
            echo "<p>Connexion admin: admin@culture-radar.com / admin123</p>";
        }
        
        // Ajouter des événements de démo
        $stmt = $pdo->query("SELECT COUNT(*) FROM events");
        $count = $stmt->fetchColumn();
        
        if ($count == 0) {
            $events = [
                ['Concert Jazz au Sunset', 'Soirée jazz exceptionnelle', 'Musique', 'Paris', '2025-02-01 20:00:00', 25],
                ['Exposition Art Moderne', 'Découvrez les nouveaux talents', 'Art', 'Paris', '2025-02-02 10:00:00', 0],
                ['Théâtre: Hamlet', 'La pièce classique de Shakespeare', 'Théâtre', 'Paris', '2025-02-03 19:30:00', 35],
                ['Festival Street Art', 'Art urbain dans le Marais', 'Art', 'Paris', '2025-02-04 14:00:00', 0],
                ['Opéra Carmen', 'Le chef-d\'œuvre de Bizet', 'Musique', 'Paris', '2025-02-05 20:00:00', 45]
            ];
            
            foreach ($events as $event) {
                $sql = "INSERT INTO events (title, description, category, city, start_date, price, is_free) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$event[0], $event[1], $event[2], $event[3], $event[4], $event[5], $event[5] == 0]);
            }
            echo "<p style='color:green'>✓ Événements de démo créés</p>";
        }
        
        echo "<h2 style='color:green'>✅ TOUT EST PRÊT POUR LA DÉMO!</h2>";
        
        // Créer/mettre à jour le fichier de config
        $config_content = "<?php
// Configuration MAMP pour Culture Radar
// Généré automatiquement

putenv('DB_HOST={$working_config['host']}');
putenv('DB_PORT={$working_config['port']}');
putenv('DB_NAME=culture_radar');
putenv('DB_USER={$working_config['user']}');
putenv('DB_PASS={$working_config['pass']}');

// Pour éviter les erreurs
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 0);
";
        
        file_put_contents(__DIR__ . '/mamp-config.php', $config_content);
        echo "<p style='color:green'>✓ Configuration sauvegardée dans mamp-config.php</p>";
        
        echo "<h3>Pages à tester:</h3>";
        echo "<ul>";
        echo "<li><a href='index.php'>Page d'accueil</a></li>";
        echo "<li><a href='login.php'>Connexion</a></li>";
        echo "<li><a href='register.php'>Inscription</a></li>";
        echo "<li><a href='discover.php'>Découvrir</a></li>";
        echo "<li><a href='explore.php'>Explorer</a></li>";
        echo "</ul>";
        
    } catch (PDOException $e) {
        echo "<p style='color:red'>Erreur: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<h2 style='color:red'>❌ Aucune configuration MAMP trouvée!</h2>";
    echo "<p>Vérifiez que MAMP est démarré et que MySQL est actif.</p>";
}
?>