<?php
/**
 * Test du service Claude SIMPLE
 */
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/services/ClaudeEventsServiceSimple.php';

$city = $_GET['city'] ?? 'Paris';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Claude Simple</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>🎯 Test Claude Simple - Prompt Court</h1>
        
        <div class="alert alert-info">
            <h4>Prompt utilisé (ULTRA COURT):</h4>
            <pre style="white-space: pre-wrap;">Utilise ta fonction web_search pour trouver des événements culturels ACTUELS à <?php echo $city; ?> cette semaine.

Retourne UNIQUEMENT ce JSON:
{
  "events": [
    {
      "title": "nom",
      "description": "description", 
      "category": "catégorie",
      "venue_name": "lieu",
      "city": "<?php echo $city; ?>",
      "date_display": "date",
      "price": 0,
      "is_free": false
    }
  ]
}

Trouve 10 vrais événements.</pre>
        </div>
        
        <form method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" name="city" class="form-control" 
                       placeholder="Ville..." value="<?php echo htmlspecialchars($city); ?>">
                <button class="btn btn-primary" type="submit">Rechercher</button>
            </div>
        </form>
        
        <?php
        echo "<h2>Recherche en cours...</h2>";
        flush();
        
        $startTime = microtime(true);
        $events = ClaudeEventsServiceSimple::searchEvents($city, 12);
        $duration = round(microtime(true) - $startTime, 2);
        
        echo "<div class='alert alert-success'>";
        echo "Temps de réponse : {$duration}s | ";
        echo count($events) . " événements trouvés";
        echo "</div>";
        
        if (empty($events)) {
            echo "<div class='alert alert-danger'>Aucun événement trouvé</div>";
        } else {
            echo "<div class='row'>";
            foreach ($events as $event) {
                echo "<div class='col-md-6 mb-3'>";
                echo "<div class='card'>";
                echo "<div class='card-body'>";
                echo "<h5 class='card-title'>" . htmlspecialchars($event['title'] ?? '') . "</h5>";
                echo "<p class='card-text'>" . htmlspecialchars($event['description'] ?? '') . "</p>";
                echo "<small class='text-muted'>";
                echo "📅 " . htmlspecialchars($event['date_display'] ?? '') . "<br>";
                echo "📍 " . htmlspecialchars($event['venue_name'] ?? '') . "<br>";
                echo "🏷️ " . htmlspecialchars($event['category'] ?? '') . "<br>";
                if ($event['is_free'] ?? false) {
                    echo "💰 Gratuit";
                } else {
                    echo "💰 " . ($event['price'] ?? '?') . "€";
                }
                echo "</small>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }
            echo "</div>";
        }
        ?>
    </div>
</body>
</html>