<?php
/**
 * Test spécifique du format de requête Google Events
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
    <title>Test Format Google Events</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h1>🎭 Test Format Requête Google Events</h1>
    
    <form method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($city); ?>" placeholder="Ville">
            <button class="btn btn-primary" type="submit">Tester</button>
        </div>
    </form>
    
    <?php
    // Test avec le format correct selon la documentation SerpAPI
    $query = "Events in $city";
    echo "<h2>Format de requête: \"$query\"</h2>";
    
    $url = "https://serpapi.com/search.json?"
         . "engine=google_events"
         . "&q=" . urlencode($query)
         . "&hl=fr"
         . "&gl=fr"
         . "&htichips=date:week"  // Cette semaine
         . "&api_key=$apiKey";
    
    echo "<p><strong>URL complète:</strong><br><code>" . htmlspecialchars($url) . "</code></p>";
    
    // Faire l'appel
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "<p><strong>Code HTTP:</strong> $httpCode</p>";
    
    if ($error) {
        echo "<div class='alert alert-danger'>Erreur cURL: $error</div>";
    } else {
        $data = json_decode($response, true);
        
        if (isset($data['error'])) {
            echo "<div class='alert alert-danger'>Erreur API: " . $data['error'] . "</div>";
        }
        
        if (isset($data['events_results']) && count($data['events_results']) > 0) {
            echo "<div class='alert alert-success'>✅ <strong>" . count($data['events_results']) . " événements trouvés!</strong></div>";
            
            echo "<h3>Événements cette semaine:</h3>";
            echo "<div class='row'>";
            
            foreach ($data['events_results'] as $i => $event) {
                echo "<div class='col-md-6 mb-3'>";
                echo "<div class='card'>";
                
                // Image si disponible
                if (!empty($event['image'])) {
                    echo "<img src='" . htmlspecialchars($event['image']) . "' class='card-img-top' style='height: 150px; object-fit: cover;'>";
                }
                
                echo "<div class='card-body'>";
                echo "<h5 class='card-title'>" . htmlspecialchars($event['title'] ?? 'Sans titre') . "</h5>";
                
                // Date
                if (isset($event['date']['when'])) {
                    echo "<p class='text-muted'>📅 " . htmlspecialchars($event['date']['when']) . "</p>";
                } elseif (isset($event['date']['start_date'])) {
                    echo "<p class='text-muted'>📅 " . htmlspecialchars($event['date']['start_date']) . "</p>";
                }
                
                // Lieu
                if (isset($event['address']) && is_array($event['address'])) {
                    echo "<p class='text-muted'>📍 " . htmlspecialchars(implode(', ', $event['address'])) . "</p>";
                } elseif (isset($event['venue']['name'])) {
                    echo "<p class='text-muted'>📍 " . htmlspecialchars($event['venue']['name']) . "</p>";
                }
                
                // Description
                if (!empty($event['description'])) {
                    echo "<p class='card-text'>" . htmlspecialchars(substr($event['description'], 0, 150)) . "...</p>";
                }
                
                // Lien
                if (!empty($event['link'])) {
                    echo "<a href='" . htmlspecialchars($event['link']) . "' target='_blank' class='btn btn-sm btn-primary'>Plus d'infos</a>";
                }
                
                echo "</div>";
                echo "</div>";
                echo "</div>";
                
                if ($i >= 5) break; // Limiter à 6 événements pour l'affichage
            }
            
            echo "</div>";
            
            // Debug: afficher la structure d'un événement
            echo "<details class='mt-4'>";
            echo "<summary>Structure d'un événement (debug)</summary>";
            echo "<pre class='bg-light p-3'>";
            print_r($data['events_results'][0] ?? []);
            echo "</pre>";
            echo "</details>";
            
        } else {
            echo "<div class='alert alert-warning'>⚠️ Aucun événement trouvé avec ce format</div>";
            
            // Essayer avec un format alternatif
            echo "<h3>Test format alternatif:</h3>";
            $altQuery = "événements $city";
            echo "<p>Requête alternative: \"$altQuery\"</p>";
            
            $altUrl = "https://serpapi.com/search.json?"
                    . "engine=google_events"
                    . "&q=" . urlencode($altQuery)
                    . "&hl=fr"
                    . "&gl=fr"
                    . "&api_key=$apiKey";
            
            $ch2 = curl_init($altUrl);
            curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch2, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
            
            $response2 = curl_exec($ch2);
            curl_close($ch2);
            
            $data2 = json_decode($response2, true);
            
            if (isset($data2['events_results']) && count($data2['events_results']) > 0) {
                echo "<div class='alert alert-info'>Le format alternatif a trouvé " . count($data2['events_results']) . " événements</div>";
            }
        }
        
        // Afficher les metadata
        if (isset($data['search_metadata'])) {
            echo "<h4>Métadonnées de recherche:</h4>";
            echo "<ul>";
            echo "<li>Status: " . ($data['search_metadata']['status'] ?? 'N/A') . "</li>";
            echo "<li>Temps total: " . ($data['search_metadata']['total_time_taken'] ?? 'N/A') . "s</li>";
            echo "<li>Moteur: " . ($data['search_metadata']['google_events_url'] ?? 'N/A') . "</li>";
            echo "</ul>";
        }
    }
    ?>
    
    <hr>
    <h3>Formats testés:</h3>
    <ul>
        <li><strong>"Events in Paris"</strong> - Format recommandé par la documentation</li>
        <li><strong>"événements Paris"</strong> - Format alternatif en français</li>
    </ul>
    
    <h3>Paramètres utilisés:</h3>
    <ul>
        <li><code>engine=google_events</code> - Utilise l'API Google Events</li>
        <li><code>htichips=date:week</code> - Filtre pour cette semaine</li>
        <li><code>hl=fr</code> - Langue française</li>
        <li><code>gl=fr</code> - Localisation France</li>
    </ul>
</div>
</body>
</html>