<?php
/**
 * Test du flux complet : recherche -> carte -> détails
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/services/HybridEventsService.php';
require_once __DIR__ . '/components/event-card-hybrid.php';

$city = $_GET['city'] ?? 'Paris';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Flux Événements - Culture Radar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .main-container {
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            padding: 30px;
            margin-top: 20px;
        }
        
        .search-box {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .loading-spinner {
            display: none;
            text-align: center;
            padding: 50px;
        }
        
        .loading-spinner.active {
            display: block;
        }
        
        .stats-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-center text-white mb-4">
            <h1 class="display-4">🎭 Culture Radar</h1>
            <p class="lead">Découvrez les événements culturels près de chez vous</p>
        </div>
        
        <div class="main-container">
            <!-- Barre de recherche -->
            <div class="search-box">
                <form method="GET" id="searchForm">
                    <div class="row g-3">
                        <div class="col-md-9">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">
                                    <i class="bi bi-geo-alt-fill"></i>
                                </span>
                                <input type="text" 
                                       name="city" 
                                       class="form-control" 
                                       placeholder="Entrez une ville..." 
                                       value="<?php echo htmlspecialchars($city); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-search"></i> Rechercher
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Loading -->
            <div class="loading-spinner" id="loadingSpinner">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <p class="mt-3">Recherche des événements en cours...</p>
            </div>
            
            <!-- Résultats -->
            <div id="results">
                <?php
                // Rechercher les événements
                $startTime = microtime(true);
                $events = HybridEventsService::searchEvents($city, 12);
                $duration = round(microtime(true) - $startTime, 2);
                
                if (!empty($events)):
                ?>
                    <!-- Stats -->
                    <div class="stats-box">
                        <h3><?php echo count($events); ?> événements trouvés</h3>
                        <p class="mb-0">à <?php echo htmlspecialchars($city); ?> • Recherche en <?php echo $duration; ?>s</p>
                    </div>
                    
                    <!-- Grille d'événements -->
                    <div class="row">
                        <?php foreach ($events as $event): ?>
                            <?php renderEventCard($event); ?>
                        <?php endforeach; ?>
                    </div>
                    
                <?php else: ?>
                    <!-- Aucun résultat -->
                    <div class="alert alert-warning text-center">
                        <i class="bi bi-exclamation-triangle fs-1 mb-3 d-block"></i>
                        <h4>Aucun événement trouvé</h4>
                        <p>Nous n'avons pas trouvé d'événements pour <?php echo htmlspecialchars($city); ?>.</p>
                        <p>Essayez avec une autre ville ou vérifiez l'orthographe.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Footer -->
            <div class="text-center mt-5 pt-4 border-top">
                <p class="text-muted">
                    <i class="bi bi-info-circle"></i> 
                    Service hybride utilisant SerpAPI pour la recherche et Claude pour l'analyse
                </p>
                <div class="btn-group" role="group">
                    <a href="test-hybrid-service.php?city=<?php echo urlencode($city); ?>" 
                       class="btn btn-outline-secondary btn-sm">
                        Test Service
                    </a>
                    <a href="discover.php?city=<?php echo urlencode($city); ?>" 
                       class="btn btn-outline-primary btn-sm">
                        Version Discover
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Afficher le spinner pendant le chargement
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            document.getElementById('loadingSpinner').classList.add('active');
            document.getElementById('results').style.opacity = '0.5';
        });
        
        // Effet smooth sur les cartes
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.event-card-hover');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.animation = 'fadeInUp 0.5s ease';
                }, index * 100);
            });
        });
    </script>
    
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</body>
</html>