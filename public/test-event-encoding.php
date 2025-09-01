<?php
/**
 * Test page for event encoding/decoding
 */

// Test event data
$testEvent = [
    'id' => 'test_123',
    'title' => 'Concert de Jazz au Sunset',
    'description' => 'Un concert exceptionnel avec les meilleurs musiciens de jazz.',
    'venue_name' => 'Le Sunset',
    'location' => 'Le Sunset, Paris',
    'city' => 'Paris',
    'date_display' => '15 septembre 2025',
    'price' => 25,
    'is_free' => false,
    'category' => 'Musique',
    'source_url' => 'https://example.com/event'
];

// Encode the data
$encoded = base64_encode(json_encode($testEvent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

// Try to decode it back
$decoded = json_decode(base64_decode($encoded), true);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Test Event Encoding</title>
    <style>
        body { 
            font-family: monospace; 
            padding: 20px; 
            background: #f0f0f0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .box {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        pre {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
        }
        .button:hover {
            background: #5a67d8;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Test d'encodage/décodage des événements</h1>
        
        <div class="box">
            <h2>1. Données originales de l'événement</h2>
            <pre><?php echo json_encode($testEvent, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?></pre>
        </div>
        
        <div class="box">
            <h2>2. Données encodées en Base64</h2>
            <p>Longueur: <?php echo strlen($encoded); ?> caractères</p>
            <pre style="word-break: break-all;"><?php echo $encoded; ?></pre>
        </div>
        
        <div class="box">
            <h2>3. Données décodées</h2>
            <?php if ($decoded === $testEvent): ?>
                <p class="success">✅ Décodage réussi ! Les données sont identiques.</p>
            <?php else: ?>
                <p class="error">❌ Problème de décodage</p>
            <?php endif; ?>
            <pre><?php echo json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?></pre>
        </div>
        
        <div class="box">
            <h2>4. Test des liens</h2>
            <p>Cliquez sur ces liens pour tester :</p>
            
            <a href="/event-details-hybrid.php?data=<?php echo $encoded; ?>" class="button">
                Test avec données encodées
            </a>
            
            <a href="/event-details-hybrid.php?id=test_123" class="button">
                Test avec ID seulement
            </a>
            
            <?php
            // Test avec un vrai événement du dashboard
            $realEvent = [
                'title' => 'Les Maîtres de la Renaissance',
                'venue_name' => 'Musée du Louvre',
                'source_url' => 'https://www.louvre.fr/expositions/maitres-renaissance-2025',
                'category' => 'Exposition',
                'city' => 'Paris',
                'date_display' => "Jusqu'au 30 octobre 2025",
                'price' => 17
            ];
            $realEncoded = base64_encode(json_encode($realEvent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            ?>
            
            <a href="/event-details-hybrid.php?data=<?php echo $realEncoded; ?>" class="button">
                Test avec événement Louvre
            </a>
        </div>
        
        <div class="box">
            <h2>5. Vérification de l'encodage URL</h2>
            <p>URL complète avec données encodées :</p>
            <pre style="word-break: break-all;">http://localhost:8888/event-details-hybrid.php?data=<?php echo $encoded; ?></pre>
            
            <p>Longueur de l'URL : <?php echo strlen('http://localhost:8888/event-details-hybrid.php?data=' . $encoded); ?> caractères</p>
            
            <?php if (strlen($encoded) > 2000): ?>
                <p class="error">⚠️ Attention : L'URL est très longue (>2000 caractères), cela pourrait poser problème.</p>
            <?php else: ?>
                <p class="success">✅ La longueur de l'URL est acceptable.</p>
            <?php endif; ?>
        </div>
        
        <div class="box">
            <h2>6. Test de décodage manuel</h2>
            <?php
            // Simuler ce que fait event-details-hybrid.php
            $testData = $_GET['data'] ?? null;
            if ($testData) {
                $decodedTest = json_decode(base64_decode($testData), true);
                if ($decodedTest) {
                    echo '<p class="success">✅ Les données passées en GET ont été décodées avec succès :</p>';
                    echo '<pre>' . json_encode($decodedTest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
                } else {
                    echo '<p class="error">❌ Impossible de décoder les données passées en GET</p>';
                    echo '<p>Erreur JSON : ' . json_last_error_msg() . '</p>';
                }
            } else {
                echo '<p>Aucune donnée passée en paramètre GET.</p>';
            }
            ?>
        </div>
    </div>
</body>
</html>