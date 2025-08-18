<?php
// Safe index.php for Railway - handles missing database gracefully
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors to users
ini_set('log_errors', 1);

// Start output buffering to catch any errors
ob_start();

try {
    // Basic headers
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    
    // Start session safely
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if we're on Railway
    $onRailway = getenv('RAILWAY_ENVIRONMENT') !== false;
    $hasDatabase = false;
    
    // Try to load config if it exists
    $configFile = __DIR__ . '/config.php';
    if (file_exists($configFile)) {
        @include_once $configFile;
    }
    
    // Check database connection without crashing
    if (class_exists('Config')) {
        try {
            $pdo = Config::getPDO();
            if ($pdo) {
                $hasDatabase = true;
            }
        } catch (Exception $e) {
            // Database not available - that's OK
            error_log("Database not available: " . $e->getMessage());
        }
    }
    
    $isLoggedIn = isset($_SESSION['user_id']);
    $userName = $isLoggedIn ? ($_SESSION['user_name'] ?? 'Utilisateur') : '';
    
} catch (Exception $e) {
    error_log("Error in index: " . $e->getMessage());
}

// Clear any error output
ob_clean();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Culture Radar - Votre boussole culturelle personnalisée</title>
    <meta name="description" content="Découvrez les meilleurs événements culturels près de chez vous avec Culture Radar. Expositions, concerts, théâtre et plus encore, personnalisés selon vos goûts.">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles (inline for safety) -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: white;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 2rem 0;
            margin-bottom: 3rem;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            color: white;
        }
        
        .logo-icon {
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .logo-text {
            font-size: 1.8rem;
            font-weight: 800;
        }
        
        .hero {
            text-align: center;
            padding: 4rem 0;
        }
        
        .hero h1 {
            font-size: 3.5rem;
            font-weight: 900;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .hero p {
            font-size: 1.25rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto 2rem;
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
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .status-box {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            margin-top: 3rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .status-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0.5rem 0;
        }
        
        .status-icon {
            font-size: 1.2rem;
        }
        
        .success { color: #10b981; }
        .warning { color: #f59e0b; }
        .error { color: #ef4444; }
        
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                max-width: 300px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <a href="/" class="logo">
                <div class="logo-icon">🧭</div>
                <span class="logo-text">Culture Radar</span>
            </a>
            
            <?php if ($isLoggedIn): ?>
                <div>
                    <span>Bonjour, <?php echo htmlspecialchars($userName); ?></span>
                    <a href="/dashboard.php" class="btn btn-secondary">Mon Dashboard</a>
                </div>
            <?php endif; ?>
        </header>
        
        <main class="hero">
            <h1>Découvrez la Culture Qui Vous Ressemble</h1>
            <p>
                Votre boussole culturelle personnalisée pour ne plus jamais manquer 
                les événements qui comptent vraiment pour vous.
            </p>
            
            <div class="cta-buttons">
                <?php if ($isLoggedIn): ?>
                    <a href="/dashboard.php" class="btn btn-primary">Accéder au Dashboard</a>
                    <a href="/discover.php" class="btn btn-secondary">Explorer</a>
                <?php else: ?>
                    <a href="/register.php" class="btn btn-primary">Créer mon compte</a>
                    <a href="/login.php" class="btn btn-secondary">Se connecter</a>
                <?php endif; ?>
            </div>
            
            <?php if ($onRailway): ?>
            <div class="status-box">
                <h3>État du déploiement Railway</h3>
                <div class="status-item">
                    <span class="status-icon success">✓</span>
                    <span>Application déployée sur Railway</span>
                </div>
                <div class="status-item">
                    <span class="status-icon <?php echo $hasDatabase ? 'success' : 'warning'; ?>">
                        <?php echo $hasDatabase ? '✓' : '⚠'; ?>
                    </span>
                    <span>Base de données: <?php echo $hasDatabase ? 'Connectée' : 'Non configurée'; ?></span>
                </div>
                <?php if (!$hasDatabase): ?>
                <div class="status-item">
                    <span class="status-icon warning">ℹ</span>
                    <span>Veuillez configurer MySQL dans Railway</span>
                </div>
                <?php endif; ?>
                <div class="status-item">
                    <span class="status-icon success">✓</span>
                    <span>PHP <?php echo PHP_VERSION; ?></span>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>