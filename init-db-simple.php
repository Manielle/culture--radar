<?php
/**
 * Script simple d'initialisation de la base de données pour Railway
 */

echo "Initialisation de la base de données Culture Radar...\n\n";

// Connexion avec MYSQL_URL
$mysql_url = getenv('MYSQL_URL');
if (!$mysql_url) {
    die("❌ MYSQL_URL non trouvée. Assurez-vous que MySQL est lié à votre service.\n");
}

try {
    $pdo = new PDO($mysql_url);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connexion à MySQL réussie!\n\n";
    
    // Créer la table users si elle n'existe pas
    echo "Création de la table users...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) UNIQUE NOT NULL,
            name VARCHAR(100),
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "✅ Table users créée/vérifiée\n\n";
    
    // Créer un utilisateur de test
    echo "Création de l'utilisateur de test...\n";
    $testPassword = password_hash('password123', PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("
        INSERT IGNORE INTO users (email, name, password) 
        VALUES (?, ?, ?)
    ");
    $stmt->execute(['test@culture-radar.fr', 'Utilisateur Test', $testPassword]);
    
    // Créer un deuxième utilisateur
    $demoPassword = password_hash('demo123', PASSWORD_DEFAULT);
    $stmt->execute(['demo@culture-radar.fr', 'Demo User', $demoPassword]);
    
    echo "✅ Utilisateurs créés:\n";
    echo "   - test@culture-radar.fr / password123\n";
    echo "   - demo@culture-radar.fr / demo123\n\n";
    
    // Vérifier les utilisateurs
    $count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    echo "Total utilisateurs dans la base: $count\n\n";
    
    echo "🎉 Initialisation terminée avec succès!\n";
    
} catch (PDOException $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    exit(1);
}
?>