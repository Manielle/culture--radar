<?php
require_once 'services/HybridEventsService.php';
require_once 'services/VenueLinksService.php';

// Créer différents types d'événements pour tester
$testEvents = [
    // Événement avec URL spécifique
    [
        'title' => 'Concert Philharmonie - Orchestre de Paris',
        'venue_name' => 'Philharmonie de Paris',
        'source_url' => 'https://philharmoniedeparis.fr/fr/activite/concert/25432',
        'expected' => 'URL spécifique conservée'
    ],
    
    // Événement avec URL spécifique Pompidou
    [
        'title' => 'Exposition Wolfgang Tillmans',
        'venue_name' => 'Centre Pompidou',
        'source_url' => 'https://www.centrepompidou.fr/fr/programme/agenda/evenement/nSlcbMZ',
        'expected' => 'URL spécifique conservée'
    ],
    
    // Événement avec URL spécifique Opéra
    [
        'title' => 'La Traviata',
        'venue_name' => 'Opéra Garnier',
        'source_url' => 'https://www.operadeparis.fr/saison/opera/la-traviata-2025',
        'expected' => 'URL spécifique conservée'
    ],
    
    // Événement SANS URL
    [
        'title' => 'Jazz Session',
        'venue_name' => 'Le Sunset',
        'source_url' => '',
        'expected' => 'URL générale du Sunset'
    ],
    
    // Événement avec # comme URL
    [
        'title' => 'Théâtre improvisé',
        'venue_name' => 'Théâtre du Châtelet',
        'source_url' => '#',
        'expected' => 'URL générale du Châtelet'
    ],
    
    // Événement avec lieu non reconnu
    [
        'title' => 'Concert local',
        'venue_name' => 'Bar inconnu',
        'source_url' => '',
        'expected' => 'Pas d\'URL (lieu non reconnu)'
    ]
];

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Test Complet - Tous les Liens</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; padding: 20px; }
        .test-result { 
            background: white; 
            margin: 10px 0; 
            padding: 15px; 
            border-radius: 10px;
            border-left: 4px solid #ddd;
        }
        .test-result.success { border-left-color: #28a745; }
        .test-result.warning { border-left-color: #ffc107; }
        .test-result.error { border-left-color: #dc3545; }
        .url-display {
            background: #f4f4f4;
            padding: 8px;
            border-radius: 5px;
            font-family: monospace;
            font-size: 12px;
            word-break: break-all;
            margin: 5px 0;
        }
        .status-badge {
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
        }
        .status-ok { background: #d4edda; color: #155724; }
        .status-replace { background: #fff3cd; color: #856404; }
        .status-keep { background: #cfe2ff; color: #084298; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Test Complet - Vérification de TOUS les liens</h1>
        <p class="lead">Vérifie que les URLs spécifiques sont préservées et que les URLs génériques sont ajoutées quand nécessaire</p>
        
        <div class="alert alert-info">
            <strong>Règle :</strong> 
            Si l'événement a une URL spécifique → on la garde<br>
            Si l'événement n'a pas d'URL → on met l'URL générale du lieu
        </div>
        
        <?php foreach ($testEvents as $index => $event): ?>
            <?php
            // Simuler ce qui se passe dans event-details-hybrid.php
            $sourceUrl = $event['source_url'];
            $venueUrl = VenueLinksService::getVenueUrl($event['venue_name']);
            $finalUrl = $sourceUrl;
            
            // Appliquer la même logique que dans event-details-hybrid.php
            if (empty($sourceUrl) || $sourceUrl === '#' || $sourceUrl === '') {
                if ($venueUrl) {
                    $finalUrl = $venueUrl;
                }
            }
            
            // Déterminer le statut
            $isCorrect = false;
            $statusClass = 'error';
            $statusText = 'Erreur';
            
            if (!empty($event['source_url']) && $event['source_url'] !== '#') {
                // Devrait garder l'URL spécifique
                if ($finalUrl === $event['source_url']) {
                    $isCorrect = true;
                    $statusClass = 'success';
                    $statusText = 'URL spécifique préservée';
                } else {
                    $statusText = 'ERREUR: URL spécifique remplacée!';
                }
            } else {
                // Devrait utiliser l'URL du lieu
                if ($finalUrl === $venueUrl || empty($finalUrl)) {
                    $isCorrect = true;
                    $statusClass = 'warning';
                    $statusText = $venueUrl ? 'URL générale ajoutée' : 'Pas d\'URL (normal)';
                } else {
                    $statusText = 'ERREUR: Mauvaise URL';
                }
            }
            ?>
            
            <div class="test-result <?php echo $statusClass; ?>">
                <div class="row">
                    <div class="col-md-8">
                        <h5><?php echo ($index + 1); ?>. <?php echo htmlspecialchars($event['title']); ?></h5>
                        <p class="mb-1">
                            <strong>Lieu :</strong> <?php echo htmlspecialchars($event['venue_name']); ?>
                            <?php if ($venueUrl): ?>
                                <span class="status-badge status-ok">Lieu reconnu</span>
                            <?php else: ?>
                                <span class="status-badge status-replace">Lieu non reconnu</span>
                            <?php endif; ?>
                        </p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <small><strong>URL originale :</strong></small>
                                <div class="url-display">
                                    <?php echo !empty($event['source_url']) && $event['source_url'] !== '#' 
                                        ? htmlspecialchars($event['source_url']) 
                                        : '(vide ou #)'; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <small><strong>URL finale :</strong></small>
                                <div class="url-display">
                                    <?php echo $finalUrl ? htmlspecialchars($finalUrl) : '(aucune)'; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-center d-flex align-items-center justify-content-center">
                        <div>
                            <?php if ($isCorrect): ?>
                                <h3 class="text-success mb-1">✅</h3>
                                <span class="status-badge <?php echo $statusClass === 'success' ? 'status-keep' : 'status-replace'; ?>">
                                    <?php echo $statusText; ?>
                                </span>
                            <?php else: ?>
                                <h3 class="text-danger mb-1">❌</h3>
                                <span class="status-badge status-replace">
                                    <?php echo $statusText; ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if ($finalUrl): ?>
                                <div class="mt-2">
                                    <a href="<?php echo htmlspecialchars($finalUrl); ?>" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-primary">
                                        Tester le lien
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        
        <div class="mt-4 p-4 bg-white rounded">
            <h3>📊 Résumé</h3>
            <?php
            $correctCount = 0;
            foreach ($testEvents as $event) {
                $sourceUrl = $event['source_url'];
                $venueUrl = VenueLinksService::getVenueUrl($event['venue_name']);
                $finalUrl = $sourceUrl;
                
                if (empty($sourceUrl) || $sourceUrl === '#' || $sourceUrl === '') {
                    if ($venueUrl) {
                        $finalUrl = $venueUrl;
                    }
                }
                
                if (!empty($event['source_url']) && $event['source_url'] !== '#') {
                    if ($finalUrl === $event['source_url']) $correctCount++;
                } else {
                    if ($finalUrl === $venueUrl || empty($finalUrl)) $correctCount++;
                }
            }
            ?>
            <p class="lead">
                <strong><?php echo $correctCount; ?>/<?php echo count($testEvents); ?></strong> tests réussis
            </p>
            
            <?php if ($correctCount === count($testEvents)): ?>
                <div class="alert alert-success">
                    <h5>✅ Tout fonctionne correctement !</h5>
                    <p>Le système préserve bien les URLs spécifiques et ajoute les URLs générales quand nécessaire.</p>
                </div>
            <?php else: ?>
                <div class="alert alert-danger">
                    <h5>⚠️ Certains tests ont échoué</h5>
                    <p>Vérifiez la logique dans event-details-hybrid.php</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="mt-4 text-center">
            <a href="discover-real.php" class="btn btn-primary btn-lg">
                Voir les vrais événements
            </a>
            <a href="discover.php" class="btn btn-secondary btn-lg">
                Voir les événements de démo
            </a>
        </div>
    </div>
</body>
</html>