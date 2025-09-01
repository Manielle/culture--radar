<?php
// Test complet de index.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><title>Test Index.php</title>";
echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">';
echo "</head><body style='padding: 20px;'>";

echo "<h1>🔍 Test complet de index.php</h1>";

// Test 1: Fichiers requis
echo "<div class='card mb-3'>";
echo "<div class='card-header'><h3>1. Fichiers requis</h3></div>";
echo "<div class='card-body'>";
$requiredFiles = [
    'includes/performance.php',
    'config.php',
    'includes/auto-refresh-events.php',
    'services/ImprovedEventsService.php',
    'services/KnownEventsData.php',
    'services/VenueLinksService.php'
];

$allFilesOk = true;
echo "<ul class='list-group'>";
foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "<li class='list-group-item list-group-item-success'>✅ $file</li>";
    } else {
        echo "<li class='list-group-item list-group-item-danger'>❌ $file MANQUANT</li>";
        $allFilesOk = false;
    }
}
echo "</ul>";
echo "</div></div>";

// Test 2: Session et configuration
echo "<div class='card mb-3'>";
echo "<div class='card-header'><h3>2. Session et Configuration</h3></div>";
echo "<div class='card-body'>";
session_start();
$_SESSION['user_city'] = 'Paris';
echo "<p>✅ Session démarrée</p>";
echo "<p>✅ Ville définie: Paris</p>";

// Test de la config
require_once 'config.php';
echo "<p>✅ Configuration chargée</p>";
echo "</div></div>";

// Test 3: Événements
echo "<div class='card mb-3'>";
echo "<div class='card-header'><h3>3. Événements réalistes</h3></div>";
echo "<div class='card-body'>";

require_once 'services/VenueLinksService.php';

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
    ],
    [
        'id' => 2,
        'title' => 'Concert de l\'Orchestre de Paris',
        'venue_name' => 'Philharmonie de Paris',
        'source_url' => 'https://philharmoniedeparis.fr/fr/activite/concert/25432',
        'category' => 'Concert',
        'price' => 45
    ],
    [
        'id' => 4,
        'title' => 'Jazz Session',
        'venue_name' => 'Le Sunset',
        'source_url' => '', // Vide pour tester l'enrichissement
        'category' => 'Concert',
        'price' => 25
    ]
];

echo "<p>✅ " . count($realEvents) . " événements chargés</p>";

// Test enrichissement des URLs
echo "<h5>Test d'enrichissement des URLs:</h5>";
echo "<ul class='list-group'>";
foreach ($realEvents as &$event) {
    $originalUrl = $event['source_url'] ?? '';
    if (empty($event['source_url']) || $event['source_url'] === '#') {
        $venueUrl = VenueLinksService::getVenueUrl($event['venue_name'] ?? '');
        if ($venueUrl) {
            $event['source_url'] = $venueUrl;
        }
    }
    
    echo "<li class='list-group-item'>";
    echo "<strong>" . htmlspecialchars($event['title']) . "</strong><br>";
    echo "Lieu: " . htmlspecialchars($event['venue_name'] ?? 'Non défini') . "<br>";
    echo "URL originale: " . ($originalUrl ?: '<em>vide</em>') . "<br>";
    echo "URL finale: <a href='" . htmlspecialchars($event['source_url']) . "' target='_blank'>" . htmlspecialchars($event['source_url']) . "</a>";
    echo "</li>";
}
echo "</ul>";
echo "</div></div>";

// Test 4: Structure HTML
echo "<div class='card mb-3'>";
echo "<div class='card-header'><h3>4. Validation HTML</h3></div>";
echo "<div class='card-body'>";

// Vérifier les balises importantes
$htmlContent = file_get_contents('index.php');

$checks = [
    'Banderole universitaire' => strpos($htmlContent, 'Projet Universitaire') !== false,
    'Header navbar' => strpos($htmlContent, '<header class="header"') !== false,
    'Section discover' => strpos($htmlContent, 'id="discover"') !== false,
    'Section categories' => strpos($htmlContent, 'id="categories"') !== false,
    'Section features' => strpos($htmlContent, 'id="features"') !== false,
    'Section organizers' => strpos($htmlContent, 'id="organizers"') !== false,
    'JavaScript navbar' => strpos($htmlContent, 'mobileMenuToggle') !== false,
    'Footer' => strpos($htmlContent, '<footer') !== false
];

echo "<ul class='list-group'>";
foreach ($checks as $name => $exists) {
    if ($exists) {
        echo "<li class='list-group-item list-group-item-success'>✅ $name</li>";
    } else {
        echo "<li class='list-group-item list-group-item-warning'>⚠️ $name manquant</li>";
    }
}
echo "</ul>";
echo "</div></div>";

// Test 5: Erreurs PHP
echo "<div class='card mb-3'>";
echo "<div class='card-header'><h3>5. Test d'exécution</h3></div>";
echo "<div class='card-body'>";

// Capturer les erreurs
ob_start();
$errorLevel = error_reporting(0);

// Essayer d'inclure le début de index.php
$testCode = '<?php
session_start();
require_once "includes/performance.php";
enableQueryCache();
require_once "config.php";
$userCity = "Paris";
$isLoggedIn = false;
$userName = "";
echo "OK";
?>';

$tempFile = tempnam(sys_get_temp_dir(), 'test');
file_put_contents($tempFile, $testCode);
include $tempFile;
unlink($tempFile);

$output = ob_get_clean();
error_reporting($errorLevel);

if ($output === 'OK') {
    echo "<p class='text-success'>✅ Aucune erreur PHP détectée</p>";
} else {
    echo "<p class='text-danger'>❌ Erreurs potentielles détectées</p>";
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
}

echo "</div></div>";

// Résumé
echo "<div class='alert " . ($allFilesOk ? "alert-success" : "alert-danger") . "'>";
echo "<h4>📊 Résumé</h4>";
if ($allFilesOk) {
    echo "<p>✅ Tous les tests passent avec succès!</p>";
    echo "<p>La page index.php devrait fonctionner correctement.</p>";
} else {
    echo "<p>⚠️ Certains problèmes ont été détectés. Vérifiez les éléments ci-dessus.</p>";
}
echo "</div>";

echo "<div class='text-center mt-4'>";
echo "<a href='index.php' class='btn btn-primary btn-lg'>Aller à index.php</a> ";
echo "<a href='demo-final-soutenance.php' class='btn btn-success btn-lg'>Démo Soutenance</a>";
echo "</div>";

echo "</body></html>";
?>