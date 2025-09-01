<?php
/**
 * Debug complet de l'API Claude
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html><head><title>Debug API Claude</title>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; }
    pre { background: #f0f0f0; padding: 10px; border-radius: 5px; overflow-x: auto; }
    .step { border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px; }
</style></head><body>";

echo "<h1>🔍 Debug Complet API Claude</h1>";

// 1. Vérifier la configuration
echo "<div class='step'>";
echo "<h2>1. Configuration API</h2>";

$configFile = __DIR__ . '/config/api-keys.php';
if (file_exists($configFile)) {
    echo "<p class='success'>✅ Fichier config trouvé</p>";
    $config = require $configFile;
    
    if (isset($config['claude']['key']) && !empty($config['claude']['key'])) {
        $apiKey = $config['claude']['key'];
        echo "<p class='success'>✅ Clé API présente</p>";
        echo "<p>Clé (masquée) : " . substr($apiKey, 0, 20) . "...</p>";
        echo "<p>Longueur : " . strlen($apiKey) . " caractères</p>";
    } else {
        echo "<p class='error'>❌ Clé API manquante ou vide</p>";
        die("</div></body></html>");
    }
} else {
    echo "<p class='error'>❌ Fichier config non trouvé</p>";
    die("</div></body></html>");
}
echo "</div>";

// 2. Test simple de l'API
echo "<div class='step'>";
echo "<h2>2. Test Simple API</h2>";

$headers = [
    'Content-Type: application/json',
    'x-api-key: ' . $apiKey,
    'anthropic-version: 2023-06-01'
];

$testData = [
    'model' => 'claude-sonnet-4-20250514',
    'max_tokens' => 100,
    'messages' => [
        [
            'role' => 'user',
            'content' => 'Réponds juste "OK" si tu fonctionnes'
        ]
    ]
];

echo "<p>Envoi de la requête de test...</p>";
echo "<pre>URL: https://api.anthropic.com/v1/messages</pre>";
echo "<pre>Model: claude-sonnet-4-20250514</pre>";

$ch = curl_init('https://api.anthropic.com/v1/messages');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

// Capturer les informations détaillées
$verbose = fopen('php://temp', 'w+');
curl_setopt($ch, CURLOPT_STDERR, $verbose);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
$curlInfo = curl_getinfo($ch);

rewind($verbose);
$verboseLog = stream_get_contents($verbose);

curl_close($ch);

echo "<p><strong>Code HTTP :</strong> $httpCode</p>";

if ($curlError) {
    echo "<p class='error'>❌ Erreur cURL : $curlError</p>";
} elseif ($httpCode === 200) {
    echo "<p class='success'>✅ API répond correctement !</p>";
    $result = json_decode($response, true);
    if (isset($result['content'][0]['text'])) {
        echo "<p>Réponse : " . htmlspecialchars($result['content'][0]['text']) . "</p>";
    }
} else {
    echo "<p class='error'>❌ Erreur HTTP $httpCode</p>";
    echo "<pre>Réponse brute : " . htmlspecialchars(substr($response, 0, 500)) . "</pre>";
    
    $errorData = json_decode($response, true);
    if (isset($errorData['error'])) {
        echo "<p class='error'>Message d'erreur : " . $errorData['error']['message'] . "</p>";
        echo "<p>Type : " . $errorData['error']['type'] . "</p>";
    }
}

echo "<details>";
echo "<summary>Voir les détails de la requête</summary>";
echo "<pre>" . htmlspecialchars($verboseLog) . "</pre>";
echo "</details>";

echo "</div>";

// 3. Test avec le service ClaudeEventsServiceV2
echo "<div class='step'>";
echo "<h2>3. Test ClaudeEventsServiceV2</h2>";

// Charger le service
$serviceFile = __DIR__ . '/services/ClaudeEventsServiceV2.php';
if (file_exists($serviceFile)) {
    echo "<p class='success'>✅ Service V2 trouvé</p>";
    require_once __DIR__ . '/config.php';
    require_once $serviceFile;
    
    echo "<p>Test de recherche pour Paris...</p>";
    
    // Activer le debug
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    try {
        $events = ClaudeEventsServiceV2::searchEvents('Paris', 1);
        
        if (!empty($events) && $events[0]['source'] !== 'fallback') {
            echo "<p class='success'>✅ Service fonctionne !</p>";
            echo "<p>Nombre d'événements : " . count($events) . "</p>";
            echo "<pre>";
            print_r($events[0]);
            echo "</pre>";
        } else {
            echo "<p class='error'>❌ Service retourne le fallback</p>";
            echo "<p>Vérifiez les logs PHP pour plus de détails</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Exception : " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p class='error'>❌ Service V2 non trouvé</p>";
}
echo "</div>";

// 4. Vérifier le modèle
echo "<div class='step'>";
echo "<h2>4. Test avec différents modèles</h2>";

$models = [
    'claude-sonnet-4-20250514' => 'Sonnet 4 (nouveau)',
    'claude-3-5-sonnet-20241022' => 'Claude 3.5 Sonnet',
    'claude-3-sonnet-20240229' => 'Claude 3 Sonnet'
];

foreach ($models as $model => $name) {
    echo "<p>Test avec <strong>$name</strong> ($model)...</p>";
    
    $testData['model'] = $model;
    
    $ch = curl_init('https://api.anthropic.com/v1/messages');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        echo "<p class='success'>✅ $model fonctionne</p>";
    } else {
        echo "<p class='error'>❌ $model : Erreur HTTP $httpCode</p>";
        $error = json_decode($response, true);
        if (isset($error['error']['message'])) {
            echo "<p class='warning'>Message : " . $error['error']['message'] . "</p>";
        }
    }
}
echo "</div>";

// 5. Recommandations
echo "<div class='step'>";
echo "<h2>5. Recommandations</h2>";

if ($httpCode === 200) {
    echo "<p class='success'>✅ L'API Claude fonctionne correctement</p>";
    echo "<p>Le problème vient probablement du parsing de la réponse dans ClaudeEventsServiceV2</p>";
    echo "<ul>";
    echo "<li>Vérifiez les logs PHP : <code>tail -f /var/log/apache2/error.log</code></li>";
    echo "<li>Testez avec un prompt plus simple</li>";
    echo "<li>Augmentez le timeout si nécessaire</li>";
    echo "</ul>";
} else {
    echo "<p class='error'>❌ Problème de connexion à l'API</p>";
    echo "<ul>";
    echo "<li>Vérifiez que la clé API est valide</li>";
    echo "<li>Vérifiez votre connexion Internet</li>";
    echo "<li>Essayez un modèle différent</li>";
    echo "</ul>";
}
echo "</div>";

echo "</body></html>";
?>