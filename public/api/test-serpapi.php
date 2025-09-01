<?php
/**
 * Test de l'API SerpAPI pour Google Events
 */

header('Content-Type: application/json');

// Clé API SerpAPI
$apiKey = 'b56aa6ec92f9f569f50f671e5133d46d5131c74c260086c37f5222bf489f2d4d';

// Test 1: Vérifier le compte
echo "Test de la clé SerpAPI...\n\n";

$accountUrl = 'https://serpapi.com/account.json?api_key=' . $apiKey;
$response = @file_get_contents($accountUrl);

if ($response) {
    $account = json_decode($response, true);
    echo "✅ Compte SerpAPI valide!\n";
    echo "- Email: " . ($account['email'] ?? 'N/A') . "\n";
    echo "- Recherches ce mois: " . ($account['this_month_usage'] ?? 0) . "\n";
    echo "- Limite mensuelle: " . ($account['plan_monthly_searches'] ?? 100) . "\n";
    echo "- Recherches restantes: " . (($account['plan_monthly_searches'] ?? 100) - ($account['this_month_usage'] ?? 0)) . "\n\n";
} else {
    echo "❌ Erreur: Impossible de vérifier le compte SerpAPI\n\n";
}

// Test 2: Recherche d'événements
echo "Test de recherche d'événements à Paris...\n\n";

$params = [
    'api_key' => $apiKey,
    'engine' => 'google_events',
    'q' => 'événements culturels Paris',
    'hl' => 'fr',
    'gl' => 'fr'
];

$eventsUrl = 'https://serpapi.com/search.json?' . http_build_query($params);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $eventsUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200 && $response) {
    $data = json_decode($response, true);
    
    if (isset($data['events_results'])) {
        echo "✅ Événements trouvés: " . count($data['events_results']) . "\n\n";
        
        // Afficher les 3 premiers événements
        foreach (array_slice($data['events_results'], 0, 3) as $i => $event) {
            echo "Événement " . ($i + 1) . ":\n";
            echo "- Titre: " . ($event['title'] ?? 'N/A') . "\n";
            echo "- Date: " . ($event['date']['when'] ?? 'N/A') . "\n";
            echo "- Lieu: " . (isset($event['venue']['name']) ? $event['venue']['name'] : 'N/A') . "\n";
            echo "- Lien: " . ($event['link'] ?? 'N/A') . "\n\n";
        }
        
        echo "\n✅ L'API fonctionne correctement!\n";
        echo "URL de test: /api/google-events.php\n";
    } else {
        echo "⚠️ Pas d'événements trouvés\n";
        echo "Debug: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
    }
} else {
    echo "❌ Erreur HTTP: $httpCode\n";
    if ($response) {
        $error = json_decode($response, true);
        echo "Message: " . ($error['error'] ?? 'Erreur inconnue') . "\n";
    }
}
?>