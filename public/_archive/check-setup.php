<?php
/**
 * Check Culture Radar Setup
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>Culture Radar - Check Setup</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .success { color: green; }
        .error { color: red; }
        .info { background: #f0f0f0; padding: 10px; margin: 10px 0; }
        pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Culture Radar - Vérification Installation</h1>
    
    <h2>1. Informations PHP</h2>
    <div class="info">
        PHP Version: <?php echo phpversion(); ?><br>
        Server: <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?><br>
        Document Root: <?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?><br>
        Current File: <?php echo __FILE__; ?><br>
        Current Dir: <?php echo __DIR__; ?>
    </div>
    
    <h2>2. Test Connexion MySQL</h2>
    <?php
    $configs = [
        ['host' => 'localhost', 'port' => '8889'],
        ['host' => '127.0.0.1', 'port' => '8889'],
        ['host' => 'localhost:8889', 'port' => null],
    ];
    
    foreach ($configs as $config) {
        echo "<div class='info'>";
        echo "<strong>Test avec host={$config['host']}" . ($config['port'] ? ", port={$config['port']}" : "") . "</strong><br>";
        
        try {
            if ($config['port']) {
                $dsn = "mysql:host={$config['host']};port={$config['port']};dbname=culture_radar;charset=utf8";
            } else {
                $dsn = "mysql:host={$config['host']};dbname=culture_radar;charset=utf8";
            }
            
            $pdo = new PDO($dsn, 'root', 'root');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            echo "<span class='success'>✅ Connexion réussie!</span><br>";
            
            // Get some info
            $result = $pdo->query("SELECT VERSION() as version");
            $row = $result->fetch(PDO::FETCH_ASSOC);
            echo "MySQL Version: " . $row['version'] . "<br>";
            
            // Count tables
            $result = $pdo->query("SHOW TABLES");
            $tables = $result->fetchAll(PDO::FETCH_COLUMN);
            echo "Tables dans culture_radar: " . count($tables) . "<br>";
            
            echo "<strong>👍 UTILISEZ CETTE CONFIGURATION!</strong>";
            break;
            
        } catch (PDOException $e) {
            echo "<span class='error'>❌ Erreur: " . $e->getMessage() . "</span>";
        }
        echo "</div>";
    }
    ?>
    
    <h2>3. Fichiers de configuration</h2>
    <div class="info">
        <?php
        $files = [
            '.env' => file_exists(__DIR__ . '/.env'),
            'config.php' => file_exists(__DIR__ . '/config.php'),
            'mamp-config.php' => file_exists(__DIR__ . '/mamp-config.php'),
            'index.php' => file_exists(__DIR__ . '/index.php'),
            'dashboard.php' => file_exists(__DIR__ . '/dashboard.php'),
        ];
        
        foreach ($files as $file => $exists) {
            echo $file . ": " . ($exists ? "<span class='success'>✅ Existe</span>" : "<span class='error'>❌ Manquant</span>") . "<br>";
        }
        ?>
    </div>
    
    <h2>4. Configuration .env actuelle</h2>
    <?php if (file_exists(__DIR__ . '/.env')): ?>
    <pre><?php 
        $env = file_get_contents(__DIR__ . '/.env');
        // Hide passwords
        $env = preg_replace('/^(.*_PASS(?:WORD)?=)(.*)$/m', '$1***', $env);
        $env = preg_replace('/^(.*_KEY=)(.*)$/m', '$1***', $env);
        echo htmlspecialchars($env);
    ?></pre>
    <?php else: ?>
    <p class="error">Fichier .env non trouvé!</p>
    <?php endif; ?>
    
    <h2>5. Liens utiles</h2>
    <ul>
        <li><a href="index.php">Page d'accueil</a></li>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="login.php">Login</a></li>
        <li><a href="explore.php">Explorer</a></li>
        <li><a href="http://localhost:8888/phpMyAdmin">phpMyAdmin</a></li>
    </ul>
</body>
</html>