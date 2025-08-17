<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/classes/Auth.php';

$auth = new Auth();
$isLoggedIn = $auth->isLoggedIn();
$user = $isLoggedIn ? $auth->getCurrentUser() : null;

// Récupérer la catégorie depuis l'URL
$category = isset($_GET['cat']) ? htmlspecialchars($_GET['cat']) : 'all';

// Mapping des catégories
$categories = [
    'spectacles' => ['name' => 'Spectacles & Théâtre', 'icon' => 'fa-theater-masks', 'color' => '#e74c3c'],
    'musique' => ['name' => 'Musique & Concerts', 'icon' => 'fa-music', 'color' => '#3498db'],
    'expositions' => ['name' => 'Expositions & Musées', 'icon' => 'fa-palette', 'color' => '#9b59b6'],
    'patrimoine' => ['name' => 'Patrimoine & Visites', 'icon' => 'fa-landmark', 'color' => '#f39c12'],
    'cinema' => ['name' => 'Cinéma & Projections', 'icon' => 'fa-film', 'color' => '#2ecc71'],
    'ateliers' => ['name' => 'Ateliers & Rencontres', 'icon' => 'fa-users', 'color' => '#e67e22']
];

$currentCategory = isset($categories[$category]) ? $categories[$category] : ['name' => 'Tous les événements', 'icon' => 'fa-calendar', 'color' => '#667eea'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $currentCategory['name']; ?> - Culture Radar</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <?php include 'includes/favicon.php'; ?>
    
    <style>
        body {
            background: #0a0a0f;
            color: white;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
        }
        
        .category-container {
            min-height: 100vh;
            padding-top: 80px;
        }
        
        .category-header {
            background: linear-gradient(135deg, <?php echo $currentCategory['color']; ?>22 0%, transparent 100%);
            padding: 3rem 2rem;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .category-icon {
            font-size: 3rem;
            color: <?php echo $currentCategory['color']; ?>;
            margin-bottom: 1rem;
        }
        
        .category-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .category-description {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .filters-bar {
            background: rgba(255, 255, 255, 0.05);
            padding: 1.5rem;
            margin: 2rem auto;
            max-width: 1200px;
            border-radius: 12px;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
        }
        
        .filter-chip {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.9rem;
        }
        
        .filter-chip:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }
        
        .filter-chip.active {
            background: <?php echo $currentCategory['color']; ?>;
            border-color: transparent;
        }
        
        .events-grid {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .event-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .event-card:hover {
            transform: translateY(-4px);
            border-color: <?php echo $currentCategory['color']; ?>88;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .event-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, <?php echo $currentCategory['color']; ?>44 0%, <?php echo $currentCategory['color']; ?>22 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: <?php echo $currentCategory['color']; ?>;
        }
        
        .event-content {
            padding: 1.5rem;
        }
        
        .event-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .event-meta {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .event-meta span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .event-price {
            margin-top: 1rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: <?php echo $currentCategory['color']; ?>;
        }
        
        .no-events {
            text-align: center;
            padding: 4rem;
            color: rgba(255, 255, 255, 0.5);
        }
        
        .no-events i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="category-container">
        <div class="category-header">
            <div class="category-icon">
                <i class="fas <?php echo $currentCategory['icon']; ?>"></i>
            </div>
            <h1 class="category-title"><?php echo $currentCategory['name']; ?></h1>
            <p class="category-description">
                Découvrez tous les événements de la catégorie <?php echo $currentCategory['name']; ?> près de chez vous
            </p>
        </div>
        
        <div class="filters-bar">
            <button class="filter-chip active" onclick="applyFilter('all')">
                <i class="fas fa-globe"></i> Tout
            </button>
            <button class="filter-chip" onclick="applyFilter('today')">
                <i class="fas fa-calendar-day"></i> Aujourd'hui
            </button>
            <button class="filter-chip" onclick="applyFilter('weekend')">
                <i class="fas fa-calendar-week"></i> Ce week-end
            </button>
            <button class="filter-chip" onclick="applyFilter('free')">
                <i class="fas fa-gift"></i> Gratuit
            </button>
            <button class="filter-chip" onclick="applyFilter('nearby')">
                <i class="fas fa-map-marker-alt"></i> À proximité
            </button>
        </div>
        
        <div class="events-grid" id="eventsGrid">
            <!-- Événements de démonstration -->
            <?php
            // Événements fictifs pour démonstration
            $demoEvents = [
                [
                    'title' => 'Concert Symphonique',
                    'venue' => 'Philharmonie de Paris',
                    'date' => 'Samedi 17 Février',
                    'time' => '20h00',
                    'price' => '25€'
                ],
                [
                    'title' => 'Exposition Impressionniste',
                    'venue' => 'Musée d\'Orsay',
                    'date' => 'Jusqu\'au 31 Mars',
                    'time' => '10h-18h',
                    'price' => 'Gratuit'
                ],
                [
                    'title' => 'Festival de Jazz',
                    'venue' => 'New Morning',
                    'date' => 'Vendredi 16 Février',
                    'time' => '21h00',
                    'price' => '35€'
                ],
                [
                    'title' => 'Pièce de Théâtre Moderne',
                    'venue' => 'Théâtre de la Ville',
                    'date' => 'Dimanche 18 Février',
                    'time' => '16h00',
                    'price' => '20€'
                ],
                [
                    'title' => 'Atelier d\'Art Créatif',
                    'venue' => 'Centre Culturel',
                    'date' => 'Mercredi 21 Février',
                    'time' => '14h00',
                    'price' => 'Gratuit'
                ],
                [
                    'title' => 'Projection Cinéma Classique',
                    'venue' => 'Cinémathèque',
                    'date' => 'Jeudi 22 Février',
                    'time' => '19h30',
                    'price' => '8€'
                ]
            ];
            
            foreach ($demoEvents as $event): ?>
                <div class="event-card" onclick="window.location.href='event-details.php'">
                    <div class="event-image">
                        <i class="fas <?php echo $currentCategory['icon']; ?>"></i>
                    </div>
                    <div class="event-content">
                        <h3 class="event-title"><?php echo $event['title']; ?></h3>
                        <div class="event-meta">
                            <span><i class="fas fa-map-marker-alt"></i> <?php echo $event['venue']; ?></span>
                            <span><i class="fas fa-calendar"></i> <?php echo $event['date']; ?></span>
                            <span><i class="fas fa-clock"></i> <?php echo $event['time']; ?></span>
                        </div>
                        <div class="event-price">
                            <?php echo $event['price'] === 'Gratuit' ? '<i class="fas fa-gift"></i> Gratuit' : $event['price']; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script>
        function applyFilter(filter) {
            // Mettre à jour les boutons actifs
            document.querySelectorAll('.filter-chip').forEach(chip => {
                chip.classList.remove('active');
            });
            event.target.closest('.filter-chip').classList.add('active');
            
            // Ici on pourrait filtrer les événements
            console.log('Filter applied:', filter);
        }
    </script>
</body>
</html>