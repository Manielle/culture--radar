<?php
/**
 * Real Weather API Integration
 * Connects to OpenWeatherMap API
 */

session_start();
require_once dirname(__DIR__) . '/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Get API key from config
$weatherKey = Config::env('OPENWEATHERMAP_API_KEY');

if (!$weatherKey || $weatherKey === 'YOUR_OPENWEATHERMAP_KEY_HERE') {
    // Return mock data if no API key
    echo json_encode([
        'success' => false,
        'weather' => [
            'temperature' => '--',
            'description' => 'Météo non disponible',
            'icon' => '☁️',
            'humidity' => '--',
            'wind_speed' => '--'
        ]
    ]);
    exit;
}

// Get location parameter
$location = $_GET['location'] ?? $_GET['city'] ?? 'Paris';

// OpenWeatherMap API URL
$apiUrl = 'https://api.openweathermap.org/data/2.5/weather';
$params = [
    'q' => $location . ',FR', // Add country code for France
    'appid' => $weatherKey,
    'units' => 'metric', // Celsius
    'lang' => 'fr' // French descriptions
];

$queryString = http_build_query($params);
$fullUrl = $apiUrl . '?' . $queryString;

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $fullUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || !$response) {
    echo json_encode([
        'success' => false,
        'weather' => [
            'temperature' => '--',
            'description' => 'Météo non disponible',
            'icon' => '☁️',
            'humidity' => '--',
            'wind_speed' => '--'
        ]
    ]);
    exit;
}

$data = json_decode($response, true);

// Map weather codes to emojis
$weatherIcons = [
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
$icon = $weatherIcons[$iconCode] ?? '☁️';

// Format weather data
$weather = [
    'temperature' => round($data['main']['temp'] ?? 0),
    'feels_like' => round($data['main']['feels_like'] ?? 0),
    'description' => ucfirst($data['weather'][0]['description'] ?? 'Nuageux'),
    'icon' => $icon,
    'humidity' => $data['main']['humidity'] ?? 0,
    'wind_speed' => round(($data['wind']['speed'] ?? 0) * 3.6), // Convert m/s to km/h
    'pressure' => $data['main']['pressure'] ?? 0,
    'visibility' => round(($data['visibility'] ?? 0) / 1000, 1), // Convert to km
    'sunrise' => date('H:i', $data['sys']['sunrise'] ?? time()),
    'sunset' => date('H:i', $data['sys']['sunset'] ?? time())
];

// Return formatted response
echo json_encode([
    'success' => true,
    'location' => $data['name'] ?? $location,
    'country' => $data['sys']['country'] ?? 'FR',
    'weather' => $weather,
    'source' => 'openweathermap'
]);
?>