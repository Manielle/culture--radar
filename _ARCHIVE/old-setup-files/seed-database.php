<?php
/**
 * Database Seeder - Create Mock Data for All Tables
 * Run this script to populate your database with test data
 */

// Set proper UTF-8 encoding
header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding('UTF-8');

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';

echo "<pre>";
echo "====================================\n";
echo "   Culture Radar Database Seeder   \n";
echo "====================================\n\n";

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Start transaction
    $pdo->beginTransaction();
    
    // 1. CREATE MOCK USERS
    echo "1. Creating mock users...\n";
    
    $users = [
        ['Marie Dupont', 'marie.dupont@email.fr', 'Paris', true],
        ['Jean Martin', 'jean.martin@email.fr', 'Lyon', true],
        ['Sophie Bernard', 'sophie.bernard@email.fr', 'Marseille', false],
        ['Pierre Durand', 'pierre.durand@email.fr', 'Toulouse', true],
        ['Camille Petit', 'camille.petit@email.fr', 'Nice', true],
        ['Lucas Moreau', 'lucas.moreau@email.fr', 'Nantes', false],
        ['Emma Laurent', 'emma.laurent@email.fr', 'Strasbourg', true],
        ['Thomas Simon', 'thomas.simon@email.fr', 'Bordeaux', true],
        ['Léa Michel', 'lea.michel@email.fr', 'Lille', false],
        ['Hugo Garcia', 'hugo.garcia@email.fr', 'Rennes', true],
        ['Admin User', 'admin@culture-radar.fr', 'Paris', true],
        ['Test User', 'test@culture-radar.fr', 'Paris', true]
    ];
    
    $userIds = [];
    $stmt = $pdo->prepare("
        INSERT INTO users (name, email, password, accepts_newsletter, is_active, onboarding_completed, created_at, last_login)
        VALUES (?, ?, ?, ?, 1, 1, ?, ?)
    ");
    
    foreach ($users as $user) {
        $password = password_hash('password123', PASSWORD_DEFAULT); // Default password for all test users
        $createdAt = date('Y-m-d H:i:s', strtotime('-' . rand(1, 365) . ' days'));
        $lastLogin = date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days'));
        
        $stmt->execute([
            $user[0], // name
            $user[1], // email
            $password,
            $user[3] ? 1 : 0, // newsletter
            $createdAt,
            $lastLogin
        ]);
        
        $userIds[] = $pdo->lastInsertId();
        echo "  ✓ Created user: {$user[0]}\n";
    }
    
    // 2. CREATE USER PROFILES
    echo "\n2. Creating user profiles...\n";
    
    $preferences = [
        ['art', 'music', 'theater'],
        ['music', 'festival', 'dance'],
        ['cinema', 'literature', 'theater'],
        ['heritage', 'art', 'exposition'],
        ['festival', 'music', 'dance'],
        ['theater', 'cinema', 'literature'],
        ['art', 'heritage', 'exposition'],
        ['music', 'dance', 'festival'],
        ['cinema', 'theater', 'art'],
        ['literature', 'heritage', 'exposition'],
        ['art', 'music', 'theater', 'cinema', 'festival'],
        ['all']
    ];
    
    $locations = ['Paris', 'Lyon', 'Marseille', 'Toulouse', 'Nice', 'Nantes', 'Strasbourg', 'Bordeaux', 'Lille', 'Rennes', 'Paris', 'Paris'];
    
    $stmt = $pdo->prepare("
        INSERT INTO user_profiles (user_id, location, preferences, budget_max, notification_enabled, onboarding_completed, created_at)
        VALUES (?, ?, ?, ?, ?, 1, ?)
    ");
    
    foreach ($userIds as $index => $userId) {
        $userLocation = $locations[$index] ?? 'Paris';
        $userPrefs = json_encode($preferences[$index] ?? ['art', 'music']);
        $budget = rand(0, 100);
        $notifications = rand(0, 1);
        $createdAt = date('Y-m-d H:i:s', strtotime('-' . rand(1, 365) . ' days'));
        
        $stmt->execute([$userId, $userLocation, $userPrefs, $budget, $notifications, $createdAt]);
        echo "  ✓ Created profile for user ID: $userId\n";
    }
    
    // 3. CREATE VENUES
    echo "\n3. Creating venues...\n";
    
    $venues = [
        ['Le Zénith de Paris', '211 Avenue Jean Jaurès', 'Paris', '75019', 48.8908, 2.3926],
        ['Opéra Garnier', 'Place de l\'Opéra', 'Paris', '75009', 48.8720, 2.3316],
        ['Musée du Louvre', 'Rue de Rivoli', 'Paris', '75001', 48.8606, 2.3376],
        ['Centre Pompidou', 'Place Georges-Pompidou', 'Paris', '75004', 48.8606, 2.3522],
        ['Théâtre du Châtelet', '1 Place du Châtelet', 'Paris', '75001', 48.8573, 2.3474],
        ['La Cigale', '120 Boulevard de Rochechouart', 'Paris', '75018', 48.8823, 2.3402],
        ['Olympia', '28 Boulevard des Capucines', 'Paris', '75009', 48.8701, 2.3282],
        ['Musée d\'Orsay', '1 Rue de la Légion d\'Honneur', 'Paris', '75007', 48.8600, 2.3266],
        ['Grand Palais', '3 Avenue du Général Eisenhower', 'Paris', '75008', 48.8661, 2.3125],
        ['Philharmonie de Paris', '221 Avenue Jean Jaurès', 'Paris', '75019', 48.8899, 2.3939]
    ];
    
    $venueIds = [];
    $stmt = $pdo->prepare("
        INSERT INTO venues (name, address, city, postal_code, latitude, longitude, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    
    foreach ($venues as $venue) {
        $stmt->execute($venue);
        $venueIds[] = $pdo->lastInsertId();
        echo "  ✓ Created venue: {$venue[0]}\n";
    }
    
    // 4. CREATE EVENTS
    echo "\n4. Creating events...\n";
    
    $eventTitles = [
        'Festival de Jazz d\'Été',
        'Exposition Impressionniste',
        'Concert Symphonique',
        'Pièce de Théâtre Classique',
        'Soirée Electro',
        'Ballet Contemporain',
        'Conférence Art Moderne',
        'Festival Street Art',
        'Opéra Carmen',
        'Stand-up Comedy Night',
        'Cinéma en Plein Air',
        'Atelier Poterie',
        'Visite Guidée Historique',
        'Concert Rock Indépendant',
        'Exposition Photo',
        'Spectacle de Magie',
        'Festival Gastronomique',
        'Marathon Culturel',
        'Soirée Poésie',
        'Concert Jazz Manouche'
    ];
    
    $categories = ['musique', 'exposition', 'theatre', 'festival', 'cinema', 'danse', 'conference', 'atelier', 'visite', 'spectacle'];
    
    $stmt = $pdo->prepare("
        INSERT INTO events (
            title, description, category, venue_name, address, city, postal_code,
            latitude, longitude, start_date, end_date, price, is_free,
            image_url, external_url, is_active, featured, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $eventIds = [];
    foreach ($eventTitles as $index => $title) {
        $venue = $venues[array_rand($venues)];
        $category = $categories[array_rand($categories)];
        $startDate = date('Y-m-d H:i:s', strtotime('+' . rand(1, 90) . ' days'));
        $endDate = date('Y-m-d H:i:s', strtotime($startDate . ' +' . rand(2, 6) . ' hours'));
        $isFree = rand(0, 100) < 30; // 30% chance of being free
        $price = $isFree ? 0 : rand(5, 80);
        $featured = rand(0, 100) < 20; // 20% chance of being featured
        
        $description = "Découvrez $title, un événement exceptionnel dans la catégorie $category. ";
        $description .= "Une expérience culturelle unique à ne pas manquer, qui vous transportera dans un univers artistique fascinant. ";
        $description .= "Que vous soyez amateur ou passionné, cet événement saura vous séduire par sa qualité et son originalité.";
        
        $imageUrl = "https://picsum.photos/seed/event$index/800/600";
        $externalUrl = "https://culture-radar.fr/event/" . $index;
        
        $stmt->execute([
            $title,
            $description,
            $category,
            $venue[0], // venue name
            $venue[1], // address
            $venue[2], // city
            $venue[3], // postal code
            $venue[4], // latitude
            $venue[5], // longitude
            $startDate,
            $endDate,
            $price,
            $isFree ? 1 : 0,
            $imageUrl,
            $externalUrl,
            1, // is_active
            $featured ? 1 : 0
        ]);
        
        $eventIds[] = $pdo->lastInsertId();
        echo "  ✓ Created event: $title\n";
    }
    
    // 5. CREATE USER INTERACTIONS (Favorites, Attendance, Reviews)
    echo "\n5. Creating user interactions...\n";
    
    // Create favorites
    $stmt = $pdo->prepare("
        INSERT IGNORE INTO user_favorites (user_id, event_id, created_at)
        VALUES (?, ?, NOW())
    ");
    
    $favoriteCount = 0;
    foreach ($userIds as $userId) {
        $numFavorites = rand(2, 8);
        $favoriteEvents = array_rand($eventIds, min($numFavorites, count($eventIds)));
        if (!is_array($favoriteEvents)) {
            $favoriteEvents = [$favoriteEvents];
        }
        
        foreach ($favoriteEvents as $eventIndex) {
            $stmt->execute([$userId, $eventIds[$eventIndex]]);
            $favoriteCount++;
        }
    }
    echo "  ✓ Created $favoriteCount favorites\n";
    
    // Create attendance records
    $stmt = $pdo->prepare("
        INSERT IGNORE INTO event_attendance (user_id, event_id, status, created_at)
        VALUES (?, ?, ?, NOW())
    ");
    
    $statuses = ['interested', 'going', 'attended'];
    $attendanceCount = 0;
    foreach ($userIds as $userId) {
        $numEvents = rand(3, 10);
        $attendEvents = array_rand($eventIds, min($numEvents, count($eventIds)));
        if (!is_array($attendEvents)) {
            $attendEvents = [$attendEvents];
        }
        
        foreach ($attendEvents as $eventIndex) {
            $status = $statuses[array_rand($statuses)];
            $stmt->execute([$userId, $eventIds[$eventIndex], $status]);
            $attendanceCount++;
        }
    }
    echo "  ✓ Created $attendanceCount attendance records\n";
    
    // Create reviews
    $stmt = $pdo->prepare("
        INSERT INTO event_reviews (user_id, event_id, rating, comment, created_at)
        VALUES (?, ?, ?, ?, ?)
    ");
    
    $reviewComments = [
        "Excellent événement! Je recommande vivement.",
        "Très belle expérience, j'y retournerai avec plaisir.",
        "Correct mais sans plus. Le prix est un peu élevé.",
        "Magnifique! Un moment inoubliable.",
        "Décevant, je m'attendais à mieux.",
        "Parfait pour une sortie en famille.",
        "Une découverte culturelle enrichissante.",
        "Bon rapport qualité-prix.",
        "L'organisation était impeccable.",
        "Un peu trop court à mon goût mais très bien."
    ];
    
    $reviewCount = 0;
    foreach ($userIds as $userId) {
        $numReviews = rand(0, 5);
        if ($numReviews > 0) {
            $reviewEvents = array_rand($eventIds, min($numReviews, count($eventIds)));
            if (!is_array($reviewEvents)) {
                $reviewEvents = [$reviewEvents];
            }
            
            foreach ($reviewEvents as $eventIndex) {
                $rating = rand(3, 5); // Ratings between 3 and 5
                $comment = $reviewComments[array_rand($reviewComments)];
                $createdAt = date('Y-m-d H:i:s', strtotime('-' . rand(1, 60) . ' days'));
                $stmt->execute([$userId, $eventIds[$eventIndex], $rating, $comment, $createdAt]);
                $reviewCount++;
            }
        }
    }
    echo "  ✓ Created $reviewCount reviews\n";
    
    // 6. CREATE NOTIFICATIONS
    echo "\n6. Creating notifications...\n";
    
    $notificationTypes = ['event_reminder', 'new_recommendation', 'price_drop', 'event_update', 'weekly_digest'];
    $notificationTitles = [
        'Rappel: Événement demain',
        'Nouvelle recommandation pour vous',
        'Baisse de prix sur un événement',
        'Mise à jour d\'événement',
        'Votre sélection hebdomadaire'
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO notifications (user_id, type, title, message, is_read, created_at)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    $notificationCount = 0;
    foreach ($userIds as $userId) {
        $numNotifications = rand(2, 8);
        for ($i = 0; $i < $numNotifications; $i++) {
            $typeIndex = array_rand($notificationTypes);
            $type = $notificationTypes[$typeIndex];
            $title = $notificationTitles[$typeIndex];
            $message = "Nous avons une information importante concernant vos événements culturels favoris.";
            $isRead = rand(0, 100) < 70 ? 1 : 0; // 70% read
            $createdAt = date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days'));
            
            $stmt->execute([$userId, $type, $title, $message, $isRead, $createdAt]);
            $notificationCount++;
        }
    }
    echo "  ✓ Created $notificationCount notifications\n";
    
    // 7. CREATE BADGES (if table exists)
    echo "\n7. Creating badges...\n";
    
    try {
        $stmt = $pdo->prepare("
            INSERT IGNORE INTO badges (name, description, icon, points, criteria, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        
        $badges = [
            ['Explorer', 'Participez à 5 événements', '🗺️', 50, json_encode(['events_attended' => 5])],
            ['Mélomane', 'Assistez à 10 concerts', '🎵', 100, json_encode(['category' => 'musique', 'count' => 10])],
            ['Critique', 'Écrivez 10 avis', '✍️', 75, json_encode(['reviews_written' => 10])],
            ['Fidèle', 'Connectez-vous 30 jours', '🏆', 150, json_encode(['days_active' => 30])],
            ['Social', 'Invitez 5 amis', '👥', 100, json_encode(['friends_invited' => 5])],
            ['Culturel', 'Explorez toutes les catégories', '🎭', 200, json_encode(['all_categories' => true])],
            ['Matinal', 'Réservez 5 événements en matinée', '🌅', 50, json_encode(['morning_events' => 5])],
            ['Économe', 'Participez à 10 événements gratuits', '💰', 75, json_encode(['free_events' => 10])]
        ];
        
        foreach ($badges as $badge) {
            $stmt->execute($badge);
            echo "  ✓ Created badge: {$badge[0]}\n";
        }
        
        // Assign some badges to users
        $stmt = $pdo->prepare("
            INSERT IGNORE INTO user_badges (user_id, badge_id, earned_at)
            VALUES (?, ?, ?)
        ");
        
        foreach ($userIds as $userId) {
            $numBadges = rand(0, 4);
            if ($numBadges > 0) {
                $userBadges = array_rand(range(1, count($badges)), $numBadges);
                if (!is_array($userBadges)) {
                    $userBadges = [$userBadges];
                }
                
                foreach ($userBadges as $badgeId) {
                    $earnedAt = date('Y-m-d H:i:s', strtotime('-' . rand(1, 100) . ' days'));
                    $stmt->execute([$userId, $badgeId + 1, $earnedAt]);
                }
            }
        }
        echo "  ✓ Assigned badges to users\n";
        
    } catch (Exception $e) {
        echo "  ⚠ Badges table not found (skipping)\n";
    }
    
    // Commit transaction
    $pdo->commit();
    
    echo "\n====================================\n";
    echo "✅ Database seeding completed!\n";
    echo "====================================\n\n";
    
    echo "Summary:\n";
    echo "- " . count($userIds) . " users created\n";
    echo "- " . count($userIds) . " user profiles created\n";
    echo "- " . count($venueIds) . " venues created\n";
    echo "- " . count($eventIds) . " events created\n";
    echo "- $favoriteCount favorites created\n";
    echo "- $attendanceCount attendance records created\n";
    echo "- $reviewCount reviews created\n";
    echo "- $notificationCount notifications created\n";
    
    echo "\n📝 Test Accounts:\n";
    echo "Email: admin@culture-radar.fr\n";
    echo "Email: test@culture-radar.fr\n";
    echo "Email: marie.dupont@email.fr\n";
    echo "Password for all: password123\n";
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Rolling back all changes...\n";
}

echo "</pre>";
?>