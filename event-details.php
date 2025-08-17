<?php
session_start();
require_once __DIR__ . '/config.php';

// Get event ID from URL
$eventId = $_GET['id'] ?? '';

if (empty($eventId)) {
    header('Location: /events.php');
    exit;
}

// Generate event details based on ID (no database needed)
$eventDetails = generateEventFromId($eventId);

function generateEventFromId($id) {
    // Create a hash from the ID for consistent data
    $hash = substr(md5($id), 0, 6);
    $hashNum = hexdec(substr($hash, 0, 2));
    
    $categories = ['Concert', 'Exposition', 'Théâtre', 'Festival', 'Conférence', 'Cinéma'];
    $venues = [
        'Le Zénith', 'L\'Olympia', 'Le Bataclan', 'Musée d\'Orsay', 
        'Centre Pompidou', 'Théâtre du Châtelet', 'Opéra Garnier',
        'La Cigale', 'Le Trianon', 'Philharmonie de Paris'
    ];
    
    $titles = [
        'Concert' => ['Jazz Night', 'Rock Festival', 'Symphonie Classique', 'Électro Party'],
        'Exposition' => ['Art Moderne', 'Photographie Contemporaine', 'Sculptures', 'Peintures Impressionnistes'],
        'Théâtre' => ['Hamlet', 'Le Malade Imaginaire', 'Cyrano de Bergerac', 'Les Misérables'],
        'Festival' => ['Festival d\'Été', 'Festival des Arts', 'Festival de Musique', 'Festival Culturel'],
        'Conférence' => ['Innovation Technologique', 'Histoire de l\'Art', 'Sciences et Société', 'Littérature Moderne'],
        'Cinéma' => ['Avant-Première', 'Rétrospective', 'Film d\'Auteur', 'Documentaire']
    ];
    
    $category = $categories[$hashNum % count($categories)];
    $venue = $venues[$hashNum % count($venues)];
    $titleOptions = $titles[$category];
    $title = $titleOptions[$hashNum % count($titleOptions)];
    
    // Generate date
    $daysOffset = $hashNum % 30;
    $eventDate = date('Y-m-d', strtotime("+$daysOffset days"));
    $eventTime = sprintf('%02d:00', 14 + ($hashNum % 8));
    
    // Generate price
    $isFree = $hashNum % 4 === 0;
    $price = $isFree ? 0 : (15 + ($hashNum % 8) * 5);
    
    return [
        'id' => $id,
        'title' => $title,
        'category' => $category,
        'venue' => $venue,
        'address' => $hashNum . ' rue de la Culture, 75001 Paris',
        'date' => $eventDate,
        'time' => $eventTime,
        'price' => $price,
        'is_free' => $isFree,
        'description' => "Découvrez cet événement exceptionnel : $title. Une expérience unique dans le cadre prestigieux de $venue.",
        'image' => '/assets/images/event-placeholder.jpg',
        'capacity' => 100 + $hashNum * 10,
        'available_seats' => 20 + $hashNum % 50
    ];
}

$event = $eventDetails;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($event['title']); ?> - Culture Radar</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    
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
        
        .event-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 3rem;
        }
        
        .event-main {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 2rem;
        }
        
        .event-sidebar {
            height: fit-content;
        }
        
        .booking-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 2rem;
            position: sticky;
            top: 100px;
        }
        
        .event-title {
            color: white;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .event-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 2rem;
        }
        
        .event-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .event-description {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.8;
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }
        
        .price-display {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 1rem;
        }
        
        .book-button {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
        }
        
        .book-button:hover {
            transform: translateY(-2px);
        }
        
        .seats-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .event-content {
                grid-template-columns: 1fr;
            }
            
            .booking-card {
                position: relative;
                top: 0;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="event-details-container">
        <div class="event-header">
            <div style="max-width: 1200px; margin: 0 auto; padding: 0 2rem;">
                <div class="category-badge" style="display: inline-block; padding: 0.5rem 1rem; background: rgba(102, 126, 234, 0.2); border: 1px solid #667eea; border-radius: 20px; color: #667eea; margin-bottom: 1rem;">
                    <?php echo htmlspecialchars($event['category']); ?>
                </div>
                <h1 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h1>
                <div class="event-meta">
                    <div class="event-meta-item">
                        <i class="fas fa-location-dot"></i>
                        <span><?php echo htmlspecialchars($event['venue']); ?></span>
                    </div>
                    <div class="event-meta-item">
                        <i class="fas fa-calendar"></i>
                        <span><?php echo date('d/m/Y', strtotime($event['date'])); ?></span>
                    </div>
                    <div class="event-meta-item">
                        <i class="fas fa-clock"></i>
                        <span><?php echo $event['time']; ?></span>
                    </div>
                    <?php if ($event['is_free']): ?>
                        <div class="event-meta-item" style="color: #48bb78;">
                            <i class="fas fa-gift"></i>
                            <span>Gratuit</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="event-content">
            <div class="event-main">
                <h2 style="color: white; margin-bottom: 1rem;">À propos de cet événement</h2>
                <div class="event-description">
                    <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                </div>
                
                <h3 style="color: white; margin-bottom: 1rem;">Informations pratiques</h3>
                <div style="color: rgba(255, 255, 255, 0.7); line-height: 2;">
                    <p><i class="fas fa-map-marker-alt"></i> <strong>Adresse complète:</strong> <?php echo htmlspecialchars($event['address']); ?></p>
                    <p><i class="fas fa-train"></i> <strong>Transports:</strong> Métro Ligne 1, 4, 7 - Station Châtelet</p>
                    <p><i class="fas fa-wheelchair"></i> <strong>Accessibilité:</strong> Lieu accessible aux PMR</p>
                    <p><i class="fas fa-users"></i> <strong>Capacité:</strong> <?php echo $event['capacity']; ?> places</p>
                </div>
            </div>
            
            <div class="event-sidebar">
                <div class="booking-card">
                    <div class="price-display">
                        <?php if ($event['is_free']): ?>
                            Gratuit
                        <?php else: ?>
                            <?php echo $event['price']; ?> €
                        <?php endif; ?>
                    </div>
                    
                    <div class="seats-info">
                        <span>Places disponibles</span>
                        <span><?php echo $event['available_seats']; ?> / <?php echo $event['capacity']; ?></span>
                    </div>
                    
                    <div style="background: rgba(255, 255, 255, 0.05); height: 8px; border-radius: 4px; margin-bottom: 2rem;">
                        <div style="background: linear-gradient(90deg, #667eea, #764ba2); height: 100%; border-radius: 4px; width: <?php echo (($event['capacity'] - $event['available_seats']) / $event['capacity'] * 100); ?>%;"></div>
                    </div>
                    
                    <button class="book-button" onclick="alert('Fonctionnalité de réservation à venir!')">
                        <i class="fas fa-ticket"></i> Réserver maintenant
                    </button>
                    
                    <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid rgba(255, 255, 255, 0.1);">
                        <button style="width: 100%; padding: 0.75rem; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: white; border-radius: 8px; cursor: pointer; margin-bottom: 0.5rem;">
                            <i class="fas fa-heart"></i> Ajouter aux favoris
                        </button>
                        <button style="width: 100%; padding: 0.75rem; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: white; border-radius: 8px; cursor: pointer;">
                            <i class="fas fa-share"></i> Partager
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="/assets/js/main.js"></script>
</body>
</html>