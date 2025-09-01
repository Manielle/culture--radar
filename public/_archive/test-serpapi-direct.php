<?php
/**
 * Test direct de SerpAPI pour diagnostiquer
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
    <title>Test Direct SerpAPI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h1>🔍 Test Direct SerpAPI</h1>
    
    <form method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($city); ?>" placeholder="Ville">
            <button class="btn btn-primary" type="submit">Tester</button>
        </div>
    </form>
    
    <?php
    echo "<h2>1. Test Google Events API</h2>";
    
    // Test 1: Google Events
    $query = urlencode("événements culturels $city");
    $url1 = "https://serpapi.com/search.json?"
          . "engine=google_events"
          . "&q=$query"
          . "&location=$city,France"
          . "&hl=fr"
          . "&gl=fr"
          . "&api_key=$apiKey";
    
    echo "<p><strong>URL:</strong> " . htmlspecialchars($url1) . "</p>";
    
    $ch = curl_init($url1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response1 = curl_exec($ch);
    $httpCode1 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error1 = curl_error($ch);
    curl_close($ch);
    
    echo "<p><strong>HTTP Code:</strong> $httpCode1</p>";
    
    if ($error1) {
        echo "<div class='alert alert-danger'>Erreur cURL: $error1</div>";
    } else {
        $data1 = json_decode($response1, true);
        
        if (isset($data1['error'])) {
            echo "<div class='alert alert-danger'>Erreur API: " . $data1['error'] . "</div>";
        }
        
        if (isset($data1['events_results'])) {
            echo "<div class='alert alert-success'>✅ " . count($data1['events_results']) . " événements trouvés!</div>";
            
            echo "<h4>Événements Google Events:</h4>";
            foreach (array_slice($data1['events_results'], 0, 5) as $event) {
                echo "<div class='card mb-2'>";
                echo "<div class='card-body'>";
                echo "<h5>" . htmlspecialchars($event['title'] ?? '') . "</h5>";
                echo "<p>Date: " . htmlspecialchars($event['date']['when'] ?? 'Non spécifié') . "</p>";
                echo "<p>Lieu: " . htmlspecialchars($event['address'][0] ?? $event['venue']['name'] ?? 'Non spécifié') . "</p>";
                if (isset($event['description'])) {
                    echo "<p>" . htmlspecialchars(substr($event['description'], 0, 200)) . "...</p>";
                }
                echo "</div></div>";
            }
        } else {
            echo "<div class='alert alert-warning'>⚠️ Pas de résultats Google Events</div>";
            echo "<details><summary>Réponse brute</summary><pre>" . htmlspecialchars(substr($response1, 0, 1000)) . "</pre></details>";
        }
    }
    
    // Test 2: Google Search normal
    echo "<hr><h2>2. Test Google Search (fallback)</h2>";
    
    $query2 = urlencode("événements culturels $city cette semaine 2025 concert exposition");
    $url2 = "https://serpapi.com/search.json?"
          . "engine=google"
          . "&q=$query2"
          . "&location=$city,France"
          . "&hl=fr"
          . "&gl=fr"
          . "&num=20"
          . "&api_key=$apiKey";
    
    echo "<p><strong>Query:</strong> événements culturels $city cette semaine 2025 concert exposition</p>";
    
    $ch = curl_init($url2);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response2 = curl_exec($ch);
    $httpCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "<p><strong>HTTP Code:</strong> $httpCode2</p>";
    
    $data2 = json_decode($response2, true);
    
    if (isset($data2['organic_results'])) {
        echo "<div class='alert alert-success'>✅ " . count($data2['organic_results']) . " résultats Google</div>";
        
        echo "<h4>Résultats pertinents:</h4>";
        foreach ($data2['organic_results'] as $result) {
            if (stripos($result['title'], 'événement') !== false || 
                stripos($result['title'], 'concert') !== false ||
                stripos($result['title'], 'exposition') !== false ||
                stripos($result['title'], 'festival') !== false ||
                stripos($result['title'], 'spectacle') !== false ||
                stripos($result['snippet'] ?? '', 'août') !== false ||
                stripos($result['snippet'] ?? '', 'septembre') !== false) {
                
                echo "<div class='card mb-2'>";
                echo "<div class='card-body'>";
                echo "<h6>" . htmlspecialchars($result['title']) . "</h6>";
                echo "<p class='small'>" . htmlspecialchars($result['snippet'] ?? '') . "</p>";
                echo "<a href='" . htmlspecialchars($result['link']) . "' target='_blank' class='btn btn-sm btn-outline-primary'>Voir</a>";
                echo "</div></div>";
            }
        }
    } else {
        echo "<div class='alert alert-warning'>⚠️ Pas de résultats Google Search</div>";
    }
    
    // Test 3: Vérifier le compte
    echo "<hr><h2>3. Vérification du compte SerpAPI</h2>";
    
    $accountUrl = "https://serpapi.com/account.json?api_key=$apiKey";
    $ch = curl_init($accountUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $accountResponse = curl_exec($ch);
    $accountCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($accountCode === 200) {
        $account = json_decode($accountResponse, true);
        echo "<div class='alert alert-info'>";
        echo "<p><strong>Plan:</strong> " . ($account['plan_name'] ?? 'Non spécifié') . "</p>";
        echo "<p><strong>Recherches ce mois:</strong> " . ($account['this_month_searches'] ?? 0) . "</p>";
        echo "<p><strong>Limite mensuelle:</strong> " . ($account['plan_searches_per_month'] ?? 'Illimité') . "</p>";
        echo "</div>";
    } else {
        echo "<div class='alert alert-warning'>Impossible de vérifier le compte (Code: $accountCode)</div>";
    }
    ?>
    
    <hr>
    <h3>Diagnostic:</h3>
    <ul>
        <li>Si Google Events ne fonctionne pas → On utilisera Google Search normal</li>
        <li>Si aucun ne fonctionne → Vérifier la clé API ou la limite mensuelle</li>
        <li>Si tout fonctionne ici mais pas dans le service → Problème de code PHP</li>
    </ul>
</div>
</body>
</html>