<?php
/**
 * Test pour valider que Claude génère les bonnes dates
 */
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/services/ClaudeEventsService.php';

$city = $_GET['city'] ?? 'Paris';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation des dates Claude</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .valid { background: #d4edda; }
        .invalid { background: #f8d7da; }
        .date-check { padding: 10px; margin: 5px 0; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1>🗓️ Validation des dates des événements</h1>
        
        <div class="alert alert-info">
            <strong>Date système actuelle :</strong> <?php echo date('l j F Y H:i:s'); ?><br>
            <strong>Timestamp Unix :</strong> <?php echo time(); ?><br>
            <strong>Timezone :</strong> <?php echo date_default_timezone_get(); ?>
        </div>
        
        <?php
        // Dates de référence
        $today = strtotime('today');
        $nextWeek = strtotime('+7 days');
        $currentMonth = date('n');
        $currentYear = date('Y');
        
        echo "<h3>Plage de dates valides :</h3>";
        echo "<ul>";
        echo "<li>De : " . date('d/m/Y', $today) . "</li>";
        echo "<li>À : " . date('d/m/Y', $nextWeek) . "</li>";
        echo "</ul>";
        
        // Appeler Claude
        echo "<h3>Recherche pour : $city</h3>";
        $events = ClaudeEventsService::searchEvents($city, 12);
        
        if (empty($events)) {
            echo "<div class='alert alert-danger'>Aucun événement trouvé</div>";
        } else {
            echo "<h3>" . count($events) . " événements trouvés :</h3>";
            
            $validCount = 0;
            $invalidCount = 0;
            $invalidDates = [];
            
            foreach ($events as $index => $event) {
                $dateDisplay = $event['date_display'] ?? 'Date non spécifiée';
                $title = $event['title'] ?? 'Sans titre';
                
                // Analyser la date
                $isValid = true;
                $reason = '';
                
                // Vérifications
                if (stripos($dateDisplay, '2024') !== false) {
                    $isValid = false;
                    $reason = "❌ Année 2024 détectée (année passée)";
                } elseif (preg_match('/\b(janvier|février|mars|avril|mai|juin|juillet)\b/i', $dateDisplay) && $currentMonth >= 8) {
                    $isValid = false;
                    $reason = "❌ Mois passé détecté (nous sommes en août)";
                } elseif (stripos($dateDisplay, '18 mai') !== false || stripos($dateDisplay, 'mai 2025') !== false) {
                    $isValid = false;
                    $reason = "❌ Mai 2025 est déjà passé !";
                } elseif (!preg_match('/\b(août|september|août 2025|septembre 2025)\b/i', $dateDisplay) && 
                         !preg_match('/\b(aujourd|ce soir|demain|cette semaine|weekend)\b/i', $dateDisplay)) {
                    $isValid = false;
                    $reason = "⚠️ Date suspecte (ni août ni septembre 2025)";
                }
                
                if ($isValid) {
                    $validCount++;
                    $cssClass = 'valid';
                    $icon = '✅';
                } else {
                    $invalidCount++;
                    $invalidDates[] = $dateDisplay;
                    $cssClass = 'invalid';
                    $icon = '❌';
                }
                
                echo "<div class='date-check $cssClass'>";
                echo "<strong>$icon Event " . ($index + 1) . ":</strong> " . htmlspecialchars($title) . "<br>";
                echo "<strong>Date affichée :</strong> " . htmlspecialchars($dateDisplay) . "<br>";
                if ($reason) {
                    echo "<strong>Problème :</strong> $reason";
                }
                echo "</div>";
            }
            
            // Résumé
            echo "<div class='alert " . ($invalidCount > 0 ? 'alert-warning' : 'alert-success') . " mt-4'>";
            echo "<h4>📊 Résumé de validation :</h4>";
            echo "<ul>";
            echo "<li>✅ Dates valides : $validCount</li>";
            echo "<li>❌ Dates invalides : $invalidCount</li>";
            echo "</ul>";
            
            if ($invalidCount > 0) {
                echo "<strong>Dates problématiques détectées :</strong>";
                echo "<ul>";
                foreach ($invalidDates as $d) {
                    echo "<li>" . htmlspecialchars($d) . "</li>";
                }
                echo "</ul>";
            }
            echo "</div>";
        }
        ?>
        
        <hr>
        <form method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" name="city" class="form-control" 
                       placeholder="Tester une autre ville..." value="<?php echo htmlspecialchars($city); ?>">
                <button class="btn btn-primary" type="submit">Tester</button>
            </div>
        </form>
        
        <a href="discover-claude.php?city=<?php echo urlencode($city); ?>" class="btn btn-success">
            Voir dans Discover Claude
        </a>
    </div>
</body>
</html>