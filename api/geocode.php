<?php
/**
 * Simple Geocoding API
 * Returns city name from coordinates
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$lat = isset($_GET['lat']) ? floatval($_GET['lat']) : null;
$lng = isset($_GET['lng']) ? floatval($_GET['lng']) : null;

if (!$lat || !$lng) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing coordinates'
    ]);
    exit;
}

// Simple city detection based on coordinates
// This is a simplified version - in production, use a real geocoding service
$cities = [
    ['name' => 'Paris', 'lat' => 48.8566, 'lng' => 2.3522, 'radius' => 20],
    ['name' => 'Lyon', 'lat' => 45.7640, 'lng' => 4.8357, 'radius' => 15],
    ['name' => 'Marseille', 'lat' => 43.2965, 'lng' => 5.3698, 'radius' => 15],
    ['name' => 'Toulouse', 'lat' => 43.6047, 'lng' => 1.4442, 'radius' => 15],
    ['name' => 'Nice', 'lat' => 43.7102, 'lng' => 7.2620, 'radius' => 10],
    ['name' => 'Nantes', 'lat' => 47.2184, 'lng' => -1.5536, 'radius' => 15],
    ['name' => 'Strasbourg', 'lat' => 48.5734, 'lng' => 7.7521, 'radius' => 10],
    ['name' => 'Montpellier', 'lat' => 43.6108, 'lng' => 3.8767, 'radius' => 10],
    ['name' => 'Bordeaux', 'lat' => 44.8378, 'lng' => -0.5792, 'radius' => 15],
    ['name' => 'Lille', 'lat' => 50.6292, 'lng' => 3.0573, 'radius' => 10],
    ['name' => 'Rennes', 'lat' => 48.1173, 'lng' => -1.6778, 'radius' => 10],
    ['name' => 'Reims', 'lat' => 49.2583, 'lng' => 4.0317, 'radius' => 10]
];

// Find nearest city
$nearestCity = null;
$minDistance = PHP_FLOAT_MAX;

foreach ($cities as $city) {
    // Calculate distance using Haversine formula
    $earthRadius = 6371; // km
    $latDiff = deg2rad($lat - $city['lat']);
    $lngDiff = deg2rad($lng - $city['lng']);
    
    $a = sin($latDiff/2) * sin($latDiff/2) +
         cos(deg2rad($city['lat'])) * cos(deg2rad($lat)) *
         sin($lngDiff/2) * sin($lngDiff/2);
    
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    $distance = $earthRadius * $c;
    
    if ($distance < $minDistance && $distance <= $city['radius']) {
        $minDistance = $distance;
        $nearestCity = $city['name'];
    }
}

// Default to Paris if no city found within radius
if (!$nearestCity) {
    $nearestCity = 'Paris';
}

echo json_encode([
    'success' => true,
    'city' => $nearestCity,
    'coordinates' => [
        'lat' => $lat,
        'lng' => $lng
    ],
    'distance' => round($minDistance, 2)
]);
?>