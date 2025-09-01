<?php
// Simple test to verify event data display

// Decode the data parameter
$eventData = [];
if (isset($_GET['data'])) {
    $eventData = json_decode(base64_decode($_GET['data']), true) ?: [];
}

// Extract values with defaults
$title = $eventData['title'] ?? 'Événement Culturel';
$venue = $eventData['venue_name'] ?? $eventData['location'] ?? 'Lieu à confirmer';
$city = $eventData['city'] ?? 'Paris';
$date = $eventData['date_display'] ?? 'Date à confirmer';
$category = $eventData['category'] ?? 'Événement';
$imageUrl = $eventData['image_url'] ?? 'https://images.unsplash.com/photo-1513038630932-13873b1a7f29?w=1600&h=900&fit=crop';

$categoryIcon = ['Exposition' => '🖼️'][$category] ?? '🎨';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($title); ?></title>
    <style>
        .event-header {
            height: 400px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: flex-end;
            padding: 40px;
            position: relative;
            color: white;
        }
        .event-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.7) 100%);
            z-index: 1;
        }
        .event-header-content {
            z-index: 2;
            position: relative;
        }
    </style>
</head>
<body style="margin: 0; font-family: Arial, sans-serif;">
    <div class="event-header" style="background-image: url('<?php echo htmlspecialchars($imageUrl); ?>'), linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="event-header-content">
            <div style="padding: 8px 16px; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border-radius: 20px; display: inline-block; margin-bottom: 15px;">
                <?php echo $categoryIcon . ' ' . htmlspecialchars($category); ?>
            </div>
            <h1 style="font-size: 2.5rem; margin: 0 0 10px 0;">
                <?php echo htmlspecialchars($title); ?>
            </h1>
            <div style="display: flex; gap: 25px; font-size: 16px;">
                <div>📅 <?php echo htmlspecialchars($date); ?></div>
                <div>📍 <?php echo htmlspecialchars($venue); ?></div>
                <div>🏙️ <?php echo htmlspecialchars($city); ?></div>
            </div>
        </div>
    </div>
    
    <div style="padding: 20px; background: #f5f5f5; margin-top: 20px;">
        <h3>Debug Info:</h3>
        <pre><?php print_r($eventData); ?></pre>
    </div>
</body>
</html>