<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/classes/Auth.php';

$auth = new Auth();
$isLoggedIn = $auth->isLoggedIn();
$user = $isLoggedIn ? $auth->getCurrentUser() : null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tous les événements - Culture Radar</title>
    
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
        
        .events-container {
            min-height: 100vh;
            padding-top: 80px;
        }
        
        .events-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 3rem 2rem;
            text-align: center;
        }
        
        .events-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .events-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .events-controls {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .view-toggle {
            display: flex;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.05);
            padding: 0.25rem;
            border-radius: 8px;
        }
        
        .view-btn {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.6);
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .view-btn.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .sort-dropdown {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
        }
        
        .categories-filter {
            max-width: 1200px;
            margin: 0 auto 2rem;
            padding: 0 2rem;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .category-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            padding: 0.5rem 1.2rem;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .category-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }
        
        .category-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: transparent;
        }
        
        .events-grid {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem 3rem;
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
            position: relative;
        }
        
        .event-card:hover {
            transform: translateY(-4px);
            border-color: rgba(102, 126, 234, 0.5);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .event-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .event-badge.free {
            background: rgba(72, 187, 120, 0.2);
            color: #48bb78;
        }
        
        .event-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #667eea22 0%, #764ba222 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: #667eea;
        }
        
        .event-content {
            padding: 1.5rem;
        }
        
        .event-category {
            color: #667eea;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }
        
        .event-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            line-height: 1.4;
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
        
        .event-footer {
            padding: 1rem 1.5rem;
            background: rgba(255, 255, 255, 0.03);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .event-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: #667eea;
        }
        
        .event-action {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.3s;
        }
        
        .event-action:hover {
            transform: scale(1.05);
        }
        
        .pagination {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            display: flex;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .page-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .page-btn:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .page-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: transparent;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="events-container">
        <div class="events-header">
            <h1 class="events-title">Tous les événements</h1>
            <p class="events-subtitle">Découvrez tous les événements culturels près de chez vous</p>
        </div>
        
        <div class="events-controls">
            <div class="view-toggle">
                <button class="view-btn active" onclick="setView('grid')">
                    <i class="fas fa-th"></i> Grille
                </button>
                <button class="view-btn" onclick="setView('list')">
                    <i class="fas fa-list"></i> Liste
                </button>
            </div>
            
            <select class="sort-dropdown">
                <option>Plus récents</option>
                <option>Plus anciens</option>
                <option>Prix croissant</option>
                <option>Prix décroissant</option>
                <option>Plus populaires</option>
            </select>
        </div>
        
        <div class="categories-filter">
            <button class="category-btn active">
                <i class="fas fa-globe"></i> Tout
            </button>
            <button class="category-btn">
                <i class="fas fa-theater-masks"></i> Théâtre
            </button>
            <button class="category-btn">
                <i class="fas fa-music"></i> Musique
            </button>
            <button class="category-btn">
                <i class="fas fa-palette"></i> Expositions
            </button>
            <button class="category-btn">
                <i class="fas fa-landmark"></i> Patrimoine
            </button>
            <button class="category-btn">
                <i class="fas fa-film"></i> Cinéma
            </button>
            <button class="category-btn">
                <i class="fas fa-users"></i> Ateliers
            </button>
        </div>
        
        <div class="events-grid" id="eventsGrid">
            <?php
            // Événements de démonstration
            $events = [
                ['title' => 'Concert Jazz Manouche', 'category' => 'Musique', 'venue' => 'Le Duc des Lombards', 'date' => '15 Février', 'price' => '25€', 'free' => false],
                ['title' => 'Exposition Van Gogh', 'category' => 'Art', 'venue' => 'Atelier des Lumières', 'date' => '16 Février', 'price' => 'Gratuit', 'free' => true],
                ['title' => 'Pièce "Le Malade Imaginaire"', 'category' => 'Théâtre', 'venue' => 'Comédie Française', 'date' => '17 Février', 'price' => '35€', 'free' => false],
                ['title' => 'Festival Street Art', 'category' => 'Art', 'venue' => 'Belleville', 'date' => '18 Février', 'price' => 'Gratuit', 'free' => true],
                ['title' => 'Concert Symphonique', 'category' => 'Musique', 'venue' => 'Philharmonie', 'date' => '19 Février', 'price' => '45€', 'free' => false],
                ['title' => 'Visite Guidée du Louvre', 'category' => 'Patrimoine', 'venue' => 'Musée du Louvre', 'date' => '20 Février', 'price' => '15€', 'free' => false],
                ['title' => 'Projection Cinéma Muet', 'category' => 'Cinéma', 'venue' => 'Cinémathèque', 'date' => '21 Février', 'price' => '8€', 'free' => false],
                ['title' => 'Atelier Poterie', 'category' => 'Atelier', 'venue' => 'Centre Culturel', 'date' => '22 Février', 'price' => 'Gratuit', 'free' => true],
                ['title' => 'Ballet "Le Lac des Cygnes"', 'category' => 'Danse', 'venue' => 'Opéra Garnier', 'date' => '23 Février', 'price' => '65€', 'free' => false],
            ];
            
            foreach ($events as $event): ?>
                <div class="event-card" onclick="window.location.href='event-details.php'">
                    <?php if ($event['free']): ?>
                        <span class="event-badge free">Gratuit</span>
                    <?php endif; ?>
                    
                    <div class="event-image">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    
                    <div class="event-content">
                        <div class="event-category"><?php echo $event['category']; ?></div>
                        <h3 class="event-title"><?php echo $event['title']; ?></h3>
                        <div class="event-meta">
                            <span><i class="fas fa-map-marker-alt"></i> <?php echo $event['venue']; ?></span>
                            <span><i class="fas fa-calendar"></i> <?php echo $event['date']; ?></span>
                        </div>
                    </div>
                    
                    <div class="event-footer">
                        <span class="event-price"><?php echo $event['price']; ?></span>
                        <button class="event-action">
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="pagination">
            <button class="page-btn">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="page-btn active">1</button>
            <button class="page-btn">2</button>
            <button class="page-btn">3</button>
            <button class="page-btn">4</button>
            <button class="page-btn">5</button>
            <button class="page-btn">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script>
        // Toggle categories
        document.querySelectorAll('.category-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });
        
        // View toggle
        function setView(view) {
            document.querySelectorAll('.view-btn').forEach(btn => btn.classList.remove('active'));
            event.target.closest('.view-btn').classList.add('active');
            
            // Here we would change the grid layout
            console.log('View changed to:', view);
        }
    </script>
</body>
</html>