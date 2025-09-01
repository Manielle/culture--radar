<?php
/**
 * Test Claude API pour vérifier les dates des événements
 */
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Charger la configuration
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/services/ClaudeEventsService.php';

$city = $_GET['city'] ?? 'Paris';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Claude - Événements du jour</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
        }
        .date-badge {
            background: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.9em;
        }
        .today { background: #dc3545; }
        .this-week { background: #ffc107; color: #333; }
        .weekend { background: #17a2b8; }
        .event-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Test Claude - Événements temporels</h1>
        
        <div class="alert alert-info">
            <strong>Date actuelle :</strong> <?php echo date('l j F Y'); ?> (<?php echo date('d/m/Y'); ?>)<br>
            <strong>Ville :</strong> <?php echo htmlspecialchars($city); ?>
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
        
        // Appeler Claude
        $startTime = microtime(true);
        $events = ClaudeEventsService::searchEvents($city, 12);
        $duration = round(microtime(true) - $startTime, 2);
        
        echo "<div class='alert alert-success'>";
        echo "<strong>✅ Réponse reçue en {$duration}s</strong><br>";
        echo count($events) . " événements trouvés";
        echo "</div>";
        
        if (!empty($events)) {
            // Analyser les dates
            $todayCount = 0;
            $weekCount = 0;
            $weekendCount = 0;
            
            echo "<h3>Événements trouvés :</h3>";
            echo "<div class='row'>";
            
            foreach ($events as $event) {
                $dateDisplay = $event['date_display'] ?? '';
                
                // Classifier par période
                $badge = '';
                if (stripos($dateDisplay, 'aujourd') !== false || stripos($dateDisplay, 'ce soir') !== false) {
                    $badge = '<span class="date-badge today">Aujourd\'hui</span>';
                    $todayCount++;
                } elseif (stripos($dateDisplay, 'samedi') !== false || stripos($dateDisplay, 'dimanche') !== false) {
                    $badge = '<span class="date-badge weekend">Weekend</span>';
                    $weekendCount++;
                } else {
                    $badge = '<span class="date-badge this-week">Cette semaine</span>';
                    $weekCount++;
                }
                
                echo "<div class='col-md-6'>";
                echo "<div class='card event-card'>";
                echo "<div class='card-body'>";
                echo "<h5 class='card-title'>" . htmlspecialchars($event['title'] ?? 'Sans titre') . "</h5>";
                echo "<p class='card-text'>" . htmlspecialchars($event['description'] ?? '') . "</p>";
                echo "<div class='mb-2'>$badge <strong>" . htmlspecialchars($dateDisplay) . "</strong></div>";
                echo "<small class='text-muted'>";
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
            
            // Statistiques
            echo "<div class='alert alert-warning mt-4'>";
            echo "<h4>📊 Répartition temporelle :</h4>";
            echo "<ul>";
            echo "<li><strong>Aujourd'hui :</strong> $todayCount événements</li>";
            echo "<li><strong>Cette semaine :</strong> $weekCount événements</li>";
            echo "<li><strong>Ce weekend :</strong> $weekendCount événements</li>";
            echo "</ul>";
            echo "</div>";
            
            // Debug: voir le prompt exact
            echo "<details class='mt-4'>";
            echo "<summary>🔧 Debug: Prompt envoyé à Claude</summary>";
            echo "<pre class='bg-light p-3'>";
            $today = date('l j F Y');
            echo "Nous sommes le $today. Trouve-moi des événements culturels qui ont lieu:\n";
            echo "- Aujourd'hui\n";
            echo "- Cette semaine\n";
            echo "- Ce weekend prochain\n";
            echo "à $city.\n\n";
            echo "Les dates doivent être réalistes et proches (dans les 7 prochains jours maximum).";
            echo "</pre>";
            echo "</details>";
            
        } else {
            echo "<div class='alert alert-danger'>";
            echo "❌ Aucun événement trouvé";
            echo "</div>";
        }
        ?>
        
        <hr>
        <div class="text-center">
            <a href="discover-claude.php?city=<?php echo urlencode($city); ?>" class="btn btn-success">
                Voir dans Discover Claude
            </a>
        </div>
    </div>
</body>
</html>