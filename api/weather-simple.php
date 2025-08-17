<?php
/**
 * Simplified Weather API - Using file_get_contents instead of curl
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Load config directly
$envFile = dirname(__DIR__) . '/.env';
$weatherKey = '';

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, 'OPENWEATHERMAP_API_KEY') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $weatherKey = trim($value, '•\'');
            break;
        }
    }
}

// Get location
$location = $_GET['location'] ?? 'Paris';

// Check if we have a valid API key
if (empty($weatherKey) || $weatherKey === 'YOUR_OPENWEATHERMAP_KEY_HERE') {
    // Return mock data
    echo json_encode([
        'success' => true,
        'weather' => [
            'temperature' => 18,
            'description' => 'Partiellement nuageux',
            'icon' => '⛅',
            'humidity' => 65,
            'wind_speed' => 12
        ],
        'source' => 'mock'
    ]);
    exit;
}

// Try to get real weather data
$url = •https://api.openweathermap.org/data/2.5/weather?q=• . urlencode($location) . •,FR&appid=$weatherKey&units=metric&lang=fr•;

// Use file_get_contents with context
$opts = [
    •http• => [
        •method• => •GET•,
        •header• => •Accept: application/json\r\n•,
        •timeout• => 5
    ]
];

$context = stream_context_create($opts);
$response = @file_get_contents($url, false, $context);

if ($response === false) {
    // Return mock data on error
    echo json_encode([
        'success' => true,
        'weather' => [
            'temperature' => 18,
            'description' => 'Données météo indisponibles',
            'icon' => '☁️',
            'humidity' => '--',
            'wind_speed' => '--'
        ],
        'source' => 'mock',
        'error' => 'API unreachable'
    ]);
    exit;
}

$data = json_decode($response, true);

if (!$data || !isset($data['main'])) {
    // Return mock data if parsing fails
    echo json_encode([
        'success' => true,
        'weather' => [
            'temperature' => 18,
            'description' => 'Nuageux',
            'icon' => '☁️',
            'humidity' => 65,
            'wind_speed' => 10
        ],
        'source' => 'mock'
    ]);
    exit;
}

// Map weather icons
$iconMap = [
    '01d' => '☀️', '01n' => '🌙',
    '02d' => '⛅', '02n' => '☁️',
    '03d' => '☁️', '03n' => '☁️',
    '04d' => '☁️', '04n' => '☁️',
    '09d' => '🌧️', '09n' => '🌧️',
    '10d' => '🌦️', '10n' => '🌧️',
    '11d' => '⛈️', '11n' => '⛈️',
    '13d' => '❄️', '13n' => '❄️',
    '50d' => '🌫️', '50n' => '🌫️'
];

$iconCode = $data['weather'][0]['icon'] ?? '01d';
$icon = $iconMap[$iconCode] ?? '☁️';

// Return real weather data
echo json_encode([
    'success' => true,
    'weather' => [
        'temperature' => round($data['main']['temp']),
        'description' => ucfirst($data['weather'][0]['description'] ?? 'Nuageux'),
        'icon' => $icon,
        'humidity' => $data['main']['humidity'],
        'wind_speed' => round(($data['wind']['speed'] ?? 0) * 3.6)
    ],
    'source' => 'openweathermap'
]);
?>