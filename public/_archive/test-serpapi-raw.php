<?php
/**
 * Test brut de l'API SerpAPI
 */

$apiKey = 'b56aa6ec92f9f569f50f671e5133d46d5131c74c260086c37f5222bf489f2d4d';

// Test simple avec recherche Google
$query = "événements Paris cette semaine";
$url = "https://serpapi.com/search.json?q=" . urlencode($query) . "&location=Paris,France&hl=fr&gl=fr&api_key=" . $apiKey;

echo "<!DOCTYPE html><html><head><title>Test SerpAPI Raw</title></head><body>";
echo "<h1>Test Raw SerpAPI</h1>";
echo "<p>URL: " . htmlspecialchars($url) . "</p>";

// Faire l'appel
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "<p>HTTP Code: $httpCode</p>";

if ($error) {
    echo "<p style='color:red;'>Erreur cURL: $error</p>";
} else {
    $data = json_decode($response, true);
    
    echo "<h2>Réponse:</h2>";
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    
    if (isset($data['error'])) {
        echo "<p style='color:red;'>Erreur API: " . $data['error'] . "</p>";
    }
    
    if (isset($data['search_metadata'])) {
        echo "<h3>Metadata:</h3>";
        echo "<p>Status: " . $data['search_metadata']['status'] . "</p>";
        echo "<p>Total time: " . $data['search_metadata']['total_time_taken'] . "s</p>";
    }
    
    if (isset($data['organic_results'])) {
        echo "<h3>Résultats trouvés: " . count($data['organic_results']) . "</h3>";
        foreach (array_slice($data['organic_results'], 0, 3) as $result) {
            echo "<div style='border:1px solid #ccc; padding:10px; margin:10px;'>";
            echo "<strong>" . htmlspecialchars($result['title']) . "</strong><br>";
            echo htmlspecialchars($result['snippet'] ?? '') . "<br>";
            echo "<a href='" . htmlspecialchars($result['link']) . "'>" . $result['link'] . "</a>";
            echo "</div>";
        }
    }
}

echo "</body></html>";
?>