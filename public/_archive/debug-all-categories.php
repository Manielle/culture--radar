<?php
/**
 * Debug du mode "Toutes catégories"
 */
require_once __DIR__ . '/services/ImprovedEventsService.php';

echo "\n=== DEBUG MODE TOUTES CATÉGORIES ===\n\n";

// Test avec paramètres "all"
$params = [
    'city' => 'Paris',
    'category' => 'all',
    'period' => 'all',
    'limit' => 30
];

echo "Paramètres de recherche:\n";
print_r($params);
echo "\n";

// Lancer la recherche
$startTime = microtime(true);
$events = ImprovedEventsService::findRealEventsWithFilters($params);
$duration = round(microtime(true) - $startTime, 2);

echo "Résultats:\n";
echo "- Nombre total d'événements: " . count($events) . "\n";
echo "- Durée de recherche: {$duration}s\n\n";

// Analyser les catégories
$categories = [];
foreach ($events as $event) {
    $cat = $event['category'] ?? 'Non défini';
    if (!isset($categories[$cat])) {
        $categories[$cat] = 0;
    }
    $categories[$cat]++;
}

echo "Répartition par catégorie:\n";
foreach ($categories as $cat => $count) {
    echo "  - $cat: $count événements\n";
}

echo "\nListe des événements:\n";
foreach ($events as $i => $event) {
    echo ($i+1) . ". " . ($event['title'] ?? 'Sans titre');
    echo " [" . ($event['category'] ?? 'N/A') . "]";
    echo " - " . ($event['date_display'] ?? 'Date N/A');
    echo "\n";
}

echo "\n=== FIN DEBUG ===\n";
?>