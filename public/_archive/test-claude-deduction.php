<?php
/**
 * Test de la capacité de Claude à déduire les informations manquantes
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/services/HybridEventsService.php';

// Exemples de résultats Google typiques avec infos incomplètes
$testResults = [
    [
        'title' => 'Festival d\'Auvers-sur-Oise',
        'snippet' => 'Le festival revient pour sa 43e édition. Musique classique et concerts...',
        'link' => 'https://www.festival-auvers.com',
        'source' => 'test'
    ],
    [
        'title' => 'Jazz à la Villette 2025',
        'snippet' => 'Le rendez-vous incontournable du jazz revient à la rentrée...',
        'link' => 'https://jazzalavillette.com',
        'source' => 'test'
    ],
    [
        'title' => 'Exposition Monet au Musée de l\'Orangerie',
        'snippet' => 'Une rétrospective exceptionnelle des Nymphéas...',
        'link' => 'https://musee-orangerie.fr/exposition/monet-2025',
        'source' => 'test'
    ],
    [
        'title' => 'Concert à l\'Olympia - Septembre 2025',
        'snippet' => 'Programmation de rentrée avec des artistes internationaux...',
        'link' => 'https://olympiahall.com/agenda',
        'source' => 'test'
    ]
];

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Test Déduction Claude</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h1>🤖 Test de Déduction Claude</h1>
    <p class="lead">Test de la capacité de Claude à enrichir et déduire les informations manquantes</p>
    
    <div class="row">
        <div class="col-md-6">
            <h3>Données d'entrée (incomplètes)</h3>
            <div class="card">
                <div class="card-body">
                    <pre class="mb-0"><?php echo json_encode($testResults, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?></pre>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <h3>Résultats enrichis par Claude</h3>
            <?php
            // Appeler le service d'enrichissement
            $enriched = HybridEventsService::enrichWithClaude($testResults, 'Paris');
            
            if (!empty($enriched)) {
                foreach ($enriched as $event) {
                    ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($event['title'] ?? 'Sans titre'); ?></h5>
                            
                            <div class="mb-2">
                                <?php if (isset($event['confidence_level'])): ?>
                                    <span class="badge bg-<?php echo $event['confidence_level'] == 'high' ? 'success' : ($event['confidence_level'] == 'medium' ? 'warning' : 'danger'); ?>">
                                        Confiance: <?php echo $event['confidence_level']; ?>
                                    </span>
                                <?php endif; ?>
                                
                                <span class="badge bg-primary">
                                    <?php echo htmlspecialchars($event['category'] ?? 'Culture'); ?>
                                </span>
                            </div>
                            
                            <ul class="list-unstyled">
                                <li><strong>📅 Date:</strong> <?php echo htmlspecialchars($event['date_display'] ?? 'Non spécifié'); ?></li>
                                <li><strong>📍 Lieu:</strong> <?php echo htmlspecialchars($event['venue_name'] ?? 'Non spécifié'); ?></li>
                                <?php if (!empty($event['address'])): ?>
                                    <li><strong>🏠 Adresse:</strong> <?php echo htmlspecialchars($event['address']); ?></li>
                                <?php endif; ?>
                                <li><strong>💰 Prix:</strong> 
                                    <?php 
                                    if ($event['is_free'] ?? false) {
                                        echo 'Gratuit';
                                    } elseif (!empty($event['price'])) {
                                        echo $event['price'] . '€';
                                    } else {
                                        echo 'Non spécifié';
                                    }
                                    ?>
                                </li>
                                <li><strong>🔗 URL:</strong> 
                                    <a href="<?php echo htmlspecialchars($event['source_url'] ?? '#'); ?>" target="_blank">
                                        <?php echo htmlspecialchars(substr($event['source_url'] ?? 'N/A', 0, 50)); ?>...
                                    </a>
                                </li>
                            </ul>
                            
                            <?php if (!empty($event['notes'])): ?>
                                <div class="alert alert-info mb-0">
                                    <small><strong>Notes:</strong> <?php echo htmlspecialchars($event['notes']); ?></small>
                                </div>
                            <?php endif; ?>
                            
                            <p class="card-text mt-2">
                                <small><?php echo htmlspecialchars($event['description'] ?? ''); ?></small>
                            </p>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="alert alert-warning">Aucun résultat enrichi</div>';
            }
            ?>
        </div>
    </div>
    
    <div class="mt-4">
        <h3>Capacités de déduction attendues:</h3>
        <ul>
            <li><strong>Festival d'Auvers-sur-Oise:</strong> Devrait déduire les dates (mars-septembre), le lieu exact (Auvers-sur-Oise, Val d'Oise)</li>
            <li><strong>Jazz à la Villette:</strong> Devrait déduire fin août/début septembre, Parc de la Villette/Philharmonie</li>
            <li><strong>Exposition Monet:</strong> Devrait déduire l'adresse du Musée de l'Orangerie (Jardin des Tuileries)</li>
            <li><strong>Concert Olympia:</strong> Devrait déduire l'adresse exacte (28 Boulevard des Capucines, 75009)</li>
        </ul>
    </div>
</div>
</body>
</html>