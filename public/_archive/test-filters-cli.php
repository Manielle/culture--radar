<?php
/**
 * Test CLI pour vérifier le fonctionnement des filtres
 */
require_once __DIR__ . '/services/ImprovedEventsService.php';

echo "\n=== TEST DES FILTRES D'ÉVÉNEMENTS ===\n\n";

// Test 1: Musique ce weekend
echo "1. Test: Musique ce weekend à Paris\n";
$params1 = [
    'city' => 'Paris',
    'category' => 'musique',
    'period' => 'weekend',
    'limit' => 5
];
$events1 = ImprovedEventsService::findRealEventsWithFilters($params1);
echo "   Résultats: " . count($events1) . " événements trouvés\n";
if (!empty($events1)) {
    foreach ($events1 as $event) {
        echo "   - " . $event['title'] . " (" . $event['category'] . ")\n";
    }
}
echo "\n";

// Test 2: Expositions cette semaine
echo "2. Test: Expositions cette semaine à Paris\n";
$params2 = [
    'city' => 'Paris',
    'category' => 'exposition',
    'period' => 'week',
    'limit' => 5
];
$events2 = ImprovedEventsService::findRealEventsWithFilters($params2);
echo "   Résultats: " . count($events2) . " événements trouvés\n";
if (!empty($events2)) {
    foreach ($events2 as $event) {
        echo "   - " . $event['title'] . " (" . $event['category'] . ")\n";
    }
}
echo "\n";

// Test 3: Théâtre aujourd'hui
echo "3. Test: Théâtre aujourd'hui à Paris\n";
$params3 = [
    'city' => 'Paris',
    'category' => 'theatre',
    'period' => 'today',
    'limit' => 5
];
$events3 = ImprovedEventsService::findRealEventsWithFilters($params3);
echo "   Résultats: " . count($events3) . " événements trouvés\n";
if (!empty($events3)) {
    foreach ($events3 as $event) {
        echo "   - " . $event['title'] . " (" . $event['category'] . ")\n";
    }
}
echo "\n";

// Test 4: Tous les événements demain
echo "4. Test: Tous les événements demain à Paris\n";
$params4 = [
    'city' => 'Paris',
    'category' => 'all',
    'period' => 'tomorrow',
    'limit' => 5
];
$events4 = ImprovedEventsService::findRealEventsWithFilters($params4);
echo "   Résultats: " . count($events4) . " événements trouvés\n";
if (!empty($events4)) {
    foreach ($events4 as $event) {
        echo "   - " . $event['title'] . " (" . $event['date_display'] . ")\n";
    }
}

echo "\n=== FIN DES TESTS ===\n";
?>