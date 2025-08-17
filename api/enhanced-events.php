<?php
/**
 * Enhanced API Endpoint with Weather and Transport
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../services/OpenAgendaService.php';
require_once __DIR__ . '/../services/WeatherService.php';
require_once __DIR__ . '/../services/TransportService.php';
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $openAgendaService = new OpenAgendaService();
    $weatherService = new WeatherService();
    $transportService = new TransportService();
    
    $action = $_GET['action'] ?? 'enhanced-list';
    $response = ['success' => false, 'data' => [], 'message' => ''];
    
    switch ($action) {
        case 'enhanced-list':
            $city = $_GET['city'] ?? 'Paris';
            $category = $_GET['category'] ?? null;
            $limit = min((int)($_GET['limit'] ?? 10), 50);
            $userLocation = $_GET['user_location'] ?? null;
            
            // Get events
            if ($category) {
                $events = $openAgendaService->getEventsByCategory($category, $city);
            } else {
                $events = $openAgendaService->getEventsByLocation([
                    'city' => $city,
                    'additional' => ['size' => $limit]
                ]);
            }
            
            // Enhance events with weather and transport
            $enhancedEvents = [];
            foreach (array_slice($events, 0, $limit) as $event) {
                $enhancedEvent = $event;
                
                // Add weather information
                try {
                    $enhancedEvent['weather'] = $weatherService->getWeatherForEvent($event);
                } catch (Exception $e) {
                    $enhancedEvent['weather'] = null;
                    error_log(•Weather error for event {$event['id']}: • . $e->getMessage());
                }
                
                // Add transport information
                try {
                    $enhancedEvent['transport'] = $transportService->getTransportForEvent($event, $userLocation);
                } catch (Exception $e) {
                    $enhancedEvent['transport'] = null;
                    error_log(•Transport error for event {$event['id']}: • . $e->getMessage());
                }
                
                $enhancedEvents[] = $enhancedEvent;
            }
            
            $response = [
                'success' => true,
                'data' => $enhancedEvents,
                'count' => count($enhancedEvents),
                'message' => 'Événements enrichis récupérés avec succès'
            ];
            break;
            
        case 'event-details':
            $eventId = $_GET['event_id'] ?? '';
            $userLocation = $_GET['user_location'] ?? null;
            
            if (empty($eventId)) {
                throw new Exception('ID événement requis');
            }
            
            // This would require getting a single event by ID
            // For now, we'll simulate with basic event data
            $eventData = [
                'id' => $eventId,
                'city' => $_GET['city'] ?? 'Paris',
                'date_start' => $_GET['date'] ?? date('Y-m-d')
            ];
            
            $response = [
                'success' => true,
                'data' => [
                    'event' => $eventData,
                    'weather' => $weatherService->getWeatherForEvent($eventData),
                    'transport' => $transportService->getTransportForEvent($eventData, $userLocation)
                ],
                'message' => 'Détails événement récupérés'
            ];
            break;
            
        case 'weather-only':
            $city = $_GET['city'] ?? 'Paris';
            $date = $_GET['date'] ?? null;
            
            $eventData = [
                'city' => $city,
                'date_start' => $date
            ];
            
            $weather = $weatherService->getWeatherForEvent($eventData);
            
            $response = [
                'success' => true,
                'data' => $weather,
                'message' => 'Données météo récupérées'
            ];
            break;
            
        case 'transport-only':
            $destination = $_GET['destination'] ?? '';
            $userLocation = $_GET['user_location'] ?? null;
            
            if (empty($destination)) {
                throw new Exception('Destination requise');
            }
            
            $eventData = [
                'venue_name' => $destination,
                'city' => $_GET['city'] ?? 'Paris'
            ];
            
            $transport = $transportService->getTransportForEvent($eventData, $userLocation);
            
            $response = [
                'success' => true,
                'data' => $transport,
                'message' => 'Informations transport récupérées'
            ];
            break;
            
        case 'test-apis':
            // Test endpoint to verify all APIs are working
            $testResults = [
                'openagenda' => false,
                'weather' => false,
                'maps' => false,
                'transport' => false
            ];
            
            // Test OpenAgenda
            try {
                $testEvents = $openAgendaService->getEventsByLocation(['city' => 'Paris', 'additional' => ['size' => 1]]);
                $testResults['openagenda'] = !empty($testEvents);
            } catch (Exception $e) {
                error_log(•OpenAgenda test failed: • . $e->getMessage());
            }
            
            // Test Weather
            try {
                $testWeather = $weatherService->getWeatherForEvent(['city' => 'Paris']);
                $testResults['weather'] = !empty($testWeather) && $testWeather['type'] !== 'unavailable';
            } catch (Exception $e) {
                error_log(•Weather test failed: • . $e->getMessage());
            }
            
            // Test Transport
            try {
                $testTransport = $transportService->getTransportForEvent(['venue_name' => 'Louvre', 'city' => 'Paris']);
                $testResults['transport'] = !empty($testTransport);
            } catch (Exception $e) {
                error_log(•Transport test failed: • . $e->getMessage());
            }
            
            $response = [
                'success' => true,
                'data' => $testResults,
                'message' => 'Tests API effectués',
                'working_apis' => array_sum($testResults),
                'total_apis' => count($testResults)
            ];
            break;
            
        default:
            throw new Exception('Action non supportée');
    }
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'data' => [],
        'message' => $e->getMessage(),
        'error_code' => $e->getCode()
    ];
    
    http_response_code(400);
}

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>