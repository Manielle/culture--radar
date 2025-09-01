<?php
// Créer un événement de test
$event = [
    'title' => 'Jazz à la Villette 2025',
    'description' => 'Le festival Jazz à la Villette revient du 28 août au 7 septembre 2025, avec une programmation où se côtoient Great Black Music, jazz, hip-hop, soul et funk. Un rendez-vous incontournable pour les amateurs de musique.',
    'category' => 'Musique',
    'venue_name' => 'Parc de la Villette',
    'address' => '211 Avenue Jean Jaurès, 75019 Paris',
    'city' => 'Paris',
    'date_display' => '28 août - 7 septembre 2025',
    'is_free' => false,
    'price' => 25,
    'source_url' => 'https://jazzalavillette.com/',
    'image' => 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=800'
];

$eventData = base64_encode(json_encode($event));
$detailUrl = '/event-details-hybrid.php?data=' . $eventData;

echo "<!DOCTYPE html><html><body>";
echo "<h1>Test Lien Détail</h1>";
echo "<p>Cliquez sur le lien pour voir la page de détails :</p>";
echo "<a href='$detailUrl' target='_blank'>Voir les détails de l'événement</a>";
echo "<br><br>";
echo "<p>URL générée :</p>";
echo "<code>" . htmlspecialchars($detailUrl) . "</code>";
echo "</body></html>";
?>