<?php
// Test du service d'événements
require_once __DIR__ . '/services/ImprovedEventsService.php';
require_once __DIR__ . '/services/KnownEventsData.php';

header('Content-Type: text/html; charset=UTF-8');

try {
    $allEvents = ImprovedEventsService::getAllEvents();
    $count = count($allEvents);
    
    echo "<!DOCTYPE html>";
    echo "<html><head><meta charset='UTF-8'><title>Test Discover Events</title></head><body>";
    echo "<h1>Test du Service d'Événements</h1>";
    echo "<p>Nombre total d'événements: <strong>$count</strong></p>";
    
    if ($count > 0) {
        echo "<h2>Premiers événements:</h2>";
        echo "<ul>";
        $max = min(5, $count);
        for ($i = 0; $i < $max; $i++) {
            $event = $allEvents[$i];
            echo "<li>";
            echo "<strong>" . htmlspecialchars($event['title'] ?? 'Sans titre') . "</strong><br>";
            echo "Catégorie: " . htmlspecialchars($event['category'] ?? 'N/A') . "<br>";
            echo "Ville: " . htmlspecialchars($event['city'] ?? 'N/A') . "<br>";
            echo "Prix: " . htmlspecialchars($event['price'] ?? '0') . " €<br>";
            echo "</li>";
        }
        echo "</ul>";
        
        // Test d'encodage
        echo "<h2>Test d'encodage Base64:</h2>";
        $testEvent = $allEvents[0];
        $encoded = base64_encode(json_encode($testEvent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        echo "<p>Événement encodé avec succès</p>";
        echo "<a href='event-details-hybrid.php?data=$encoded'>Tester le lien vers event-details-hybrid.php</a>";
        
    } else {
        echo "<p style='color: red;'>AUCUN ÉVÉNEMENT TROUVÉ!</p>";
    }
    
    echo "</body></html>";
    
} catch (Exception $e) {
    echo "<h1 style='color: red;'>ERREUR!</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>