<?php
/**
 * Simple OpenAgenda API Test
 * Direct test without using Config class
 */

echo "<pre>";
echo "====================================\n";
echo "   OpenAgenda API Test (Simple)    \n";
echo "====================================\n\n";

// Read .env file directly
$envFile = __DIR__ . '/.env';
$apiKey = '';

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, 'OPENAGENDA_API_KEY') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $apiKey = trim($value, '"\'');
            break;
        }
    }
}

echo "API Key found: " . ($apiKey ? 'Yes (' . substr($apiKey, 0, 10) . '...)' : 'No') . "\n\n";

if (!$apiKey || $apiKey === 'YOUR_OPENAGENDA_KEY_HERE') {
    echo "❌ No valid API key found in .env file\n";
    echo "</pre>";
    exit;
}

// Test 1: Simple API test
echo "Test 1: Testing API connection...\n";
echo "URL: https://api.openagenda.com/v2/agendas\n";
echo "Method: GET with key in header\n\n";

// Using file_get_contents with context
$opts = [
    "http" => [
        "method" => "GET",
        "header" => "key: $apiKey\r\nAccept: application/json\r\n",
        "timeout" => 10
    ]
];

$context = stream_context_create($opts);
$response = @file_get_contents("https://api.openagenda.com/v2/agendas?size=1", false, $context);

if ($response === false) {
    echo "❌ Failed to connect to OpenAgenda API\n";
    echo "This could be due to:\n";
    echo "- Network issues\n";
    echo "- Invalid API key\n";
    echo "- Server blocking outbound HTTPS\n";
} else {
    $data = json_decode($response, true);
    if ($data && isset($data['agendas'])) {
        echo "✅ API Connection successful!\n";
        echo "Response contains " . count($data['agendas']) . " agenda(s)\n";
        
        if (count($data['agendas']) > 0) {
            $agenda = $data['agendas'][0];
            echo "\nFirst agenda:\n";
            echo "  Title: " . ($agenda['title'] ?? 'N/A') . "\n";
            echo "  UID: " . ($agenda['uid'] ?? 'N/A') . "\n";
        }
    } else {
        echo "⚠️ Connected but unexpected response format\n";
        echo "Response: " . substr($response, 0, 200) . "\n";
    }
}

// Test 2: Try to get events
echo "\n\nTest 2: Fetching events...\n";

// Try multiple agenda UIDs
$agendaUids = ['82308696', '16853059', '65439955', '96947974'];
$foundEvents = false;

foreach ($agendaUids as $uid) {
    $url = "https://api.openagenda.com/v2/agendas/$uid/events?size=3";
    $response = @file_get_contents($url, false, $context);
    
    if ($response !== false) {
        $data = json_decode($response, true);
        if ($data && isset($data['events']) && count($data['events']) > 0) {
            echo "✅ Found events in agenda $uid!\n";
            foreach ($data['events'] as $event) {
                $title = is_array($event['title']) ? ($event['title']['fr'] ?? reset($event['title'])) : $event['title'];
                echo "  - " . $title . "\n";
            }
            $foundEvents = true;
            break;
        }
    }
}

if (!$foundEvents) {
    echo "⚠️ No events found in tested agendas\n";
    echo "The agenda UIDs might be invalid or have no events\n";
}

echo "\n====================================\n";
echo "Test complete!\n";
echo "</pre>";
?>