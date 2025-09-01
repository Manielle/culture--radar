<?php
/**
 * Test et création d'événements de démonstration
 */

session_start();
require_once __DIR__ . '/config.php';

$message = '';
$events = [];
$error = '';

try {
    $pdo = Config::getPDO();
    
    // Vérifier le nombre d'événements
    $stmt = $pdo->query("SELECT COUNT(*) FROM events");
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        // Créer des événements de démonstration
        $demoEvents = [
            [
                'title' => 'Concert Jazz au Sunset',
                'description' => 'Une soirée jazz exceptionnelle avec des artistes locaux',
                'category' => 'Musique',
                'start_date' => date('Y-m-d H:i:s', strtotime('+2 days')),
                'end_date' => date('Y-m-d H:i:s', strtotime('+2 days +3 hours')),
                'venue_name' => 'Le Sunset Jazz Club',
                'venue_address' => '60 Rue des Lombards, 75001 Paris',
                'city' => 'Paris',
                'price' => 25.00,
                'is_free' => 0,
                'image_url' => 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=800',
                'is_active' => 1,
                'featured' => 1
            ],
            [
                'title' => 'Exposition Art Moderne',
                'description' => 'Découvrez les œuvres d\'artistes contemporains émergents',
                'category' => 'Art',
                'start_date' => date('Y-m-d H:i:s', strtotime('+1 week')),
                'end_date' => date('Y-m-d H:i:s', strtotime('+1 month')),
                'venue_name' => 'Galerie d\'Art Moderne',
                'venue_address' => '11 Avenue du Président Wilson, 75116 Paris',
                'city' => 'Paris',
                'price' => 0,
                'is_free' => 1,
                'image_url' => 'https://images.unsplash.com/photo-1518998053901-5348d3961a04?w=800',
                'is_active' => 1,
                'featured' => 0
            ],
            [
                'title' => 'Festival de Théâtre',
                'description' => 'Trois jours de théâtre avec des troupes internationales',
                'category' => 'Théâtre',
                'start_date' => date('Y-m-d H:i:s', strtotime('+3 days')),
                'end_date' => date('Y-m-d H:i:s', strtotime('+6 days')),
                'venue_name' => 'Théâtre National',
                'venue_address' => 'Place de la Bastille, 75012 Paris',
                'city' => 'Paris',
                'price' => 35.00,
                'is_free' => 0,
                'image_url' => 'https://images.unsplash.com/photo-1503095396549-807759245b35?w=800',
                'is_active' => 1,
                'featured' => 1
            ],
            [
                'title' => 'Atelier de Peinture',
                'description' => 'Apprenez les techniques de peinture à l\'huile',
                'category' => 'Atelier',
                'start_date' => date('Y-m-d H:i:s', strtotime('+5 days')),
                'end_date' => date('Y-m-d H:i:s', strtotime('+5 days +4 hours')),
                'venue_name' => 'Atelier des Arts',
                'venue_address' => '25 Rue de la Roquette, 75011 Paris',
                'city' => 'Paris',
                'price' => 45.00,
                'is_free' => 0,
                'image_url' => 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?w=800',
                'is_active' => 1,
                'featured' => 0
            ],
            [
                'title' => 'Concert Rock Gratuit',
                'description' => 'Concert en plein air avec plusieurs groupes rock',
                'category' => 'Musique',
                'start_date' => date('Y-m-d H:i:s', strtotime('+1 day')),
                'end_date' => date('Y-m-d H:i:s', strtotime('+1 day +5 hours')),
                'venue_name' => 'Parc de la Villette',
                'venue_address' => '211 Avenue Jean Jaurès, 75019 Paris',
                'city' => 'Paris',
                'price' => 0,
                'is_free' => 1,
                'image_url' => 'https://images.unsplash.com/photo-1459749411175-04bf5292ceea?w=800',
                'is_active' => 1,
                'featured' => 1
            ],
            [
                'title' => 'Danse Contemporaine',
                'description' => 'Spectacle de danse moderne par la troupe nationale',
                'category' => 'Danse',
                'start_date' => date('Y-m-d H:i:s', strtotime('+10 days')),
                'end_date' => date('Y-m-d H:i:s', strtotime('+10 days +2 hours')),
                'venue_name' => 'Opéra Bastille',
                'venue_address' => 'Place de la Bastille, 75012 Paris',
                'city' => 'Paris',
                'price' => 55.00,
                'is_free' => 0,
                'image_url' => 'https://images.unsplash.com/photo-1508700929628-666bc8bd84ea?w=800',
                'is_active' => 1,
                'featured' => 0
            ],
            [
                'title' => 'Cinéma en Plein Air',
                'description' => 'Projection de films classiques sous les étoiles',
                'category' => 'Cinéma',
                'start_date' => date('Y-m-d H:i:s', strtotime('+4 days 21:00')),
                'end_date' => date('Y-m-d H:i:s', strtotime('+4 days 23:30')),
                'venue_name' => 'Parc de Belleville',
                'venue_address' => '47 Rue des Couronnes, 75020 Paris',
                'city' => 'Paris',
                'price' => 0,
                'is_free' => 1,
                'image_url' => 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=800',
                'is_active' => 1,
                'featured' => 0
            ],
            [
                'title' => 'Conférence Tech & Innovation',
                'description' => 'Les dernières tendances en technologie et innovation',
                'category' => 'Conférence',
                'start_date' => date('Y-m-d H:i:s', strtotime('+2 weeks')),
                'end_date' => date('Y-m-d H:i:s', strtotime('+2 weeks +3 hours')),
                'venue_name' => 'Station F',
                'venue_address' => '5 Parvis Alan Turing, 75013 Paris',
                'city' => 'Paris',
                'price' => 20.00,
                'is_free' => 0,
                'image_url' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800',
                'is_active' => 1,
                'featured' => 0
            ]
        ];
        
        // Insérer les événements
        $stmt = $pdo->prepare("
            INSERT INTO events (title, description, category, start_date, end_date, 
                              venue_name, venue_address, city, price, is_free, 
                              image_url, is_active, featured, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        foreach ($demoEvents as $event) {
            $stmt->execute([
                $event['title'],
                $event['description'],
                $event['category'],
                $event['start_date'],
                $event['end_date'],
                $event['venue_name'],
                $event['venue_address'],
                $event['city'],
                $event['price'],
                $event['is_free'],
                $event['image_url'],
                $event['is_active'],
                $event['featured']
            ]);
        }
        
        $message = count($demoEvents) . " événements de démonstration créés avec succès !";
    } else {
        $message = "La base de données contient déjà $count événement(s).";
    }
    
    // Récupérer tous les événements
    $stmt = $pdo->query("SELECT * FROM events ORDER BY start_date ASC");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $error = "Erreur : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Événements - Culture Radar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
        }
        .event-card {
            margin-bottom: 20px;
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .event-image {
            height: 200px;
            object-fit: cover;
        }
        .badge-free {
            background: #10b981;
        }
        .badge-featured {
            background: #f59e0b;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">🎭 Test des Événements - Culture Radar</h1>
        
        <?php if ($message): ?>
        <div class="alert alert-success">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <div class="mb-4">
            <a href="discover.php" class="btn btn-primary">Voir la page Discover</a>
            <a href="index.php" class="btn btn-secondary">Retour à l'accueil</a>
        </div>
        
        <h2>Événements dans la base de données (<?php echo count($events); ?>)</h2>
        
        <?php if (empty($events)): ?>
        <div class="alert alert-warning">
            Aucun événement trouvé. Rechargez cette page pour créer des événements de démonstration.
        </div>
        <?php else: ?>
        <div class="row">
            <?php foreach ($events as $event): ?>
            <div class="col-md-4">
                <div class="card event-card">
                    <?php if ($event['image_url']): ?>
                    <img src="<?php echo htmlspecialchars($event['image_url']); ?>" class="card-img-top event-image" alt="<?php echo htmlspecialchars($event['title']); ?>">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title">
                            <?php echo htmlspecialchars($event['title']); ?>
                            <?php if ($event['is_free']): ?>
                            <span class="badge badge-free">Gratuit</span>
                            <?php endif; ?>
                            <?php if ($event['featured']): ?>
                            <span class="badge badge-featured">En vedette</span>
                            <?php endif; ?>
                        </h5>
                        <p class="card-text">
                            <small class="text-muted">
                                📍 <?php echo htmlspecialchars($event['venue_name']); ?><br>
                                📅 <?php echo date('d/m/Y H:i', strtotime($event['start_date'])); ?><br>
                                💰 <?php echo $event['is_free'] ? 'Gratuit' : $event['price'] . '€'; ?>
                            </small>
                        </p>
                        <p class="card-text"><?php echo htmlspecialchars($event['description']); ?></p>
                        <span class="badge bg-secondary"><?php echo htmlspecialchars($event['category']); ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>