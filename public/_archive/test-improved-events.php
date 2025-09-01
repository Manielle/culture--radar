<?php
/**
 * Test du service amélioré pour trouver de vrais événements
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/services/ImprovedEventsService.php';
require_once __DIR__ . '/components/event-card-hybrid.php';

$city = $_GET['city'] ?? 'Paris';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Événements Réels - Culture Radar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .hero-section {
            background: white;
            border-radius: 30px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }
        
        .search-strategies {
            background: rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 30px;
            color: white;
        }
        
        .strategy-badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 5px 15px;
            border-radius: 20px;
            margin: 5px;
        }
        
        .event-card {
            transition: all 0.3s;
        }
        
        .event-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        
        .confidence-indicator {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }
        
        .confidence-high { background: #28a745; }
        .confidence-medium { background: #ffc107; }
        .confidence-low { background: #dc3545; }
        
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .stats-number {
            font-size: 2em;
            font-weight: bold;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Hero Section -->
        <div class="hero-section">
            <h1 class="text-center mb-4">
                <i class="bi bi-calendar-check-fill text-primary"></i>
                Événements Réels & Vérifiés
            </h1>
            <p class="text-center text-muted mb-4">
                Service amélioré : recherche multi-sources avec enrichissement intelligent
            </p>
            
            <form method="GET" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-9">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text">
                                <i class="bi bi-geo-alt-fill"></i>
                            </span>
                            <input type="text" 
                                   name="city" 
                                   class="form-control" 
                                   placeholder="Ville (Paris, Lyon, Marseille...)" 
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
        
        <!-- Stratégies de recherche -->
        <div class="search-strategies">
            <h4 class="text-center mb-3">🔍 Stratégies de Recherche Active</h4>
            <div class="text-center">
                <span class="strategy-badge">
                    <i class="bi bi-star-fill"></i> Événements Connus
                </span>
                <span class="strategy-badge">
                    <i class="bi bi-shield-check"></i> Sites Fiables
                </span>
                <span class="strategy-badge">
                    <i class="bi bi-calendar-date"></i> Dates Précises
                </span>
                <span class="strategy-badge">
                    <i class="bi bi-robot"></i> Enrichissement IA
                </span>
            </div>
        </div>
        
        <!-- Résultats -->
        <?php
        echo "<div class='text-center text-white mb-3'>";
        echo "<div class='spinner-border' role='status'></div>";
        echo "<p>Recherche en cours sur multiples sources...</p>";
        echo "</div>";
        flush();
        
        $startTime = microtime(true);
        $events = ImprovedEventsService::findRealEvents($city, 12);
        $duration = round(microtime(true) - $startTime, 2);
        
        // Masquer le spinner
        echo "<script>document.querySelector('.spinner-border').parentElement.style.display='none';</script>";
        
        if (!empty($events)):
        ?>
            <!-- Statistiques -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number"><?php echo count($events); ?></div>
                        <div class="text-muted">Événements trouvés</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number"><?php echo $duration; ?>s</div>
                        <div class="text-muted">Temps de recherche</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number">
                            <?php 
                            $highConfidence = count(array_filter($events, fn($e) => ($e['confidence'] ?? '') === 'high'));
                            echo $highConfidence;
                            ?>
                        </div>
                        <div class="text-muted">Haute confiance</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number">
                            <?php 
                            $withDates = count(array_filter($events, fn($e) => !empty($e['date_display']) && $e['date_display'] !== 'Date à confirmer'));
                            echo $withDates;
                            ?>
                        </div>
                        <div class="text-muted">Avec dates</div>
                    </div>
                </div>
            </div>
            
            <!-- Grille d'événements -->
            <div class="row">
                <?php foreach ($events as $event): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 event-card position-relative">
                            <?php if (isset($event['confidence'])): ?>
                                <div class="confidence-indicator confidence-<?php echo $event['confidence']; ?>" 
                                     title="Confiance: <?php echo $event['confidence']; ?>"></div>
                            <?php endif; ?>
                            
                            <?php
                            // Image par catégorie
                            $categoryImages = [
                                'Musique' => 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=400',
                                'Festival' => 'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?w=400',
                                'Art' => 'https://images.unsplash.com/photo-1561214115-f2f134cc4912?w=400',
                                'Théâtre' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400',
                                'Exposition' => 'https://images.unsplash.com/photo-1554907984-15263bfd63bd?w=400'
                            ];
                            $image = $event['image'] ?? $categoryImages[$event['category'] ?? 'Culture'] ?? 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=400';
                            ?>
                            
                            <img src="<?php echo htmlspecialchars($image); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($event['title'] ?? ''); ?>" 
                                 style="height: 200px; object-fit: cover;">
                            
                            <div class="card-body">
                                <div class="mb-2">
                                    <span class="badge bg-primary">
                                        <?php echo htmlspecialchars($event['category'] ?? 'Culture'); ?>
                                    </span>
                                    <?php if ($event['is_free'] ?? false): ?>
                                        <span class="badge bg-success">Gratuit</span>
                                    <?php elseif (!empty($event['price'])): ?>
                                        <span class="badge bg-warning text-dark"><?php echo $event['price']; ?>€</span>
                                    <?php endif; ?>
                                </div>
                                
                                <h5 class="card-title">
                                    <?php echo htmlspecialchars($event['title'] ?? 'Événement'); ?>
                                </h5>
                                
                                <p class="text-muted small mb-2">
                                    <i class="bi bi-calendar-event"></i> 
                                    <strong><?php echo htmlspecialchars($event['date_display'] ?? 'Date à confirmer'); ?></strong><br>
                                    <i class="bi bi-geo-alt"></i> 
                                    <?php echo htmlspecialchars($event['venue_name'] ?? 'Lieu à confirmer'); ?>
                                    <?php if (!empty($event['address'])): ?>
                                        <br><small><?php echo htmlspecialchars($event['address']); ?></small>
                                    <?php endif; ?>
                                </p>
                                
                                <p class="card-text">
                                    <?php 
                                    $desc = $event['description'] ?? '';
                                    echo htmlspecialchars(substr($desc, 0, 100));
                                    if (strlen($desc) > 100) echo '...';
                                    ?>
                                </p>
                                
                                <div class="mt-auto">
                                    <?php if (!empty($event['detail_url'])): ?>
                                        <a href="<?php echo htmlspecialchars($event['detail_url']); ?>" 
                                           class="btn btn-primary btn-sm w-100">
                                            <i class="bi bi-eye"></i> Voir les détails
                                        </a>
                                    <?php elseif (!empty($event['source_url'])): ?>
                                        <a href="<?php echo htmlspecialchars($event['source_url']); ?>" 
                                           target="_blank" 
                                           class="btn btn-outline-primary btn-sm w-100">
                                            <i class="bi bi-box-arrow-up-right"></i> Plus d'infos
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
        <?php else: ?>
            <div class="alert alert-warning text-center">
                <i class="bi bi-exclamation-triangle fs-1 mb-3 d-block"></i>
                <h4>Aucun événement trouvé</h4>
                <p>Essayez avec une autre ville ou vérifiez l'orthographe.</p>
            </div>
        <?php endif; ?>
        
        <!-- Debug Info -->
        <div class="mt-5">
            <details>
                <summary class="text-white">🔧 Informations de débogage</summary>
                <div class="bg-dark text-white p-3 rounded mt-2">
                    <p>Ville recherchée : <?php echo htmlspecialchars($city); ?></p>
                    <p>Durée de recherche : <?php echo $duration; ?> secondes</p>
                    <p>Nombre d'événements : <?php echo count($events); ?></p>
                    <p>Service utilisé : ImprovedEventsService</p>
                    <p>Stratégies : Événements connus + Sites fiables + Dates précises + Enrichissement Claude</p>
                </div>
            </details>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>