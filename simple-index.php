<?php
// Simple index for Railway testing
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Culture Radar - Test</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            color: white;
        }
        .container {
            text-align: center;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        h1 {
            margin: 0 0 1rem 0;
            font-size: 3rem;
        }
        .info {
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 10px;
            margin: 1rem 0;
        }
        .success {
            color: #4ade80;
        }
        .error {
            color: #f87171;
        }
        a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
            display: inline-block;
            margin: 0.5rem;
        }
        a:hover {
            background: rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎭 Culture Radar</h1>
        <p>Site de découverte culturelle</p>
        
        <div class="info">
            <h2>État du système</h2>
            <p class="success">✅ PHP <?php echo phpversion(); ?> fonctionne</p>
            
            <?php
            // Test config file
            if (file_exists('config.php')) {
                echo '<p class="success">✅ config.php trouvé</p>';
                require_once 'config.php';
                if (defined('APP_NAME')) {
                    echo '<p class="success">✅ Configuration chargée</p>';
                }
            } else {
                echo '<p class="error">❌ config.php manquant</p>';
            }
            
            // Test database connection (optional)
            $dbConnected = false;
            if (getenv('MYSQLHOST')) {
                try {
                    $pdo = new PDO(
                        'mysql:host=' . getenv('MYSQLHOST') . ';dbname=' . getenv('MYSQLDATABASE'),
                        getenv('MYSQLUSER'),
                        getenv('MYSQLPASSWORD')
                    );
                    $dbConnected = true;
                    echo '<p class="success">✅ Base de données connectée</p>';
                } catch (Exception $e) {
                    echo '<p class="error">⚠️ Base de données non connectée (optionnel)</p>';
                }
            } else {
                echo '<p>ℹ️ Mode sans base de données</p>';
            }
            ?>
        </div>
        
        <div class="info">
            <h2>Pages disponibles</h2>
            <a href="/index.php">Page d'accueil</a>
            <a href="/search.php">Recherche</a>
            <a href="/events.php">Événements</a>
            <a href="/test.php">Page de test</a>
        </div>
        
        <div class="info">
            <h2>API Endpoints</h2>
            <a href="/api/google-events.php?city=Paris">API Google Events</a>
            <a href="/api/trigger-scraping.php?secret=test">Trigger Scraping</a>
        </div>
    </div>
</body>
</html>