<?php
// Simple working index for Railway
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test database connection
$db_status = "Not connected";
$db_error = "";

try {
    // Try Railway environment variables first
    $host = getenv('MYSQLHOST') ?: 'centerbeam.proxy.rlwy.net';
    $port = getenv('MYSQLPORT') ?: '48330';
    $user = getenv('MYSQLUSER') ?: 'root';
    $pass = getenv('MYSQLPASSWORD') ?: 'tBixYXRKGkGAZuyxGHFZzaTxQAGXvJJH';
    $dbname = getenv('MYSQLDATABASE') ?: 'railway';
    
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    $db_status = "Connected successfully!";
} catch (Exception $e) {
    $db_error = $e->getMessage();
    $db_status = "Connection failed";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Culture Radar - Votre Guide Culturel Personnalisé</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .nav {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
            text-decoration: none;
        }
        
        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }
        
        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: background 0.3s;
        }
        
        .nav-links a:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .hero {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .hero-content {
            text-align: center;
            color: white;
            max-width: 800px;
        }
        
        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .hero p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.95;
        }
        
        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 1rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.3s, box-shadow 0.3s;
            display: inline-block;
        }
        
        .btn-primary {
            background: white;
            color: #667eea;
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid white;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 4rem;
            padding: 0 2rem;
        }
        
        .feature {
            background: rgba(255, 255, 255, 0.1);
            padding: 2rem;
            border-radius: 16px;
            backdrop-filter: blur(5px);
        }
        
        .feature h3 {
            margin-bottom: 1rem;
            font-size: 1.25rem;
        }
        
        .feature p {
            opacity: 0.9;
            line-height: 1.6;
        }
        
        .status {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
        }
        
        .status.success {
            border-left: 4px solid #4caf50;
        }
        
        .status.error {
            border-left: 4px solid #f44336;
        }
        
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }
            
            .nav-links {
                display: none;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="nav">
            <a href="/" class="logo">🎭 Culture Radar</a>
            <ul class="nav-links">
                <li><a href="/discover.php">Découvrir</a></li>
                <li><a href="/login.php">Connexion</a></li>
                <li><a href="/register.php">Inscription</a></li>
                <li><a href="/dashboard.php">Mon Espace</a></li>
            </ul>
        </nav>
    </header>
    
    <main class="hero">
        <div class="hero-content">
            <h1>Bienvenue sur Culture Radar</h1>
            <p>Votre guide personnalisé pour découvrir les meilleurs événements culturels près de chez vous</p>
            
            <div class="cta-buttons">
                <a href="/register.php" class="btn btn-primary">Commencer Gratuitement</a>
                <a href="/discover.php" class="btn btn-secondary">Explorer les Événements</a>
            </div>
            
            <div class="features">
                <div class="feature">
                    <h3>🤖 IA Personnalisée</h3>
                    <p>Notre intelligence artificielle apprend vos préférences pour vous recommander les événements parfaits</p>
                </div>
                <div class="feature">
                    <h3>📍 Près de Chez Vous</h3>
                    <p>Découvrez des événements culturels dans votre quartier ou explorez de nouveaux horizons</p>
                </div>
                <div class="feature">
                    <h3>🎨 Tout Type d'Événements</h3>
                    <p>Concerts, expositions, théâtre, cinéma, conférences... Trouvez ce qui vous passionne</p>
                </div>
            </div>
        </div>
    </main>
    
    <div class="status <?php echo $db_status === 'Connected successfully!' ? 'success' : 'error'; ?>">
        Database: <?php echo htmlspecialchars($db_status); ?>
        <?php if ($db_error): ?>
            <br><small><?php echo htmlspecialchars($db_error); ?></small>
        <?php endif; ?>
    </div>
</body>
</html>