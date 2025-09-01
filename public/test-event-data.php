<?php
// Test de décodage des données d'événement
header('Content-Type: text/html; charset=UTF-8');

$eventData = [];
if (isset($_GET['data'])) {
    $encodedData = $_GET['data'];
    $jsonData = base64_decode($encodedData);
    $eventData = json_decode($jsonData, true) ?: [];
}

// Valeurs extraites
$title = $eventData['title'] ?? 'Événement Culturel';
$description = $eventData['description'] ?? 'Découvrez cet événement culturel exceptionnel près de chez vous.';
$venue = $eventData['venue_name'] ?? $eventData['location'] ?? 'Lieu à confirmer';
$city = $eventData['city'] ?? 'Paris';
$date = $eventData['date_display'] ?? $eventData['date'] ?? 'Date à confirmer';
$category = $eventData['category'] ?? 'Événement';
$price = $eventData['price'] ?? 0;
$imageUrl = $eventData['image_url'] ?? '';

// Image par défaut si nécessaire
if (empty($imageUrl)) {
    $defaultImages = [
        'Exposition' => 'https://images.unsplash.com/photo-1513038630932-13873b1a7f29?w=1600&h=900&fit=crop'
    ];
    $imageUrl = $defaultImages[$category] ?? 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=1600&h=900&fit=crop';
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Event Data</title>
    <style>
        body { font-family: monospace; padding: 20px; }
        .field { margin: 10px 0; padding: 10px; background: #f0f0f0; }
        .label { font-weight: bold; color: #333; }
        img { max-width: 500px; margin-top: 10px; }
    </style>
</head>
<body>
    <h1>Test Event Data Decoding</h1>
    
    <div class="field">
        <span class="label">Title:</span> <?php echo htmlspecialchars($title); ?>
    </div>
    
    <div class="field">
        <span class="label">Category:</span> <?php echo htmlspecialchars($category); ?>
    </div>
    
    <div class="field">
        <span class="label">Venue:</span> <?php echo htmlspecialchars($venue); ?>
    </div>
    
    <div class="field">
        <span class="label">City:</span> <?php echo htmlspecialchars($city); ?>
    </div>
    
    <div class="field">
        <span class="label">Date:</span> <?php echo htmlspecialchars($date); ?>
    </div>
    
    <div class="field">
        <span class="label">Price:</span> <?php echo $price; ?> €
    </div>
    
    <div class="field">
        <span class="label">Description:</span> <?php echo htmlspecialchars($description); ?>
    </div>
    
    <div class="field">
        <span class="label">Image URL:</span> <?php echo htmlspecialchars($imageUrl); ?>
        <?php if ($imageUrl): ?>
            <br><img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="Event image">
        <?php endif; ?>
    </div>
    
    <hr>
    <h2>Raw Data:</h2>
    <pre><?php print_r($eventData); ?></pre>
</body>
</html>