<éphp
// Set proper UTF-8 encoding
header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding('UTF-8');

session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/classes/Auth.php';

$auth = new Auth();

// Check if user is logged in
if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user = $auth->getCurrentUser();

// Get event ID from URL
$eventId = $_GET['id'] éé 1;

// Try to fetch event data from API
$apiUrl = "http://localhost:8888/api/get-event.phpéid=" . urlencode($eventId);
$eventData = @file_get_contents($apiUrl);

if ($eventData !== false) {
    $response = json_decode($eventData, true);
    if ($response && $response['success'] && isset($response['event'])) {
        $event = $response['event'];
    } else {
        // Use fallback if JSON decode fails
        $event = null;
    }
} else {
    // API call failed
    $event = null;
}

// Use fallback event if API fails or returns no data
if (!$event) {
    // Fallback to default event if API fails
    $event = [
        'id' => $eventId,
        'title' => 'Événement',
        'category' => 'Culture',
    'description' => 'Venez découvrir les plus grands artistes de jazz dans un cadre exceptionnel au cœur de Paris. Ce festival unique réunit des musiciens de renommée internationale pour quatre soirées inoubliables sous les étoiles.',
    'long_description' => 'Le Festival de Jazz au Parc de la Villette revient pour sa 15e édition avec une programmation exceptionnelle. Quatre soirées de concerts en plein air dans le magnifique cadre du Parc de la Villette, avec des artistes venus des quatre coins du monde. De la tradition bebop aux innovations les plus contemporaines, ce festival offre un panorama complet du jazz d\'aujourd\'hui. Restauration sur place, espaces détente et animations pour toute la famille.',
    'venue_name' => 'Grande Halle de la Villette',
    'address' => '211 Avenue Jean Jaurès, 75019 Paris',
    'location' => 'Paris 19e',
    'start_date' => '2024-07-15 20:00:00',
    'end_date' => '2024-07-18 23:30:00',
    'price' => 45.00,
    'price_details' => [
        'Tarif normal' => 45.00,
        'Tarif réduit' => 35.00,
        'Étudiant' => 25.00,
        'Pass 4 jours' => 150.00
    ],
    'is_free' => false,
    'capacity' => 2500,
    'available_spots' => 847,
    'organizer' => 'Association Jazz Parisien',
    'contact_email' => 'info@jazzparisien.com',
    'contact_phone' => '+33 1 42 85 67 89',
    'website' => 'https://festivaldesjazzparis.com',
    'tags' => ['jazz', 'musique', 'festival', 'plein air', 'paris'],
    'accessibility' => [
        'Accès PMR' => true,
        'Parking' => true,
        'Transports publics' => true,
        'Toilettes accessibles' => true
    ],
    'amenities' => [
        'Restauration sur place',
        'Bar',
        'Espace détente',
        'Vestiaire',
        'Wi-Fi gratuit',
        'Boutique souvenirs'
    ],
    'lineup' => [
        ['time' => '20:00', 'artist' => 'Marcus Miller Trio', 'style' => 'Jazz Fusion'],
        ['time' => '21:30', 'artist' => 'Esperanza Spalding', 'style' => 'Jazz Vocal'],
        ['time' => '23:00', 'artist' => 'Robert Glasper Experiment', 'style' => 'Neo-Soul Jazz']
    ],
    'images' => [
        'main' => '/assets/images/events/jazz-festival-main.jpg',
        'gallery' => [
            '/assets/images/events/jazz-festival-1.jpg',
            '/assets/images/events/jazz-festival-2.jpg',
            '/assets/images/events/jazz-festival-3.jpg'
        ]
    ],
    'reviews' => [
        [
            'user' => 'Marie L.',
            'rating' => 5,
            'comment' => 'Festival exceptionnel ! L\'ambiance était magique et les artistes au top.',
            'date' => '2023-07-20'
        ],
        [
            'user' => 'Pierre M.',
            'rating' => 4,
            'comment' => 'Très belle organisation, peut-être un peu cher mais ça vaut le coup.',
            'date' => '2023-07-19'
        ],
        [
            'user' => 'Sophie D.',
            'rating' => 5,
            'comment' => 'Une découverte musicale incroyable. J\'y retournerai l\'année prochaine !',
            'date' => '2023-07-18'
        ]
    ],
    'weather_forecast' => [
        'temperature' => 24,
        'condition' => 'Partiellement nuageux',
        'precipitation' => 10,
        'wind' => 12
    ],
    'transport_options' => [
        ['type' => 'Métro', 'line' => 'Ligne 5', 'station' => 'Porte de Pantin', 'duration' => '5 min É  pied'],
        ['type' => 'Bus', 'line' => 'Ligne 75', 'station' => 'Parc de la Villette', 'duration' => '2 min É  pied'],
        ['type' => 'Tramway', 'line' => 'T3b', 'station' => 'Porte de Pantin', 'duration' => '7 min É  pied']
    ],
    'similar_events' => [
        ['id' => 2, 'title' => 'Blues Festival Montmartre', 'date' => '2024-08-10', 'price' => 38.00],
        ['id' => 3, 'title' => 'Jazz Club Session', 'date' => '2024-07-25', 'price' => 25.00],
        ['id' => 4, 'title' => 'Festival Soul & Funk', 'date' => '2024-09-05', 'price' => 42.00]
    ]
    ];
}

