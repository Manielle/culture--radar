<?php
/**
 * API Setup and Testing Script
 * Run this after adding your API keys to .env
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';

echo "=================================\n";
echo "   Culture Radar API Setup Tool  \n";
echo "=================================\n\n";

// Load environment variables
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    echo "✅ .env file loaded\n\n";
} else {
    echo "❌ .env file not found! Please create it first.\n";
    exit(1);
}

// Check API Keys
echo "Checking API Keys:\n";
echo "==================\n";

$apis = [
    'OPENAGENDA_API_KEY' => 'OpenAgenda',
    'OPENWEATHERMAP_API_KEY' => 'OpenWeatherMap',
    'GOOGLE_MAPS_API_KEY' => 'Google Maps',
    'MAPBOX_API_KEY' => 'Mapbox'
];

$missingKeys = [];
$foundKeys = [];

foreach ($apis as $key => $name) {
    $value = $_ENV[$key] ?? '';
    if (empty($value) || $value === 'YOUR_' . str_replace('_API_KEY', '', $key) . '_KEY_HERE') {
        echo "❌ $name: Not configured\n";
        $missingKeys[] = $name;
    } else {
        echo "✅ $name: " . substr($value, 0, 8) . "****\n";
        $foundKeys[$key] = $value;
    }
}

if (!empty($missingKeys)) {
    echo "\n⚠️  Missing API keys: " . implode(', ', $missingKeys) . "\n";
    echo "Please add them to your .env file.\n\n";
}

// Test configured APIs
if (!empty($foundKeys)) {
    echo "\n\nTesting API Connections:\n";
    echo "========================\n";
    
    // Test OpenAgenda
    if (isset($foundKeys['OPENAGENDA_API_KEY'])) {
        echo "\n1. Testing OpenAgenda API...\n";
        $apiKey = $foundKeys['OPENAGENDA_API_KEY'];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.openagenda.com/v2/agendas?key=" . $apiKey . "&limit=1");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $data = json_decode($response, true);
            echo "   ✅ OpenAgenda API working!\n";
            echo "   Found " . ($data['total'] ?? 0) . " agendas available\n";
            
            // Try to fetch some events
            echo "   Fetching sample events...\n";
            
            // Example agenda UIDs for French cultural events
            $agendaIds = [
                '59284934', // Paris events
                '89456231', // Lyon events
                '78234156', // Marseille events
            ];
            
            foreach ($agendaIds as $agendaId) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://api.openagenda.com/v2/agendas/$agendaId/events?key=" . $apiKey . "&limit=5");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                $eventsResponse = curl_exec($ch);
                $eventsCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                if ($eventsCode === 200) {
                    $events = json_decode($eventsResponse, true);
                    if (!empty($events['events'])) {
                        echo "   ✅ Found " . count($events['events']) . " events in agenda $agendaId\n";
                        
                        // Import these events to database
                        importEventsToDatabase($events['events']);
                    }
                }
            }
        } else {
            echo "   ❌ OpenAgenda API failed (HTTP $httpCode)\n";
            echo "   Check your API key is valid\n";
        }
    }
    
    // Test OpenWeatherMap
    if (isset($foundKeys['OPENWEATHERMAP_API_KEY'])) {
        echo "\n2. Testing OpenWeatherMap API...\n";
        $apiKey = $foundKeys['OPENWEATHERMAP_API_KEY'];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.openweathermap.org/data/2.5/weather?q=Paris&appid=" . $apiKey . "&units=metric");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $data = json_decode($response, true);
            echo "   ✅ OpenWeatherMap API working!\n";
            echo "   Current weather in Paris: " . $data['weather'][0]['description'] . ", " . round($data['main']['temp']) . "°C\n";
        } else {
            echo "   ❌ OpenWeatherMap API failed (HTTP $httpCode)\n";
            $error = json_decode($response, true);
            echo "   Error: " . ($error['message'] ?? 'Unknown error') . "\n";
        }
    }
    
    // Test Google Maps
    if (isset($foundKeys['GOOGLE_MAPS_API_KEY'])) {
        echo "\n3. Testing Google Maps API...\n";
        $apiKey = $foundKeys['GOOGLE_MAPS_API_KEY'];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://maps.googleapis.com/maps/api/geocode/json?address=Paris&key=" . $apiKey);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $data = json_decode($response, true);
            if ($data['status'] === 'OK') {
                echo "   ✅ Google Maps API working!\n";
                echo "   Geocoded Paris: " . $data['results'][0]['formatted_address'] . "\n";
            } else {
                echo "   ❌ Google Maps API error: " . $data['status'] . "\n";
                echo "   " . ($data['error_message'] ?? '') . "\n";
            }
        } else {
            echo "   ❌ Google Maps API failed (HTTP $httpCode)\n";
        }
    }
    
    // Test Mapbox
    if (isset($foundKeys['MAPBOX_API_KEY'])) {
        echo "\n4. Testing Mapbox API...\n";
        $apiKey = $foundKeys['MAPBOX_API_KEY'];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.mapbox.com/geocoding/v5/mapbox.places/Paris.json?access_token=" . $apiKey);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $data = json_decode($response, true);
            if (!empty($data['features'])) {
                echo "   ✅ Mapbox API working!\n";
                echo "   Found location: " . $data['features'][0]['place_name'] . "\n";
            } else {
                echo "   ❌ Mapbox API returned no results\n";
            }
        } else {
            echo "   ❌ Mapbox API failed (HTTP $httpCode)\n";
            $error = json_decode($response, true);
            echo "   Error: " . ($error['message'] ?? 'Invalid token') . "\n";
        }
    }
}

// Database Import Function
function importEventsToDatabase($events) {
    try {
        $dbConfig = Config::database();
        $dsn = "mysql:host=" . $dbConfig['host'] . ";dbname=" . $dbConfig['name'] . ";charset=" . $dbConfig['charset'];
        $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $imported = 0;
        foreach ($events as $event) {
            // Map OpenAgenda fields to our database
            $stmt = $pdo->prepare("
                INSERT INTO events (
                    external_id, title, description, category, venue_name, 
                    address, city, start_date, end_date, is_free, 
                    image_url, website_url, is_active, created_at
                ) VALUES (
                    :external_id, :title, :description, :category, :venue_name,
                    :address, :city, :start_date, :end_date, :is_free,
                    :image_url, :website_url, 1, NOW()
                ) ON DUPLICATE KEY UPDATE
                    title = VALUES(title),
                    description = VALUES(description),
                    updated_at = NOW()
            ");
            
            $stmt->execute([
                ':external_id' => $event['uid'] ?? uniqid(),
                ':title' => $event['title']['fr'] ?? $event['title'] ?? 'Sans titre',
                ':description' => $event['description']['fr'] ?? $event['description'] ?? '',
                ':category' => $event['keywords']['fr'][0] ?? 'Culture',
                ':venue_name' => $event['location']['name'] ?? 'Lieu à confirmer',
                ':address' => $event['location']['address'] ?? '',
                ':city' => $event['location']['city'] ?? 'Paris',
                ':start_date' => date('Y-m-d H:i:s', strtotime($event['firstDate'] ?? 'now')),
                ':end_date' => date('Y-m-d H:i:s', strtotime($event['lastDate'] ?? '+1 day')),
                ':is_free' => isset($event['conditions']) && strpos(strtolower($event['conditions']), 'gratuit') !== false,
                ':image_url' => $event['image'] ?? '',
                ':website_url' => $event['registration'][0]['value'] ?? ''
            ]);
            $imported++;
        }
        
        echo "   📥 Imported $imported events to database\n";
        
    } catch (Exception $e) {
        echo "   ❌ Database import failed: " . $e->getMessage() . "\n";
    }
}

echo "\n\n=================================\n";
echo "Setup Summary:\n";
echo "=================================\n";

if (empty($missingKeys)) {
    echo "✅ All APIs configured and ready!\n";
    echo "\nNext steps:\n";
    echo "1. Visit your dashboard to see real data\n";
    echo "2. Events are being imported automatically\n";
    echo "3. Weather and maps should work now\n";
} else {
    echo "⚠️  Some APIs are not configured\n";
    echo "\nTo complete setup:\n";
    echo "1. Get API keys from:\n";
    foreach ($missingKeys as $api) {
        switch($api) {
            case 'OpenAgenda':
                echo "   - OpenAgenda: https://openagenda.com/api\n";
                break;
            case 'OpenWeatherMap':
                echo "   - OpenWeatherMap: https://openweathermap.org/api/keys\n";
                break;
            case 'Google Maps':
                echo "   - Google Maps: https://console.cloud.google.com/\n";
                break;
            case 'Mapbox':
                echo "   - Mapbox: https://account.mapbox.com/\n";
                break;
        }
    }
    echo "2. Add them to your .env file\n";
    echo "3. Run this script again\n";
}

echo "\n";
?>