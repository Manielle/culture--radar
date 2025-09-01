<?php
/**
 * Test direct de l'API Claude pour les événements
 */
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Charger la configuration
$configFile = __DIR__ . '/config/api-keys.php';
$apiKey = '';

if (file_exists($configFile)) {
    $config = require $configFile;
    $apiKey = $config['claude']['key'] ?? '';
}

$city = $_GET['city'] ?? 'Paris';
$events = [];
$error = '';
$rawResponse = '';

if ($apiKey && $apiKey !== 'VOTRE_CLE_API_CLAUDE_ICI') {
    // Appeler directement l'API Claude
    $headers = [
        'Content-Type: application/json',
        'x-api-key: ' . $apiKey,
        'anthropic-version: 2023-06-01'
    ];
    
    $today = date('l j F Y');
    $prompt = "Nous sommes le $today. Trouve-moi 8 événements culturels qui ont lieu aujourd'hui, cette semaine et ce weekend à $city. 
    
Retourne UNIQUEMENT un JSON valide (sans texte avant ou après) dans ce format exact:
{
  \"events\": [
    {
      \"title\": \"Nom de l'événement\",
      \"description\": \"Description courte\",
      \"category\": \"Musique/Art/Théâtre/Cinéma/Festival\",
      \"venue_name\": \"Nom du lieu\",
      \"address\": \"Adresse complète\",
      \"city\": \"$city\",
      \"date_display\": \"Date en français (ex: Samedi 15 juin)\",
      \"price\": 25,
      \"is_free\": false
    }
  ]
}";
    
    $data = [
        'model' => 'claude-sonnet-4-20250514',
        'max_tokens' => 2000,
        'messages' => [
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ],
        'temperature' => 0.7
    ];
    
    $ch = curl_init('https://api.anthropic.com/v1/messages');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    $rawResponse = $response;
    
    if ($curlError) {
        $error = "Erreur cURL: " . $curlError;
    } elseif ($httpCode !== 200) {
        $error = "Erreur HTTP: " . $httpCode;
        if ($response) {
            $errorData = json_decode($response, true);
            if (isset($errorData['error']['message'])) {
                $error .= " - " . $errorData['error']['message'];
            }
        }
    } else {
        // Parser la réponse
        $result = json_decode($response, true);
        
        if ($result && isset($result['content'][0]['text'])) {
            $text = $result['content'][0]['text'];
            
            // Extraire le JSON du texte
            if (preg_match('/\{[\s\S]*"events"[\s\S]*\}/m', $text, $matches)) {
                $jsonData = json_decode($matches[0], true);
                if ($jsonData && isset($jsonData['events'])) {
                    $events = $jsonData['events'];
                } else {
                    $error = "Impossible de parser le JSON des événements";
                }
            } else {
                $error = "Aucun JSON trouvé dans la réponse";
            }
        } else {
            $error = "Format de réponse inattendu";
        }
    }
} else {
    $error = "Clé API non configurée. Veuillez d'abord configurer votre clé API.";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test API Claude Events - Culture Radar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
        }
        .test-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        .event-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        .event-card:hover {
            transform: translateY(-5px);
        }
        .debug-section {
            background: #1e293b;
            color: #10b981;
            padding: 20px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
            overflow-x: auto;
            margin-top: 30px;
        }
        .error-alert {
            background: #fee2e2;
            color: #dc2626;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .success-alert {
            background: #d1fae5;
            color: #065f46;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="test-header">
            <h1>🧪 Test Direct API Claude - Événements</h1>
            <p class="mb-0">Recherche d'événements pour : <strong><?php echo htmlspecialchars($city); ?></strong></p>
        </div>
        
        <!-- Formulaire de test -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="">
                    <div class="input-group">
                        <input type="text" name="city" class="form-control" 
                               placeholder="Entrez une ville..." 
                               value="<?php echo htmlspecialchars($city); ?>">
                        <button class="btn btn-primary" type="submit">
                            Tester une autre ville
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <?php if (!$apiKey || $apiKey === 'VOTRE_CLE_API_CLAUDE_ICI'): ?>
        <div class="error-alert">
            <h4>⚠️ Configuration requise</h4>
            <p>La clé API Claude n'est pas configurée.</p>
            <a href="test-claude-api.php" class="btn btn-warning">Configurer la clé API</a>
        </div>
        <?php elseif ($error): ?>
        <div class="error-alert">
            <h4>❌ Erreur</h4>
            <p><?php echo htmlspecialchars($error); ?></p>
        </div>
        <?php elseif (!empty($events)): ?>
        <div class="success-alert">
            <h4>✅ Succès !</h4>
            <p><?php echo count($events); ?> événements trouvés pour <?php echo htmlspecialchars($city); ?></p>
        </div>
        
        <!-- Affichage des événements -->
        <div class="row">
            <?php foreach ($events as $event): ?>
            <div class="col-md-6">
                <div class="card event-card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <?php echo htmlspecialchars($event['title'] ?? 'Sans titre'); ?>
                            <?php if ($event['is_free'] ?? false): ?>
                            <span class="badge bg-success">Gratuit</span>
                            <?php endif; ?>
                        </h5>
                        <p class="card-text">
                            <?php echo htmlspecialchars($event['description'] ?? ''); ?>
                        </p>
                        <div class="text-muted">
                            <small>
                                📍 <?php echo htmlspecialchars($event['venue_name'] ?? ''); ?><br>
                                📅 <?php echo htmlspecialchars($event['date_display'] ?? ''); ?><br>
                                🏷️ <?php echo htmlspecialchars($event['category'] ?? ''); ?><br>
                                <?php if (!($event['is_free'] ?? false)): ?>
                                💰 <?php echo $event['price'] ?? 'Prix non indiqué'; ?>€
                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <!-- Debug section -->
        <?php if ($rawResponse): ?>
        <div class="debug-section">
            <h5>🔍 Réponse brute de l'API (Debug)</h5>
            <pre><?php 
                $decoded = json_decode($rawResponse, true);
                echo htmlspecialchars(json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            ?></pre>
        </div>
        <?php endif; ?>
        
        <div class="text-center mt-4">
            <a href="discover-claude.php?city=<?php echo urlencode($city); ?>" class="btn btn-success">
                Voir dans la page Discover
            </a>
            <a href="test-claude-api.php" class="btn btn-secondary">
                Configuration API
            </a>
        </div>
    </div>
</body>
</html>