<?php
require_once 'services/VenueLinksService.php';

// Tester quelques lieux
$venues = [
    'Le Sunset',
    'Philharmonie de Paris',
    'Opéra Garnier',
    'Musée du Louvre',
    'Théâtre du Châtelet',
    'Centre Pompidou',
    'La Cigale',
    'Moulin Rouge',
    'Cinémathèque Française',
    'Le 104'
];

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Test Liens Venues</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Test du Service VenueLinks</h1>
        <p>Ce service trouve automatiquement les sites officiels des lieux culturels parisiens.</p>
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Lieu</th>
                    <th>URL Trouvée</th>
                    <th>Type</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($venues as $venue): ?>
                    <?php $info = VenueLinksService::getVenueInfo($venue); ?>
                    <tr>
                        <td><?php echo $venue; ?></td>
                        <td>
                            <?php if ($info['verified']): ?>
                                <span class="badge bg-success">✓ Vérifié</span>
                            <?php endif; ?>
                            <small><?php echo $info['url']; ?></small>
                        </td>
                        <td>
                            <span class="badge bg-secondary"><?php echo $info['type']; ?></span>
                        </td>
                        <td>
                            <a href="<?php echo $info['url']; ?>" target="_blank" class="btn btn-sm btn-primary">
                                Visiter
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <h3 class="mt-5">Exemple d'événement avec lien automatique</h3>
        <?php
        // Créer un événement de test
        $testEvent = [
            'title' => 'Concert Jazz au Sunset',
            'venue_name' => 'Le Sunset',
            'description' => 'Soirée jazz exceptionnelle',
            'category' => 'Musique',
            'city' => 'Paris',
            'date_display' => '30 Août 2025',
            'price' => 25
        ];
        
        $encoded = base64_encode(json_encode($testEvent));
        ?>
        
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?php echo $testEvent['title']; ?></h5>
                <p class="card-text">Lieu : <?php echo $testEvent['venue_name']; ?></p>
                <a href="event-details-hybrid.php?data=<?php echo $encoded; ?>" 
                   class="btn btn-primary">
                    Voir l'événement
                </a>
                <p class="mt-2 text-muted">
                    <small>Le bouton "Plus d'infos" redirigera automatiquement vers : 
                    <?php echo VenueLinksService::getVenueUrl($testEvent['venue_name']); ?></small>
                </p>
            </div>
        </div>
        
        <div class="alert alert-info mt-4">
            <h5>Comment ça marche ?</h5>
            <ul>
                <li>Le service VenueLinksService contient une base de données des principaux lieux culturels parisiens</li>
                <li>Quand un utilisateur clique sur "Plus d'infos" sur un événement</li>
                <li>Le système trouve automatiquement le site officiel du lieu</li>
                <li>L'utilisateur est redirigé vers le vrai site (ex: sunset-sunside.com pour Le Sunset)</li>
            </ul>
        </div>
    </div>
</body>
</html>