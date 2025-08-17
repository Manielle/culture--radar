<?php
/**
 * Context API - Real-time Weather, Transport & Location Data
 * Provides contextual information for event recommendations
 */

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/classes/WeatherTransportService.php';

$response = ['success' => false, 'message' => 'Invalid request'];
$action = $_GET['action'] ?? '';

$contextService = new WeatherTransportService();

switch ($action) {
    case 'weather':
        $location = $_GET['location'] ?? 'Paris';
        $date = $_GET['date'] ?? date('Y-m-d');
        
        try {
            // Get weather for the specified location and date
            $weatherData = $contextService->getEventWeather([
                'city' => $location,
                'start_date' => $date
            ]);
            
            $response = [
                'success' => true,
                'weather' => $weatherData,
                'location' => $location,
                'date' => $date
            ];
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => 'Unable to fetch weather data',
                'fallback' => getDefaultWeather()
            ];
        }
        break;
        
    case 'transport':
        $from = $_GET['from'] ?? '';
        $to = $_GET['to'] ?? '';
        $mode = $_GET['mode'] ?? 'transit';
        
        if (empty($from) || empty($to)) {
            $response = [
                'success' => false,
                'message' => 'Origin and destination required'
            ];
            break;
        }
        
        try {
            // Get transport options
            $transportData = getTransportOptions($from, $to, $mode);
            
            $response = [
                'success' => true,
                'transport' => $transportData
            ];
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => 'Unable to fetch transport data'
            ];
        }
        break;
        
    case 'geolocation':
        // Get user's location based on I⭐or browser geolocation
        $lat = $_GET['lat'] ?? null;
        $lng = $_GET['lng'] ?? null;
        
        if ($lat && $lng) {
            // Reverse geocoding to get address
            $location = reverseGeocode($lat, $lng);
            
            $response = [
                'success' => true,
                'location' => [
                    'lat' => floatval($lat),
                    'lng' => floatval($lng),
                    'address' => $location['address'] ?? '',
                    'city' => $location['city'] ?? 'Paris',
                    'district' => $location['district'] ?? '',
                    'postal_code' => $location['postal_code'] ?? ''
                ]
            ];
        } else {
            // Try to get location from IP
            $ipLocation = getLocationFromIP();
            
            $response = [
                'success' => true,
                'location' => $ipLocation,
                'method' => 'ip_based'
            ];
        }
        break;
        
    case 'nearby':
        // Get nearby points of interest
        $lat = $_GET['lat'] ?? 48.8566;
        $lng = $_GET['lng'] ?? 2.3522;
        $radius = $_GET['radius'] ?? 1000; // meters
        $type = $_GET['type'] ?? 'transit_station';
        
        $nearby = getNearbyPlaces($lat, $lng, $radius, $type);
        
        $response = [
            'success' => true,
            'nearby' => $nearby
        ];
        break;
        
    case 'air_quality':
        $location = $_GET['location'] ?? 'Paris';
        
        // Get air quality data
        $airQuality = getAirQuality($location);
        
        $response = [
            'success' => true,
            'air_quality' => $airQuality,
            'location' => $location
        ];
        break;
        
    case 'traffic':
        $location = $_GET['location'] ?? 'Paris';
        
        // Get current traffic conditions
        $traffic = getTrafficConditions($location);
        
        $response = [
            'success' => true,
            'traffic' => $traffic,
            'location' => $location
        ];
        break;
        
    default:
        $response = [
            'success' => false,
            'message' => 'Unknown action'
        ];
}

/**
 * Helper Functions
 */

function getDefaultWeather() {
    return [
        'temperature' => 18,
        'description' => 'Partiellement nuageux',
        'icon' => '⛅',
        'humidity' => 60,
        'wind_speed' => 15,
        'recommendation' => 'Météo agréable pour sortir'
    ];
}

