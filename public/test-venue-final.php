<?php
require_once 'services/KnownEventsData.php';
require_once 'services/VenueLinksService.php';

// Récupérer quelques événements
$events = KnownEventsData::getKnownEvents('Paris');
$testEvents = array_slice($events, 0, 5);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Test Final - Liens Venues</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .event-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            margin: 10px 0;
            background: #f9f9f9;
        }
        .url-info {
            background: #e9ecef;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            font-family: monospace;
            font-size: 12px;
        }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>🎭 Test Final - Système de Liens Automatiques</h1>
        <p class="lead">Vérification que les événements redirigent vers les bons sites officiels</p>
        
        <div class="alert alert-info">
            <h5>Comment ça marche :</h5>
            <ol>
                <li>L'événement affiche le lieu (ex: "Le Sunset")</li>
                <li>Le système trouve automatiquement le site officiel (sunset-sunside.com)</li>
                <li>Le bouton "Plus d'infos" redirige vers ce site</li>
            </ol>
        </div>
        
        <h2 class="mt-4">Événements de test :</h2>
        
        <?php foreach ($testEvents as $event): ?>
            <div class="event-card">
                <h4><?php echo htmlspecialchars($event['title']); ?></h4>
                <p>
                    <strong>Lieu :</strong> <?php echo htmlspecialchars($event['venue_name']); ?><br>
                    <strong>Catégorie :</strong> <?php echo htmlspecialchars($event['category']); ?><br>
                    <strong>Date :</strong> <?php echo htmlspecialchars($event['date_display']); ?>
                </p>
                
                <div class="url-info">
                    <?php 
                    $venueUrl = VenueLinksService::getVenueUrl($event['venue_name']);
                    $sourceUrl = $event['source_url'] ?? '#';
                    $isOfficial = $venueUrl && strpos($venueUrl, 'google.com') === false;
                    ?>
                    
                    <strong>Analyse du lieu "<?php echo $event['venue_name']; ?>" :</strong><br>
                    
                    <?php if ($isOfficial): ?>
                        <span class="success">✅ Site officiel trouvé !</span><br>
                        URL trouvée : <a href="<?php echo $venueUrl; ?>" target="_blank"><?php echo $venueUrl; ?></a><br>
                    <?php else: ?>
                        <span class="error">❌ Pas de site officiel dans la base</span><br>
                        URL par défaut : <?php echo $venueUrl ?: 'Aucune'; ?><br>
                    <?php endif; ?>
                    
                    <br>
                    <strong>Dans l'événement :</strong><br>
                    source_url = <?php echo $sourceUrl; ?><br>
                    detail_url = <?php echo substr($event['detail_url'], 0, 50); ?>...
                </div>
                
                <div class="mt-3">
                    <a href="<?php echo $event['detail_url']; ?>" class="btn btn-primary">
                        Voir l'événement complet
                    </a>
                    
                    <?php if ($sourceUrl !== '#'): ?>
                        <a href="<?php echo $sourceUrl; ?>" target="_blank" class="btn btn-success">
                            Tester le lien direct
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        
        <div class="alert alert-success mt-4">
            <h5>✅ Lieux reconnus automatiquement :</h5>
            <ul>
                <li>Le Sunset → sunset-sunside.com</li>
                <li>Accor Arena → accorarenaareneparis.com</li>
                <li>Stade de France → stadefrance.com</li>
                <li>Parc de la Villette → lavillette.com</li>
                <li>Philharmonie de Paris → philharmoniedeparis.fr</li>
                <li>Opéra Garnier → operadeparis.fr</li>
                <li>... et 80+ autres lieux culturels parisiens</li>
            </ul>
        </div>
        
        <div class="text-center mt-4">
            <h3>Pour votre soutenance :</h3>
            <p>Quand vous cliquez sur "Plus d'infos" sur un événement,<br>
            vous êtes automatiquement redirigé vers le vrai site du lieu !</p>
            
            <a href="discover.php" class="btn btn-lg btn-primary mt-3">
                Retour à Discover
            </a>
        </div>
    </div>
</body>
</html>