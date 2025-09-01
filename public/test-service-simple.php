<?php
require_once __DIR__ . '/services/ImprovedEventsService.php';
require_once __DIR__ . '/services/KnownEventsData.php';

try {
    echo "Test du service ImprovedEventsService\n";
    echo "=====================================\n\n";
    
    // Test 1: findRealEvents
    echo "Test 1: findRealEvents('', 10)\n";
    $events = ImprovedEventsService::findRealEvents('', 10);
    echo "Nombre d'événements retournés: " . count($events) . "\n\n";
    
    if (count($events) > 0) {
        echo "Premier événement:\n";
        print_r($events[0]);
    }
    
    // Test 2: KnownEventsData
    echo "\n\nTest 2: KnownEventsData::getEvents()\n";
    $knownEvents = KnownEventsData::getEvents();
    echo "Nombre d'événements connus: " . count($knownEvents) . "\n";
    
} catch (Exception $e) {
    echo "ERREUR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
?>