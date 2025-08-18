<?php
// Culture Radar - Beautiful Landing Page
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? ($_SESSION['user_name'] ?? 'Utilisateur') : '';

// Database connection test (for status indicator)
$db_status = "Checking...";
try {
    $host = getenv('MYSQLHOST') ?: 'centerbeam.proxy.rlwy.net';
    $port = getenv('MYSQLPORT') ?: '48330';
    $user = getenv('MYSQLUSER') ?: 'root';
    $pass = getenv('MYSQLPASSWORD') ?: 'tBixYXRKGkGAZuyxGHFZzaTxQAGXvJJH';
    $dbname = getenv('MYSQLDATABASE') ?: 'railway';
    
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    $db_status = "Connected";
} catch (Exception $e) {
    $db_status = "Offline";
}

// Include the beautiful HTML landing page
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Culture Radar Paris | Découverte Culturelle IA | Votre boussole culturelle révolutionnaire</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    
    <meta name="description" content="Découvrez les trésors culturels cachés de Paris avec Culture Radar. Intelligence artificielle + géolocalisation pour des recommandations culturelles personnalisées.">
    
    <!-- FONTS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Include the full CSS from culture-radar-landingv2.html -->
    <link rel="stylesheet" href="/assets/css/landing.css">
</head>
<body>
    <!-- PHP Integration -->
    <script>
        window.userLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
        window.userName = "<?php echo htmlspecialchars($userName); ?>";
        window.dbStatus = "<?php echo htmlspecialchars($db_status); ?>";
    </script>

    <!-- Include the beautiful landing page content -->
    <?php include 'culture-radar-landingv2.html'; ?>
    
    <!-- Database Status Indicator -->
    <div style="position: fixed; bottom: 10px; right: 10px; background: rgba(0,0,0,0.8); color: white; padding: 5px 10px; border-radius: 5px; font-size: 12px; z-index: 9999;">
        DB: <?php echo $db_status; ?>
    </div>
</body>
</html>