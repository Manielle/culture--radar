<?php
require_once __DIR__ . '/services/KnownEventsData.php';

// Test the discover logic
$allEvents = KnownEventsData::getKnownEvents();
$filters = [
    'category' => '',
    'city' => '',
    'price_min' => '',
    'price_max' => '',
    'date_from' => '',
    'date_to' => '',
    'is_free' => 0,
    'search' => '',
    'sort' => 'relevance'
];

echo "<!DOCTYPE html><html><head><title>Debug Discover</title></head><body>";
echo "<h1>Debug Discover Page</h1>";

echo "<h2>Étape 1: Récupération des événements</h2>";
echo "<p>Nombre d'événements récupérés: <strong>" . count($allEvents) . "</strong></p>";

if (count($allEvents) > 0) {
    echo "<h3>Premier événement:</h3>";
    echo "<pre>";
    print_r($allEvents[0]);
    echo "</pre>";
}

// Apply filters (like discover.php does)
$filteredEvents = [];

foreach ($allEvents as $event) {
    // No filters applied, should add all events
    $filteredEvents[] = $event;
}

echo "<h2>Étape 2: Après filtrage</h2>";
echo "<p>Nombre d'événements après filtrage: <strong>" . count($filteredEvents) . "</strong></p>";

// Pagination
$page = 1;
$limit = 50;
$offset = ($page - 1) * $limit;
$totalEvents = count($filteredEvents);
$events = array_slice($filteredEvents, $offset, $limit);

echo "<h2>Étape 3: Après pagination</h2>";
echo "<p>Total événements: <strong>$totalEvents</strong></p>";
echo "<p>Page: $page, Limite: $limit, Offset: $offset</p>";
echo "<p>Événements à afficher: <strong>" . count($events) . "</strong></p>";

if (count($events) > 0) {
    echo "<h3>Événements qui devraient s'afficher:</h3>";
    echo "<ol>";
    foreach ($events as $event) {
        echo "<li>" . htmlspecialchars($event['title'] ?? 'Sans titre') . " - " . 
             htmlspecialchars($event['city'] ?? 'N/A') . "</li>";
    }
    echo "</ol>";
    
    // Test encoding
    echo "<h3>Test d'encodage du premier événement:</h3>";
    $testEvent = $events[0];
    try {
        $encoded = base64_encode(json_encode($testEvent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        echo "<p style='color: green;'>✓ Encodage réussi</p>";
        echo "<p>Lien: <a href='event-details-hybrid.php?data=$encoded'>Voir détails</a></p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Erreur d'encodage: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>PROBLÈME: Aucun événement à afficher!</p>";
}

echo "</body></html>";
?>