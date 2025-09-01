<?php
/**
 * Test du Service Hybride : SerpAPI + Claude
 */
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/services/HybridEventsService.php';

$city = $_GET['city'] ?? 'Paris';

// Vérifier la configuration
$configFile = __DIR__ . '/config/api-keys.php';
$config = require $configFile;
$hasSerpAPI = !empty($config['serpapi']['key']) && $config['serpapi']['key'] !== '';
$hasClaude = !empty($config['claude']['key']);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔄 Service Hybride - SerpAPI + Claude</title>
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
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
        }
        .status-ok { background: #d4edda; color: #155724; }
        .status-error { background: #f8d7da; color: #721c24; }
        .event-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .confidence-badge {
            background: #ffc107;
            color: #000;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 0.8em;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-card">
            <h1 class="text-center">🔄 Service Hybride</h1>
            <p class="text-center text-muted">SerpAPI (recherche Google) + Claude (analyse IA)</p>
            
            <div class="row mt-4">
                <div class="col-md-6 text-center">
                    <h5>🔍 SerpAPI</h5>
                    <?php if ($hasSerpAPI): ?>
                        <span class="status-badge status-ok">✅ Configuré</span>
                    <?php else: ?>
                        <span class="status-badge status-error">❌ Clé manquante</span>
                        <div class="mt-2">
                            <a href="https://serpapi.com/users/sign_up" target="_blank" class="btn btn-sm btn-warning">
                                Obtenir une clé gratuite
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 text-center">
                    <h5>🤖 Claude</h5>
                    <?php if ($hasClaude): ?>
                        <span class="status-badge status-ok">✅ Configuré</span>
                    <?php else: ?>
                        <span class="status-badge status-error">❌ Clé manquante</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <form method="GET" class="mt-4">
                <div class="input-group input-group-lg">
                    <input type="text" name="city" class="form-control" 
                           placeholder="Entrez une ville..." 
                           value="<?php echo htmlspecialchars($city); ?>">
                    <button class="btn btn-primary" type="submit">
                        🔍 Rechercher
                    </button>
                </div>
            </form>
        </div>
        
        <?php if (!$hasSerpAPI): ?>
        <div class="alert alert-warning">
            <h4>⚠️ Configuration SerpAPI requise</h4>
            <ol>
                <li>Créez un compte gratuit sur <a href="https://serpapi.com/users/sign_up" target="_blank">serpapi.com</a></li>
                <li>Copiez votre clé API depuis <a href="https://serpapi.com/manage-api-key" target="_blank">votre dashboard</a></li>
                <li>Ajoutez-la dans <code>/config/api-keys.php</code> :<br>
                    <code>'serpapi' => ['key' => 'VOTRE_CLE_ICI']</code>
                </li>
            </ol>
            <p><strong>Gratuit :</strong> 100 recherches/mois</p>
        </div>
        <?php else: ?>
        
        <div class="text-center text-white mb-3">
            <h3>Fonctionnement du service hybride :</h3>
            <p>1️⃣ SerpAPI cherche sur Google Events → 2️⃣ Claude analyse et enrichit → 3️⃣ Résultats structurés</p>
        </div>
        
        <?php
        echo "<div class='alert alert-info'>Recherche en cours pour : <strong>$city</strong></div>";
        flush();
        
        $startTime = microtime(true);
        $events = HybridEventsService::searchEvents($city, 12);
        $duration = round(microtime(true) - $startTime, 2);
        
        echo "<div class='alert alert-success'>";
        echo "✅ Recherche terminée en {$duration}s | ";
        echo count($events) . " événements trouvés";
        echo "</div>";
        
        if (empty($events)) {
            echo "<div class='alert alert-danger'>";
            echo "Aucun événement trouvé. Vérifiez les logs pour plus de détails.";
            echo "</div>";
        } else {
            echo "<div class='row'>";
            foreach ($events as $event) {
                echo "<div class='col-md-6 col-lg-4'>";
                echo "<div class='event-card'>";
                
                // Titre
                echo "<h5>" . htmlspecialchars($event['title'] ?? 'Sans titre') . "</h5>";
                
                // Confidence si disponible
                if (isset($event['confidence'])) {
                    echo "<span class='confidence-badge'>Confiance: " . round($event['confidence'] * 100) . "%</span> ";
                }
                
                // Catégorie
                echo "<span class='badge bg-primary'>" . htmlspecialchars($event['category'] ?? 'Culture') . "</span>";
                
                // Description
                echo "<p class='mt-2'>" . htmlspecialchars($event['description'] ?? '') . "</p>";
                
                // Détails
                echo "<div class='small text-muted'>";
                echo "📅 " . htmlspecialchars($event['date_display'] ?? 'Date à confirmer') . "<br>";
                echo "📍 " . htmlspecialchars($event['venue_name'] ?? 'Lieu non spécifié') . "<br>";
                
                if (isset($event['address']) && $event['address']) {
                    echo "🏠 " . htmlspecialchars($event['address']) . "<br>";
                }
                
                if ($event['is_free'] ?? false) {
                    echo "💰 <strong>GRATUIT</strong><br>";
                } elseif (isset($event['price']) && $event['price'] !== null) {
                    echo "💰 " . $event['price'] . "€<br>";
                }
                
                if (isset($event['detail_url'])) {
                    echo "<a href='" . htmlspecialchars($event['detail_url']) . "' class='btn btn-sm btn-primary mt-2'>Voir détails</a>";
                } elseif (isset($event['source_url']) && $event['source_url']) {
                    echo "<a href='" . htmlspecialchars($event['source_url']) . "' target='_blank' class='btn btn-sm btn-outline-primary mt-2'>Source</a>";
                }
                
                // Source
                echo "<div class='mt-2' style='font-size: 0.8em;'>";
                echo "Source: " . ($event['source'] ?? 'hybrid');
                echo "</div>";
                
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }
            echo "</div>";
        }
        ?>
        <?php endif; ?>
        
        <div class="text-center mt-4">
            <a href="discover-claude.php?city=<?php echo urlencode($city); ?>" 
               class="btn btn-success btn-lg">
                Utiliser dans Discover
            </a>
        </div>
    </div>
</body>
</html>