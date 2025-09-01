<?php
$_GET['city'] = 'Paris';
ob_start();
require 'api/load-events.php';
$output = ob_get_clean();
$data = json_decode($output, true);

echo "RECHERCHE: PSG vs Angers\n";
echo "========================\n\n";

if ($data && isset($data['events'])) {
    $found = false;
    foreach ($data['events'] as $event) {
        if (stripos($event['title'], 'psg') !== false || 
            stripos($event['title'], 'paris saint') !== false ||
            stripos($event['title'], 'angers') !== false) {
            $found = true;
            echo "✅ TROUVÉ:\n";
            echo "Titre: " . $event['title'] . "\n";
            echo "Catégorie: " . ($event['category'] ?? 'Non défini') . "\n";
            echo "Lieu: " . $event['venue_name'] . "\n";
            echo "Adresse: " . ($event['address'] ?? 'Non spécifiée') . "\n";
            echo "Date: " . $event['date_display'] . "\n";
            echo "Prix: " . ($event['price'] !== null ? $event['price'] . '€' : 'Non défini') . "\n";
            echo "Gratuit: " . ($event['is_free'] ? 'OUI' : 'NON') . "\n";
            echo "Source: " . ($event['source'] ?? 'inconnue') . "\n";
            echo "Description: " . substr($event['description'] ?? '', 0, 300) . "...\n";
            echo "\nLiens billetterie:\n";
            if (isset($event['ticket_links']) && is_array($event['ticket_links'])) {
                foreach ($event['ticket_links'] as $link) {
                    echo "- " . $link['source'] . ": " . $link['link'] . "\n";
                }
            }
            echo str_repeat("-", 80) . "\n\n";
        }
    }
    
    if (!$found) {
        echo "❌ Aucun match PSG vs Angers trouvé dans les événements\n";
        echo "\nPremiers événements disponibles:\n";
        foreach (array_slice($data['events'], 0, 5) as $idx => $event) {
            echo ($idx + 1) . ". " . $event['title'] . " (" . $event['category'] . ")\n";
        }
    }
} else {
    echo "❌ Erreur lors du chargement des événements\n";
}

echo "\nINFO: Paris Saint-Germain vs Angers\n";
echo "====================================\n";
echo "C'est un match de football de Ligue 1 entre:\n";
echo "- PSG (Paris Saint-Germain) - équipe de Paris\n";
echo "- SCO Angers - équipe d'Angers\n";
echo "\nCe type d'événement sportif apparaît car:\n";
echo "1. Google Events détecte les matchs sportifs importants\n";
echo "2. Le PSG joue au Parc des Princes à Paris\n";
echo "3. C'est un événement culturel majeur pour la ville\n";
?>