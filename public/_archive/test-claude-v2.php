<?php
/**
 * Test de la nouvelle version du service Claude avec gestion intelligente des dates
 */
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/services/ClaudeEventsServiceV2.php';

$city = $_GET['city'] ?? 'Paris';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎭 Culture Radar V2 - Test Claude</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1400px;
        }
        .header-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .event-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .event-card:hover {
            transform: translateY(-5px);
        }
        .date-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: bold;
        }
        .today { background: #ff6b6b; color: white; }
        .this-week { background: #4ecdc4; color: white; }
        .weekend { background: #45b7d1; color: white; }
        .free-badge { background: #51cf66; color: white; }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .stat-card {
            background: rgba(255,255,255,0.9);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #667eea;
        }
        .team-credits {
            background: rgba(255,255,255,0.1);
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header-card">
            <h1 class="text-center">🎭 Culture Radar V2</h1>
            <p class="text-center text-muted">Service Claude avec gestion intelligente des dates</p>
            
            <div class="team-credits">
                <strong>Équipe IA School :</strong> Manouk, Safiatou, Gloria, Hidaya
            </div>
            
            <form method="GET" class="mt-4">
                <div class="input-group input-group-lg">
                    <input type="text" name="city" class="form-control" 
                           placeholder="Entrez une ville..." 
                           value="<?php echo htmlspecialchars($city); ?>">
                    <button class="btn btn-primary" type="submit">
                        🔍 Rechercher des événements
                    </button>
                </div>
            </form>
        </div>
        
        <?php
        // Info système
        echo "<div class='alert alert-info'>";
        echo "<strong>📅 Date système :</strong> " . date('l j F Y H:i:s') . "<br>";
        echo "<strong>📍 Ville :</strong> " . htmlspecialchars($city);
        echo "</div>";
        
        // Recherche des événements
        echo "<div class='text-center text-white mb-3'>";
        echo "<h3>⏳ Recherche en cours...</h3>";
        echo "</div>";
        flush();
        
        $startTime = microtime(true);
        $events = ClaudeEventsServiceV2::searchEvents($city, 12);
        $duration = round(microtime(true) - $startTime, 2);
        
        // Statistiques
        $stats = ClaudeEventsServiceV2::getEventStats($events);
        ?>
        
        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total']; ?></div>
                <div>Événements trouvés</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['free']; ?></div>
                <div>Événements gratuits</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['free_percentage']; ?>%</div>
                <div>Taux de gratuité</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $duration; ?>s</div>
                <div>Temps de réponse</div>
            </div>
        </div>
        
        <!-- Événements -->
        <div class="row">
            <?php
            foreach ($events as $index => $event) {
                $dateDisplay = $event['date_display'] ?? 'Date non spécifiée';
                
                // Déterminer le badge de période
                $periodBadge = '';
                if (stripos($dateDisplay, 'aujourd') !== false || stripos($dateDisplay, 'ce soir') !== false) {
                    $periodBadge = '<span class="date-badge today">Aujourd\'hui</span>';
                } elseif (stripos($dateDisplay, 'samedi') !== false || stripos($dateDisplay, 'dimanche') !== false) {
                    $periodBadge = '<span class="date-badge weekend">Weekend</span>';
                } else {
                    $periodBadge = '<span class="date-badge this-week">Cette semaine</span>';
                }
                
                // Badge gratuit
                $freeBadge = ($event['is_free'] ?? false) ? '<span class="date-badge free-badge">GRATUIT</span>' : '';
                
                echo "<div class='col-md-6 col-lg-4'>";
                echo "<div class='event-card'>";
                
                // Titre et badges
                echo "<h5>" . htmlspecialchars($event['title'] ?? 'Sans titre') . "</h5>";
                echo "<div class='mb-2'>$periodBadge $freeBadge</div>";
                
                // Description
                echo "<p class='text-muted'>" . htmlspecialchars($event['description'] ?? '') . "</p>";
                
                // Détails
                echo "<div class='small'>";
                echo "<strong>📅 Date :</strong> " . htmlspecialchars($dateDisplay) . "<br>";
                echo "<strong>📍 Lieu :</strong> " . htmlspecialchars($event['venue_name'] ?? 'Non spécifié') . "<br>";
                echo "<strong>🏷️ Catégorie :</strong> " . htmlspecialchars($event['category'] ?? 'Culture') . "<br>";
                
                if (!($event['is_free'] ?? false)) {
                    echo "<strong>💰 Prix :</strong> " . ($event['price'] ?? '?') . "€<br>";
                }
                
                // Score Culture Radar si disponible
                if (isset($event['culture_radar_score'])) {
                    echo "<strong>⭐ Score CR :</strong> " . $event['culture_radar_score'] . "/100<br>";
                }
                
                echo "</div>";
                
                // Validation de la date
                $currentYear = date('Y');
                $isValidDate = true;
                $dateValidation = '';
                
                if (strpos($dateDisplay, '2024') !== false) {
                    $isValidDate = false;
                    $dateValidation = '<div class="alert alert-danger mt-2">⚠️ Date 2024 détectée (année passée)</div>';
                } elseif (preg_match('/\b(mai|juin|juillet)\b/i', $dateDisplay) && date('n') >= 8) {
                    $isValidDate = false;
                    $dateValidation = '<div class="alert alert-warning mt-2">⚠️ Mois passé détecté</div>';
                }
                
                echo $dateValidation;
                
                echo "</div>";
                echo "</div>";
            }
            
            if (empty($events)) {
                echo "<div class='col-12'>";
                echo "<div class='alert alert-warning'>Aucun événement trouvé pour $city</div>";
                echo "</div>";
            }
            ?>
        </div>
        
        <!-- Debug info -->
        <div class="mt-4">
            <details>
                <summary class="text-white">🔧 Informations de debug</summary>
                <div class="bg-light p-3 rounded mt-2">
                    <h5>Statistiques par catégorie :</h5>
                    <pre><?php print_r($stats['by_category']); ?></pre>
                    
                    <h5>Répartition temporelle :</h5>
                    <pre><?php print_r($stats['by_date']); ?></pre>
                </div>
            </details>
        </div>
        
        <!-- Actions -->
        <div class="text-center mt-4">
            <a href="discover-claude.php?city=<?php echo urlencode($city); ?>" 
               class="btn btn-success btn-lg">
                Utiliser dans Discover Claude
            </a>
        </div>
    </div>
</body>
</html>