<?php
/**
 * Redirection vers la page unifiée event-details.php
 */

// Récupérer les paramètres et rediriger vers la nouvelle page
if (isset($_GET['data'])) {
    header('Location: /event-details.php?data=' . $_GET['data']);
} elseif (isset($_GET['id'])) {
    header('Location: /event-details.php?id=' . $_GET['id']);
} else {
    header('Location: /event-details.php');
}
exit();

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Debug Event Details</title>
    <style>
        body { font-family: monospace; padding: 20px; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ccc; }
        .success { background: #d4edda; }
        .error { background: #f8d7da; }
        pre { background: #f4f4f4; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
<h1>Debug Event Details - Étape par étape</h1>";

// Étape 1: Données reçues
echo "<div class='section'>";
echo "<h2>1. Données reçues dans \$_GET['data']:</h2>";
if (empty($eventData)) {
    echo "<p class='error'>❌ AUCUNE donnée reçue!</p>";
} else {
    echo "<p class='success'>✅ Données reçues (longueur: " . strlen($eventData) . ")</p>";
    echo "<pre>" . htmlspecialchars(substr($eventData, 0, 200)) . "...</pre>";
}
echo "</div>";

// Étape 2: Tentative de décodage base64
echo "<div class='section'>";
echo "<h2>2. Décodage Base64:</h2>";

$attempts = [];

// Tentative 1: Direct
$decoded1 = @base64_decode($eventData);
$attempts[] = [
    'method' => 'Direct base64_decode',
    'success' => $decoded1 !== false,
    'result' => $decoded1
];

// Tentative 2: Avec urldecode
$decoded2 = @base64_decode(urldecode($eventData));
$attempts[] = [
    'method' => 'urldecode puis base64_decode',
    'success' => $decoded2 !== false,
    'result' => $decoded2
];

// Tentative 3: Remplacer espaces par +
$decoded3 = @base64_decode(str_replace(' ', '+', $eventData));
$attempts[] = [
    'method' => 'Remplacer espaces par + puis base64_decode',
    'success' => $decoded3 !== false,
    'result' => $decoded3
];

// Tentative 4: rawurldecode
$decoded4 = @base64_decode(rawurldecode($eventData));
$attempts[] = [
    'method' => 'rawurldecode puis base64_decode',
    'success' => $decoded4 !== false,
    'result' => $decoded4
];

$successfulDecode = false;
$decodedJson = null;

foreach ($attempts as $i => $attempt) {
    echo "<h3>Tentative " . ($i + 1) . ": " . $attempt['method'] . "</h3>";
    if ($attempt['success']) {
        echo "<p class='success'>✅ Décodage réussi</p>";
        echo "<pre>" . htmlspecialchars(substr($attempt['result'], 0, 500)) . "</pre>";
        if (!$successfulDecode) {
            $successfulDecode = true;
            $decodedJson = $attempt['result'];
        }
    } else {
        echo "<p class='error'>❌ Échec</p>";
    }
}
echo "</div>";

// Étape 3: Parsing JSON
echo "<div class='section'>";
echo "<h2>3. Parsing JSON:</h2>";

if ($successfulDecode && $decodedJson) {
    $event = @json_decode($decodedJson, true);
    $jsonError = json_last_error();
    
    if ($event !== null && $jsonError === JSON_ERROR_NONE) {
        echo "<p class='success'>✅ JSON parsé avec succès!</p>";
        echo "<h3>Événement décodé:</h3>";
        echo "<pre>" . json_encode($event, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
        
        echo "<h3>Lien fonctionnel:</h3>";
        echo "<a href='event-details.php?data=" . $eventData . "' style='padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;'>✅ Ouvrir dans event-details.php</a>";
    } else {
        echo "<p class='error'>❌ Erreur JSON: " . json_last_error_msg() . "</p>";
        echo "<p>JSON Error Code: " . $jsonError . "</p>";
        echo "<h3>String à parser:</h3>";
        echo "<pre>" . htmlspecialchars($decodedJson) . "</pre>";
    }
} else {
    echo "<p class='error'>❌ Pas de données à parser</p>";
}
echo "</div>";

// Étape 4: Test avec un événement de test
echo "<div class='section'>";
echo "<h2>4. Créer un événement de test:</h2>";

$testEvent = [
    'id' => 'debug_test',
    'title' => 'Test Debug éèà',
    'category' => 'Test',
    'venue_name' => 'Lieu Test',
    'city' => 'Paris',
    'start_date' => '2025-08-30 20:00:00',
    'price' => 10
];

$testEncoded = base64_encode(json_encode($testEvent, JSON_UNESCAPED_UNICODE));
echo "<p>Événement de test encodé:</p>";
echo "<pre>" . $testEncoded . "</pre>";
echo "<a href='debug-event-details.php?data=" . $testEncoded . "' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px;'>Tester ce debug</a>";
echo "<a href='event-details.php?data=" . $testEncoded . "' style='padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;'>Ouvrir dans event-details.php</a>";
echo "</div>";

// Étape 5: Vérifier la configuration PHP
echo "<div class='section'>";
echo "<h2>5. Configuration PHP:</h2>";
echo "<ul>";
echo "<li>PHP Version: " . phpversion() . "</li>";
echo "<li>Default Charset: " . ini_get('default_charset') . "</li>";
echo "<li>JSON disponible: " . (function_exists('json_decode') ? '✅ Oui' : '❌ Non') . "</li>";
echo "<li>Base64 disponible: " . (function_exists('base64_decode') ? '✅ Oui' : '❌ Non') . "</li>";
echo "</ul>";
echo "</div>";

echo "</body></html>";
?>