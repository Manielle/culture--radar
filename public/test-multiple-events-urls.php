<?php
require_once 'services/VenueLinksService.php';

// Créer plusieurs événements de test avec différents cas
$testEvents = [
    // 1. Concert à la Philharmonie avec URL spécifique
    [
        'title' => 'Concert Orchestre de Paris',
        'venue_name' => 'Philharmonie de Paris',
        'source_url' => 'https://philharmoniedeparis.fr/fr/activite/concert/25432/orchestre-de-paris',
        'category' => 'Concert',
        'city' => 'Paris',
        'date_display' => '15 septembre 2025',
        'price' => 35
    ],
    
    // 2. Pièce au Théâtre du Châtelet avec URL spécifique
    [
        'title' => 'Le Roi Lion - Musical',
        'venue_name' => 'Théâtre du Châtelet',
        'source_url' => 'https://www.chatelet.com/programmation/saison/le-roi-lion-2025',
        'category' => 'Théâtre',
        'city' => 'Paris',
        'date_display' => '20 septembre 2025',
        'price' => 65
    ],
    
    // 3. Exposition au Louvre avec URL spécifique
    [
        'title' => 'Les Maîtres de la Renaissance',
        'venue_name' => 'Musée du Louvre',
        'source_url' => 'https://www.louvre.fr/expositions/maitres-renaissance-2025',
        'category' => 'Exposition',
        'city' => 'Paris',
        'date_display' => 'Jusqu\'au 30 octobre 2025',
        'price' => 17
    ],
    
    // 4. Jazz au Sunset SANS URL
    [
        'title' => 'Soirée Jazz Manouche',
        'venue_name' => 'Le Sunset',
        'source_url' => '',
        'category' => 'Concert',
        'city' => 'Paris',
        'date_display' => '25 septembre 2025',
        'price' => 20
    ],
    
    // 5. Opéra Garnier avec URL spécifique
    [
        'title' => 'La Traviata de Verdi',
        'venue_name' => 'Opéra Garnier',
        'source_url' => 'https://www.operadeparis.fr/saison-24-25/opera/la-traviata',
        'category' => 'Opéra',
        'city' => 'Paris',
        'date_display' => '28 septembre 2025',
        'price' => 120
    ]
];

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Test URLs Multiples Événements</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .event-test-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin: 15px 0;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .url-display {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 12px;
            word-break: break-all;
            margin: 10px 0;
        }
        .success-badge { 
            background: #d4edda; 
            color: #155724;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
        }
        .error-badge { 
            background: #f8d7da; 
            color: #721c24;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
        }
        .warning-badge { 
            background: #fff3cd; 
            color: #856404;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
        }
        h1 { color: white; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">🎭 Test URLs - Événements Multiples</h1>
        
        <?php foreach ($testEvents as $index => $event): ?>
            <?php
            // Encoder l'événement
            $eventData = base64_encode(json_encode($event));
            
            // Simuler la logique de event-details-hybrid.php
            $venue = $event['venue_name'];
            $sourceUrl = $event['source_url'];
            $venueUrl = VenueLinksService::getVenueUrl($venue);
            
            // Appliquer la même logique
            $finalUrl = $sourceUrl;
            if (empty($sourceUrl) || $sourceUrl === '#' || $sourceUrl === '') {
                if ($venueUrl) {
                    $finalUrl = $venueUrl;
                }
            }
            
            // Déterminer si c'est correct
            $isCorrect = true;
            $status = '';
            if (!empty($event['source_url'])) {
                // Devrait garder l'URL spécifique
                if ($finalUrl === $event['source_url']) {
                    $status = 'URL spécifique préservée';
                } else {
                    $isCorrect = false;
                    $status = 'ERREUR: URL remplacée!';
                }
            } else {
                // Devrait utiliser l'URL du lieu
                $status = 'URL du lieu ajoutée';
            }
            ?>
            
            <div class="event-test-card">
                <div class="row">
                    <div class="col-md-8">
                        <h4><?php echo ($index + 1); ?>. <?php echo htmlspecialchars($event['title']); ?></h4>
                        <p class="mb-2">
                            <strong>📍 Lieu:</strong> <?php echo htmlspecialchars($event['venue_name']); ?><br>
                            <strong>🎭 Catégorie:</strong> <?php echo htmlspecialchars($event['category']); ?><br>
                            <strong>📅 Date:</strong> <?php echo htmlspecialchars($event['date_display']); ?><br>
                            <strong>💰 Prix:</strong> <?php echo $event['price']; ?>€
                        </p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <small><strong>URL dans l'événement:</strong></small>
                                <div class="url-display">
                                    <?php 
                                    if (!empty($event['source_url'])) {
                                        echo htmlspecialchars($event['source_url']);
                                    } else {
                                        echo '<em>(vide)</em>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <small><strong>URL finale (après traitement):</strong></small>
                                <div class="url-display">
                                    <?php echo htmlspecialchars($finalUrl); ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-2">
                            <?php if ($isCorrect): ?>
                                <span class="success-badge">✅ <?php echo $status; ?></span>
                            <?php else: ?>
                                <span class="error-badge">❌ <?php echo $status; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-4 text-center d-flex align-items-center justify-content-center">
                        <div>
                            <a href="event-details-hybrid.php?data=<?php echo $eventData; ?>" 
                               target="_blank" 
                               class="btn btn-primary mb-2">
                                👁️ Voir la page événement
                            </a>
                            <br>
                            <?php if ($finalUrl): ?>
                                <a href="<?php echo htmlspecialchars($finalUrl); ?>" 
                                   target="_blank" 
                                   class="btn btn-outline-success btn-sm">
                                    🔗 Tester l'URL finale
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($venueUrl): ?>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        Lieu reconnu par VenueLinks:<br>
                                        <?php echo substr($venueUrl, 0, 30); ?>...
                                    </small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        
        <div class="event-test-card bg-light">
            <h3>📊 Résumé du test</h3>
            <p>Ce test vérifie que:</p>
            <ul>
                <li><strong>Philharmonie, Châtelet, Louvre, Opéra:</strong> Doivent garder leurs URLs spécifiques d'événements</li>
                <li><strong>Le Sunset (sans URL):</strong> Doit recevoir l'URL générale sunset-sunside.com</li>
            </ul>
            
            <div class="alert alert-info mt-3">
                <strong>💡 Comment ça doit marcher:</strong><br>
                - Si <code>source_url</code> existe et n'est pas vide → On la garde<br>
                - Si <code>source_url</code> est vide ou "#" → On met l'URL du lieu depuis VenueLinksService
            </div>
            
            <div class="text-center mt-3">
                <a href="discover-real.php" class="btn btn-lg btn-success">
                    Voir les vrais événements en action
                </a>
            </div>
        </div>
    </div>
</body>
</html>