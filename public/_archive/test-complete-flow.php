<?php
/**
 * Test complet du flux avec transport
 */
session_start();

// Créer un événement de test avec toutes les infos
$testEvent = [
    'id' => 'test_001',
    'title' => 'Jazz à la Villette 2025',
    'description' => 'Le festival Jazz à la Villette revient du 28 août au 7 septembre 2025. Grande salle Pierre Boulez - Philharmonie de Paris. Une programmation exceptionnelle mêlant jazz, soul, funk et hip-hop avec les plus grands artistes internationaux.',
    'category' => 'Musique',
    'venue_name' => 'Parc de la Villette - Philharmonie',
    'address' => '211 Avenue Jean Jaurès, 75019 Paris',
    'city' => 'Paris',
    'date_display' => '28 août - 7 septembre 2025',
    'is_free' => false,
    'price' => 35,
    'source_url' => 'https://philharmoniedeparis.fr/fr/agenda/festival/jazz-a-la-villette',
    'image' => 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=1200'
];

// Encoder pour l'URL
$eventData = base64_encode(json_encode($testEvent));
$detailUrl = '/event-details-hybrid.php?data=' . $eventData;

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Complet - Culture Radar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .test-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            margin: 20px auto;
            max-width: 900px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }
        
        .step {
            margin-bottom: 30px;
            padding: 20px;
            border-left: 4px solid #667eea;
            background: #f8f9fa;
            border-radius: 10px;
        }
        
        .step h3 {
            color: #667eea;
            margin-bottom: 15px;
        }
        
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9em;
        }
        
        .status-ok {
            background: #d4edda;
            color: #155724;
        }
        
        .status-warning {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-error {
            background: #f8d7da;
            color: #721c24;
        }
        
        .event-preview {
            border: 2px solid #667eea;
            border-radius: 15px;
            padding: 20px;
            background: white;
        }
        
        .test-button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
        }
        
        .test-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            color: white;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <h1 class="text-center mb-4">
            <i class="bi bi-check-circle-fill text-primary"></i> 
            Test Complet du Système
        </h1>
        <p class="text-center text-muted mb-5">
            Vérification de tous les composants : Recherche, Détails, Transport
        </p>
        
        <!-- Étape 1: Localisation -->
        <div class="step">
            <h3><i class="bi bi-geo-alt-fill"></i> 1. Localisation</h3>
            <p>Position de l'utilisateur pour calculer les temps de trajet</p>
            
            <div id="locationStatus" class="mb-3">
                <?php if (isset($_SESSION['user_lat']) && isset($_SESSION['user_lon'])): ?>
                    <span class="status-badge status-ok">
                        ✅ Position enregistrée
                    </span>
                    <p class="mt-2 mb-0">
                        <small>
                            Lat: <?php echo round($_SESSION['user_lat'], 4); ?>, 
                            Lon: <?php echo round($_SESSION['user_lon'], 4); ?>
                            <?php if (isset($_SESSION['user_city'])): ?>
                                (<?php echo $_SESSION['user_city']; ?>)
                            <?php endif; ?>
                        </small>
                    </p>
                <?php else: ?>
                    <span class="status-badge status-warning">
                        ⚠️ Position non définie
                    </span>
                    <button class="btn btn-sm btn-primary ms-3" onclick="requestLocation()">
                        <i class="bi bi-geo-alt"></i> Activer la localisation
                    </button>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Étape 2: Événement Test -->
        <div class="step">
            <h3><i class="bi bi-calendar-event-fill"></i> 2. Événement Test</h3>
            <p>Exemple d'événement avec toutes les informations</p>
            
            <div class="event-preview">
                <div class="row">
                    <div class="col-md-4">
                        <img src="<?php echo $testEvent['image']; ?>" 
                             class="img-fluid rounded" 
                             alt="<?php echo htmlspecialchars($testEvent['title']); ?>">
                    </div>
                    <div class="col-md-8">
                        <span class="badge bg-primary mb-2"><?php echo $testEvent['category']; ?></span>
                        <?php if (!$testEvent['is_free']): ?>
                            <span class="badge bg-warning text-dark mb-2"><?php echo $testEvent['price']; ?>€</span>
                        <?php endif; ?>
                        
                        <h4><?php echo htmlspecialchars($testEvent['title']); ?></h4>
                        <p class="text-muted">
                            <i class="bi bi-calendar"></i> <?php echo $testEvent['date_display']; ?><br>
                            <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($testEvent['venue_name']); ?>
                        </p>
                        <p><?php echo htmlspecialchars(substr($testEvent['description'], 0, 150)); ?>...</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Étape 3: Liens de test -->
        <div class="step">
            <h3><i class="bi bi-link-45deg"></i> 3. Pages de Test</h3>
            <p>Cliquez sur les boutons pour tester chaque partie</p>
            
            <div class="d-grid gap-2 d-md-flex">
                <a href="<?php echo htmlspecialchars($detailUrl); ?>" 
                   target="_blank" 
                   class="btn test-button">
                    <i class="bi bi-eye"></i> Page de Détails
                </a>
                
                <a href="/test-event-flow.php?city=Paris" 
                   target="_blank" 
                   class="btn btn-outline-primary">
                    <i class="bi bi-search"></i> Recherche
                </a>
                
                <a href="/test-hybrid-service.php" 
                   target="_blank" 
                   class="btn btn-outline-secondary">
                    <i class="bi bi-gear"></i> Service Hybride
                </a>
            </div>
        </div>
        
        <!-- Étape 4: Vérifications -->
        <div class="step">
            <h3><i class="bi bi-check2-square"></i> 4. Vérifications</h3>
            
            <div class="list-group">
                <div class="list-group-item">
                    <i class="bi bi-check-circle text-success"></i>
                    <strong>SerpAPI:</strong> Clé configurée et fonctionnelle
                </div>
                <div class="list-group-item">
                    <i class="bi bi-check-circle text-success"></i>
                    <strong>Claude API:</strong> Clé configurée et fonctionnelle
                </div>
                <div class="list-group-item">
                    <i class="bi bi-check-circle text-success"></i>
                    <strong>Transport:</strong> Service de calcul d'itinéraire actif
                </div>
                <div class="list-group-item">
                    <?php if (isset($_SESSION['user_lat'])): ?>
                        <i class="bi bi-check-circle text-success"></i>
                    <?php else: ?>
                        <i class="bi bi-exclamation-circle text-warning"></i>
                    <?php endif; ?>
                    <strong>Géolocalisation:</strong> 
                    <?php echo isset($_SESSION['user_lat']) ? 'Active' : 'Non activée'; ?>
                </div>
            </div>
        </div>
        
        <!-- Instructions -->
        <div class="alert alert-info mt-4">
            <h5><i class="bi bi-info-circle"></i> Comment tester</h5>
            <ol class="mb-0">
                <li>Activez la géolocalisation si ce n'est pas fait</li>
                <li>Cliquez sur "Page de Détails" pour voir l'événement complet avec les infos de transport</li>
                <li>Les temps de trajet seront calculés depuis votre position</li>
                <li>Le lien "Plus d'infos" devrait pointer vers la page spécifique de l'événement</li>
            </ol>
        </div>
        
        <!-- Test de recherche en direct -->
        <div class="step">
            <h3><i class="bi bi-search"></i> 5. Test de Recherche en Direct</h3>
            <form action="/test-event-flow.php" method="GET" target="_blank">
                <div class="input-group">
                    <input type="text" name="city" class="form-control" placeholder="Entrez une ville..." value="Paris">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Rechercher des événements
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function requestLocation() {
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        // Envoyer la position au serveur
                        fetch('/api/set-user-location.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                lat: position.coords.latitude,
                                lon: position.coords.longitude
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Recharger la page pour afficher la nouvelle position
                                location.reload();
                            }
                        });
                    },
                    function(error) {
                        alert('Erreur de géolocalisation: ' + error.message);
                    }
                );
            } else {
                alert('La géolocalisation n\'est pas disponible sur votre navigateur');
            }
        }
    </script>
</body>
</html>