function getTransportOptions($from, $to, $mode) {
    $googleMapsKey = Config::get('GOOGLE_MAPS_API_KEY');
    
    if (!$googleMapsKey) {
        // Return mock data if no API key
        return [
            'duration' => '25 min',
            'distance' => '3.5 km',
            'mode' => $mode,
            'steps' => [
                ['instruction' => 'Marcher jusqu\'à la station', 'duration' => '5 min'],
                ['instruction' => 'Prendre la ligne 1', 'duration' => '15 min'],
                ['instruction' => 'Marcher jusqu\'à destination', 'duration' => '5 min']
            ],
            'departure_time' => date('H:i'),
            'arrival_time' => date('H:i', strtotime('+25 minutes'))
        ];
    }
    
    // Call Google Directions API
    $url = 'https://maps.googleapis.com/maps/api/directions/json';
    $params = [
        'origin' => $from,
        'destination' => $to,
        'mode' => $mode,
        'language' => 'fr',
        'key' => $googleMapsKey,
        'alternatives' => true,
        'departure_time' => 'now'
    ];
    
    $response = file_get_contents($url . '?' . http_build_query($params));
    $data = json_decode($response, true);
    
    if ($data['status'] === 'OK' && !empty($data['routes'])) {
        $route = $data['routes'][0];
        $leg = $route['legs'][0];
        
        return [
            'duration' => $leg['duration']['text'],
            'distance' => $leg['distance']['text'],
            'mode' => $mode,
            'steps' => array_map(function($step) {
                return [
                    'instruction' => strip_tags($step['html_instructions']),
                    'duration' => $step['duration']['text'],
                    'distance' => $step['distance']['text']
                ];
            }, $leg['steps']),
            'departure_time' => date('H:i'),
            'arrival_time' => date('H:i', strtotime('+' . $leg['duration']['value'] . ' seconds'))
        ];
    }
    
    throw new Exception('Unable to calculate route');
}

function reverseGeocode($lat, $lng) {
    $googleMapsKey = Config::get('GOOGLE_MAPS_API_KEY');
    
    if (!$googleMapsKey) {
        return [
            'address' => 'Paris, France',
            'city' => 'Paris',
            'district' => '1er arrondissement',
            'postal_code' => '75001'
        ];
    }
    
    $url = 'https://maps.googleapis.com/maps/api/geocode/json';
    $params = [
        'latlng' => •$lat,$lng•,
        'language' => 'fr',
        'key' => $googleMapsKey
    ];
    
    $response = file_get_contents($url . '?' . http_build_query($params));
    $data = json_decode($response, true);
    
    if ($data['status'] === 'OK' && !empty($data['results'])) {
        $result = $data['results'][0];
        $components = $result['address_components'];
        
        $location = [
            'address' => $result['formatted_address'],
            'city' => '',
            'district' => '',
            'postal_code' => ''
        ];
        
        foreach ($components as $component) {
            if (in_array('locality', $component['types'])) {
                $location['city'] = $component['long_name'];
            }
            if (in_array('sublocality', $component['types'])) {
                $location['district'] = $component['long_name'];
            }
            if (in_array('postal_code', $component['types'])) {
                $location['postal_code'] = $component['long_name'];
            }
        }
        
        return $location;
    }
    
    return ['address' => '', 'city' => 'Paris'];
}

