<?php
session_start();

// Utiliser la configuration Railway
require_once __DIR__ . '/railway-config.php';

// Obtenir la connexion à la base de données
try {
    $pdo = getDatabaseConnection();
} catch(Exception $e) {
    // Log l'erreur mais continue
    error_log("Database connection error: " . $e->getMessage());
    $pdo = null;
}

// Charger les services s'ils existent
if (file_exists(__DIR__ . '/services/OpenAgendaService.php')) {
    require_once __DIR__ . '/services/OpenAgendaService.php';
}
if (file_exists(__DIR__ . '/services/GoogleEventsService.php')) {
    require_once __DIR__ . '/services/GoogleEventsService.php';
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';

// Fetch events from Google Events API for the search section
$searchEvents = [];

// Vérifier si le service existe
if (class_exists('GoogleEventsService')) {
    $googleEventsService = new GoogleEventsService();
    
    try {
        // Utiliser les événements de l'API
        $allEvents = $googleEventsService->getEventsByCity('Paris');
        
        if (empty($allEvents)) {
            // Fallback sur les événements de démo
            $allEvents = $googleEventsService->getDemoEvents('Paris');
        }
        
        // Limiter à 12 événements pour l'affichage
        $searchEvents = array_slice($allEvents, 0, 12);
        
    } catch (Exception $e) {
        error_log("Error fetching events: " . $e->getMessage());
        // Utiliser les événements de démonstration en cas d'erreur
        $searchEvents = $googleEventsService->getDemoEvents('Paris');
    }
}

// Le reste du code reste identique...
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
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="/" class="nav-logo">
                <i class="fas fa-radar"></i>
                Culture Radar
            </a>
            <ul class="nav-menu">
                <li><a href="#search" class="nav-link">Découvrir</a></li>
                <li><a href="#features" class="nav-link">Fonctionnalités</a></li>
                <li><a href="#about" class="nav-link">À propos</a></li>
                <?php if($isLoggedIn): ?>
                    <li><a href="dashboard.php" class="nav-link">Mon Espace</a></li>
                    <li><a href="logout.php" class="nav-link">Déconnexion</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="nav-link">Connexion</a></li>
                <?php endif; ?>
            </ul>
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-background">
            <div class="animated-bg"></div>
        </div>
        <div class="hero-content">
            <h1 class="hero-title">Découvrez la culture qui vous entoure</h1>
            <p class="hero-subtitle">Explorez événements, expositions et spectacles près de chez vous</p>
            <div class="hero-search">
                <form class="search-form" action="search.php" method="GET">
                    <input type="text" class="search-input" placeholder="Rechercher un événement, un lieu..." name="q">
                    <button type="submit" class="search-button">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
            <div class="hero-stats">
                <div class="stat">
                    <span class="stat-number">500+</span>
                    <span class="stat-label">Événements</span>
                </div>
                <div class="stat">
                    <span class="stat-number">50+</span>
                    <span class="stat-label">Lieux culturels</span>
                </div>
                <div class="stat">
                    <span class="stat-number">10k+</span>
                    <span class="stat-label">Utilisateurs</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Search Section with Events -->
    <section id="search" class="search-section">
        <div class="container">
            <h2 class="section-title">Que cherchez-vous aujourd'hui ?</h2>
            
            <!-- Filter Buttons -->
            <div class="filter-container">
                <button class="filter-btn active" data-filter="all">Tous</button>
                <button class="filter-btn" data-filter="Paris">Paris</button>
                <button class="filter-btn" data-filter="Lyon">Lyon</button>
                <button class="filter-btn" data-filter="Marseille">Marseille</button>
                <button class="filter-btn" data-filter="gratuit">Gratuit</button>
            </div>

            <!-- Events Grid -->
            <div class="events-grid" id="eventsGrid">
                <?php if (!empty($searchEvents)): ?>
                    <?php foreach($searchEvents as $event): ?>
                        <div class="event-card" data-city="<?php echo htmlspecialchars($event['city'] ?? 'Paris'); ?>" 
                             data-price="<?php echo isset($event['price']['is_free']) && $event['price']['is_free'] ? 'gratuit' : 'payant'; ?>">
                            <div class="event-category-tag"><?php echo htmlspecialchars($event['category'] ?? 'Événement'); ?></div>
                            <div class="event-image">
                                <?php if (!empty($event['image'])): ?>
                                    <img src="<?php echo htmlspecialchars($event['image']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>">
                                <?php else: ?>
                                    <div class="event-placeholder">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="event-content">
                                <h3 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                                <p class="event-description"><?php echo htmlspecialchars(substr($event['description'] ?? '', 0, 100)) . '...'; ?></p>
                                <div class="event-meta">
                                    <span class="event-date">
                                        <i class="far fa-calendar"></i>
                                        <?php echo htmlspecialchars($event['date'] ?? 'Date à définir'); ?>
                                    </span>
                                    <span class="event-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?php echo htmlspecialchars($event['venue'] ?? 'Lieu à définir'); ?>
                                    </span>
                                    <?php if (isset($event['price'])): ?>
                                        <span class="event-price">
                                            <?php echo htmlspecialchars($event['price']['price_text'] ?? 'Prix non spécifié'); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <a href="event-details.php?id=<?php echo urlencode($event['id']); ?>" class="event-link">
                                    En savoir plus <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-events">
                        <i class="fas fa-calendar-times"></i>
                        <p>Aucun événement disponible pour le moment.</p>
                        <p>Revenez bientôt pour découvrir de nouveaux événements !</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Include the rest of your HTML sections here... -->

    <script src="assets/js/main.js"></script>
</body>
</html>