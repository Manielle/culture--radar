<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Simple test to see if APIs are reachable
echo json_encode([
    'test' => 'working',
    'timestamp' => time(),
    'php_version' => PHP_VERSION,
    'apis_configured' => [
        'openweather' => !empty($_ENV['OPENWEATHERMAP_API_KEY']),
        'openagenda' => !empty($_ENV['OPENAGENDA_API_KEY']),
        'google_maps' => !empty($_ENV['GOOGLE_MAPS_API_KEY'])
    ]
]);
?>