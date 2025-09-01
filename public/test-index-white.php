<?php
// Copie de index.php mais avec debug
session_start();
require_once __DIR__ . '/config.php';

$userCity = $_SESSION['user_city'] ?? 'Paris';
$isLoggedIn = isset($_SESSION['user_id']);

// Charger quelques événements
require_once __DIR__ . '/services/KnownEventsData.php';
$localEvents = array_slice(KnownEventsData::getKnownEvents('Paris'), 0, 6);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Index - Culture Radar</title>
    
    <!-- CSS inline pour éviter les problèmes de chargement -->
    <style>
        /* Reset et styles de base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: #0a0a0f;
            color: white;
            min-height: 100vh;
            visibility: visible !important;
            display: block !important;
        }
        
        /* Debug: forcer la visibilité */
        body * {
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        .debug-info {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #f00;
            color: white;
            padding: 10px;
            z-index: 99999;
            text-align: center;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .hero {
            padding: 100px 20px 50px;
            text-align: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
        }
        
        .events-section {
            padding: 50px 20px;
            background: white;
            color: #333;
        }
        
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        
        .event-card {
            background: #f9f9f9;
            border-radius: 10px;
            padding: 20px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <!-- Debug info -->
    <div class="debug-info">
        ⚠️ PAGE DE TEST - Si vous voyez ce message, la page se charge correctement
    </div>
    
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Culture Radar</h1>
            <p>Votre boussole culturelle intelligente</p>
        </div>
    </section>
    
    <!-- Events Section -->
    <section class="events-section">
        <div class="container">
            <h2>Événements populaires</h2>
            
            <div class="events-grid">
                <?php foreach ($localEvents as $event): ?>
                    <div class="event-card">
                        <h3><?php echo htmlspecialchars($event['title'] ?? 'Sans titre'); ?></h3>
                        <p>📍 <?php echo htmlspecialchars($event['city'] ?? 'Paris'); ?></p>
                        <p>📅 <?php echo htmlspecialchars($event['date_display'] ?? 'Bientôt'); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    
    <!-- Test JavaScript -->
    <script>
        console.log('✅ JavaScript chargé');
        
        // Vérifier que le body est visible
        window.addEventListener('load', function() {
            console.log('✅ Page complètement chargée');
            console.log('Body visibility:', window.getComputedStyle(document.body).visibility);
            console.log('Body display:', window.getComputedStyle(document.body).display);
            
            // Forcer la visibilité si nécessaire
            document.body.style.visibility = 'visible';
            document.body.style.display = 'block';
            document.body.style.opacity = '1';
            
            // Vérifier s'il y a des éléments qui couvrent la page
            const allElements = document.querySelectorAll('*');
            allElements.forEach(el => {
                const style = window.getComputedStyle(el);
                if (style.position === 'fixed' && 
                    style.width === '100%' && 
                    style.height === '100%' && 
                    style.background !== 'none' && 
                    style.background !== 'transparent') {
                    console.warn('⚠️ Élément qui couvre toute la page:', el);
                }
            });
        });
        
        // Capturer les erreurs
        window.addEventListener('error', function(e) {
            console.error('❌ Erreur JavaScript:', e.message);
            // Ne pas masquer la page en cas d'erreur
            document.body.style.display = 'block';
            document.body.style.visibility = 'visible';
        });
    </script>
</body>
</html>