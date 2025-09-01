<?php
// Test pour comprendre les chemins du serveur
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test Server Path</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .info-box { background: #f0f0f0; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .test-link { display: inline-block; margin: 5px; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <h1>Test de configuration du serveur</h1>
    
    <div class="info-box">
        <h2>Informations serveur:</h2>
        <ul>
            <li><strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT']; ?></li>
            <li><strong>Script Name:</strong> <?php echo $_SERVER['SCRIPT_NAME']; ?></li>
            <li><strong>Request URI:</strong> <?php echo $_SERVER['REQUEST_URI']; ?></li>
            <li><strong>PHP Self:</strong> <?php echo $_SERVER['PHP_SELF']; ?></li>
            <li><strong>Current File:</strong> <?php echo __FILE__; ?></li>
            <li><strong>Current Dir:</strong> <?php echo __DIR__; ?></li>
            <li><strong>Server Name:</strong> <?php echo $_SERVER['SERVER_NAME']; ?></li>
            <li><strong>Server Port:</strong> <?php echo $_SERVER['SERVER_PORT']; ?></li>
        </ul>
    </div>
    
    <div class="info-box">
        <h2>Vérification des fichiers:</h2>
        <?php
        $files = [
            'event-details.php',
            './event-details.php',
            '/event-details.php',
            __DIR__ . '/event-details.php',
            'event-details-hybrid.php',
            'index.php',
            'discover.php'
        ];
        
        foreach ($files as $file) {
            $exists = file_exists($file);
            $class = $exists ? 'success' : 'error';
            echo "<div class='info-box $class'>";
            echo "<strong>$file:</strong> " . ($exists ? '✅ Existe' : '❌ N\'existe pas');
            if ($exists) {
                echo " (Path complet: " . realpath($file) . ")";
            }
            echo "</div>";
        }
        ?>
    </div>
    
    <div class="info-box">
        <h2>Test des liens:</h2>
        <?php
        $testData = base64_encode(json_encode([
            'title' => 'Test Event',
            'category' => 'Test',
            'city' => 'Paris'
        ]));
        ?>
        
        <p>Essayez ces différentes variantes:</p>
        
        <a href="event-details.php?data=<?php echo $testData; ?>" class="test-link">
            event-details.php (relatif)
        </a>
        
        <a href="./event-details.php?data=<?php echo $testData; ?>" class="test-link">
            ./event-details.php
        </a>
        
        <a href="/event-details.php?data=<?php echo $testData; ?>" class="test-link">
            /event-details.php (absolu)
        </a>
        
        <a href="http://localhost:8888/event-details.php?data=<?php echo $testData; ?>" class="test-link">
            URL complète
        </a>
        
        <a href="event-details-hybrid.php?data=<?php echo $testData; ?>" class="test-link">
            event-details-hybrid.php (qui marche)
        </a>
    </div>
    
    <div class="info-box">
        <h2>Base URL suggérée:</h2>
        <?php
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $baseUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
        ?>
        <p><strong>Base URL:</strong> <?php echo $baseUrl; ?></p>
        <p><strong>Event Details URL:</strong> <?php echo $baseUrl; ?>/event-details.php</p>
        
        <a href="<?php echo $baseUrl; ?>/event-details.php?data=<?php echo $testData; ?>" class="test-link">
            Tester avec base URL
        </a>
    </div>
</body>
</html>