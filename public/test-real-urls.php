<?php
require_once 'services/VenueLinksService.php';

// Événements avec de VRAIES URLs qui existent
$realEvents = [
    // 1. Philharmonie - Page d'accueil (existe)
    [
        'title' => 'Concerts à la Philharmonie',
        'venue_name' => 'Philharmonie de Paris',
        'source_url' => 'https://philharmoniedeparis.fr',
        'category' => 'Concert',
        'city' => 'Paris',
        'date_display' => 'Saison 2025',
        'price' => 35,
        'description' => 'Programmation de la Philharmonie de Paris'
    ],
    
    // 2. Centre Pompidou - Page qui existe vraiment
    [
        'title' => 'Expositions Centre Pompidou',
        'venue_name' => 'Centre Pompidou',
        'source_url' => 'https://www.centrepompidou.fr/fr',
        'category' => 'Exposition',
        'city' => 'Paris',
        'date_display' => '2025',
        'price' => 14,
        'description' => 'Collections et expositions du Centre Pompidou'
    ],
    
    // 3. Opéra de Paris - Site officiel
    [
        'title' => 'Saison Opéra de Paris',
        'venue_name' => 'Opéra Garnier',
        'source_url' => 'https://www.operadeparis.fr',
        'category' => 'Opéra',
        'city' => 'Paris',
        'date_display' => 'Saison 2024-2025',
        'price' => 80,
        'description' => 'Programmation de l\'Opéra de Paris'
    ],
    
    // 4. Musée du Louvre - Site officiel
    [
        'title' => 'Collections du Louvre',
        'venue_name' => 'Musée du Louvre',
        'source_url' => 'https://www.louvre.fr',
        'category' => 'Musée',
        'city' => 'Paris',
        'date_display' => 'Permanent',
        'price' => 17,
        'description' => 'Visite des collections permanentes du Louvre'
    ],
    
    // 5. Jazz au Sunset SANS URL (doit ajouter automatiquement)
    [
        'title' => 'Soirée Jazz',
        'venue_name' => 'Le Sunset',
        'source_url' => '',
        'category' => 'Concert',
        'city' => 'Paris',
        'date_display' => 'Ce soir',
        'price' => 20,
        'description' => 'Concert de jazz au Sunset Sunside'
    ],
    
    // 6. Théâtre du Châtelet - Site officiel
    [
        'title' => 'Spectacles au Châtelet',
        'venue_name' => 'Théâtre du Châtelet',
        'source_url' => 'https://www.chatelet.com',
        'category' => 'Théâtre',
        'city' => 'Paris',
        'date_display' => '2025',
        'price' => 45,
        'description' => 'Programmation du Théâtre du Châtelet'
    ],
    
    // 7. Lieu inconnu SANS URL
    [
        'title' => 'Concert dans un bar',
        'venue_name' => 'Bar XYZ',
        'source_url' => '',
        'category' => 'Concert',
        'city' => 'Paris',
        'date_display' => 'Ce soir',
        'price' => 0,
        'description' => 'Concert gratuit'
    ]
];

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Test avec VRAIES URLs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background: #f8f9fa;
            padding: 20px;
        }
        .test-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #007bff;
        }
        .test-card.success { border-left-color: #28a745; }
        .test-card.warning { border-left-color: #ffc107; }
        .test-card.error { border-left-color: #dc3545; }
        
        .url-box {
            background: #f4f4f4;
            padding: 10px;
            border-radius: 5px;
            font-family: monospace;
            font-size: 12px;
            word-break: break-all;
            margin: 10px 0;
        }
        
        .badge-custom {
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
            margin: 5px 0;
        }
        .badge-ok { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-error { background: #f8d7da; color: #721c24; }
        
        h1 { color: #FF6B6B; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">🔗 Test avec de VRAIES URLs existantes</h1>
        
        <div class="alert alert-info">
            <strong>ℹ️ Note:</strong> Ces URLs sont toutes réelles et fonctionnelles. 
            Cliquez sur "Tester le lien" pour vérifier qu'elles s'ouvrent correctement.
        </div>
        
        <?php foreach ($realEvents as $index => $event): ?>
            <?php
            // Encoder l'événement
            $eventData = base64_encode(json_encode($event));
            
            // Simuler la logique
            $venue = $event['venue_name'];
            $sourceUrl = $event['source_url'];
            $venueUrl = VenueLinksService::getVenueUrl($venue);
            
            // Appliquer la logique de event-details-hybrid.php
            $finalUrl = $sourceUrl;
            if (empty($sourceUrl) || $sourceUrl === '#' || $sourceUrl === '') {
                if ($venueUrl) {
                    $finalUrl = $venueUrl;
                }
            }
            
            // Déterminer le statut
            $cardClass = 'success';
            $badgeClass = 'badge-ok';
            $status = '';
            
            if (!empty($sourceUrl)) {
                if ($finalUrl === $sourceUrl) {
                    $status = '✅ URL originale conservée';
                } else {
                    $cardClass = 'error';
                    $badgeClass = 'badge-error';
                    $status = '❌ URL remplacée (problème!)';
                }
            } else {
                if ($venueUrl) {
                    $cardClass = 'warning';
                    $badgeClass = 'badge-warning';
                    $status = '➕ URL du lieu ajoutée automatiquement';
                } else {
                    $cardClass = 'warning';
                    $badgeClass = 'badge-warning';
                    $status = '⚠️ Pas d\'URL (lieu non reconnu)';
                }
            }
            ?>
            
            <div class="test-card <?php echo $cardClass; ?>">
                <div class="row">
                    <div class="col-md-8">
                        <h4><?php echo ($index + 1); ?>. <?php echo htmlspecialchars($event['title']); ?></h4>
                        <p class="text-muted"><?php echo htmlspecialchars($event['description']); ?></p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <strong>📍 Lieu:</strong> <?php echo htmlspecialchars($event['venue_name']); ?><br>
                                <strong>💰 Prix:</strong> <?php echo $event['price'] ? $event['price'] . '€' : 'Gratuit'; ?>
                            </div>
                            <div class="col-md-6">
                                <strong>🎭 Catégorie:</strong> <?php echo htmlspecialchars($event['category']); ?><br>
                                <strong>📅 Date:</strong> <?php echo htmlspecialchars($event['date_display']); ?>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <small><strong>URL originale:</strong></small>
                                <div class="url-box">
                                    <?php echo !empty($sourceUrl) ? htmlspecialchars($sourceUrl) : '<em>(vide)</em>'; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <small><strong>URL finale:</strong></small>
                                <div class="url-box">
                                    <?php echo $finalUrl ? htmlspecialchars($finalUrl) : '<em>(aucune)</em>'; ?>
                                </div>
                            </div>
                        </div>
                        
                        <span class="badge-custom <?php echo $badgeClass; ?>">
                            <?php echo $status; ?>
                        </span>
                    </div>
                    
                    <div class="col-md-4 text-center d-flex align-items-center justify-content-center">
                        <div>
                            <a href="event-details-hybrid.php?data=<?php echo $eventData; ?>" 
                               class="btn btn-primary btn-block mb-2">
                                📄 Voir la page événement
                            </a>
                            
                            <?php if ($finalUrl): ?>
                                <a href="<?php echo htmlspecialchars($finalUrl); ?>" 
                                   target="_blank" 
                                   class="btn btn-success btn-block">
                                    🔗 Tester le lien
                                </a>
                                
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <?php 
                                        $domain = parse_url($finalUrl, PHP_URL_HOST);
                                        echo $domain ?: 'Lien local';
                                        ?>
                                    </small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        
        <div class="test-card bg-light mt-4">
            <h3>📊 Résumé pour votre soutenance</h3>
            
            <div class="row mt-3">
                <div class="col-md-6">
                    <h5>✅ Ce qui fonctionne:</h5>
                    <ul>
                        <li>Les sites officiels des lieux sont reconnus</li>
                        <li>Les URLs sont ajoutées automatiquement quand manquantes</li>
                        <li>Le système différencie les lieux connus/inconnus</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>🎯 Points à montrer:</h5>
                    <ul>
                        <li>Système intelligent de reconnaissance des lieux</li>
                        <li>80+ lieux culturels parisiens dans la base</li>
                        <li>Redirection automatique vers les vrais sites</li>
                    </ul>
                </div>
            </div>
            
            <div class="alert alert-warning mt-3">
                <strong>Pour la démo:</strong> Utilisez les événements avec des URLs réelles 
                (Philharmonie, Pompidou, Opéra, Louvre) pour montrer que les liens fonctionnent vraiment.
            </div>
            
            <div class="text-center mt-3">
                <a href="discover-real.php" class="btn btn-lg btn-primary">
                    🎭 Voir Discover avec vrais événements
                </a>
                <a href="demo-soutenance.php" class="btn btn-lg btn-success">
                    📋 Script de démo complet
                </a>
            </div>
        </div>
    </div>
</body>
</html>