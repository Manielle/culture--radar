<?php
/**
 * Ticketmaster Discovery API Integration
 * Free API for real-time events
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Get parameters
$location = $_GET['location'] ?? 'Paris';
$limit = min(intval($_GET['limit'] ?? 20), 50); // Max 50 per request
$category = $_GET['category'] ?? null;

// Ticketmaster API settings
$apiKey = 'GAAGfWGR6F4MiGQGfMmtGG2Z3kHuGsAt'; // Free test key - replace with your own
$baseUrl = 'https://app.ticketmaster.com/discovery/v2/events.json';

// Map French cities to country codes and coordinates
$cityData = [
    'Paris' => ['lat' => '48.8566', 'lng' => '2.3522', 'radius' => '50'],
    'Lyon' => ['lat' => '45.7640', 'lng' => '4.8357', 'radius' => '30'],
    'Marseille' => ['lat' => '43.2965', 'lng' => '5.3698', 'radius' => '30'],
    'Toulouse' => ['lat' => '43.6047', 'lng' => '1.4442', 'radius' => '30'],
    'Nice' => ['lat' => '43.7102', 'lng' => '7.2620', 'radius' => '30'],
    'Nantes' => ['lat' => '47.2184', 'lng' => '-1.5536', 'radius' => '30'],
    'Strasbourg' => ['lat' => '48.5734', 'lng' => '7.7521', 'radius' => '30'],
    'Bordeaux' => ['lat' => '44.8378', 'lng' => '-0.5792', 'radius' => '30'],
    'Lille' => ['lat' => '50.6292', 'lng' => '3.0573', 'radius' => '30'],
];

// Get city coordinates or default to Paris
$cityInfo = $cityData[$location] ?? $cityData['Paris'];

// Build query parameters
$params = [
    'apikey' => $apiKey,
    'latlong' => $cityInfo['lat'] . ',' . $cityInfo['lng'],
    'radius' => $cityInfo['radius'],
    'unit' => 'km',
    'size' => $limit,
    'countryCode' => 'FR',
    'locale' => 'fr',
    'sort' => 'date,asc',
    'startDateTime' => date('Y-m-d\TH:i:s\Z', strtotime('now'))
];

// Add category filter if specified
if ($category) {
    $categoryMap = [
        'music' => 'Music',
        'musique' => 'Music',
        'sport' => 'Sports',
        'theater' => 'Arts & Theatre',
        'theatre' => 'Arts & Theatre',
        'family' => 'Family',
        'festival' => 'Music'
    ];
    
    if (isset($categoryMap[$category])) {
        $params['classificationName'] = $categoryMap[$category];
    }
}

// Make API request
$url = $baseUrl . '?' . http_build_query($params);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// Process response
$events = [];

if ($httpCode === 200 && $response) {
    $data = json_decode($response, true);
    
    if (isset($data['_embedded']['events'])) {
        foreach ($data['_embedded']['events'] as $event) {
            // Get venue info
            $venue = $event['_embedded']['venues'][0] ?? null;
            
            // Get price range
            $priceMin = $event['priceRanges'][0]['min'] ?? 0;
            $priceMax = $event['priceRanges'][0]['max'] ?? null;
            $price = $priceMin;
            
            // Get image
            $image = null;
            foreach ($event['images'] ?? [] as $img) {
                if ($img['width'] >= 500) {
                    $image = $img['url'];
                    break;
                }
            }
            if (!$image && isset($event['images'][0])) {
                $image = $event['images'][0]['url'];
            }
            
            // Format event
            $events[] = [
                'id' => $event['id'],
                'title' => $event['name'],
                'description' => $event['info'] ?? $event['pleaseNote'] ?? '',
                'category' => strtolower($event['classifications'][0]['segment']['name'] ?? 'event'),
                'venue_name' => $venue['name'] ?? 'Lieu à confirmer',
                'address' => $venue['address']['line1'] ?? '',
                'city' => $venue['city']['name'] ?? $location,
                'postal_code' => $venue['postalCode'] ?? '',
                'latitude' => $venue['location']['latitude'] ?? null,
                'longitude' => $venue['location']['longitude'] ?? null,
                'start_date' => $event['dates']['start']['dateTime'] ?? $event['dates']['start']['localDate'],
                'start_time' => $event['dates']['start']['localTime'] ?? null,
                'price' => $price,
                'price_min' => $priceMin,
                'price_max' => $priceMax,
                'currency' => $event['priceRanges'][0]['currency'] ?? 'EUR',
                'is_free' => $price == 0,
                'image_url' => $image,
                'external_url' => $event['url'],
                'ticket_url' => $event['url'],
                'source' => 'ticketmaster',
                'status' => $event['dates']['status']['code'] ?? 'onsale'
            ];
        }
    }
}

// If no events found, use mock data
if (empty($events)) {
    $events = [
        [
            'id' => 'mock1',
            'title' => 'Concert Rock au Zénith',
            'description' => 'Une soirée rock inoubliable avec les meilleurs groupes.',
            'category' => 'musique',
            'venue_name' => 'Le Zénith',
            'address' => 'Avenue Jean Jaurès',
            'city' => $location,
            'start_date' => date('Y-m-d H:i:s', strtotime('+3 days')),
            'price' => 35,
            'is_free' => false,
            'image_url' => 'https://via.placeholder.com/600x400',
            'source' => 'mock'
        ],
        [
            'id' => 'mock2',
            'title' => 'Festival de Jazz',
            'description' => 'Trois jours de jazz avec des artistes internationaux.',
            'category' => 'festival',
            'venue_name' => 'Parc Floral',
            'address' => 'Route de la Pyramide',
            'city' => $location,
            'start_date' => date('Y-m-d', strtotime('next friday')),
            'price' => 0,
            'is_free' => true,
            'source' => 'mock'
        ],
        [
            'id' => 'mock3',
            'title' => 'Théâtre Classique',
            'description' => 'Une pièce de Molière revisitée.',
            'category' => 'theatre',
            'venue_name' => 'Théâtre Municipal',
            'address' => 'Place du Théâtre',
            'city' => $location,
            'start_date' => date('Y-m-d 20:30:00', strtotime('+5 days')),
            'price' => 25,
            'is_free' => false,
            'source' => 'mock'
        ]
    ];
}

// Return response
echo json_encode([
    'success' => true,
    'location' => $location,
    'total' => count($events),
    'events' => $events,
    'source' => !empty($events) && $events[0]['source'] !== 'mock' ? 'ticketmaster' : 'mock',
    'api_status' => $httpCode === 200 ? 'connected' : 'failed'
]);
?>