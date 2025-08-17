#!/usr/bin/env php
<?php
/**
 * Script pour lancer manuellement le scraping
 * Utile pour les tests et le débogage
 */

require_once __DIR__ . '/../config.php';

echo "\n";
echo "╔══════════════════════════════════════════════════╗\n";
echo "║     CULTURE RADAR - SCRAPING MANUEL              ║\n";
echo "╚══════════════════════════════════════════════════╝\n";
echo "\n";

// Vérifier les arguments
$options = getopt("c:h", ["city:", "help", "test", "verbose"]);

if (isset($options['h']) || isset($options['help'])) {
    echo "Usage: php manual-scrape.php [options]\n\n";
    echo "Options:\n";
    echo "  -c, --city=CITY   Scrapper uniquement une ville spécifique\n";
    echo "  --test            Mode test (sans enregistrement en base)\n";
    echo "  --verbose         Afficher plus de détails\n";
    echo "  -h, --help        Afficher cette aide\n\n";
    echo "Exemples:\n";
    echo "  php manual-scrape.php                    # Scrapper toutes les villes\n";
    echo "  php manual-scrape.php --city=Paris       # Scrapper uniquement Paris\n";
    echo "  php manual-scrape.php --test --verbose   # Mode test avec détails\n";
    echo "\n";
    exit(0);
}

$testMode = isset($options['test']);
$verbose = isset($options['verbose']);
$specificCity = $options['c'] ?? $options['city'] ?? null;

if ($testMode) {
    echo "🧪 MODE TEST - Aucune donnée ne sera enregistrée\n\n";
}

if ($specificCity) {
    echo "🏙️  Scraping de la ville: $specificCity\n\n";
}

// Vérifier la connexion à la base de données
try {
    require_once __DIR__ . '/../includes/db.php';
    $db = Database::getInstance()->getConnection();
    echo "✅ Connexion à la base de données OK\n";
} catch (Exception $e) {
    echo "❌ Erreur de connexion à la base de données: " . $e->getMessage() . "\n";
    echo "Assurez-vous que MySQL est démarré et que la base de données existe.\n";
    echo "\nPour créer la base de données:\n";
    echo "  mysql -u root -p < setup-database.sql\n\n";
    exit(1);
}

// Vérifier la clé API
if (!defined('SERPAPI_KEY') || !SERPAPI_KEY) {
    echo "⚠️  Clé API SerpAPI non configurée\n";
    echo "La clé par défaut sera utilisée (limite de requêtes)\n\n";
}

echo "Démarrage du scraping...\n";
echo str_repeat("-", 50) . "\n\n";

// Lancer le scraping
if ($testMode) {
    // Mode test - afficher juste ce qui serait fait
    echo "Villes à scrapper:\n";
    $cities = ['Paris', 'Lyon', 'Marseille', 'Toulouse', 'Nice', 'Nantes', 'Strasbourg', 'Montpellier', 'Bordeaux', 'Lille', 'Rennes', 'Reims'];
    
    if ($specificCity) {
        $cities = [$specificCity];
    }
    
    foreach ($cities as $city) {
        echo "  • $city\n";
    }
    echo "\n";
    
    // Tester l'API pour une ville
    $testCity = $specificCity ?? 'Paris';
    echo "Test de l'API pour $testCity...\n";
    
    $url = "https://serpapi.com/search.json";
    $params = [
        'api_key' => SERPAPI_KEY,
        'engine' => 'google_events',
        'q' => "événements culturels $testCity",
        'location' => "$testCity, France",
        'gl' => 'fr',
        'hl' => 'fr',
        'num' => 5
    ];
    
    $queryString = http_build_query($params);
    $fullUrl = "$url?$queryString";
    
    if ($verbose) {
        echo "URL: $fullUrl\n\n";
    }
    
    $response = @file_get_contents($fullUrl);
    if ($response) {
        $data = json_decode($response, true);
        if (isset($data['events_results'])) {
            echo "✅ API fonctionne - " . count($data['events_results']) . " événements trouvés\n\n";
            
            if ($verbose) {
                echo "Exemples d'événements:\n";
                foreach (array_slice($data['events_results'], 0, 3) as $event) {
                    echo "  • " . ($event['title'] ?? 'Sans titre') . "\n";
                    if (isset($event['venue']['name'])) {
                        echo "    📍 " . $event['venue']['name'] . "\n";
                    }
                    if (isset($event['date']['when'])) {
                        echo "    📅 " . $event['date']['when'] . "\n";
                    }
                    echo "\n";
                }
            }
        } else {
            echo "⚠️  Aucun événement trouvé\n";
        }
    } else {
        echo "❌ Erreur lors de l'appel à l'API\n";
    }
    
} else {
    // Mode réel - lancer le scraping
    require_once __DIR__ . '/scrape-events.php';
}

echo "\n";
echo str_repeat("=", 50) . "\n";
echo "Terminé!\n\n";

// Afficher les instructions pour le cron
echo "Pour configurer le scraping automatique:\n";
echo "  bash setup-cron.sh\n\n";
echo "Pour voir les logs:\n";
echo "  tail -f logs/scraping_*.log\n\n";