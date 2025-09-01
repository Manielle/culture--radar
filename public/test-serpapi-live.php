<?php
require_once 'config.php';
require_once 'includes/env.php';

// Récupérer la clé
$serpApiKey = Env::get('SERPAPI_KEY');

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Test SerpAPI Live</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 50px; background: #f8f9fa; }
        .result-card { background: white; padding: 20px; border-radius: 10px; margin: 20px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: green; }
        .error { color: red; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Test SerpAPI Live</h1>
        
        <div class="result-card">
            <h3>Configuration</h3>
            <?php if ($serpApiKey && $serpApiKey !== 'your_serpapi_key_here'): ?>
                <p class="success">✅ Clé SerpAPI trouvée : <?php echo substr($serpApiKey, 0, 10); ?>...<?php echo substr($serpApiKey, -5); ?></p>
            <?php else: ?>
                <p class="error">❌ Clé SerpAPI manquante</p>
            <?php endif; ?>
        </div>
        
        <?php if ($serpApiKey && $serpApiKey !== 'your_serpapi_key_here'): ?>
        <div class="result-card">
            <h3>Test de recherche d'événements réels</h3>
            <?php
            // Faire une vraie recherche
            $query = urlencode("concerts Paris septembre 2025");
            $url = "https://serpapi.com/search.json?"
                 . "engine=google"
                 . "&q=$query"
                 . "&location=Paris,France"
                 . "&hl=fr"
                 . "&gl=fr"
                 . "&num=10"
                 . "&api_key=" . $serpApiKey;
            
            echo "<p>Recherche : <code>concerts Paris septembre 2025</code></p>";
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($error) {
                echo "<p class='error'>Erreur cURL : $error</p>";
            } elseif ($httpCode !== 200) {
                echo "<p class='error'>Erreur HTTP : Code $httpCode</p>";
                echo "<pre>" . htmlspecialchars($response) . "</pre>";
            } else {
                $data = json_decode($response, true);
                
                if (isset($data['error'])) {
                    echo "<p class='error'>Erreur API : " . $data['error'] . "</p>";
                } else {
                    echo "<p class='success'>✅ Recherche réussie !</p>";
                    
                    // Afficher les résultats
                    if (isset($data['organic_results'])) {
                        echo "<h4>Résultats trouvés :</h4>";
                        echo "<ul>";
                        foreach (array_slice($data['organic_results'], 0, 5) as $result) {
                            echo "<li>";
                            echo "<strong>" . htmlspecialchars($result['title'] ?? '') . "</strong><br>";
                            echo "<small>" . htmlspecialchars($result['snippet'] ?? '') . "</small><br>";
                            echo "<a href='" . htmlspecialchars($result['link'] ?? '#') . "' target='_blank'>Voir sur le site</a>";
                            echo "</li><br>";
                        }
                        echo "</ul>";
                    }
                    
                    // Afficher les événements Google si présents
                    if (isset($data['events_results'])) {
                        echo "<h4>Événements Google trouvés :</h4>";
                        echo "<ul>";
                        foreach ($data['events_results'] as $event) {
                            echo "<li>";
                            echo "<strong>" . htmlspecialchars($event['title'] ?? '') . "</strong><br>";
                            echo "Date : " . htmlspecialchars($event['date']['when'] ?? '') . "<br>";
                            echo "Lieu : " . htmlspecialchars($event['venue']['name'] ?? '') . "<br>";
                            echo "</li><br>";
                        }
                        echo "</ul>";
                    }
                    
                    // Info sur l'utilisation de l'API
                    if (isset($data['search_metadata']['google_events_url'])) {
                        echo "<p>URL Google Events : <a href='" . $data['search_metadata']['google_events_url'] . "' target='_blank'>Voir sur Google</a></p>";
                    }
                }
            }
            ?>
        </div>
        
        <div class="result-card">
            <h3>Utiliser les vrais événements</h3>
            <a href="discover-real.php" class="btn btn-primary btn-lg">
                🎭 Voir la page Discover avec vrais événements
            </a>
            
            <div class="mt-3">
                <h5>Pages disponibles :</h5>
                <ul>
                    <li><a href="discover.php">discover.php</a> - Événements de démo (KnownEventsData)</li>
                    <li><a href="discover-real.php">discover-real.php</a> - VRAIS événements (SerpAPI)</li>
                </ul>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>