<?php
// Debug complet de l'affichage event-details
$config = [];
if (file_exists('config/api-keys.php')) {
    $config = include 'config/api-keys.php';
}

// Traitement des données d'événement
$eventData = [];
$eventId = null;

if (isset($_GET['data'])) {
    $encodedData = $_GET['data'];
    $jsonData = base64_decode($encodedData);
    $eventData = json_decode($jsonData, true) ?: [];
    echo "<!-- DEBUG: Data decoded successfully -->\n";
    echo "<!-- Event Data: " . print_r($eventData, true) . " -->\n";
}

// Valeurs par défaut
$title = $eventData['title'] ?? 'Événement Culturel';
$description = $eventData['description'] ?? 'Découvrez cet événement culturel exceptionnel près de chez vous.';
$venue = $eventData['venue_name'] ?? $eventData['location'] ?? 'Lieu à confirmer';
$city = $eventData['city'] ?? 'Paris';
$date = $eventData['date_display'] ?? $eventData['date'] ?? 'Date à confirmer';
$category = $eventData['category'] ?? 'Événement';
$price = $eventData['price'] ?? 0;
$sourceUrl = $eventData['source_url'] ?? '#';
$latitude = $eventData['latitude'] ?? 48.8566;
$longitude = $eventData['longitude'] ?? 2.3522;
$imageUrl = $eventData['image_url'] ?? '';

// Générer une image par défaut basée sur la catégorie si pas d'image
if (empty($imageUrl)) {
    $defaultImages = [
        'Exposition' => 'https://images.unsplash.com/photo-1513038630932-13873b1a7f29?w=1600&h=900&fit=crop',
        'Concert' => 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=1600&h=900&fit=crop',
        'Théâtre' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=1600&h=900&fit=crop',
        'Festival' => 'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?w=1600&h=900&fit=crop',
        'Cinéma' => 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=1600&h=900&fit=crop',
        'Danse' => 'https://images.unsplash.com/photo-1508700929628-666bc8bd84ea?w=1600&h=900&fit=crop',
        'Conférence' => 'https://images.unsplash.com/photo-1475721027785-f74eccf877e2?w=1600&h=900&fit=crop'
    ];
    $imageUrl = $defaultImages[$category] ?? 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=1600&h=900&fit=crop';
}

// Déterminer l'icône de catégorie
$categoryIcons = [
    'Exposition' => '🖼️',
    'Concert' => '🎵',
    'Théâtre' => '🎭',
    'Festival' => '🎪',
    'Cinéma' => '🎬',
    'Danse' => '💃',
    'Conférence' => '🎤'
];
$categoryIcon = $categoryIcons[$category] ?? '🎨';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Debug Event Display</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .debug-info { background: #f0f0f0; padding: 20px; margin: 20px 0; border-radius: 8px; }
        .field { margin: 10px 0; }
        .label { font-weight: bold; color: #333; }
        .value { color: #666; }
        .header-preview { 
            height: 300px; 
            background-size: cover;
            background-position: center;
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            margin: 20px 0;
        }
        .header-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            background: linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,0.8) 100%);
            color: white;
        }
    </style>
</head>
<body>
    <h1>Debug Event Display</h1>
    
    <div class="debug-info">
        <h2>Extracted Values:</h2>
        <div class="field">
            <span class="label">Title:</span> 
            <span class="value"><?php echo htmlspecialchars($title); ?></span>
        </div>
        <div class="field">
            <span class="label">Category:</span> 
            <span class="value"><?php echo htmlspecialchars($category); ?> <?php echo $categoryIcon; ?></span>
        </div>
        <div class="field">
            <span class="label">Venue:</span> 
            <span class="value"><?php echo htmlspecialchars($venue); ?></span>
        </div>
        <div class="field">
            <span class="label">City:</span> 
            <span class="value"><?php echo htmlspecialchars($city); ?></span>
        </div>
        <div class="field">
            <span class="label">Date:</span> 
            <span class="value"><?php echo htmlspecialchars($date); ?></span>
        </div>
        <div class="field">
            <span class="label">Price:</span> 
            <span class="value"><?php echo $price; ?> €</span>
        </div>
        <div class="field">
            <span class="label">Image URL:</span> 
            <span class="value"><?php echo htmlspecialchars($imageUrl); ?></span>
        </div>
    </div>
    
    <div class="debug-info">
        <h2>Header Preview:</h2>
        <div class="header-preview" style="background-image: url('<?php echo htmlspecialchars($imageUrl); ?>'), linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="header-overlay">
                <div style="margin-bottom: 10px;">
                    <?php echo $categoryIcon; ?> <?php echo htmlspecialchars($category); ?>
                </div>
                <h2 style="margin: 0 0 10px 0; font-size: 24px;">
                    <?php echo htmlspecialchars($title); ?>
                </h2>
                <div style="font-size: 14px;">
                    📅 <?php echo htmlspecialchars($date); ?> | 
                    📍 <?php echo htmlspecialchars($venue); ?> | 
                    🏙️ <?php echo htmlspecialchars($city); ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="debug-info">
        <h2>Raw Event Data:</h2>
        <pre><?php print_r($eventData); ?></pre>
    </div>
</body>
</html>