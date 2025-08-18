<?php
/**
 * Script de debug pour Railway
 * Teste la connexion MySQL et affiche les variables d'environnement
 */

header('Content-Type: text/plain');

echo "=== RAILWAY DEBUG ===\n\n";

// 1. Vérifier les variables d'environnement MySQL
echo "1. Variables d'environnement MySQL:\n";
echo "-----------------------------------\n";
$mysqlVars = [
    'MYSQL_URL',
    'MYSQLHOST', 
    'MYSQLPORT',
    'MYSQLUSER',
    'MYSQLPASSWORD',
    'MYSQLDATABASE',
    'RAILWAY_ENVIRONMENT',
    'RAILWAY_PUBLIC_DOMAIN'
];

foreach ($mysqlVars as $var) {
    $value = getenv($var);
    if ($value) {
        // Masquer les mots de passe
        if (strpos($var, 'PASSWORD') !== false) {
            echo $var . ": ***" . substr($value, -4) . "\n";
        } else {
            echo $var . ": " . $value . "\n";
        }
    } else {
        echo $var . ": [NOT SET]\n";
    }
}

echo "\n2. Test de connexion MySQL:\n";
echo "----------------------------\n";

// Essayer avec MYSQL_URL si disponible
if (getenv('MYSQL_URL')) {
    try {
        $pdo = new PDO(getenv('MYSQL_URL'));
        echo "✅ Connexion avec MYSQL_URL réussie!\n";
        
        // Tester une requête simple
        $stmt = $pdo->query("SELECT VERSION() as version");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "MySQL Version: " . $result['version'] . "\n";
        
        // Lister les tables
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        echo "Tables trouvées: " . count($tables) . "\n";
        if (count($tables) > 0) {
            echo "Liste des tables: " . implode(", ", $tables) . "\n";
        }
        
    } catch (PDOException $e) {
        echo "❌ Erreur de connexion avec MYSQL_URL:\n";
        echo $e->getMessage() . "\n";
    }
} else {
    // Essayer avec les variables individuelles
    if (getenv('MYSQLHOST')) {
        try {
            $host = getenv('MYSQLHOST');
            $port = getenv('MYSQLPORT') ?: '3306';
            $db = getenv('MYSQLDATABASE');
            $user = getenv('MYSQLUSER');
            $pass = getenv('MYSQLPASSWORD');
            
            $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
            echo "Tentative de connexion avec DSN: $dsn\n";
            
            $pdo = new PDO($dsn, $user, $pass);
            echo "✅ Connexion avec variables individuelles réussie!\n";
            
        } catch (PDOException $e) {
            echo "❌ Erreur de connexion:\n";
            echo $e->getMessage() . "\n";
        }
    } else {
        echo "❌ Aucune variable MySQL détectée!\n";
        echo "Assurez-vous que les services sont liés dans Railway.\n";
    }
}

echo "\n3. Configuration PHP:\n";
echo "---------------------\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Extensions chargées:\n";
echo "- PDO: " . (extension_loaded('pdo') ? '✅' : '❌') . "\n";
echo "- PDO MySQL: " . (extension_loaded('pdo_mysql') ? '✅' : '❌') . "\n";
echo "- MySQLi: " . (extension_loaded('mysqli') ? '✅' : '❌') . "\n";

echo "\n4. Fichiers de configuration:\n";
echo "-----------------------------\n";
$configFiles = [
    'config.php',
    'config-railway.php', 
    'config-railway-fix.php',
    'config-auto.php'
];

foreach ($configFiles as $file) {
    echo $file . ": " . (file_exists($file) ? "✅ Existe" : "❌ Manquant") . "\n";
}

echo "\n=== FIN DU DEBUG ===\n";
?>