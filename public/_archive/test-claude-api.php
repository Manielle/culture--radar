<?php
/**
 * Page de test et configuration de l'API Claude
 */
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$message = '';
$error = '';
$apiKey = '';
$testResult = null;

// Charger la configuration existante
$configFile = __DIR__ . '/config/api-keys.php';
if (file_exists($configFile)) {
    $config = require $configFile;
    $apiKey = $config['claude']['key'] ?? '';
}

// Si formulaire soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_key'])) {
        $newKey = trim($_POST['api_key']);
        
        // Sauvegarder la clé
        $configContent = "<?php
/**
 * Configuration des clés API
 * IMPORTANT: Ne jamais commiter ce fichier sur GitHub !
 */

return [
    'claude' => [
        'key' => '$newKey',
        'model' => 'claude-sonnet-4-20250514',
        'api_url' => 'https://api.anthropic.com/v1/messages'
    ],
    
    'openai' => [
        'key' => '',
    ],
    
    'google_maps' => [
        'key' => '',
    ]
];
?>";
        
        // Créer le dossier config s'il n'existe pas
        if (!is_dir(__DIR__ . '/config')) {
            mkdir(__DIR__ . '/config', 0755, true);
        }
        
        file_put_contents($configFile, $configContent);
        $message = "Clé API sauvegardée avec succès !";
        $apiKey = $newKey;
    }
    
    if (isset($_POST['test_api'])) {
        $testKey = $apiKey ?: trim($_POST['api_key']);
        
        if ($testKey && $testKey !== 'VOTRE_CLE_API_CLAUDE_ICI') {
            // Test simple de l'API
            $headers = [
                'Content-Type: application/json',
                'x-api-key: ' . $testKey,
                'anthropic-version: 2023-06-01'
            ];
            
            $data = [
                'model' => 'claude-sonnet-4-20250514',
                'max_tokens' => 100,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => 'Réponds juste "OK" si tu fonctionnes'
                    ]
                ]
            ];
            
            $ch = curl_init('https://api.anthropic.com/v1/messages');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200) {
                $testResult = "✅ API Claude fonctionne correctement !";
            } else {
                $testResult = "❌ Erreur API (Code HTTP: $httpCode)";
                if ($httpCode === 401) {
                    $testResult .= " - Clé API invalide";
                }
            }
        } else {
            $error = "Veuillez entrer une clé API valide";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration API Claude - Culture Radar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .config-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .api-input {
            font-family: 'Courier New', monospace;
            background: #f8f9fa;
        }
        .info-box {
            background: #f0f9ff;
            border-left: 4px solid #0ea5e9;
            padding: 15px;
            margin: 20px 0;
        }
        .success-box {
            background: #d1fae5;
            border-left: 4px solid #10b981;
            padding: 15px;
            margin: 20px 0;
        }
        .error-box {
            background: #fee2e2;
            border-left: 4px solid #ef4444;
            padding: 15px;
            margin: 20px 0;
        }
        .step {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        .step-number {
            display: inline-block;
            width: 30px;
            height: 30px;
            background: #764ba2;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 30px;
            margin-right: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="config-card">
            <h1 class="text-center mb-4">🔑 Configuration API Claude</h1>
            
            <?php if ($message): ?>
            <div class="success-box">
                <?php echo $message; ?>
            </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
            <div class="error-box">
                <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <?php if ($testResult): ?>
            <div class="<?php echo strpos($testResult, '✅') !== false ? 'success-box' : 'error-box'; ?>">
                <?php echo $testResult; ?>
            </div>
            <?php endif; ?>
            
            <?php if (!$apiKey || $apiKey === 'VOTRE_CLE_API_CLAUDE_ICI'): ?>
            <!-- Instructions pour obtenir une clé -->
            <div class="info-box">
                <h4>📌 Comment obtenir une clé API Claude ?</h4>
                <div class="step">
                    <span class="step-number">1</span>
                    <strong>Créer un compte Anthropic</strong><br>
                    Allez sur <a href="https://console.anthropic.com" target="_blank">console.anthropic.com</a> et créez un compte
                </div>
                
                <div class="step">
                    <span class="step-number">2</span>
                    <strong>Générer une clé API</strong><br>
                    Dans le dashboard, allez dans "API Keys" et cliquez sur "Create Key"
                </div>
                
                <div class="step">
                    <span class="step-number">3</span>
                    <strong>Copier la clé</strong><br>
                    Copiez la clé qui commence par "sk-ant-api..." et collez-la ci-dessous
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Formulaire de configuration -->
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="api_key" class="form-label">
                        <strong>Clé API Claude</strong>
                    </label>
                    <input type="text" 
                           class="form-control api-input" 
                           id="api_key" 
                           name="api_key" 
                           placeholder="sk-ant-api03-..." 
                           value="<?php echo htmlspecialchars($apiKey); ?>"
                           required>
                    <small class="text-muted">
                        La clé commence par "sk-ant-api" et fait environ 100 caractères
                    </small>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" name="save_key" class="btn btn-primary btn-lg">
                        💾 Sauvegarder la clé
                    </button>
                    <button type="submit" name="test_api" class="btn btn-secondary">
                        🧪 Tester la connexion API
                    </button>
                </div>
            </form>
            
            <?php if ($apiKey && $apiKey !== 'VOTRE_CLE_API_CLAUDE_ICI'): ?>
            <hr class="my-4">
            
            <div class="success-box">
                <h5>✅ Configuration active</h5>
                <p>Votre clé API est configurée. Vous pouvez maintenant :</p>
                <div class="d-grid gap-2 mt-3">
                    <a href="discover-claude.php" class="btn btn-success btn-lg">
                        🎭 Découvrir les événements avec Claude
                    </a>
                    <a href="test-claude-events.php" class="btn btn-info">
                        🧪 Tester la recherche d'événements
                    </a>
                </div>
            </div>
            <?php endif; ?>
            
            <hr class="my-4">
            
            <div class="text-center text-muted">
                <small>
                    <strong>Note :</strong> La clé API est stockée localement et n'est jamais partagée.<br>
                    Ne commitez jamais le fichier config/api-keys.php sur GitHub.
                </small>
            </div>
        </div>
    </div>
</body>
</html>