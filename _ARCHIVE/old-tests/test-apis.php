<?php
/**
 * Test Real API Integrations
 */

require_once __DIR__ . '/config.php';

echo "====================================\n";
echo "   Culture Radar API Test Suite    \n";
echo "====================================\n\n";

// Test OpenWeatherMap API
echo "1. Testing OpenWeatherMap API...\n";
echo "   API Key: " . (Config::env('OPENWEATHERMAP_API_KEY') ? '✅ Configured' : '❌ Missing') . "\n";

$weatherKey = Config::env('OPENWEATHERMAP_API_KEY');
if ($weatherKey && $weatherKey !== 'YOUR_OPENWEATHERMAP_KEY_HERE') {
    $url = "https://api.openweathermap.org/data/2.5/weather?q=Paris,FR&appid=$weatherKey&units=metric&lang=fr";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        echo "   ✅ Weather API working!\n";
        echo "   Current weather in Paris: " . $data['weather'][0]['description'] . "\n";
        echo "   Temperature: " . round($data['main']['temp']) . "°C\n";
    } else {
        echo "   ❌ Weather API failed (HTTP $httpCode)\n";
        if ($httpCode === 401) {
            echo "   Invalid API key!\n";
        }
    }
} else {
    echo "   ⚠️  No API key configured\n";
}

echo "\n";

// Test OpenAgenda API
echo "2. Testing OpenAgenda API...\n";
echo "   API Key: " . (Config::env('OPENAGENDA_API_KEY') ? '✅ Configured' : '❌ Missing') . "\n";

$openAgendaKey = Config::env('OPENAGENDA_API_KEY');
if ($openAgendaKey && $openAgendaKey !== 'YOUR_OPENAGENDA_KEY_HERE') {
    $url = "https://api.openagenda.com/v2/events?key=$openAgendaKey&size=1&relative[]=current&locationQuery=Paris";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'User-Agent: CultureRadar/1.0'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        echo "   ✅ OpenAgenda API working!\n";
        echo "   Total events found: " . ($data['total'] ?? 0) . "\n";
        if (isset($data['events'][0])) {
            $event = $data['events'][0];
            echo "   Sample event: " . ($event['title']['fr'] ?? $event['title'] ?? 'N/A') . "\n";
        }
    } else {
        echo "   ❌ OpenAgenda API failed (HTTP $httpCode)\n";
        if ($httpCode === 401 || $httpCode === 403) {
            echo "   Invalid API key!\n";
        }
    }
} else {
    echo "   ⚠️  No API key configured\n";
}

echo "\n";

// Test Google Maps API
echo "3. Testing Google Maps API...\n";
$googleKey = Config::env('GOOGLE_MAPS_API_KEY');
echo "   API Key: " . ($googleKey && $googleKey !== 'YOUR_GOOGLE_MAPS_KEY_HERE' ? '✅ Configured' : '❌ Missing') . "\n";

if ($googleKey && $googleKey !== 'YOUR_GOOGLE_MAPS_KEY_HERE') {
    // Test geocoding API
    $address = urlencode("Tour Eiffel, Paris, France");
    $url = "https://maps.googleapis.com/maps/api/geocode/json?address=$address&key=$googleKey";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if ($data['status'] === 'OK') {
            echo "   ✅ Google Maps API working!\n";
            $location = $data['results'][0]['geometry']['location'];
            echo "   Eiffel Tower coordinates: " . $location['lat'] . ", " . $location['lng'] . "\n";
        } else {
            echo "   ❌ Google Maps API error: " . $data['status'] . "\n";
            if ($data['status'] === 'REQUEST_DENIED') {
                echo "   API key may be invalid or restricted\n";
            }
        }
    } else {
        echo "   ❌ Google Maps API failed (HTTP $httpCode)\n";
    }
} else {
    echo "   ⚠️  No API key configured\n";
}

echo "\n====================================\n";
echo "Test Summary:\n";
echo "====================================\n";

// Test the actual API endpoints
echo "\nTesting Culture Radar API Endpoints:\n\n";

// Test weather endpoint
echo "Testing /api/real-weather.php...\n";
$weatherTest = file_get_contents('http://localhost:8888/api/real-weather.php?location=Paris');
if ($weatherTest) {
    $data = json_decode($weatherTest, true);
    if ($data['success']) {
        echo "✅ Weather endpoint working\n";
    } else {
        echo "⚠️ Weather endpoint returned but no data\n";
    }
}

// Test events endpoint
echo "\nTesting /api/real-events.php...\n";
$eventsTest = file_get_contents('http://localhost:8888/api/real-events.php?location=Paris&limit=3');
if ($eventsTest) {
    $data = json_decode($eventsTest, true);
    if ($data['success'] && isset($data['events'])) {
        echo "✅ Events endpoint working\n";
        echo "   Found " . count($data['events']) . " events\n";
    } else {
        echo "⚠️ Events endpoint returned but no data\n";
    }
}

echo "\n✅ Test complete!\n";
echo "\nIf any APIs are not working:\n";
echo "1. Check that your API keys are valid\n";
echo "2. Ensure the API services are not blocked by firewall\n";
echo "3. Verify API quotas haven't been exceeded\n";
?>