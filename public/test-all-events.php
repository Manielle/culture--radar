<?php
// Test pour vérifier que tous les types d'événements fonctionnent
require_once __DIR__ . '/services/KnownEventsData.php';

$events = KnownEventsData::getKnownEvents('Paris');

// Prendre des événements de différentes catégories
$categories = [];
foreach ($events as $event) {
    $cat = $event['category'] ?? 'Autre';
    if (!isset($categories[$cat])) {
        $categories[$cat] = $event;
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Test Tous les Événements</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
        }
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .event-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .category-badge {
            display: inline-block;
            padding: 5px 15px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 20px;
            font-size: 0.85rem;
            margin-bottom: 10px;
        }
        .event-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #2d3748;
            margin: 10px 0;
        }
        .event-info {
            color: #718096;
            font-size: 0.9rem;
            margin: 5px 0;
        }
        .test-links {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
        }
        .test-link {
            display: inline-block;
            padding: 8px 15px;
            margin: 5px;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 0.85rem;
        }
        .link-details { background: #007bff; }
        .link-hybrid { background: #28a745; }
        .link-simple { background: #ffc107; color: black; }
        .stats {
            background: #f0f4ff;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Test de tous les événements Culture Radar</h1>
        
        <div class="stats">
            <h2>📊 Statistiques</h2>
            <p><strong>Total d'événements:</strong> <?php echo count($events); ?></p>
            <p><strong>Catégories trouvées:</strong> <?php echo implode(', ', array_keys($categories)); ?></p>
        </div>
        
        <h2>📅 Événements par catégorie</h2>
        <div class="events-grid">
            <?php foreach ($categories as $category => $event): 
                // Encoder l'événement pour l'URL
                $eventData = base64_encode(json_encode($event, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            ?>
            <div class="event-card">
                <span class="category-badge"><?php echo htmlspecialchars($category); ?></span>
                <div class="event-title"><?php echo htmlspecialchars($event['title'] ?? 'Sans titre'); ?></div>
                <div class="event-info">📍 <?php echo htmlspecialchars($event['venue_name'] ?? 'Lieu non spécifié'); ?></div>
                <div class="event-info">📅 <?php echo htmlspecialchars($event['date_display'] ?? $event['start_date'] ?? 'Date à confirmer'); ?></div>
                <div class="event-info">💰 <?php echo isset($event['price']) ? ($event['price'] == 0 ? 'Gratuit' : $event['price'] . '€') : 'Prix non spécifié'; ?></div>
                
                <div class="test-links">
                    <a href="event-details.php?data=<?php echo $eventData; ?>" class="test-link link-details" target="_blank">
                        event-details.php
                    </a>
                    <a href="event-details-hybrid.php?data=<?php echo $eventData; ?>" class="test-link link-hybrid" target="_blank">
                        hybrid
                    </a>
                    <a href="event-details-simple.php?data=<?php echo $eventData; ?>" class="test-link link-simple" target="_blank">
                        debug
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <h2 style="margin-top: 40px;">🎯 Test avec tous les événements (<?php echo count($events); ?> au total)</h2>
        <div class="events-grid">
            <?php foreach (array_slice($events, 0, 12) as $event): 
                $eventData = base64_encode(json_encode($event, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            ?>
            <div class="event-card">
                <span class="category-badge"><?php echo htmlspecialchars($event['category'] ?? 'Culture'); ?></span>
                <div class="event-title"><?php echo htmlspecialchars(substr($event['title'] ?? 'Sans titre', 0, 50)); ?></div>
                <div class="event-info">📍 <?php echo htmlspecialchars($event['city'] ?? 'Paris'); ?></div>
                <div class="event-info">💰 <?php echo isset($event['price']) ? ($event['price'] == 0 ? 'Gratuit' : $event['price'] . '€') : 'Prix non spécifié'; ?></div>
                
                <div class="test-links">
                    <a href="event-details.php?data=<?php echo $eventData; ?>" class="test-link link-details" target="_blank">
                        Ouvrir les détails
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <script>
    // Test JavaScript pour vérifier l'encodage
    console.log('Test de tous les événements chargé');
    console.log('Nombre total d\'événements:', <?php echo count($events); ?>);
    </script>
</body>
</html>