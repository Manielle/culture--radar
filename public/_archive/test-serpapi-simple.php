<?php
/**
 * Test simple de SerpAPI avec différents formats
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

$apiKey = 'b56aa6ec92f9f569f50f671e5133d46d5131c74c260086c37f5222bf489f2d4d';
$city = $_GET['city'] ?? 'Paris';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Test SerpAPI Simple</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h1>🔍 Test Simple SerpAPI</h1>
    
    <form method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($city); ?>" placeholder="Ville">
            <button class="btn btn-primary" type="submit">Tester</button>
        </div>
    </form>
    
    <?php
    // Test 1: Juste le nom de la ville
    echo "<h2>1. Query simple: \"$city\"</h2>";
    $url1 = "https://serpapi.com/search.json?"
          . "engine=google_events"
          . "&q=" . urlencode($city)
          . "&hl=fr"
          . "&gl=fr"
          . "&api_key=$apiKey";
    
    $ch = curl_init($url1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response1 = curl_exec($ch);
    $httpCode1 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $data1 = json_decode($response1, true);
    
    echo "<p>HTTP Code: $httpCode1</p>";
    
    if (isset($data1['events_results']) && count($data1['events_results']) > 0) {
        echo "<div class='alert alert-success'>✅ " . count($data1['events_results']) . " événements trouvés!</div>";
        
        foreach (array_slice($data1['events_results'], 0, 3) as $event) {
            echo "<div class='card mb-2'>";
            echo "<div class='card-body'>";
            echo "<h6>" . htmlspecialchars($event['title'] ?? '') . "</h6>";
            if (isset($event['date']['when'])) {
                echo "<p>📅 " . htmlspecialchars($event['date']['when']) . "</p>";
            }
            if (isset($event['address']) && is_array($event['address'])) {
                echo "<p>📍 " . htmlspecialchars(implode(', ', $event['address'])) . "</p>";
            }
            echo "</div></div>";
        }
    } else {
        echo "<div class='alert alert-warning'>Aucun événement trouvé</div>";
    }
    
    // Test 2: Avec location parameter
    echo "<hr><h2>2. Avec paramètre location</h2>";
    $url2 = "https://serpapi.com/search.json?"
          . "engine=google_events"
          . "&q=" . urlencode($city)
          . "&location=" . urlencode("$city, France")
          . "&hl=fr"
          . "&gl=fr"
          . "&api_key=$apiKey";
    
    $ch = curl_init($url2);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response2 = curl_exec($ch);
    curl_close($ch);
    
    $data2 = json_decode($response2, true);
    
    if (isset($data2['events_results']) && count($data2['events_results']) > 0) {
        echo "<div class='alert alert-success'>✅ " . count($data2['events_results']) . " événements avec location</div>";
    } else {
        echo "<div class='alert alert-warning'>Pas d'événements avec location</div>";
    }
    
    // Test 3: Recherche Google normale comme fallback
    echo "<hr><h2>3. Fallback: Google Search</h2>";
    $query = "concerts expositions festivals $city août septembre 2025";
    $url3 = "https://serpapi.com/search.json?"
          . "engine=google"
          . "&q=" . urlencode($query)
          . "&location=" . urlencode("$city, France")
          . "&hl=fr"
          . "&gl=fr"
          . "&num=10"
          . "&api_key=$apiKey";
    
    $ch = curl_init($url3);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response3 = curl_exec($ch);
    curl_close($ch);
    
    $data3 = json_decode($response3, true);
    
    if (isset($data3['organic_results'])) {
        echo "<div class='alert alert-info'>✅ " . count($data3['organic_results']) . " résultats Google Search</div>";
        
        $eventCount = 0;
        foreach ($data3['organic_results'] as $result) {
            $title = strtolower($result['title'] ?? '');
            $snippet = strtolower($result['snippet'] ?? '');
            
            if (strpos($title . $snippet, 'concert') !== false ||
                strpos($title . $snippet, 'exposition') !== false ||
                strpos($title . $snippet, 'festival') !== false ||
                strpos($title . $snippet, 'spectacle') !== false ||
                strpos($title . $snippet, 'théâtre') !== false) {
                
                echo "<div class='card mb-2'>";
                echo "<div class='card-body'>";
                echo "<h6>" . htmlspecialchars($result['title']) . "</h6>";
                echo "<p class='small'>" . htmlspecialchars($result['snippet'] ?? '') . "</p>";
                echo "<a href='" . htmlspecialchars($result['link']) . "' target='_blank'>Voir</a>";
                echo "</div></div>";
                
                $eventCount++;
                if ($eventCount >= 3) break;
            }
        }
    }
    ?>
    
    <hr>
    <h3>Résumé:</h3>
    <ul>
        <li>Google Events API semble limité pour les villes françaises</li>
        <li>Le fallback Google Search trouve des résultats pertinents</li>
        <li>Claude peut enrichir ces résultats bruts</li>
    </ul>
</div>
</body>
</html>