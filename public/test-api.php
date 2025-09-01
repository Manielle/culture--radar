<?php
/**
 * Test script for API integration
 */

require_once __DIR__ . '/services/HybridEventsService.php';
require_once __DIR__ . '/services/OpenAgendaService.php';

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Test API Integration</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .test-section { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { color: green; }
        .error { color: red; }
        .event { margin: 10px 0; padding: 10px; background: #f9f9f9; border-left: 3px solid #007bff; }
        pre { background: #f0f0f0; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Test des APIs Culture Radar</h1>
    
    <div class="test-section">
        <h2>1. Configuration des clés API</h2>
        <?php
        $configFile = __DIR__ . '/config/api-keys.php';
        if (file_exists($configFile)) {
            $config = require $configFile;
            
            echo "<p>🔑 Claude API: " . (!empty($config['claude']['key']) ? '<span class="success">✓ Configurée</span>' : '<span class="error">✗ Manquante</span>') . "</p>";
            echo "<p>🔑 SerpAPI: " . (!empty($config['serpapi']['key']) ? '<span class="success">✓ Configurée</span>' : '<span class="error">✗ Manquante</span>') . "</p>";
            echo "<p>📋 Mode Demo: " . ($config['demo_mode'] ? '<span class="error">Activé (APIs désactivées)</span>' : '<span class="success">Désactivé (APIs actives)</span>') . "</p>";
            
            if (!empty($config['claude']['key'])) {
                echo "<p>Claude Key (début): " . substr($config['claude']['key'], 0, 20) . "...</p>";
            }
            if (!empty($config['serpapi']['key'])) {
                echo "<p>SerpAPI Key (début): " . substr($config['serpapi']['key'], 0, 20) . "...</p>";
            }
        } else {
            echo '<p class="error">❌ Fichier de configuration introuvable</p>';
        }
        ?>
    </div>
    
    <div class="test-section">
        <h2>2. Test HybridEventsService (SerpAPI + Claude)</h2>
        <?php
        try {
            echo "<p>Recherche d'événements à Paris...</p>";
            $events = HybridEventsService::searchEvents('Paris', 3);
            
            if (!empty($events)) {
                echo '<p class="success">✓ ' . count($events) . ' événements trouvés via les APIs</p>';
                foreach ($events as $event) {
                    echo '<div class="event">';
                    echo '<strong>' . htmlspecialchars($event['title'] ?? 'Sans titre') . '</strong><br>';
                    echo 'Date: ' . htmlspecialchars($event['date_display'] ?? 'Non spécifié') . '<br>';
                    echo 'Lieu: ' . htmlspecialchars($event['venue_name'] ?? $event['location'] ?? 'Non spécifié') . '<br>';
                    echo 'Source: ' . htmlspecialchars($event['source'] ?? 'Unknown') . '<br>';
                    echo '</div>';
                }
            } else {
                echo '<p class="error">✗ Aucun événement trouvé (vérifiez les clés API)</p>';
            }
        } catch (Exception $e) {
            echo '<p class="error">❌ Erreur: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
        ?>
    </div>
    
    <div class="test-section">
        <h2>3. Test OpenAgendaService</h2>
        <?php
        try {
            echo "<p>Test du service OpenAgenda (qui utilise maintenant les APIs)...</p>";
            $events = OpenAgendaService::getEvents('Lyon', 3);
            
            if (!empty($events)) {
                echo '<p class="success">✓ ' . count($events) . ' événements trouvés</p>';
                foreach ($events as $event) {
                    echo '<div class="event">';
                    echo '<strong>' . htmlspecialchars($event['title'] ?? 'Sans titre') . '</strong><br>';
                    if (isset($event['source']) && $event['source'] === 'hybrid_serpapi_claude') {
                        echo '<span class="success">✓ Données provenant des vraies APIs</span><br>';
                    } else {
                        echo '<span class="error">⚠️ Données de fallback (APIs non disponibles)</span><br>';
                    }
                    echo '</div>';
                }
            } else {
                echo '<p class="error">✗ Aucun événement trouvé</p>';
            }
        } catch (Exception $e) {
            echo '<p class="error">❌ Erreur: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
        ?>
    </div>
    
    <div class="test-section">
        <h2>4. Logs d'erreur récents</h2>
        <?php
        $logFile = __DIR__ . '/logs/error.log';
        if (file_exists($logFile)) {
            $logs = file_get_contents($logFile);
            $lines = array_slice(explode("\n", $logs), -10); // Last 10 lines
            echo '<pre>' . htmlspecialchars(implode("\n", $lines)) . '</pre>';
        } else {
            echo '<p>Aucun fichier de log trouvé</p>';
        }
        ?>
    </div>
    
    <div class="test-section">
        <h2>Résumé</h2>
        <?php
        $apiWorking = !empty(HybridEventsService::searchEvents('Paris', 1));
        if ($apiWorking) {
            echo '<p class="success">✅ Les APIs fonctionnent correctement!</p>';
            echo '<p>Les événements sont récupérés depuis SerpAPI et enrichis avec Claude AI.</p>';
        } else {
            echo '<p class="error">⚠️ Les APIs ne fonctionnent pas ou sont désactivées.</p>';
            echo '<p>Vérifiez que:</p>';
            echo '<ul>';
            echo '<li>Les clés API sont correctement configurées dans .env</li>';
            echo '<li>DEMO_MODE est défini sur false</li>';
            echo '<li>Les clés API sont valides et actives</li>';
            echo '</ul>';
        }
        ?>
    </div>
</body>
</html>