<?php
// Test simple pour vérifier que PHP fonctionne
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html>
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-W2J74HSR1E"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag("js", new Date());
      gtag("config", "G-W2J74HSR1E");
    </script>
    <title>✅ Culture Radar - Check</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        .box {
            background: white;
            color: #333;
            padding: 40px;
            border-radius: 20px;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        h1 { color: #28a745; }
        a {
            display: inline-block;
            margin: 10px;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
        }
        a:hover {
            background: #5a67d8;
            transform: translateY(-2px);
        }
        .url {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            font-family: monospace;
            color: #007bff;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>✅ PHP Fonctionne!</h1>
        <p><strong>Culture Radar est accessible</strong></p>
        
        <div class="url">
            URL actuelle: <?php echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>
        </div>
        
        <p>Port: <?php echo $_SERVER['SERVER_PORT']; ?></p>
        
        <h2>Liens directs:</h2>
        <div>
            <a href="/">🏠 Accueil</a>
            <a href="/events.php">🎭 Événements</a>
            <a href="/login.php">🔐 Connexion</a>
            <a href="/register.php">📝 Inscription</a>
        </div>
        
        <h3 style="color: #666; margin-top: 30px;">URLs d'accès:</h3>
        <div class="url">http://localhost:8888/</div>
        <div class="url">http://localhost:8888/index.php</div>
        <div class="url">http://localhost:8888/events.php</div>
    </div>
</body>
</html>