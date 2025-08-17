<?php
/**
 * OpenAgenda Events API Integration
 * Using the official OpenAgenda API v2
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Load configuration
require_once dirname(__DIR__) . '/config.php';

// Get API key
$apiKey = Config::env('OPENAGENDA_API_KEY');

if (!$apiKey || $apiKey === 'YOUR_OPENAGENDA_KEY_HERE') {
    echo json_encode([
        'success' => false,
        'message' => 'API key not configured',
        'events' => []
    ]);
    exit;
}

// Get parameters
$location = $_GET['location'] ?? 'Paris';
$limit = intval($_GET['limit'] ?? 20);
$page = intval($_GET['page'] ?? 1);
$category = $_GET['category'] ?? null;

// OpenAgenda public agenda UIDs for French cultural events
// These are public agendas that can be accessed with the API
$agendaUids = [
    '28185236', // Agenda culturel France
    '87688269', // Événements Paris
    '65180707', // Culture et patrimoine
    '82885684', // Spectacles et concerts
];

// Try each agenda until we find events
$allEvents = [];

foreach ($agendaUids as $agendaUid) {
    // Build API URL for this specific agenda
    $apiUrl = •https://api.openagenda.com/v2/agendas/{$agendaUid}/events•;
    
    // Build query parameters
    $params = [
        'size' => $limit,
        'page' => $page,
        'sort' => 'updatedAt.desc',
        'relative' => ['current', 'upcoming'],
        'search' => $location // Search in all text fields
    ];
    
    // Add timing filters for upcoming events
    $params['timings[gte]'] = date('Y-m-d');
    
    // Add category filter if specified
    if ($category) {
        $categoryMap = [
            'art' => 'Arts visuels',
            'music' => 'Concert',
            'musique' => 'Concert', 
            'theater' => 'Spectacle vivant',
            'theatre' => 'Spectacle vivant',
            'cinema' => 'Projection',
            'literature' => 'Lecture',
            'heritage' => 'Visite',
            'dance' => 'Danse',
            'danse' => 'Danse',
            'festival' => 'Festival'
        ];
        
        if (isset($categoryMap[$category])) {
            $params['keywords'] = [$categoryMap[$category]];
        }
    }
    
    // Make API request with key in header (recommended by OpenAgenda)
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl . '?' . http_build_query($params));
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
    
    if ($httpCode === 200 && $response) {
        $data = json_decode($response, true);
        
        if (isset($data['events']) && count($data['events']) > 0) {
            foreach ($data['events'] as $event) {
                // Transform to our format
                $timing = $event['firstTiming'] ?? $event['lastTiming'] ?? null;
                $location = $event['location'] ?? [];
                
                // Get image URL
                $image = null;
                if (isset($event['image'])) {
                    if (is_string($event['image'])) {
                        $image = $event['image'];
                    } elseif (isset($event['image']['base']) && isset($event['image']['filename'])) {
                        $image = $event['image']['base'] . $event['image']['filename'];
                    }
                }
                
                // Determine if free
                $isFree = false;
                if (isset($event['conditions'])) {
                    $conditions = is_array($event['conditions']) ? implode(' ', $event['conditions']) : $event['conditions'];
                    $isFree = stripos($conditions, 'gratuit') !== false || stripos($conditions, 'entrée libre') !== false;
                }
                
                $allEvents[] = [
                    'id' => $event['uid'] ?? uniqid(),
                    'title' => $event['title']['fr'] ?? $event['title'] ?? 'Sans titre',
                    'description' => strip_tags($event['description']['fr'] ?? $event['description'] ?? ''),
                    'category' => $event['keywords']['fr'][0] ?? 'culture',
                    'venue_name' => $location['name'] ?? 'Lieu à définir',
                    'address' => $location['address'] ?? '',
                    'city' => $location['city'] ?? $location['locality'] ?? '',
                    'postal_code' => $location['postalCode'] ?? '',
                    'latitude' => $location['latitude'] ?? null,
                    'longitude' => $location['longitude'] ?? null,
                    'start_date' => $timing ? date('Y-m-d H:i:s', strtotime($timing['begin'])) : null,
                    'end_date' => $timing ? date('Y-m-d H:i:s', strtotime($timing['end'])) : null,
                    'price' => $isFree ? 0 : null,
                    'is_free' => $isFree,
                    'image_url' => $image,
                    'external_url' => $event['originAgenda']['url'] ?? null,
                    'source' => 'openagenda',
                    'agenda_uid' => $agendaUid
                ];
            }
        }
    }
    
    // If we have enough events, stop searching other agendas
    if (count($allEvents) >= $limit) {
        break;
    }
}

// If we still don't have events, use fallback
if (empty($allEvents)) {
    // Return mock events as fallback
    $allEvents = [
        [
            'id' => 'mock1',
            'title' => 'Exposition au Louvre',
            'description' => 'Découvrez les chefs-d\'œuvre du musée.',
            'category' => 'exposition',
            'venue_name' => 'Musée du Louvre',
            'address' => 'Rue de Rivoli, 75001 Paris',
            'city' => $location,
            'start_date' => date('Y-m-d H:i:s', strtotime('+1 day')),
            'price' => 17,
            'is_free' => false,
            'source' => 'mock'
        ],
        [
            'id' => 'mock2', 
            'title' => 'Concert Jazz',
            'description' => 'Soirée jazz avec des artistes locaux.',
            'category' => 'musique',
            'venue_name' => 'Jazz Club',
            'address' => '10 Rue des Arts',
            'city' => $location,
            'start_date' => date('Y-m-d 21:00:00', strtotime('+2 days')),
            'price' => 20,
            'is_free' => false,
            'source' => 'mock'
        ],
        [
            'id' => 'mock3',
            'title' => 'Festival de rue',
            'description' => 'Art et performances dans les rues.',
            'category' => 'festival',
            'venue_name' => 'Centre ville',
            'address' => 'Place centrale',
            'city' => $location,
            'start_date' => date('Y-m-d', strtotime('next saturday')),
            'price' => 0,
            'is_free' => true,
            'source' => 'mock'
        ]
    ];
}

// Limit results
$allEvents = array_slice($allEvents, 0, $limit);

// Return response
echo json_encode([
    'success' => true,
    'location' => $location,
    'total' => count($allEvents),
    'events' => $allEvents,
    'source' => !empty($allEvents) && $allEvents[0]['source'] === 'openagenda' ? 'openagenda' : 'mock'
]);
?>