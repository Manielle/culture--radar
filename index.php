<?php
// Détection de l'environnement Railway
$isRailway = getenv('RAILWAY_ENVIRONMENT') !== false;

// Configuration de la base de données selon l'environnement
if ($isRailway) {
    // Railway MySQL
    $db_host = getenv('MYSQLHOST') ?: 'centerbeam.proxy.rlwy.net';
    $db_port = getenv('MYSQLPORT') ?: '48330';
    $db_name = getenv('MYSQLDATABASE') ?: 'railway';
    $db_user = getenv('MYSQLUSER') ?: 'root';
    $db_pass = getenv('MYSQLPASSWORD') ?: 'tBixYXRKGkGAZuyxGHFZzaTxQAGXvJJH';
} else {
    // Local MAMP
    $db_host = '127.0.0.1';
    $db_port = '8889';
    $db_name = 'culture_radar';
    $db_user = 'root';
    $db_pass = 'root';
}

// Démarrer la session
session_start();

// Connexion à la base de données
$pdo = null;
try {
    $dsn = "mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8mb4";
    $pdo = new PDO($dsn, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    // Continue sans base de données
}

// Charger les services si disponibles
$services = [
    '/services/GoogleEventsService.php',
    '/services/OpenAgendaService.php'
];

foreach ($services as $service) {
    if (file_exists(__DIR__ . $service)) {
        require_once __DIR__ . $service;
    }
}

// Variables utilisateur
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? ($_SESSION['user_name'] ?? 'Utilisateur') : '';

// Événements de démonstration
$demoEvents = [
    [
        'id' => 'demo-1',
        'title' => 'Concert de Jazz au Sunset',
        'description' => 'Une soirée jazz exceptionnelle avec des artistes internationaux',
        'date' => date('Y-m-d'),
        'time' => '21:00',
        'venue' => 'Le Sunset-Sunside',
        'address' => '60 Rue des Lombards, 75001 Paris',
        'city' => 'Paris',
        'price' => ['is_free' => false, 'price' => 25, 'price_text' => '25€'],
        'category' => 'concert',
        'image' => 'https://images.unsplash.com/photo-1415201364774-f6f0bb35f28f?w=400'
    ],
    [
        'id' => 'demo-2',
        'title' => 'Exposition Monet',
        'description' => 'Les Nymphéas de Claude Monet',
        'date' => date('Y-m-d', strtotime('+1 day')),
        'time' => '10:00 - 18:00',
        'venue' => 'Musée de l\'Orangerie',
        'address' => 'Jardin des Tuileries, 75001 Paris',
        'city' => 'Paris',
        'price' => ['is_free' => false, 'price' => 12, 'price_text' => '12€'],
        'category' => 'museum',
        'image' => 'https://images.unsplash.com/photo-1554907984-15263bfd63bd?w=400'
    ],
    [
        'id' => 'demo-3',
        'title' => 'Théâtre: Le Malade Imaginaire',
        'description' => 'La célèbre pièce de Molière',
        'date' => date('Y-m-d', strtotime('+2 days')),
        'time' => '20:00',
        'venue' => 'Comédie-Française',
        'address' => '1 Place Colette, 75001 Paris',
        'city' => 'Paris',
        'price' => ['is_free' => false, 'price' => 35, 'price_text' => '35€'],
        'category' => 'theater',
        'image' => 'https://images.unsplash.com/photo-1503095396549-807759245b35?w=400'
    ],
    [
        'id' => 'demo-4',
        'title' => 'Festival de Street Art',
        'description' => 'Découvrez les artistes urbains du moment',
        'date' => date('Y-m-d', strtotime('+3 days')),
        'time' => 'Toute la journée',
        'venue' => 'Belleville',
        'address' => 'Quartier Belleville, 75020 Paris',
        'city' => 'Paris',
        'price' => ['is_free' => true, 'price' => 0, 'price_text' => 'Gratuit'],
        'category' => 'festival',
        'image' => 'https://images.unsplash.com/photo-1499781350541-7783f6c6a0c8?w=400'
    ]
];

// Utiliser les événements de démo
$searchEvents = array_slice($demoEvents, 0, 12);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Culture Radar - Découvrez la culture autour de vous</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        /* Styles de base pour assurer l'affichage */
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
        }
        .nav-logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #667eea;
            text-decoration: none;
        }
        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
            margin: 0;
            padding: 0;
        }
        .nav-link {
            color: #333;
            text-decoration: none;
            transition: color 0.3s;
        }
        .nav-link:hover {
            color: #667eea;
        }
        .hero {
            padding: 5rem 2rem;
            text-align: center;
            color: white;
        }
        .hero-title {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .hero-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        .event-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .event-card:hover {
            transform: translateY(-5px);
        }
        .event-image {
            height: 200px;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .event-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .event-content {
            padding: 1.5rem;
        }
        .event-title {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            color: #333;
        }
        .event-meta {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-top: 1rem;
            color: #666;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="/" class="nav-logo">
                <i class="fas fa-radar"></i> Culture Radar
            </a>
            <ul class="nav-menu">
                <li><a href="#search" class="nav-link">Découvrir</a></li>
                <li><a href="#features" class="nav-link">Fonctionnalités</a></li>
                <?php if($isLoggedIn): ?>
                    <li><a href="dashboard.php" class="nav-link">Mon Espace</a></li>
                    <li><a href="logout.php" class="nav-link">Déconnexion</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="nav-link">Connexion</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <h1 class="hero-title">Découvrez la culture qui vous entoure</h1>
        <p class="hero-subtitle">Explorez événements, expositions et spectacles près de chez vous</p>
    </section>

    <!-- Events Section -->
    <section id="search" class="container">
        <h2 style="text-align: center; color: white; margin-bottom: 2rem;">Événements à découvrir</h2>
        
        <div class="events-grid">
            <?php foreach($searchEvents as $event): ?>
                <div class="event-card">
                    <div class="event-image">
                        <?php if (!empty($event['image'])): ?>
                            <img src="<?php echo htmlspecialchars($event['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($event['title']); ?>">
                        <?php else: ?>
                            <i class="fas fa-calendar-alt" style="font-size: 3rem; color: #ccc;"></i>
                        <?php endif; ?>
                    </div>
                    <div class="event-content">
                        <h3 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                        <p><?php echo htmlspecialchars($event['description']); ?></p>
                        <div class="event-meta">
                            <span><i class="far fa-calendar"></i> <?php echo htmlspecialchars($event['date']); ?></span>
                            <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['venue']); ?></span>
                            <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($event['price']['price_text']); ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Debug info (remove in production) -->
    <?php if ($isRailway): ?>
    <!-- Running on Railway -->
    <?php endif; ?>
</body>
</html>