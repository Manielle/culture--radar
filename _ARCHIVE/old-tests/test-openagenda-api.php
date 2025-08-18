<?php
/**
 * Test OpenAgenda API Connection
 */

require_once __DIR__ . '/config.php';

echo "====================================\n";
echo "   OpenAgenda API Test            \n";
echo "====================================\n\n";

$apiKey = Config::env('OPENAGENDA_API_KEY');
echo "API Key: " . ($apiKey ? substr($apiKey, 0, 10) . '...' : 'NOT FOUND') . "\n\n";

if (!$apiKey || $apiKey === 'YOUR_OPENAGENDA_KEY_HERE') {
    echo "❌ No valid API key found in .env file\n";
    exit;
}

// Test 1: Try to get public agendas
echo "Test 1: Fetching public agendas...\n";
$url = "https://api.openagenda.com/v2/agendas";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'key: ' . $apiKey,
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode\n";

if ($error) {
    echo "❌ cURL Error: $error\n";
} elseif ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "✅ API Connection successful!\n";
    echo "Total agendas found: " . ($data['total'] ?? 0) . "\n";
    
    if (isset($data['agendas']) && count($data['agendas']) > 0) {
        echo "\nFirst few agendas:\n";
        foreach (array_slice($data['agendas'], 0, 3) as $agenda) {
            echo "  - " . ($agenda['title'] ?? 'No title') . " (UID: " . ($agenda['uid'] ?? 'N/A') . ")\n";
        }
    }
} elseif ($httpCode === 401) {
    echo "❌ Authentication failed - Invalid API key\n";
} elseif ($httpCode === 403) {
    echo "❌ Access denied - Check API key permissions\n";
} else {
    echo "❌ Request failed\n";
    echo "Response: " . substr($response, 0, 200) . "\n";
}

// Test 2: Try to get events from a specific agenda
echo "\n\nTest 2: Fetching events from a public agenda...\n";

// Using a known public agenda UID (you may need to replace with a valid one)
$agendaUid = '28185236';
$url = "https://api.openagenda.com/v2/agendas/{$agendaUid}/events?size=5";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'key: ' . $apiKey,
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    if (isset($data['events'])) {
        echo "✅ Events fetched successfully!\n";
        echo "Total events: " . ($data['total'] ?? 0) . "\n";
        
        if (count($data['events']) > 0) {
            echo "\nFirst few events:\n";
            foreach (array_slice($data['events'], 0, 3) as $event) {
                $title = $event['title']['fr'] ?? $event['title'] ?? 'No title';
                $location = $event['location']['city'] ?? 'Unknown location';
                echo "  - $title ($location)\n";
            }
        }
    } else {
        echo "⚠️ No events found in response\n";
    }
} elseif ($httpCode === 404) {
    echo "❌ Agenda not found (UID: $agendaUid)\n";
    echo "You may need to find valid public agenda UIDs\n";
} else {
    echo "❌ Failed to fetch events\n";
}

echo "\n====================================\n";
echo "Test complete!\n";
echo "\nIf the tests failed, check:\n";
echo "1. Your API key is valid and active\n";
echo "2. The agenda UIDs are correct\n";
echo "3. Your server can make outbound HTTPS requests\n";
?>