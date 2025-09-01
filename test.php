<?php
// Simple test page to verify Railway deployment
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test - Culture Radar</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            max-width: 600px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        h1 {
            margin-top: 0;
            font-size: 2.5rem;
        }
        .status {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .success {
            color: #10b981;
        }
        .error {
            color: #ef4444;
        }
        .info {
            color: #3b82f6;
        }
        pre {
            background: rgba(0, 0, 0, 0.3);
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧭 Culture Radar</h1>
        <h2>Test de déploiement Railway</h2>
        
        <div class="status">
            <h3>🖥️ Serveur</h3>
            <p class="success">✓ PHP Version: <?php echo PHP_VERSION; ?></p>
            <p class="success">✓ Serveur: <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></p>
            <p class="info">ℹ️ Port: <?php echo $_SERVER['SERVER_PORT'] ?? 'Unknown'; ?></p>
        </div>
        
        <div class="status">
            <h3>🔧 Variables d'environnement Railway</h3>
            <?php
            $railway_vars = [
                'RAILWAY_ENVIRONMENT' => getenv('RAILWAY_ENVIRONMENT'),
                'RAILWAY_PUBLIC_DOMAIN' => getenv('RAILWAY_PUBLIC_DOMAIN'),
                'PORT' => getenv('PORT')
            ];
            
            foreach ($railway_vars as $key => $value) {
                if ($value) {
                    echo "<p class='success'>✓ $key: $value</p>";
                } else {
                    echo "<p class='error'>✗ $key: Non défini</p>";
                }
            }
            ?>
        </div>
        
        <div class="status">
            <h3>💾 Base de données MySQL</h3>
            <?php
            // Check for MySQL environment variables
            $mysql_configured = false;
            
            if (getenv('MYSQL_URL')) {
                echo "<p class='success'>✓ MYSQL_URL est configuré</p>";
                $mysql_configured = true;
            } elseif (getenv('MYSQLHOST')) {
                echo "<p class='success'>✓ Variables MySQL individuelles configurées</p>";
                echo "<p class='info'>ℹ️ MYSQLHOST: " . getenv('MYSQLHOST') . "</p>";
                echo "<p class='info'>ℹ️ MYSQLDATABASE: " . getenv('MYSQLDATABASE') . "</p>";
                $mysql_configured = true;
            } else {
                echo "<p class='error'>✗ Aucune configuration MySQL détectée</p>";
                echo "<p class='error'>⚠️ Vous devez lier le service MySQL dans Railway</p>";
            }
            
            // Try to connect if configured
            if ($mysql_configured) {
                try {
                    require_once __DIR__ . '/config.php';
                    $pdo = Config::getPDO();
                    if ($pdo) {
                        echo "<p class='success'>✓ Connexion à MySQL réussie!</p>";
                    } else {
                        echo "<p class='error'>✗ Impossible de se connecter à MySQL</p>";
                    }
                } catch (Exception $e) {
                    echo "<p class='error'>✗ Erreur de connexion: " . htmlspecialchars($e->getMessage()) . "</p>";
                }
            }
            ?>
        </div>
        
        <div class="status">
            <h3>📁 Fichiers et permissions</h3>
            <?php
            $dirs = ['cache', 'logs', 'uploads'];
            foreach ($dirs as $dir) {
                if (is_dir(__DIR__ . '/' . $dir)) {
                    $writable = is_writable(__DIR__ . '/' . $dir);
                    if ($writable) {
                        echo "<p class='success'>✓ /$dir existe et est accessible en écriture</p>";
                    } else {
                        echo "<p class='error'>✗ /$dir existe mais n'est pas accessible en écriture</p>";
                    }
                } else {
                    echo "<p class='error'>✗ /$dir n'existe pas</p>";
                }
            }
            ?>
        </div>
        
        <div class="status">
            <h3>🔗 Liens utiles</h3>
            <p>
                <a href="/" style="color: #60a5fa;">Page d'accueil</a> | 
                <a href="/health.php" style="color: #60a5fa;">Health Check</a> | 
                <a href="/register.php" style="color: #60a5fa;">Inscription</a>
            </p>
        </div>
    </div>
</body>
</html>