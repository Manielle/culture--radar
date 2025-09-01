<?php
require_once __DIR__ . '/services/KnownEventsData.php';

echo "Test de KnownEventsData::getKnownEvents()\n";
echo "==========================================\n\n";

// Test avec ville vide
$events1 = KnownEventsData::getKnownEvents('');
echo "getKnownEvents(''): " . count($events1) . " événements\n";

// Test avec Paris
$events2 = KnownEventsData::getKnownEvents('Paris');
echo "getKnownEvents('Paris'): " . count($events2) . " événements\n";

// Test avec toutes les villes
$events3 = KnownEventsData::getKnownEvents();
echo "getKnownEvents(): " . count($events3) . " événements\n";

if (count($events3) > 0) {
    echo "\nPremier événement:\n";
    echo "Titre: " . $events3[0]['title'] . "\n";
    echo "Ville: " . $events3[0]['city'] . "\n";
    echo "Catégorie: " . $events3[0]['category'] . "\n";
}
?>