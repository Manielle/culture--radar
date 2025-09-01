<?php
// Simple index to test if PHP is working at all
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Culture Radar - Test Simple</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            max-width: 800px;
            width: 100%;
        }
        h1 { margin-top: 0; }
        .info {
            background: rgba(0, 0, 0, 0.2);
            padding: 15px;
            border-radius: 10px;
            margin: 10px 0;
        }
        .success { color: #10b981; }
        .error { color: #ef4444; }
        a { color: #60a5fa; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧭 Culture Radar - Page de Test Simple</h1>
        
        <div class="info">
            <h2>État du Serveur</h2>
            <p class="success">✅ PHP fonctionne: Version <?php echo PHP_VERSION; ?></p>
            <p>📅 Date/Heure: <?php echo date('Y-m-d H:i:s'); ?></p>
            <p>🖥️ Serveur: <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></p>
            <p>🔗 URL: <?php echo $_SERVER['HTTP_HOST'] ?? 'Unknown'; ?></p>
        </div>

        <div class="info">
            <h2>Variables Railway</h2>
            <?php
            $port = getenv('PORT') ?: $_SERVER['SERVER_PORT'] ?? '80';
            echo "<p>Port: $port</p>";
            
            if (getenv('RAILWAY_ENVIRONMENT')) {
                echo '<p class="success">✅ Environnement Railway détecté: ' . getenv('RAILWAY_ENVIRONMENT') . '</p>';
            } else {
                echo '<p class="error">⚠️ Pas dans un environnement Railway</p>';
            }
            ?>
        </div>

        <div class="info">
            <h2>Test MySQL (Sans Config Complexe)</h2>
            <?php
            // Test direct MySQL connection without complex config
            $mysql_url = getenv('MYSQL_URL');
            $mysql_host = getenv('MYSQLHOST');
            
            if ($mysql_url) {
                echo '<p class="success">✅ MYSQL_URL trouvé</p>';
                $parsed = parse_url($mysql_url);
                echo '<p>Host: ' . ($parsed['host'] ?? 'unknown') . '</p>';
                echo '<p>Database: ' . ltrim($parsed['path'] ?? '', '/') . '</p>';
            } elseif ($mysql_host) {
                echo '<p class="success">✅ MYSQLHOST trouvé: ' . $mysql_host . '</p>';
                echo '<p>Database: ' . (getenv('MYSQLDATABASE') ?: 'non défini') . '</p>';
            } else {
                echo '<p class="error">❌ Aucune configuration MySQL trouvée</p>';
                echo '<p>Variables disponibles:</p>';
                echo '<pre>';
                foreach ($_ENV as $key => $value) {
                    if (stripos($key, 'MYSQL') !== false || stripos($key, 'DATABASE') !== false) {
                        echo "$key = " . substr($value, 0, 20) . "...\n";
                    }
                }
                echo '</pre>';
            }
            ?>
        </div>

        <div class="info">
            <h2>Informations PHP</h2>
            <p><a href="<?php echo $_SERVER['PHP_SELF']; ?>?phpinfo=1">Voir phpinfo()</a></p>
            <?php
            if (isset($_GET['phpinfo'])) {
                phpinfo();
            }
            ?>
        </div>

        <div class="info">
            <h2>Navigation</h2>
            <p>
                <a href="/test.php">Test Complet</a> | 
                <a href="/health.php">Health Check JSON</a> | 
                <a href="/index.php">Index Original</a>
            </p>
        </div>
    </div>
</body>
</html>