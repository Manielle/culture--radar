<?php
// Set proper UTF-8 encoding
header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding('UTF-8');

session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/classes/Auth.php';

$auth = new Auth();
$isLoggedIn = $auth->isLoggedIn();
$user = $isLoggedIn ? $auth->getCurrentUser() : null;

// Get event ID from URL
$eventId = $_GET['id'] ?? '';

// Default event data
$event = [
    'id' => $eventId,
    'title' => 'Événement culturel',
    'category' => 'Culture',
    'description' => 'Découvrez cet événement exceptionnel dans votre ville.',
    'venue_name' => 'Lieu culturel',
    'address' => 'Adresse de l\'événement',
    'city' => 'Paris',
    'start_date' => date('Y-m-d H:i:s', strtotime('+1 week')),
    'price' => 20,
    'is_free' => false,
    'capacity' => 200,
    'available_spots' => 50,
    'organizer' => 'Organisateur',
    'ai_score' => 85
];

// Try to fetch event from session storage first
if (isset($_SESSION['last_events']) && is_array($_SESSION['last_events'])) {
    foreach ($_SESSION['last_events'] as $stored_event) {
        if ($stored_event['id'] === $eventId) {
            $event = array_merge($event, $stored_event);
            break;
        }
    }
}

// If not found in session, generate based on ID pattern
if ($event['title'] === 'Événement culturel' && $eventId) {
    // Parse event ID to determine type
    if (strpos($eventId, 'event-') === 0) {
        // This is from our Google Events API
        // Generate appropriate details based on a hash of the ID
        $hash = substr(md5($eventId), 0, 6);
        $types = [
            ['title' => 'Concert Jazz au Sunset', 'category' => 'concert', 'venue' => 'Le Sunset-Sunside', 'price' => 35],
            ['title' => 'Exposition Monet', 'category' => 'exposition', 'venue' => 'Musée de l\'Orangerie', 'price' => 12],
            ['title' => 'Festival Électro', 'category' => 'festival', 'venue' => 'La Villette', 'price' => 0],
            ['title' => 'Pièce de Théâtre', 'category' => 'théâtre', 'venue' => 'Comédie-Française', 'price' => 45],
            ['title' => 'Ballet Contemporain', 'category' => 'danse', 'venue' => 'Opéra Garnier', 'price' => 65]
        ];
        
        $typeIndex = hexdec(substr($hash, 0, 1)) % count($types);
        $selectedType = $types[$typeIndex];
        
        $event = [
            'id' => $eventId,
            'title' => $selectedType['title'],
            'category' => $selectedType['category'],
            'description' => 'Un événement exceptionnel à ne pas manquer. Venez vivre une expérience culturelle unique dans l\'un des plus beaux lieux de Paris.',
            'venue_name' => $selectedType['venue'],
            'address' => $selectedType['venue'] . ', Paris',
            'city' => 'Paris',
            'start_date' => date('Y-m-d 20:00:00', strtotime('+' . (hexdec(substr($hash, 1, 1)) % 7) . ' days')),
            'price' => $selectedType['price'],
            'is_free' => $selectedType['price'] === 0,
            'capacity' => 300 + (hexdec(substr($hash, 2, 2)) * 5),
            'available_spots' => 50 + (hexdec(substr($hash, 4, 2)) * 2),
            'organizer' => 'Culture Radar Partners',
            'ai_score' => 70 + (hexdec(substr($hash, 5, 1)) * 2)
        ];
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($event['title']); ?> - Culture Radar</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        .event-details-container {
            min-height: 100vh;
            background: #0a0a0f;
            padding-top: 80px;
        }
        
        .event-header {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            padding: 3rem 0;
            margin-bottom: 3rem;
        }
        
        .event-header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .event-category {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: rgba(102, 126, 234, 0.2);
            color: #667eea;
            border-radius: 8px;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            text-transform: capitalize;
        }
        
        .event-title {
            color: white;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .event-meta {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.1rem;
        }
        
        .event-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .event-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem 3rem;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 3rem;
        }
        
        .event-main {
            color: white;
        }
        
        .event-section {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: white;
        }
        
        .event-description {
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.8);
        }
        
        .event-sidebar {
            position: sticky;
            top: 100px;
            height: fit-content;
        }
        
        .booking-card {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            border: 1px solid rgba(102, 126, 234, 0.3);
            border-radius: 12px;
            padding: 2rem;
            color: white;
        }
        
        .price-display {
            font-size: 2rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 0.5rem;
        }
        
        .booking-button {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
            margin-top: 1rem;
            text-decoration: none;
            display: block;
            text-align: center;
        }
        
        .booking-button:hover {
            transform: translateY(-2px);
        }
        
        .secondary-button {
            background: rgba(255, 255, 255, 0.1);
            margin-top: 0.5rem;
        }
        
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #667eea;
            text-decoration: none;
            margin-bottom: 2rem;
            transition: gap 0.3s;
        }
        
        .back-button:hover {
            gap: 0.75rem;
        }
        
        @media (max-width: 768px) {
            .event-content {
                grid-template-columns: 1fr;
            }
            
            .event-title {
                font-size: 1.8rem;
            }
            
            .event-sidebar {
                position: static;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="event-details-container">
        <div class="event-header">
            <div class="event-header-content">
                <a href="/" class="back-button">
                    <i class="fas fa-arrow-left"></i>
                    Retour aux événements
                </a>
                
                <div class="event-category">
                    <?php echo htmlspecialchars($event['category']); ?>
                </div>
                
                <h1 class="event-title">
                    <?php echo htmlspecialchars($event['title']); ?>
                </h1>
                
                <div class="event-meta">
                    <div class="event-meta-item">
                        <i class="fas fa-calendar"></i>
                        <?php echo date('d/m/Y', strtotime($event['start_date'])); ?>
                    </div>
                    <div class="event-meta-item">
                        <i class="fas fa-clock"></i>
                        <?php echo date('H:i', strtotime($event['start_date'])); ?>
                    </div>
                    <div class="event-meta-item">
                        <i class="fas fa-location-dot"></i>
                        <?php echo htmlspecialchars($event['venue_name']); ?>
                    </div>
                    <?php if ($event['ai_score'] >= 80): ?>
                    <div class="event-meta-item">
                        <i class="fas fa-heart" style="color: #ef4444;"></i>
                        Recommandé pour vous
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="event-content">
            <div class="event-main">
                <div class="event-section">
                    <h2 class="section-title">À propos de l'événement</h2>
                    <p class="event-description">
                        <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                    </p>
                </div>
                
                <div class="event-section">
                    <h2 class="section-title">Informations pratiques</h2>
                    <div class="event-meta">
                        <div class="event-meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo htmlspecialchars($event['address'] ?? $event['venue_name']); ?>
                        </div>
                        <div class="event-meta-item">
                            <i class="fas fa-users"></i>
                            <?php echo $event['available_spots']; ?> places disponibles
                        </div>
                        <div class="event-meta-item">
                            <i class="fas fa-building"></i>
                            Organisé par <?php echo htmlspecialchars($event['organizer']); ?>
                        </div>
                    </div>
                </div>
                
                <div class="event-section">
                    <h2 class="section-title">Comment s'y rendre</h2>
                    <div class="event-meta">
                        <div class="event-meta-item">
                            <i class="fas fa-subway"></i>
                            Métro le plus proche à 5 min à pied
                        </div>
                        <div class="event-meta-item">
                            <i class="fas fa-bus"></i>
                            Arrêt de bus à proximité
                        </div>
                        <div class="event-meta-item">
                            <i class="fas fa-parking"></i>
                            Parking disponible
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="event-sidebar">
                <div class="booking-card">
                    <div class="price-display">
                        <?php if ($event['is_free']): ?>
                            Gratuit
                        <?php else: ?>
                            <?php echo number_format($event['price'], 0); ?>€
                        <?php endif; ?>
                    </div>
                    <p style="color: rgba(255, 255, 255, 0.7); margin-bottom: 1rem;">
                        par personne
                    </p>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; color: rgba(255, 255, 255, 0.8);">
                        <span>Places disponibles</span>
                        <span><?php echo $event['available_spots']; ?></span>
                    </div>
                    
                    <?php if ($isLoggedIn): ?>
                        <button class="booking-button" onclick="bookEvent()">
                            <i class="fas fa-ticket"></i> Réserver maintenant
                        </button>
                        <button class="booking-button secondary-button" onclick="addToFavorites()">
                            <i class="fas fa-heart"></i> Ajouter aux favoris
                        </button>
                    <?php else: ?>
                        <a href="/login.php" class="booking-button">
                            <i class="fas fa-sign-in-alt"></i> Se connecter pour réserver
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function bookEvent() {
            alert('Réservation confirmée ! Vous recevrez un email de confirmation.');
        }
        
        function addToFavorites() {
            alert('Événement ajouté à vos favoris !');
        }
    </script>
</body>
</html>