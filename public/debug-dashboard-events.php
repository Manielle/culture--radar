<?php
/**
 * Debug page to see what events are being shown in dashboard
 */
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/services/HybridEventsService.php';

// Get events like dashboard does
$city = $_SESSION['user_city'] ?? 'Paris';
$events = HybridEventsService::searchEvents($city, 6, true);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Debug Dashboard Events</title>
    <style>
        body {
            font-family: monospace;
            padding: 20px;
            background: #1a1a2e;
            color: #fff;
        }
        .event-box {
            background: rgba(255,255,255,0.1);
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
        }
        pre {
            background: #000;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
            color: #0f0;
        }
        .link {
            display: inline-block;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
        .warning {
            color: #ff6b6b;
            font-weight: bold;
        }
        .success {
            color: #51cf66;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>🔍 Debug Dashboard Events</h1>
    <p>Ville: <?php echo htmlspecialchars($city); ?></p>
    <p>Nombre d'événements trouvés: <?php echo count($events); ?></p>
    
    <?php foreach ($events as $index => $event): ?>
        <div class="event-box">
            <h2>Événement #<?php echo $index + 1; ?>: <?php echo htmlspecialchars($event['title'] ?? 'Sans titre'); ?></h2>
            
            <?php
            // Prepare essential data like dashboard does
            $essentialData = [
                'id' => $event['id'] ?? uniqid(),
                'title' => $event['title'] ?? 'Événement',
                'description' => $event['description'] ?? '',
                'venue_name' => $event['venue_name'] ?? $event['location'] ?? '',
                'location' => $event['location'] ?? '',
                'city' => $event['city'] ?? 'Paris',
                'date_display' => $event['date_display'] ?? $event['date'] ?? '',
                'price' => $event['price'] ?? 0,
                'is_free' => $event['is_free'] ?? false,
                'category' => $event['category'] ?? 'Culture',
                'source_url' => $event['source_url'] ?? $event['link'] ?? ''
            ];
            
            $encoded = base64_encode(json_encode($essentialData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $urlLength = strlen('/event-details-hybrid.php?data=' . $encoded);
            ?>
            
            <h3>Données essentielles:</h3>
            <pre><?php echo json_encode($essentialData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?></pre>
            
            <h3>Encodage:</h3>
            <p>Longueur encodée: <?php echo strlen($encoded); ?> caractères</p>
            <p>Longueur URL totale: <?php echo $urlLength; ?> caractères 
                <?php if ($urlLength > 2000): ?>
                    <span class="warning">⚠️ URL trop longue!</span>
                <?php else: ?>
                    <span class="success">✅ OK</span>
                <?php endif; ?>
            </p>
            
            <h3>Test de décodage:</h3>
            <?php
            $decoded = json_decode(base64_decode($encoded), true);
            if ($decoded) {
                echo '<p class="success">✅ Décodage réussi</p>';
            } else {
                echo '<p class="warning">❌ Échec du décodage: ' . json_last_error_msg() . '</p>';
            }
            ?>
            
            <a href="/event-details-hybrid.php?data=<?php echo $encoded; ?>" class="link" target="_blank">
                Tester ce lien →
            </a>
            
            <details>
                <summary>Voir toutes les données de l'événement</summary>
                <pre><?php echo json_encode($event, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?></pre>
            </details>
        </div>
    <?php endforeach; ?>
    
    <?php if (empty($events)): ?>
        <div class="event-box">
            <p class="warning">Aucun événement trouvé!</p>
            <p>Vérifiez que l'API Hybrid fonctionne correctement.</p>
        </div>
    <?php endif; ?>
</body>
</html>