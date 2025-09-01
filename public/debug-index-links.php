<?php
// Debug pour voir ce qui est généré sur index.php
require_once __DIR__ . '/services/KnownEventsData.php';

$events = array_slice(KnownEventsData::getKnownEvents('Paris'), 0, 3);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Debug Index Links</title>
    <style>
        body { padding: 20px; font-family: Arial, sans-serif; }
        .event-test { border: 1px solid #ddd; padding: 20px; margin: 20px 0; }
        .link-test { 
            display: inline-block; 
            margin: 10px; 
            padding: 10px 20px; 
            background: #007bff; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px; 
        }
        pre { background: #f4f4f4; padding: 10px; overflow-x: auto; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>Debug des liens sur Index</h1>
    
    <?php foreach($events as $index => $event): ?>
    <div class="event-test">
        <h2>Événement <?php echo $index + 1; ?>: <?php echo htmlspecialchars($event['title'] ?? 'Sans titre'); ?></h2>
        
        <h3>1. Données de l'événement:</h3>
        <pre><?php echo json_encode($event, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?></pre>
        
        <h3>2. Encodage:</h3>
        <?php 
        $encoded = base64_encode(json_encode($event, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        ?>
        <p><strong>Base64 (<?php echo strlen($encoded); ?> caractères):</strong></p>
        <pre style="word-break: break-all;"><?php echo substr($encoded, 0, 200); ?>...</pre>
        
        <h3>3. URL finale:</h3>
        <pre>event-details.php?data=<?php echo substr($encoded, 0, 100); ?>...</pre>
        
        <h3>4. Test du lien:</h3>
        <a href="event-details.php?data=<?php echo $encoded; ?>" class="link-test">
            Tester ce lien
        </a>
        
        <h3>5. Vérification du décodage:</h3>
        <?php
        $decoded = base64_decode($encoded);
        $eventCheck = json_decode($decoded, true);
        if ($eventCheck) {
            echo '<p class="success">✅ Décodage OK - ' . ($eventCheck['title'] ?? 'Sans titre') . '</p>';
        } else {
            echo '<p class="error">❌ Erreur de décodage</p>';
        }
        ?>
    </div>
    <?php endforeach; ?>
    
    <h2>Test JavaScript</h2>
    <div id="js-test"></div>
    
    <script>
    function safeBase64Encode(str) {
        return btoa(unescape(encodeURIComponent(str)));
    }
    
    // Test avec un événement JS
    const testEvent = {
        title: "Concert Test JS",
        category: "Musique",
        venue_name: "Lieu Test",
        city: "Paris",
        price: 20
    };
    
    const encoded = safeBase64Encode(JSON.stringify(testEvent));
    
    document.getElementById('js-test').innerHTML = `
        <div class="event-test">
            <h3>Événement JavaScript</h3>
            <pre>${JSON.stringify(testEvent, null, 2)}</pre>
            <a href="event-details.php?data=${encoded}" class="link-test">
                Tester lien JS
            </a>
        </div>
    `;
    
    // Ajouter des listeners pour debug
    document.querySelectorAll('a[href*="event-details.php"]').forEach(link => {
        link.addEventListener('click', function(e) {
            console.log('Lien cliqué:', this.href);
            // Vérifier si le lien est valide
            if (!this.href || this.href === 'javascript:void(0)') {
                e.preventDefault();
                alert('Lien invalide!');
            }
        });
    });
    </script>
</body>
</html>