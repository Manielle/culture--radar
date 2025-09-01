<?php
/**
 * Test de l'API OpenWeatherMap
 * Vérifie que la clé API fonctionne correctement
 */

// Charger la configuration
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/classes/WeatherTransportService.php';

// Créer une instance du service météo
$weatherService = new WeatherTransportService();

// Tester différentes villes
$cities = ['Paris', 'Lyon', 'Marseille'];
$results = [];

foreach ($cities as $city) {
    $weather = $weatherService->getCurrentWeatherForCity($city);
    $results[$city] = $weather;
}

// Tester aussi les prévisions
$forecast = $weatherService->getForecastForCity('Paris', 3);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test API Météo - Culture Radar</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            color: white;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .status {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .success {
            background: rgba(34, 197, 94, 0.2);
            border-color: rgba(34, 197, 94, 0.5);
        }
        .error {
            background: rgba(239, 68, 68, 0.2);
            border-color: rgba(239, 68, 68, 0.5);
        }
        .city-weather {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .weather-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        h1 {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 30px;
        }
        h2 {
            color: #fbbf24;
            margin-bottom: 15px;
        }
        .temp {
            font-size: 2rem;
            font-weight: bold;
            color: #60a5fa;
        }
        .icon {
            font-size: 3rem;
            margin-right: 10px;
        }
        .detail {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            padding: 10px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 8px;
        }
        .forecast-day {
            background: rgba(255, 255, 255, 0.05);
            padding: 15px;
            border-radius: 10px;
            margin: 10px 0;
        }
        pre {
            background: rgba(0, 0, 0, 0.3);
            padding: 15px;
            border-radius: 10px;
            overflow-x: auto;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🌤️ Test de l'API OpenWeatherMap</h1>
        
        <?php 
        $apiKey = $_ENV['OPENWEATHER_API_KEY'] ?? '';
        if ($apiKey && $apiKey === '4f70ce6daf82c0e77d6128bc7fadf972'): 
        ?>
            <div class="status success">
                <h2>✅ Clé API Configurée</h2>
                <p>La clé OpenWeatherMap est correctement configurée dans l'environnement.</p>
            </div>
        <?php else: ?>
            <div class="status error">
                <h2>❌ Clé API Non Trouvée</h2>
                <p>La clé OpenWeatherMap n'est pas configurée ou incorrecte.</p>
                <p>Clé attendue : 4f70ce6daf82c0e77d6128bc7fadf972</p>
                <p>Clé trouvée : <?php echo $apiKey ? substr($apiKey, 0, 10) . '...' : 'AUCUNE'; ?></p>
            </div>
        <?php endif; ?>
        
        <h2>📍 Météo Actuelle par Ville</h2>
        <div class="weather-grid">
            <?php foreach ($results as $city => $weather): ?>
                <div class="city-weather">
                    <h3><?php echo $city; ?></h3>
                    <?php if ($weather['type'] !== 'unavailable'): ?>
                        <div style="display: flex; align-items: center; margin: 20px 0;">
                            <span class="icon"><?php echo $weather['icon'] ?? '🌤️'; ?></span>
                            <span class="temp"><?php echo $weather['temperature'] ?? 'N/A'; ?>°C</span>
                        </div>
                        <p><?php echo $weather['description'] ?? 'Pas de description'; ?></p>
                        
                        <div class="detail">
                            <span>💧 Humidité</span>
                            <span><?php echo $weather['humidity'] ?? 'N/A'; ?>%</span>
                        </div>
                        <div class="detail">
                            <span>💨 Vent</span>
                            <span><?php echo $weather['wind_speed'] ?? 'N/A'; ?> km/h</span>
                        </div>
                        <?php if (isset($weather['pressure'])): ?>
                        <div class="detail">
                            <span>🌡️ Pression</span>
                            <span><?php echo $weather['pressure']; ?> hPa</span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($weather['recommendation'])): ?>
                        <h4 style="margin-top: 20px;">💡 Recommandations</h4>
                        <?php 
                        $recs = is_array($weather['recommendation']) ? $weather['recommendation'] : [$weather['recommendation']];
                        foreach ($recs as $rec): 
                        ?>
                            <p style="margin: 5px 0; font-size: 0.9rem;">• <?php echo $rec; ?></p>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <p style="color: #ef4444;">❌ Données non disponibles</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if ($forecast && !isset($forecast['error'])): ?>
        <h2>📅 Prévisions sur 3 jours pour Paris</h2>
        <div class="city-weather">
            <?php if (isset($forecast['forecasts'])): ?>
                <?php foreach ($forecast['forecasts'] as $day): ?>
                    <div class="forecast-day">
                        <h4><?php echo $day['day'] ?? ''; ?> - <?php echo $day['date'] ?? ''; ?></h4>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <span class="icon"><?php echo $day['icon'] ?? '🌤️'; ?></span>
                                <span><?php echo $day['description'] ?? ''; ?></span>
                            </div>
                            <div>
                                <span style="color: #60a5fa;">↑ <?php echo round($day['temp_max'] ?? 0); ?>°</span>
                                <span style="color: #94a3b8;">↓ <?php echo round($day['temp_min'] ?? 0); ?>°</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Pas de données de prévision disponibles</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <h2>🔧 Debug - Données Brutes</h2>
        <div class="city-weather">
            <h3>Configuration</h3>
            <pre><?php 
            echo "PHP Version: " . PHP_VERSION . "\n";
            echo "OpenWeather API Key: " . ($apiKey ? substr($apiKey, 0, 10) . '...' : 'NOT SET') . "\n";
            echo "Environment: " . ($_ENV['APP_ENV'] ?? 'not set') . "\n";
            ?></pre>
            
            <h3>Réponse API Paris (JSON)</h3>
            <pre><?php echo json_encode($results['Paris'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?></pre>
        </div>
        
        <div style="text-align: center; margin-top: 40px;">
            <a href="/api/weather.php?action=current&city=Paris" target="_blank" style="color: #fbbf24;">
                🔗 Tester l'endpoint API directement
            </a>
        </div>
    </div>
</body>
</html>