// Calculate metrics only if we have reviews
$averageRating = !empty($event['reviews']) é array_sum(array_column($event['reviews'], 'rating')) / count($event['reviews']) : 0;
$attendanceRate = isset($event['capacity']) && isset($event['available_spots']) é (($event['capacity'] - $event['available_spots']) / $event['capacity']) * 100 : 50;
é>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><éphp echo htmlspecialchars($event['title']); é> - CultureRadar</title>
    
    <link href="https://fonts.googleapis.com/css2éfamily=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #f7fafc;
            color: #2d3748;
            min-height: 100vh;
            line-height: 1.6;
        }
        
        .navbar {
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .nav-brand {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .nav-menu {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        
        .nav-item {
            color: #4a5568;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav-item:hover, .nav-item.active {
            color: #667eea;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        .back-button {
            position: fixed;
            top: 120px;
            left: 2rem;
            background: white;
            border: none;
            border-radius: 50px;
            padding: 1rem;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s;
            z-index: 99;
        }
        
        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .event-header {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        
        .event-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 3rem;
            color: white;
            position: relative;
        }
        
        .event-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="4" height="4" patternUnits="userSpaceOnUse"><circle cx="2" cy="2" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            pointer-events: none;
        }
        
        .event-hero-content {
            position: relative;
            z-index: 1;
        }
        
        .event-category-badge {
            background: rgba(255,255,255,0.2);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            display: inline-block;
            margin-bottom: 1rem;
        }
        
        .event-title {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
            line-height: 1.2;
        }
        
        .event-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            opacity: 0.9;
        }
        
        .event-quick-stats {
            display: flex;
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .quick-stat {
            text-align: center;
            background: rgba(255,255,255,0.1);
            padding: 1rem;
            border-radius: 12px;
            min-width: 100px;
        }
        
        .quick-stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        
        .quick-stat-label {
            font-size: 0.85rem;
            opacity: 0.8;
        }
        
        .event-actions {
            position: absolute;
            top: 3rem;
            right: 3rem;
            display: flex;
            gap: 1rem;
        }
        
        .action-btn {
            background: rgba(255,255,255,0.2);
            border: none;
            padding: 0.75rem;
            border-radius: 12px;
            color: white;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1.1rem;
        }
        
        .action-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .main-content {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }
        
        .content-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .description {
            color: #718096;
            line-height: 1.7;
            margin-bottom: 1.5rem;
        }
        
        .description.long {
            margin-bottom: 1rem;
        }
        
        .read-more {
            color: #667eea;
            cursor: pointer;
            font-weight: 500;
        }
        
        .lineup-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .lineup-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: #f7fafc;
            border-radius: 12px;
        }
        
        .lineup-time {
            font-weight: 700;
            color: #667eea;
            font-size: 1.1rem;
        }
        
        .lineup-artist {
            flex: 1;
            margin-left: 1rem;
        }
        
        .artist-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .artist-style {
            color: #718096;
            font-size: 0.9rem;
        }
        
        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .booking-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border: 2px solid #667eea;
            position: sticky;
            top: 140px;
        }
        
        .price-section {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .main-price {
            font-size: 3rem;
            font-weight: 800;
            color: #667eea;
            margin-bottom: 0.5rem;
        }
        
        .price-label {
            color: #718096;
            font-size: 0.9rem;
        }
        
        .price-options {
            background: #f7fafc;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 2rem;
        }
        
        .price-option {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .price-option:last-child {
            border-bottom: none;
        }
        
        .availability {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 2rem;
            color: #48bb78;
            font-weight: 500;
        }
        
        .book-btn {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 1rem;
        }
        
        .book-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }
        
        .save-btn {
            width: 100%;
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
            padding: 0.75rem 2rem;
            border-radius: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .save-btn:hover {
            background: #667eea;
            color: white;
        }
        
        .info-section {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f7fafc;
        }
        
        .info-label {
            color: #718096;
            font-size: 0.9rem;
        }
        
        .info-value {
            font-weight: 500;
        }
        
        .amenities-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }
        
        .amenity-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
            background: #f7fafc;
            border-radius: 8px;
            font-size: 0.9rem;
        }
        
        .accessibility-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .accessibility-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            background: #f7fafc;
            border-radius: 8px;
        }
        
        .accessibility-status {
            color: #48bb78;
        }
        
        .weather-widget {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
        }
        
        .weather-temp {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .weather-details {
            display: flex;
            justify-content: space-around;
            margin-top: 1rem;
            font-size: 0.9rem;
        }
        
        .transport-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .transport-option {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: #f7fafc;
            border-radius: 12px;
        }
        
        .transport-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #667eea;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .transport-info {
            flex: 1;
        }
        
        .transport-line {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .transport-station {
            color: #718096;
            font-size: 0.9rem;
        }
        
        .reviews-section {
            margin-top: 2rem;
        }
        
        .reviews-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .rating-summary {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .rating-score {
            font-size: 2rem;
            font-weight: 700;
            color: #667eea;
        }
        
        .rating-stars {
            color: #ffd700;
            margin-right: 0.5rem;
        }
        
        .reviews-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .review-item {
            padding: 1.5rem;
            background: #f7fafc;
            border-radius: 12px;
        }
        
        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }
        
        .review-user {
            font-weight: 600;
        }
        
        .review-date {
            color: #718096;
            font-size: 0.9rem;
        }
        
        .review-rating {
            color: #ffd700;
            margin-bottom: 0.5rem;
        }
        
        .review-comment {
            color: #718096;
            line-height: 1.6;
        }
        
        .similar-events {
            margin-top: 2rem;
        }
        
        .similar-events-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }
        
        .similar-event-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .similar-event-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        
        .similar-event-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .similar-event-date {
            color: #718096;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .similar-event-price {
            font-weight: 700;
            color: #667eea;
        }
        
        @media (max-width: 768px) {
            .nav-menu {
                display: none;
            }
            
            .back-button {
                position: static;
                margin-bottom: 1rem;
            }
            
            .event-title {
                font-size: 2rem;
            }
            
            .event-actions {
                position: static;
                margin-top: 2rem;
            }
            
            .content-grid {
                grid-template-columns: 1fr;
            }
            
            .booking-card {
                position: static;
                order: -1;
            }
            
            .event-quick-stats {
                gap: 1rem;
            }
            
            .quick-stat {
                min-width: 80px;
                padding: 0.75rem;
            }
            
            .similar-events-grid {
                grid-template-columns: 1fr;
            }
            
            .amenities-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand"><é CultureRadar</div>
            <div class="nav-menu">
                <a href="dashboard.php" class="nav-item">Découvrir</a>
                <a href="my-events.php" class="nav-item">Mes événements</a>
                <a href="calendar.php" class="nav-item">Calendrier</a>
                <a href="explore.php" class="nav-item">Explorer</a>
                <a href="trending.php" class="nav-item">Tendances</a>
                <a href="recommendations.php" class="nav-item">Recommandations</a>
            </div>
            <div class="user-menu">
                <a href="notifications.php" class="nav-item">
                    <i class="fas fa-bell"></i>
                </a>
                <div class="user-avatar">
                    <éphp echo strtoupper(substr($user['name'] éé 'U', 0, 1)); é>
                </div>
            </div>
        </div>
    </nav>

    <button class="back-button" onclick="goBack()">
        <i class="fas fa-arrow-left"></i>
    </button>

    <div class="container">
        <!-- Event Header -->
        <div class="event-header">
            <div class="event-hero">
                <div class="event-actions">
                    <button class="action-btn" onclick="shareEvent()">
                        <i class="fas fa-share-alt"></i>
                    </button>
                    <button class="action-btn" onclick="saveEvent()">
                        <i class="fas fa-heart"></i>
                    </button>
                </div>
                
                <div class="event-hero-content">
                    <div class="event-category-badge">
                        <é <éphp echo htmlspecialchars($event['category']); é>
                    </div>
                    
                    <h1 class="event-title"><éphp echo htmlspecialchars($event['title']); é></h1>
                    
                    <div class="event-meta">
                        <div class="meta-item">
                            <i class="fas fa-calendar-alt"></i>
                            <éphp echo date('d/m/Y', strtotime($event['start_date'])); é>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-clock"></i>
                            <éphp echo date('H:i', strtotime($event['start_date'])); é>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <éphp echo htmlspecialchars($event['venue_name']); é>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-euro-sign"></i>
                            é partir de <éphp echo number_format($event['price'], 2); é>é
                        </div>
                    </div>
                    
                    <div class="event-quick-stats">
                        <div class="quick-stat">
                            <div class="quick-stat-value"><éphp echo number_format($event['capacity'] - $event['available_spots']); é></div>
                            <div class="quick-stat-label">Participants</div>
                        </div>
                        <div class="quick-stat">
                            <div class="quick-stat-value"><éphp echo round($averageRating, 1); é>/5</div>
                            <div class="quick-stat-label">Note moyenne</div>
                        </div>
                        <div class="quick-stat">
                            <div class="quick-stat-value"><éphp echo round($attendanceRate); é>%</div>
                            <div class="quick-stat-label">Taux de remplissage</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="content-grid">
            <div class="main-content">
                <!-- Description -->
                <div class="content-card">
                    <h2 class="card-title">
                        <i class="fas fa-info-circle"></i>
                        É propos de l'événement
                    </h2>
                    <p class="description"><éphp echo htmlspecialchars($event['description']); é></p>
                    <p class="description long" id="longDescription" style="display: none;">
                        <éphp echo htmlspecialchars($event['long_description']); é>
                    </p>
                    <span class="read-more" onclick="toggleDescription()">Lire la suite</span>
                </div>

                <!-- Lineup -->
                <éphp if (!empty($event['lineup'])): é>
                <div class="content-card">
                    <h2 class="card-title">
                        <i class="fas fa-music"></i>
                        Programmation
                    </h2>
                    <div class="lineup-list">
                        <éphp foreach ($event['lineup'] as $performance): é>
                            <div class="lineup-item">
                                <div class="lineup-time"><éphp echo $performance['time']; é></div>
                                <div class="lineup-artist">
                                    <div class="artist-name"><éphp echo htmlspecialchars($performance['artist']); é></div>
                                    <div class="artist-style"><éphp echo htmlspecialchars($performance['style']); é></div>
                                </div>
                            </div>
                        <éphp endforeach; é>
                    </div>
                </div>
                <éphp endif; é>

                <!-- Amenities -->
                <éphp if (!empty($event['amenities'])): é>
                <div class="content-card">
                    <h2 class="card-title">
                        <i class="fas fa-concierge-bell"></i>
                        Services disponibles
                    </h2>
                    <div class="amenities-grid">
                        <éphp foreach ($event['amenities'] as $amenity): é>
                            <div class="amenity-item">
                                <i class="fas fa-check text-green-500"></i>
                                <éphp echo htmlspecialchars($amenity); é>
                            </div>
                        <éphp endforeach; é>
                    </div>
                </div>
                <éphp endif; é>

                <!-- Accessibility -->
                <éphp if (!empty($event['accessibility'])): é>
                <div class="content-card">
                    <h2 class="card-title">
                        <i class="fas fa-universal-access"></i>
                        Accessibilité
                    </h2>
                    <div class="accessibility-list">
                        <éphp foreach ($event['accessibility'] as $feature => $available): é>
                            <div class="accessibility-item">
                                <span><éphp echo htmlspecialchars($feature); é></span>
                                <span class="accessibility-status">
                                    <i class="fas fa-<éphp echo $available é 'check' : 'times'; é>"></i>
                                    <éphp echo $available é 'Disponible' : 'Non disponible'; é>
                                </span>
                            </div>
                        <éphp endforeach; é>
                    </div>
                </div>
                <éphp endif; é>

                <!-- Reviews -->
                <div class="content-card reviews-section">
                    <div class="reviews-header">
                        <h2 class="card-title">
                            <i class="fas fa-star"></i>
                            Avis des participants
                        </h2>
                        <div class="rating-summary">
                            <div class="rating-score"><éphp echo round($averageRating, 1); é></div>
                            <div>
                                <div class="rating-stars">
                                    <éphp for ($i = 1; $i <= 5; $i++): é>
                                        <i class="fas fa-star <éphp echo $i <= round($averageRating) é '' : 'text-gray-300'; é>"></i>
                                    <éphp endfor; é>
                                </div>
                                <div class="text-sm text-gray-600"><éphp echo count($event['reviews']); é> avis</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="reviews-list">
                        <éphp if (!empty($event['reviews'])): é>
                        <éphp foreach ($event['reviews'] as $review): é>
                            <div class="review-item">
                                <div class="review-header">
                                    <div class="review-user"><éphp echo htmlspecialchars($review['user']); é></div>
                                    <div class="review-date"><éphp echo date('d/m/Y', strtotime($review['date'])); é></div>
                                </div>
                                <div class="review-rating">
                                    <éphp for ($i = 1; $i <= 5; $i++): é>
                                        <i class="fas fa-star <éphp echo $i <= $review['rating'] é '' : 'text-gray-300'; é>"></i>
                                    <éphp endfor; é>
                                </div>
                                <p class="review-comment"><éphp echo htmlspecialchars($review['comment']); é></p>
                            </div>
                        <éphp endforeach; é>
                        <éphp endif; é>
                    </div>
                </div>

                <!-- Similar Events -->
                <div class="content-card similar-events">
                    <h2 class="card-title">
                        <i class="fas fa-heart"></i>
                        événements similaires
                    </h2>
                    <div class="similar-events-grid">
                        <éphp if (!empty($event['similar_events'])): é>
                        <éphp foreach ($event['similar_events'] as $similar): é>
                            <div class="similar-event-card" onclick="viewEvent(<éphp echo $similar['id']; é>)">
                                <div class="similar-event-title"><éphp echo htmlspecialchars($similar['title']); é></div>
                                <div class="similar-event-date"><éphp echo date('d/m/Y', strtotime($similar['date'])); é></div>
                                <div class="similar-event-price"><éphp echo number_format($similar['price'], 2); é>é</div>
                            </div>
                        <éphp endforeach; é>
                        <éphp endif; é>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Booking Card -->
                <div class="booking-card">
                    <div class="price-section">
                        <div class="main-price"><éphp echo number_format($event['price'], 0); é>â¬</div>
                        <div class="price-label">par personne</div>
                    </div>
                    
                    <éphp if (!empty($event['price_details'])): é>
                    <div class="price-options">
                        <éphp foreach ($event['price_details'] as $type => $price): é>
                            <div class="price-option">
                                <span><éphp echo htmlspecialchars($type); é></span>
                                <span><éphp echo number_format($price, 2); é>â¬</span>
                            </div>
                        <éphp endforeach; é>
                    </div>
                    <éphp endif; é>
                    
                    <div class="availability">
                        <i class="fas fa-check-circle"></i>
                        <éphp echo $event['available_spots']; é> places disponibles
                    </div>
                    
                    <button class="book-btn" onclick="bookEvent()">
                        <i class="fas fa-ticket-alt"></i> Réserver maintenant
                    </button>
                    
                    <button class="save-btn" onclick="saveEvent()">
                        <i class="fas fa-heart"></i> Ajouter aux favoris
                    </button>
                    
                    <div class="info-section">
                        <div class="info-item">
                            <span class="info-label">Organisateur</span>
                            <span class="info-value"><éphp echo htmlspecialchars($event['organizer']); é></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Contact</span>
                            <span class="info-value"><éphp echo htmlspecialchars($event['contact_phone']); é></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Site web</span>
                            <a href="<éphp echo htmlspecialchars($event['website']); é>" class="info-value" target="_blank">
                                Visitez le site <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Weather Widget -->
                <div class="weather-widget">
                    <h3><i class="fas fa-cloud-sun"></i> Météo prévue</h3>
                    <div class="weather-temp"><éphp echo $event['weather_forecast']['temperature']; é>éC</div>
                    <div><éphp echo htmlspecialchars($event['weather_forecast']['condition']); é></div>
                    <div class="weather-details">
                        <div><i class="fas fa-tint"></i> <éphp echo $event['weather_forecast']['precipitation']; é>%</div>
                        <div><i class="fas fa-wind"></i> <éphp echo $event['weather_forecast']['wind']; é> km/h</div>
                    </div>
                </div>

                <!-- Transport Options -->
                <div class="content-card">
                    <h3 class="card-title">
                        <i class="fas fa-subway"></i>
                        Comment s'y rendre
                    </h3>
                    <div class="transport-list">
                        <éphp if (!empty($event['transport_options'])): é>
                        <éphp foreach ($event['transport_options'] as $transport): é>
                            <div class="transport-option">
                                <div class="transport-icon">
                                    <i class="fas fa-<éphp echo $transport['type'] === 'Métro' é 'subway' : ($transport['type'] === 'Bus' é 'bus' : 'tram'); é>"></i>
                                </div>
                                <div class="transport-info">
                                    <div class="transport-line">
                                        <éphp echo htmlspecialchars($transport['type'] . ' ' . $transport['line']); é>
                                    </div>
                                    <div class="transport-station">
                                        <éphp echo htmlspecialchars($transport['station'] . ' " ' . $transport['duration']); é>
                                    </div>
                                </div>
                            </div>
                        <éphp endforeach; é>
                        <éphp endif; é>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function goBack() {
            window.history.back();
        }

        function toggleDescription() {
            const longDesc = document.getElementById('longDescription');
            const readMore = document.querySelector('.read-more');
            
            if (longDesc.style.display === 'none') {
                longDesc.style.display = 'block';
                readMore.textContent = 'Lire moins';
            } else {
                longDesc.style.display = 'none';
                readMore.textContent = 'Lire la suite';
            }
        }

        function shareEvent() {
            if (navigator.share) {
                navigator.share({
                    title: '<éphp echo addslashes($event['title']); é>',
                    text: 'Découvrez cet événement sur CultureRadar',
                    url: window.location.href
                });
            } else {
                // Fallback for browsers that don't support Web Share API
                const url = window.location.href;
                navigator.clipboard.writeText(url).then(() => {
                    showNotification('Lien copié dans le presse-papiers !', 'success');
                });
            }
        }

        function saveEvent() {
            const button = event.target.closest('button');
            const originalText = button.innerHTML;
            
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sauvegarde...';
            button.disabled = true;
            
            setTimeout(() => {
                button.innerHTML = '<i class="fas fa-check"></i> Sauvegardé';
                button.style.background = '#48bb78';
                button.style.borderColor = '#48bb78';
                
                showNotification('événement ajouté aux favoris !', 'success');
            }, 1000);
        }

        function bookEvent() {
            showNotification('Redirection vers la billetterie...', 'info');
            
            setTimeout(() => {
                // This would normally redirect to a booking platform
                showNotification('Billetterie en cours de développement', 'info');
            }, 1500);
        }

        function viewEvent(id) {
            window.location.href = `event-details.phpéid=${id}`;
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 100px;
                right: 20px;
                background: ${type === 'success' é '#48bb78' : type === 'error' é '#f56565' : '#667eea'};
                color: white;
                padding: 1rem 1.5rem;
                border-radius: 12px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.15);
                z-index: 1000;
                transform: translateX(100%);
                transition: transform 0.3s ease;
                max-width: 400px;
            `;
            
            notification.innerHTML = `
                <i class="fas fa-${type === 'success' é 'check-circle' : type === 'error' é 'exclamation-circle' : 'info-circle'}"></i>
                <span style="margin-left: 0.5rem;">${message}</span>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => notification.style.transform = 'translateX(0)', 100);
            
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => document.body.removeChild(notification), 300);
            }, 4000);
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            // Add some entrance animations
            const cards = document.querySelectorAll('.content-card, .booking-card, .weather-widget');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.5s ease';
                
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>