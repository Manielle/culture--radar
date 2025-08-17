<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/classes/Auth.php';

$auth = new Auth();
$isLoggedIn = $auth->isLoggedIn();
$user = $isLoggedIn ? $auth->getCurrentUser() : null;

$searchQuery = isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '';
$filterType = isset($_GET['filter']) ? htmlspecialchars($_GET['filter']) : 'all';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche - Culture Radar</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <?php include 'includes/favicon.php'; ?>
    
    <style>
        .search-container {
            min-height: 100vh;
            background: #0a0a0f;
            padding-top: 80px;
        }
        
        .search-header {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            text-align: center;
        }
        
        .search-box {
            max-width: 600px;
            margin: 0 auto 3rem;
            position: relative;
        }
        
        .search-input {
            width: 100%;
            padding: 1rem 3rem 1rem 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: white;
            font-size: 1.1rem;
            transition: all 0.3s;
        }
        
        .search-input:focus {
            outline: none;
            border-color: #667eea;
            background: rgba(255, 255, 255, 0.08);
        }
        
        .search-btn {
            position: absolute;
            right: 0.5rem;
            top: 50%;
            transform: translateY(-50%);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            color: white;
            cursor: pointer;
            transition: transform 0.3s;
        }
        
        .search-btn:hover {
            transform: translateY(-50%) scale(1.05);
        }
        
        .results-section {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .results-header {
            margin-bottom: 2rem;
            color: white;
        }
        
        .results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .result-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .result-card:hover {
            transform: translateY(-4px);
            border-color: rgba(102, 126, 234, 0.5);
            background: rgba(255, 255, 255, 0.08);
        }
        
        .result-title {
            color: white;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .result-meta {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .no-results {
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
            padding: 3rem;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="search-container">
        <div class="search-header">
            <h1 style="color: white; font-size: 2.5rem; margin-bottom: 1rem;">Rechercher un événement</h1>
            <p style="color: rgba(255, 255, 255, 0.6); font-size: 1.1rem;">Trouvez les événements culturels qui vous correspondent</p>
        </div>
        
        <div class="search-box">
            <form method="GET" action="">
                <input type="text" 
                       name="q" 
                       class="search-input" 
                       placeholder="Rechercher par nom, lieu, catégorie..." 
                       value="<?php echo $searchQuery; ?>"
                       autofocus>
                <input type="hidden" name="filter" value="<?php echo $filterType; ?>">
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
        
        <!-- Filtres rapides -->
        <div class="quick-filters" style="text-align: center; margin-bottom: 2rem;">
            <style>
                .filter-chip {
                    background: rgba(255, 255, 255, 0.05);
                    border: 1px solid rgba(255, 255, 255, 0.1);
                    color: white;
                    padding: 0.5rem 1rem;
                    margin: 0.25rem;
                    border-radius: 20px;
                    cursor: pointer;
                    transition: all 0.3s;
                    font-size: 0.9rem;
                }
                .filter-chip:hover {
                    background: rgba(255, 255, 255, 0.1);
                    border-color: #667eea;
                }
                .filter-chip.active {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    border-color: transparent;
                }
            </style>
            <button class="filter-chip <?php echo $filterType === 'all' ? 'active' : ''; ?>" 
                    onclick="applyFilter('all')">Tout</button>
            <button class="filter-chip <?php echo $filterType === 'today' ? 'active' : ''; ?>" 
                    onclick="applyFilter('today')">Aujourd'hui</button>
            <button class="filter-chip <?php echo $filterType === 'weekend' ? 'active' : ''; ?>" 
                    onclick="applyFilter('weekend')">Ce week-end</button>
            <button class="filter-chip <?php echo $filterType === 'free' ? 'active' : ''; ?>" 
                    onclick="applyFilter('free')">Gratuit</button>
            <button class="filter-chip <?php echo $filterType === 'nearby' ? 'active' : ''; ?>" 
                    onclick="applyFilter('nearby')">À proximité</button>
        </div>
        
        <div class="results-section">
            <?php if ($searchQuery || $filterType !== 'all'): ?>
                <div class="results-header">
                    <?php if ($searchQuery): ?>
                        <h2>Résultats pour "<?php echo $searchQuery; ?>"</h2>
                    <?php else: ?>
                        <h2>Événements <?php 
                            switch($filterType) {
                                case 'today': echo "d'aujourd'hui"; break;
                                case 'weekend': echo "du week-end"; break;
                                case 'free': echo "gratuits"; break;
                                case 'nearby': echo "à proximité"; break;
                                default: echo "disponibles";
                            }
                        ?></h2>
                    <?php endif; ?>
                    <p style="color: rgba(255, 255, 255, 0.6);">Recherche en cours...</p>
                </div>
                
                <div class="results-grid" id="searchResults">
                    <!-- Les résultats seront chargés ici -->
                </div>
            <?php else: ?>
                <div class="no-results">
                    <i class="fas fa-search" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                    <p>Commencez à taper pour rechercher des événements ou utilisez les filtres</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="assets/js/main.js"></script>
    <script>
        // Fonction pour appliquer un filtre
        function applyFilter(filter) {
            if (window.eventFilters) {
                window.eventFilters.applyFilter(filter);
            } else {
                const searchQuery = document.querySelector('.search-input').value;
                const params = new URLSearchParams();
                if (searchQuery) params.set('q', searchQuery);
                params.set('filter', filter);
                window.location.href = '?' + params.toString();
            }
        }
        
        <?php if ($searchQuery || $filterType !== 'all'): ?>
        // Initialize filters with search parameters
        document.addEventListener('DOMContentLoaded', () => {
            if (window.eventFilters) {
                window.eventFilters.currentFilter = '<?php echo $filterType; ?>';
                window.eventFilters.loadEvents();
            } else {
                // Fallback to old system
                setTimeout(() => {
            const resultsContainer = document.getElementById('searchResults');
            let mockResults = [
                {
                    title: 'Concert de Jazz<?php echo $searchQuery ? " - " . $searchQuery : ""; ?>',
                    location: 'Paris',
                    date: '15 Février 2024',
                    category: 'Musique',
                    price: 25
                },
                {
                    title: 'Exposition Art Moderne',
                    location: 'Musée d\'Orsay',
                    date: '20 Février 2024',
                    category: 'Exposition',
                    price: 0
                },
                {
                    title: 'Pièce de Théâtre',
                    location: 'Théâtre de la Ville',
                    date: '25 Février 2024',
                    category: 'Théâtre',
                    price: 35
                },
                {
                    title: 'Festival de Danse',
                    location: 'Opéra Garnier',
                    date: '<?php echo date("d/m/Y"); ?>',
                    category: 'Danse',
                    price: 0
                },
                {
                    title: 'Concert Symphonique',
                    location: 'Philharmonie',
                    date: '<?php 
                        $weekend = new DateTime();
                        $weekend->modify('next saturday');
                        echo $weekend->format("d/m/Y");
                    ?>',
                    category: 'Musique',
                    price: 45
                }
            ];
            
            // Appliquer le filtre
            <?php if ($filterType === 'free'): ?>
                mockResults = mockResults.filter(e => e.price === 0);
            <?php elseif ($filterType === 'today'): ?>
                mockResults = mockResults.filter(e => e.date === '<?php echo date("d/m/Y"); ?>');
            <?php elseif ($filterType === 'weekend'): ?>
                // Filtrer pour le week-end
                mockResults = mockResults.slice(3, 5);
            <?php elseif ($filterType === 'nearby'): ?>
                // Simuler les événements à proximité
                mockResults = mockResults.slice(0, 3);
            <?php endif; ?>
            
            if (mockResults.length > 0) {
                resultsContainer.innerHTML = mockResults.map(event => `
                    <div class="result-card" onclick="window.location.href='event-details.php'">
                        <h3 class="result-title">${event.title}</h3>
                        <div class="result-meta">
                            <span><i class="fas fa-tag"></i> ${event.category}</span>
                            <span><i class="fas fa-map-marker-alt"></i> ${event.location}</span>
                            <span><i class="fas fa-calendar"></i> ${event.date}</span>
                            ${event.price === 0 ? 
                                '<span style="color: #48bb78;"><i class="fas fa-gift"></i> Gratuit</span>' : 
                                `<span><i class="fas fa-euro-sign"></i> ${event.price}€</span>`
                            }
                        </div>
                    </div>
                `).join('');
            } else {
                resultsContainer.innerHTML = `
                    <div style="text-align: center; padding: 3rem; color: rgba(255, 255, 255, 0.6);">
                        <i class="fas fa-search" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                        <p>Aucun événement trouvé avec ces critères</p>
                    </div>
                `;
            }
            
            // Mettre à jour le message de résultats
            document.querySelector('.results-header p').innerHTML = 
                `${mockResults.length} événement${mockResults.length > 1 ? 's' : ''} trouvé${mockResults.length > 1 ? 's' : ''}`;
                }, 500);
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>