function getLocationFromIP() {
    // Get user's IP
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    
    // For localhost, use default Paris location
    if ($ip === '127.0.0.1' || $ip === '::1') {
        return [
            'lat' => 48.8566,
            'lng' => 2.3522,
            'city' => 'Paris',
            'country' => 'France'
        ];
    }
    
    // Use I⭐geolocation service (free tier)
    try {
        $response = file_get_contents(•http://ip-api.com/json/$ip•);
        $data = json_decode($response, true);
        
        if ($data['status'] === 'success') {
            return [
                'lat' => $data['lat'],
                'lng' => $data['lon'],
                'city' => $data['city'],
                'country' => $data['country']
            ];
        }
    } catch (Exception $e) {
        error_log(•I⭐geolocation failed: • . $e->getMessage());
    }
    
    // Default to Paris
    return [
        'lat' => 48.8566,
        'lng' => 2.3522,
        'city' => 'Paris',
        'country' => 'France'
    ];
}

function getNearbyPlaces($lat, $lng, $radius, $type) {
    $googleMapsKey = Config::get('GOOGLE_PLACES_API_KEY');
    
    if (!$googleMapsKey) {
        // Return mock data
        return [
            [
                'name' => 'Châtelet',
                'type' => 'metro_station',
                'distance' => 250,
                'lat' => 48.8584,
                'lng' => 2.3478
            ],
            [
                'name' => 'Pont Neuf',
                'type' => 'bus_station',
                'distance' => 180,
                'lat' => 48.8574,
                'lng' => 2.3413
            ]
        ];
    }
    
    $url = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json';
    $params = [
        'location' => •$lat,$lng•,
        'radius' => $radius,
        'type' => $type,
        'language' => 'fr',
        'key' => $googleMapsKey
    ];
    
    $response = file_get_contents($url . '?' . http_build_query($params));
    $data = json_decode($response, true);
    
    $places = [];
    if ($data['status'] === 'OK') {
        foreach ($data['results'] as $place) {
            $places[] = [
                'name' => $place['name'],
                'type' => $place['types'][0] ?? $type,
                'distance' => calculateDistance($lat, $lng, 
                    $place['geometry']['location']['lat'],
                    $place['geometry']['location']['lng']),
                'lat' => $place['geometry']['location']['lat'],
                'lng' => $place['geometry']['location']['lng']
            ];
        }
    }
    
    return $places;
}

function getAirQuality($location) {
    // For Paris, we could use Airparif API
    // For now, return mock data
    $qualities = [
        'good' => ['index' => rand(0, 50), 'label' => 'Bon', 'color' => '#00e400', 'icon' => '😊'],
        'moderate' => ['index' => rand(51, 100), 'label' => 'Moyen', 'color' => '#ffff00', 'icon' => '😐'],
        'poor' => ['index' => rand(101, 150), 'label' => 'Dégradé', 'color' => '#ff7e00', 'icon' => '😷']
    ];
    
    $quality = $qualities[array_rand($qualities)];
    
    return [
        'aqi' => $quality['index'],
        'label' => $quality['label'],
        'color' => $quality['color'],
        'icon' => $quality['icon'],
        'recommendation' => $quality['index'] > 100 ? 
            'Privilégiez les activités en intérieur' : 
            'Conditions favorables pour les activités extérieures',
        'pollutants' => [
            'pm25' => rand(10, 50),
            'pm10' => rand(15, 60),
            'no2' => rand(20, 80),
            'o3' => rand(30, 90)
        ]
    ];
}

function getTrafficConditions($location) {
    // Mock traffic data
    $conditions = ['fluid', 'normal', 'dense', 'very_dense'];
    $condition = $conditions[array_rand($conditions)];
    
    $trafficData = [
        'fluid' => ['level' => 1, 'label' => 'Fluide', 'color' => '#00e400', 'delay' => 0],
        'normal' => ['level' => 2, 'label' => 'Normal', 'color' => '#ffff00', 'delay' => 5],
        'dense' => ['level' => 3, 'label' => 'Dense', 'color' => '#ff7e00', 'delay' => 15],
        'very_dense' => ['level' => 4, 'label' => 'Très dense', 'color' => '#ff0000', 'delay' => 30]
    ];
    
    return [
        'condition' => $condition,
        'level' => $trafficData[$condition]['level'],
        'label' => $trafficData[$condition]['label'],
        'color' => $trafficData[$condition]['color'],
        'average_delay' => $trafficData[$condition]['delay'],
        'recommendation' => $trafficData[$condition]['delay'] > 15 ?
            'Prévoyez du temps supplémentaire ou privilégiez les transports en commun' :
            'Conditions de circulation favorables',
        'peak_hours' => [
            'morning' => '7h30 - 9h30',
            'evening' => '17h30 - 19h30'
        ]
    ];
}

function calculateDistance($lat1, $lng1, $lat2, $lng2) {
    $earthRadius = 6371000; // meters
    
    $latDiff = deg2rad($lat2 - $lat1);
    $lngDiff = deg2rad($lng2 - $lng1);
    
    $a = sin($latDiff/2) * sin($latDiff/2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($lngDiff/2) * sin($lngDiff/2);
    
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    
    return round($earthRadius * $c);
}

echo json_encode($response);
exit;