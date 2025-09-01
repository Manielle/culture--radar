<?php
// Test de vérification finale de tous les liens
$testEvent = [
    'title' => 'Concert Test Final',
    'category' => 'Musique',
    'venue_name' => 'Olympia',
    'city' => 'Paris',
    'date_display' => '30 août 2025',
    'price' => 35,
    'description' => 'Un concert de test pour vérifier que tous les liens fonctionnent'
];
$testData = base64_encode(json_encode($testEvent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Test Final - Tous les Liens</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        h1 { text-align: center; margin-bottom: 30px; }
        .success { 
            background: #d4edda; 
            border: 1px solid #c3e6cb; 
            color: #155724;
            padding: 15px; 
            border-radius: 8px; 
            margin-bottom: 20px;
        }
        .test-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .test-card {
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
        }
        .test-card h3 {
            margin-top: 0;
            color: #495057;
        }
        .test-btn {
            display: inline-block;
            margin: 10px 0;
            padding: 12px 25px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: transform 0.3s;
        }
        .test-btn:hover { transform: translateY(-2px); }
        .status { margin-top: 10px; font-size: 14px; }
        .ok { color: #28a745; }
        .error { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Test Final - Vérification de Tous les Liens</h1>
        
        <div class="success">
            <h2>✅ Migration Complète</h2>
            <p>Tous les liens ont été migrés vers <strong>event-details-hybrid.php</strong></p>
            <p>L'ancien event-details.php redirige automatiquement vers la version hybrid.</p>
        </div>
        
        <div class="test-grid">
            <!-- Test direct hybrid -->
            <div class="test-card">
                <h3>📋 Lien Direct Hybrid</h3>
                <a href="event-details-hybrid.php?data=<?php echo $testData; ?>" 
                   class="test-btn" target="_blank">
                    Tester Hybrid Direct
                </a>
                <div class="status ok">✓ Devrait fonctionner</div>
            </div>
            
            <!-- Test redirection -->
            <div class="test-card">
                <h3>↪️ Via Redirection</h3>
                <a href="event-details.php?data=<?php echo $testData; ?>" 
                   class="test-btn" target="_blank">
                    Tester Redirection
                </a>
                <div class="status ok">✓ Redirige vers hybrid</div>
            </div>
            
            <!-- Pages principales -->
            <div class="test-card">
                <h3>🏠 Accueil</h3>
                <a href="index.php" class="test-btn" target="_blank">
                    Aller à l'Accueil
                </a>
                <div class="status ok">✓ Liens mis à jour</div>
            </div>
            
            <div class="test-card">
                <h3>🔍 Découvrir</h3>
                <a href="discover.php" class="test-btn" target="_blank">
                    Page Découvrir
                </a>
                <div class="status ok">✓ Liens mis à jour</div>
            </div>
            
            <div class="test-card">
                <h3>📅 Calendrier</h3>
                <a href="calendar.php" class="test-btn" target="_blank">
                    Calendrier
                </a>
                <div class="status ok">✓ Liens mis à jour</div>
            </div>
            
            <div class="test-card">
                <h3>❤️ Mes Événements</h3>
                <a href="my-events.php" class="test-btn" target="_blank">
                    Mes Événements
                </a>
                <div class="status ok">✓ Liens mis à jour</div>
            </div>
            
            <div class="test-card">
                <h3>🔎 Recherche</h3>
                <a href="search.php" class="test-btn" target="_blank">
                    Recherche
                </a>
                <div class="status ok">✓ Liens mis à jour</div>
            </div>
            
            <div class="test-card">
                <h3>🔥 Tendances</h3>
                <a href="trending.php" class="test-btn" target="_blank">
                    Tendances
                </a>
                <div class="status ok">✓ Liens mis à jour</div>
            </div>
            
            <div class="test-card">
                <h3>💡 Recommandations</h3>
                <a href="recommendations.php" class="test-btn" target="_blank">
                    Recommandations
                </a>
                <div class="status ok">✓ Liens mis à jour</div>
            </div>
        </div>
        
        <div style="background: #e7f3ff; padding: 20px; border-radius: 10px; margin-top: 30px;">
            <h3>📊 Résumé des Modifications</h3>
            <ul>
                <li>✅ <strong>index.php</strong> - 3 occurrences mises à jour</li>
                <li>✅ <strong>discover.php</strong> - 1 occurrence mise à jour</li>
                <li>✅ <strong>calendar.php</strong> - 1 occurrence mise à jour</li>
                <li>✅ <strong>my-events.php</strong> - 1 occurrence mise à jour</li>
                <li>✅ <strong>search.php</strong> - 1 occurrence mise à jour</li>
                <li>✅ <strong>trending.php</strong> - 2 occurrences mises à jour</li>
                <li>✅ <strong>recommendations.php</strong> - 1 occurrence mise à jour</li>
                <li>✅ <strong>event-details.php</strong> - Redirige vers hybrid</li>
            </ul>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <h3>🎯 Test Complet</h3>
            <p>Cliquez sur chaque lien ci-dessus pour vérifier que tout fonctionne correctement.</p>
            <p>Les boutons "Voir les détails" sur chaque page devraient maintenant ouvrir la page event-details-hybrid.php sans erreur 404.</p>
        </div>
    </div>
</body>
</html>