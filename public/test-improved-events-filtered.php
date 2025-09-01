<?php
/**
 * Test du service amélioré avec filtres
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/services/ImprovedEventsService.php';
// require_once __DIR__ . '/components/event-card-hybrid.php'; // Fichier non présent

$city = $_GET['city'] ?? 'Paris';
$category = $_GET['category'] ?? 'all';
$period = $_GET['period'] ?? 'all';

// Définir les dates selon la période sélectionnée
$dateFilter = '';
$periodLabel = '';
$today = new DateTime();

switch($period) {
    case 'today':
        $dateFilter = $today->format('j F Y');
        $periodLabel = "Aujourd'hui (" . $today->format('d/m') . ")";
        break;
    case 'tomorrow':
        $tomorrow = clone $today;
        $tomorrow->modify('+1 day');
        $dateFilter = $tomorrow->format('j F Y');
        $periodLabel = "Demain (" . $tomorrow->format('d/m') . ")";
        break;
    case 'weekend':
        $saturday = clone $today;
        $saturday->modify('next saturday');
        $sunday = clone $saturday;
        $sunday->modify('+1 day');
        $dateFilter = $saturday->format('j F') . ' OR ' . $sunday->format('j F Y');
        $periodLabel = "Ce weekend (" . $saturday->format('d/m') . "-" . $sunday->format('d/m') . ")";
        break;
    case 'week':
        $endWeek = clone $today;
        $endWeek->modify('+7 days');
        $dateFilter = 'cette semaine';
        $periodLabel = "Cette semaine (jusqu'au " . $endWeek->format('d/m') . ")";
        break;
    case 'month':
        $dateFilter = $today->format('F Y');
        $periodLabel = "Ce mois (" . $today->format('F Y') . ")";
        break;
    default:
        $dateFilter = 'août septembre 2025';
        $periodLabel = "Tous les événements";
        break;
}

// Catégories disponibles
$categories = [
    'all' => ['label' => 'Toutes catégories', 'icon' => 'bi-grid-3x3-gap-fill', 'color' => 'primary'],
    'musique' => ['label' => 'Musique', 'icon' => 'bi-music-note-beamed', 'color' => 'success'],
    'theatre' => ['label' => 'Théâtre', 'icon' => 'bi-mask', 'color' => 'danger'],
    'exposition' => ['label' => 'Expositions', 'icon' => 'bi-palette-fill', 'color' => 'warning'],
    'festival' => ['label' => 'Festivals', 'icon' => 'bi-stars', 'color' => 'info'],
    'cinema' => ['label' => 'Cinéma', 'icon' => 'bi-film', 'color' => 'dark'],
    'danse' => ['label' => 'Danse', 'icon' => 'bi-person-arms-up', 'color' => 'purple']
];

// Périodes disponibles
$periods = [
    'today' => ['label' => "Aujourd'hui", 'icon' => 'bi-calendar-day'],
    'tomorrow' => ['label' => 'Demain', 'icon' => 'bi-calendar-plus'],
    'weekend' => ['label' => 'Ce weekend', 'icon' => 'bi-calendar-week'],
    'week' => ['label' => 'Cette semaine', 'icon' => 'bi-calendar-range'],
    'month' => ['label' => 'Ce mois', 'icon' => 'bi-calendar-month'],
    'all' => ['label' => 'Toutes dates', 'icon' => 'bi-calendar']
];

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Événements Filtrés - Culture Radar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .hero-section {
            background: white;
            border-radius: 30px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }
        
        .filter-section {
            background: white;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        
        .filter-btn {
            border: 2px solid #e0e0e0;
            background: white;
            color: #333;
            padding: 10px 20px;
            border-radius: 25px;
            margin: 5px;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .filter-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: transparent;
        }
        
        .category-filter {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            margin-bottom: 20px;
        }
        
        .period-filter {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }
        
        .event-card {
            transition: all 0.3s;
            border: none;
            border-radius: 15px;
            overflow: hidden;
        }
        
        .event-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        
        .stats-ribbon {
            background: rgba(255,255,255,0.95);
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
        }
        
        .stat-item {
            text-align: center;
            padding: 10px;
        }
        
        .stat-number {
            font-size: 1.8em;
            font-weight: bold;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        .loading-overlay.show {
            display: flex;
        }
        
        .loading-content {
            background: white;
            padding: 30px;
            border-radius: 20px;
            text-align: center;
        }
        
        .quick-filters {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 20px;
        }
        
        .quick-filter-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
        }
        
        .quick-filter-btn:hover {
            transform: scale(1.05);
            color: white;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }
        
        .purple { background: #764ba2; }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;"></div>
            <h5>Recherche en cours...</h5>
            <p class="text-muted mb-0">Analyse de multiples sources</p>
        </div>
    </div>
    
    <div class="container">
        <!-- Hero Section -->
        <div class="hero-section">
            <h1 class="text-center mb-4">
                <i class="bi bi-funnel-fill text-primary"></i>
                Événements Culturels
            </h1>
            <p class="text-center text-muted mb-4">
                <?php if ($category === 'all'): ?>
                    <strong>📅 Événements des 2 prochaines semaines - Au moins 1 par catégorie</strong>
                <?php else: ?>
                    Filtrez par catégorie et période pour trouver l'événement parfait
                <?php endif; ?>
            </p>
            
            <!-- Recherche ville -->
            <form method="GET" class="mb-4" id="searchForm">
                <div class="row g-3">
                    <div class="col-md-9">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text">
                                <i class="bi bi-geo-alt-fill"></i>
                            </span>
                            <input type="text" 
                                   name="city" 
                                   class="form-control" 
                                   placeholder="Ville (Paris, Lyon, Marseille...)" 
                                   value="<?php echo htmlspecialchars($city); ?>">
                            <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
                            <input type="hidden" name="period" value="<?php echo htmlspecialchars($period); ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-search"></i> Rechercher
                        </button>
                    </div>
                </div>
            </form>
            
            <!-- Filtres rapides -->
            <div class="quick-filters">
                <a href="?city=<?php echo urlencode($city); ?>&category=musique&period=weekend" 
                   class="quick-filter-btn">
                    <i class="bi bi-music-note-beamed"></i> Concerts ce weekend
                </a>
                <a href="?city=<?php echo urlencode($city); ?>&category=exposition&period=week" 
                   class="quick-filter-btn">
                    <i class="bi bi-palette-fill"></i> Expos cette semaine
                </a>
                <a href="?city=<?php echo urlencode($city); ?>&category=theatre&period=today" 
                   class="quick-filter-btn">
                    <i class="bi bi-mask"></i> Théâtre ce soir
                </a>
            </div>
        </div>
        
        <!-- Section Filtres -->
        <div class="filter-section">
            <h4 class="text-center mb-4">
                <i class="bi bi-filter-circle"></i> Filtres
            </h4>
            
            <!-- Filtres par catégorie -->
            <div class="mb-4">
                <h6 class="text-center text-muted mb-3">CATÉGORIES</h6>
                <div class="category-filter">
                    <?php foreach ($categories as $key => $cat): ?>
                        <a href="?city=<?php echo urlencode($city); ?>&category=<?php echo $key; ?>&period=<?php echo $period; ?>" 
                           class="filter-btn <?php echo $category === $key ? 'active' : ''; ?>">
                            <i class="<?php echo $cat['icon']; ?>"></i>
                            <?php echo $cat['label']; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Filtres par période -->
            <div>
                <h6 class="text-center text-muted mb-3">PÉRIODE</h6>
                <div class="period-filter">
                    <?php foreach ($periods as $key => $per): ?>
                        <a href="?city=<?php echo urlencode($city); ?>&category=<?php echo $category; ?>&period=<?php echo $key; ?>" 
                           class="filter-btn <?php echo $period === $key ? 'active' : ''; ?>">
                            <i class="<?php echo $per['icon']; ?>"></i>
                            <?php echo $per['label']; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Filtres actifs -->
        <?php if ($category === 'all'): ?>
        <div class="alert alert-success">
            <i class="bi bi-grid-3x3-gap-fill"></i>
            <strong>Mode découverte activé !</strong>
            <p class="mb-0 mt-2">
                🎯 Affichage des événements des <strong>14 prochains jours</strong><br>
                ✨ Au moins <strong>1 événement par catégorie</strong> (Musique, Théâtre, Expo, Festival, Cinéma, Danse)<br>
                📊 Résultats diversifiés pour une expérience culturelle complète
            </p>
        </div>
        <?php elseif ($category !== 'all' || $period !== 'all'): ?>
        <div class="alert alert-info">
            <i class="bi bi-funnel"></i>
            <strong>Filtres actifs:</strong>
            <?php if ($category !== 'all'): ?>
                <span class="badge bg-primary ms-2">
                    <i class="<?php echo $categories[$category]['icon']; ?>"></i>
                    <?php echo $categories[$category]['label']; ?>
                </span>
            <?php endif; ?>
            <?php if ($period !== 'all'): ?>
                <span class="badge bg-success ms-2">
                    <i class="bi bi-calendar"></i>
                    <?php echo $periodLabel; ?>
                </span>
            <?php endif; ?>
            <a href="?city=<?php echo urlencode($city); ?>" class="btn btn-sm btn-outline-secondary float-end">
                <i class="bi bi-x-circle"></i> Réinitialiser
            </a>
        </div>
        <?php endif; ?>
        
        <!-- Résultats -->
        <?php
        // Construire la requête selon les filtres
        $searchQuery = '';
        
        // Ajouter la catégorie
        if ($category !== 'all') {
            switch($category) {
                case 'musique':
                    $searchQuery .= 'concert festival jazz rock électro musique ';
                    break;
                case 'theatre':
                    $searchQuery .= 'théâtre pièce spectacle comédie drame ';
                    break;
                case 'exposition':
                    $searchQuery .= 'exposition musée galerie art vernissage ';
                    break;
                case 'festival':
                    $searchQuery .= 'festival fête événement ';
                    break;
                case 'cinema':
                    $searchQuery .= 'cinéma film projection séance avant-première ';
                    break;
                case 'danse':
                    $searchQuery .= 'danse ballet chorégraphie spectacle ';
                    break;
            }
        }
        
        // Ajouter la période
        $searchQuery .= $dateFilter . ' ' . $city;
        
        // Rechercher les événements avec les filtres
        $startTime = microtime(true);
        
        // Utiliser le nouveau service avec filtres
        $searchParams = [
            'city' => $city,
            'category' => $category,
            'period' => $period,
            'limit' => ($category === 'all' ? 30 : 24)  // Plus d'événements pour "Toutes catégories"
        ];
        
        $events = ImprovedEventsService::findRealEventsWithFilters($searchParams);
        
        $duration = round(microtime(true) - $startTime, 2);
        
        if (!empty($events)):
        ?>
            <!-- Statistiques -->
            <div class="stats-ribbon">
                <div class="stat-item">
                    <div class="stat-number"><?php echo count($events); ?></div>
                    <div class="text-muted">Événements</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo $duration; ?>s</div>
                    <div class="text-muted">Recherche</div>
                </div>
                <?php if ($category === 'all'): ?>
                <div class="stat-item">
                    <div class="stat-number">
                        <?php 
                        $uniqueCategories = count(array_unique(array_map('strtolower', array_column($events, 'category'))));
                        echo $uniqueCategories;
                        ?>
                    </div>
                    <div class="text-muted">Catégories</div>
                </div>
                <?php endif; ?>
                <div class="stat-item">
                    <div class="stat-number">
                        <?php 
                        $uniqueVenues = count(array_unique(array_column($events, 'venue_name')));
                        echo $uniqueVenues;
                        ?>
                    </div>
                    <div class="text-muted">Lieux</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">
                        <?php 
                        $freeEvents = count(array_filter($events, fn($e) => $e['is_free'] ?? false));
                        echo $freeEvents;
                        ?>
                    </div>
                    <div class="text-muted">Gratuits</div>
                </div>
            </div>
            
            <!-- Grille d'événements -->
            <div class="row">
                <?php 
                // Afficher plus d'événements quand "Toutes catégories" est sélectionné
                $displayLimit = ($category === 'all') ? 18 : 12;
                foreach (array_slice($events, 0, $displayLimit) as $event): 
                ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 event-card">
                            <?php
                            $categoryImages = [
                                'Musique' => 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=400',
                                'Festival' => 'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?w=400',
                                'Art' => 'https://images.unsplash.com/photo-1561214115-f2f134cc4912?w=400',
                                'Théâtre' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400',
                                'Exposition' => 'https://images.unsplash.com/photo-1554907984-15263bfd63bd?w=400',
                                'Cinéma' => 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=400',
                                'Danse' => 'https://images.unsplash.com/photo-1508807526345-15e9b5f4eaff?w=400'
                            ];
                            $image = $event['image'] ?? $categoryImages[$event['category'] ?? 'Culture'] ?? 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=400';
                            ?>
                            
                            <img src="<?php echo htmlspecialchars($image); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($event['title'] ?? ''); ?>" 
                                 style="height: 200px; object-fit: cover;">
                            
                            <div class="card-body d-flex flex-column">
                                <div class="mb-2">
                                    <span class="badge bg-<?php echo $categories[$category]['color'] ?? 'primary'; ?>">
                                        <?php echo htmlspecialchars($event['category'] ?? 'Culture'); ?>
                                    </span>
                                    <?php if ($event['is_free'] ?? false): ?>
                                        <span class="badge bg-success">Gratuit</span>
                                    <?php elseif (!empty($event['price'])): ?>
                                        <span class="badge bg-warning text-dark"><?php echo $event['price']; ?>€</span>
                                    <?php endif; ?>
                                </div>
                                
                                <h5 class="card-title">
                                    <?php echo htmlspecialchars($event['title'] ?? 'Événement'); ?>
                                </h5>
                                
                                <p class="text-muted small mb-2">
                                    <i class="bi bi-calendar-event"></i> 
                                    <strong><?php echo htmlspecialchars($event['date_display'] ?? 'Date à confirmer'); ?></strong><br>
                                    <i class="bi bi-geo-alt"></i> 
                                    <?php echo htmlspecialchars($event['venue_name'] ?? 'Lieu à confirmer'); ?>
                                </p>
                                
                                <p class="card-text flex-grow-1">
                                    <?php 
                                    $desc = $event['description'] ?? '';
                                    echo htmlspecialchars(substr($desc, 0, 100));
                                    if (strlen($desc) > 100) echo '...';
                                    ?>
                                </p>
                                
                                <div class="mt-auto">
                                    <?php if (!empty($event['detail_url'])): ?>
                                        <a href="<?php echo htmlspecialchars($event['detail_url']); ?>" 
                                           class="btn btn-primary btn-sm w-100">
                                            <i class="bi bi-eye"></i> Voir les détails
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
        <?php else: ?>
            <div class="alert alert-warning text-center">
                <i class="bi bi-calendar-x fs-1 mb-3 d-block"></i>
                <h4>Aucun événement trouvé</h4>
                <p>Aucun événement ne correspond à vos critères.</p>
                <p>Essayez de modifier vos filtres ou la période recherchée.</p>
                <a href="?city=<?php echo urlencode($city); ?>" class="btn btn-primary">
                    <i class="bi bi-arrow-clockwise"></i> Réinitialiser les filtres
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Afficher le loader lors de la soumission du formulaire
        document.getElementById('searchForm').addEventListener('submit', function() {
            document.getElementById('loadingOverlay').classList.add('show');
        });
        
        // Afficher le loader lors du clic sur un filtre
        document.querySelectorAll('.filter-btn, .quick-filter-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (!e.ctrlKey && !e.metaKey) {
                    document.getElementById('loadingOverlay').classList.add('show');
                }
            });
        });
    </script>
</body>
</html>