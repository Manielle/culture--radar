<?php
// Simplest possible test
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Direct loading of config
$envFile = dirname(__DIR__) . '/.env';
$config = [];

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $config[trim($key)] = trim($value, '•\'');
        }
    }
}

$weatherKey = $config['OPENWEATHERMAP_API_KEY'] ?? '';
$openAgendaKey = $config['OPENAGENDA_API_KEY'] ?? '';

// Simple test response
echo json_encode([
    'success' => true,
    'config_loaded' => !empty($config),
    'weather_key_present' => !empty($weatherKey) && $weatherKey !== 'YOUR_OPENWEATHERMAP_KEY_HERE',
    'openagenda_key_present' => !empty($openAgendaKey) && $openAgenda !== 'YOUR_OPENAGENDA_KEY_HERE',
    'weather_key_length' => strlen($weatherKey),
    'openagenda_key_length' => strlen($openAgendaKey),
    'php_version' => PHP_VERSION,
    'curl_enabled' => function_exists('curl_init'),
    'json_enabled' => function_exists('json_encode')
]);
?>