<?php
// Test simple de lien vers event-details.php
$event = [
    'id' => 'test_simple',
    'title' => 'Concert Test avec accents éèà',
    'description' => 'Description avec caractères spéciaux',
    'category' => 'Musique',
    'venue_name' => 'Théâtre de l\'Odéon',
    'city' => 'Paris',
    'start_date' => '2025-08-30 20:00:00',
    'price' => 25
];

// Encoder avec les bonnes options
$encoded = base64_encode(json_encode($event, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test Simple</title>
</head>
<body>
    <h1>Test de lien simple</h1>
    
    <h2>Événement:</h2>
    <pre><?php echo json_encode($event, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?></pre>
    
    <h2>Encodé:</h2>
    <p style="word-break: break-all;"><?php echo $encoded; ?></p>
    
    <h2>Lien:</h2>
    <a href="event-details.php?data=<?php echo $encoded; ?>" style="padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; display: inline-block;">
        Ouvrir l'événement
    </a>
    
    <hr>
    
    <h2>Test JavaScript:</h2>
    <button onclick="testJS()">Créer lien JS</button>
    <div id="js-result"></div>
    
    <script>
    function safeBase64Encode(str) {
        return btoa(unescape(encodeURIComponent(str)));
    }
    
    function testJS() {
        const event = {
            id: 'test_js',
            title: 'Concert JS avec accents éèà',
            description: 'Description JS',
            category: 'Musique',
            venue_name: "Théâtre de l'Odéon",
            city: 'Paris',
            start_date: '2025-08-30 20:00:00',
            price: 30
        };
        
        const encoded = safeBase64Encode(JSON.stringify(event));
        
        document.getElementById('js-result').innerHTML = `
            <h3>Lien créé en JS:</h3>
            <a href="event-details.php?data=${encoded}" style="padding: 10px 20px; background: #764ba2; color: white; text-decoration: none; border-radius: 5px; display: inline-block;">
                Ouvrir événement JS
            </a>
        `;
    }
    </script>
</body>
</html>