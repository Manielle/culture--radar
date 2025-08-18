<?php
/**
 * Test Updated OpenAgenda API Integration with Mock Fallback
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/services/OpenAgendaService.php';

header('Content-Type: text/plain; charset=utf-8');

try {
    $openAgendaService = new OpenAgendaService();
    $cities = ['Paris', 'Lyon', 'Bordeaux', 'Toulouse'];
    $results = [];
    
    echo "Testing Updated OpenAgenda Service with Mock Fallback...\n\n";
    
    foreach ($cities as $city) {
        echo "=== Fetching events for $city ===\n";
        
        $events = $openAgendaService->getEventsByLocation([
            'city' => $city,
            'additional' => ['size' => 2]
        ]);
        
        $results[$city] = [
            'count' => count($events),
            'events' => array_slice($events, 0, 2)
        ];
        
        echo "Found " . count($events) . " events in $city\n";
        
        if (!empty($events)) {
            foreach (array_slice($events, 0, 2) as $event) {
                echo "  📍 " . $event['title'] . "\n";
                echo "     Category: " . $event['category'] . "\n";
                echo "     Venue: " . $event['venue_name'] . "\n";
                echo "     Price: " . ($event['is_free'] ? 'Gratuit' : ($event['price'] ? $event['price'] . '€' : 'Prix libre')) . "\n";
                echo "     Source: " . $event['source'] . "\n";
                echo "\n";
            }
        }
        echo "\n";
    }
    
    echo "=== Testing Category Filtering ===\n";
    $musicEvents = $openAgendaService->getEventsByCategory('musique', 'Paris');
    echo "Music events in Paris: " . count($musicEvents) . "\n";
    
    if (!empty($musicEvents)) {
        $event = $musicEvents[0];
        echo "  🎵 " . $event['title'] . " (" . $event['venue_name'] . ")\n";
    }
    
    echo "\n=== Summary ===\n";
    $totalEvents = array_sum(array_column($results, 'count'));
    echo "Total events found: $totalEvents\n";
    echo "Cities covered: " . count($results) . "\n";
    echo "Service working: " . ($totalEvents > 0 ? "✅ YES" : "❌ NO") . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString();
}
?>