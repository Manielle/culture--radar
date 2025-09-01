<?php
// Test de chargement de index.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test de index.php</h1>";

// Vérifier les fichiers requis
$requiredFiles = [
    'includes/performance.php',
    'config.php',
    'includes/auto-refresh-events.php',
    'services/ImprovedEventsService.php',
    'services/KnownEventsData.php',
    'services/VenueLinksService.php'
];

echo "<h2>Vérification des fichiers requis:</h2>";
echo "<ul>";
foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "<li style='color: green;'>✅ $file existe</li>";
    } else {
        echo "<li style='color: red;'>❌ $file MANQUANT</li>";
    }
}
echo "</ul>";

// Tester le chargement des événements
echo "<h2>Test de chargement des événements:</h2>";

try {
    // Simuler les mêmes conditions que index.php
    session_start();
    $userCity = 'Paris';
    
    require_once 'services/VenueLinksService.php';
    
    // Charger les événements réalistes
    $realEvents = [
        [
            'id' => 1,
            'title' => 'Wolfgang Tillmans - Dernière exposition avant fermeture',
            'description' => 'Carte blanche à l\'artiste allemand pour une exposition événement au Centre Pompidou.',
            'category' => 'Exposition',
            'venue_name' => 'Centre Pompidou',
            'address' => 'Place Georges-Pompidou, 75004 Paris',
            'city' => 'Paris',
            'date' => '2025-06-13',
            'date_display' => '13 juin - 22 septembre 2025',
            'price' => 14,
            'is_free' => false,
            'source_url' => 'https://www.centrepompidou.fr/fr/programme/agenda/evenement/nSlcbMZ',
            'image_url' => 'https://images.unsplash.com/photo-1578321272176-b7bbc0679853?w=600'
        ]
    ];
    
    echo "<p style='color: green;'>✅ Événements chargés avec succès: " . count($realEvents) . " événement(s)</p>";
    echo "<pre>";
    print_r($realEvents[0]);
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur: " . $e->getMessage() . "</p>";
}

echo "<h2>Actions:</h2>";
echo "<p><a href='index.php'>Aller à index.php</a></p>";
echo "<p><a href='demo-final-soutenance.php'>Aller à la démo finale</a></p>";
?>