<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/classes/Auth.php';

$auth = new Auth();
$isLoggedIn = false;
$user = null;

// Désactiver la vérification de connexion temporairement
// if (!$auth->isLoggedIn()) {
//     header('Location: login.php');
//     exit();
// }
// $user = $auth->getCurrentUser();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explorer - CultureRadar</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #f7fafc;
            color: #2d3748;
        }
        
        .navbar {
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .nav-brand {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
        }
        
        .nav-menu {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        
        .nav-item {
            color: #4a5568;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav-item:hover, .nav-item.active {
            color: #667eea;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .search-section {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        .search-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .search-input {
            flex: 1;
            padding: 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .search-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .search-btn {
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
        }
        
        .search-btn:hover {
            transform: translateY(-2px);
        }
        
        .filters {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .filter-label {
            font-size: 0.9rem;
            font-weight: 500;
            color: #4a5568;
        }
        
        .filter-select {
            padding: 0.5rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .filter-select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .category-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        .category-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }
        
        .category-card.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .category-icon {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        
        .category-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .category-count {
            font-size: 0.85rem;
            opacity: 0.8;
        }
        
        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .results-count {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2d3748;
        }
        
        .sort-options {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
        }
        
        .event-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .event-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }
        
        .event-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
        }
        
        .event-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255,255,255,0.95);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #764ba2;
        }
        
        .event-content {
            padding: 1.5rem;
        }
        
        .event-category {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background: #f0f4ff;
            color: #667eea;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 0.75rem;
        }
        
        .event-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #2d3748;
        }
        
        .event-info {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            color: #718096;
            font-size: 0.9rem;
        }
        
        .event-info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .map-container {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            margin-top: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #718096;
        }
        
        @media (max-width: 768px) {
            .nav-menu {
                display: none;
            }
            
            .events-grid {
                grid-template-columns: 1fr;
            }
            
            .categories-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="nav-brand"><img src="logo-192x192.png" alt="Culture Radar Logo" style="width: 30px; height: 30px; vertical-align: middle; margin-right: 8px;">CultureRadar</a>
            <div class="nav-menu">
                <a href="dashboard.php" class="nav-item">Découvrir</a>
                <a href="my-events.php" class="nav-item">Mes événements</a>
                <a href="calendar.php" class="nav-item">Calendrier</a>
                <a href="explore.php" class="nav-item active">Explorer</a>
            </div>
            <div class="user-menu">
                <a href="notifications.php" class="nav-item">
                    <i class="fas fa-bell"></i>
                </a>
                <div class="user-avatar">
                    <?php echo strtoupper(substr($user['name'] ?? 'U', 0, 1)); ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Search Section -->
        <div class="search-section">
            <div class="search-bar">
                <input type="text" class="search-input" placeholder="Rechercher un événement, artiste, lieu..." id="searchInput">
                <button class="search-btn" onclick="performSearch()">
                    <i class="fas fa-search"></i> Rechercher
                </button>
            </div>
            
            <div class="filters">
                <div class="filter-group">
                    <label class="filter-label">Lieu</label>
                    <select class="filter-select" id="locationFilter">
                        <option value="">Tous les lieux</option>
                        <option value="paris">Paris</option>
                        <option value="lyon">Lyon</option>
                        <option value="marseille">Marseille</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">Date</label>
                    <select class="filter-select" id="dateFilter">
                        <option value="">Toutes les dates</option>
                        <option value="today">Aujourd'hui</option>
                        <option value="tomorrow">Demain</option>
                        <option value="week">Cette semaine</option>
                        <option value="month">Ce mois</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">Prix</label>
                    <select class="filter-select" id="priceFilter">
                        <option value="">Tous les prix</option>
                        <option value="free">Gratuit</option>
                        <option value="0-20">0-20€</option>
                        <option value="20-50">20-50€</option>
                        <option value="50+">50€+</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">Distance</label>
                    <select class="filter-select" id="distanceFilter">
                        <option value="">Toute distance</option>
                        <option value="5">< 5 km</option>
                        <option value="10">< 10 km</option>
                        <option value="25">< 25 km</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Categories -->
        <div class="categories-grid">
            <div class="category-card" data-category="musique">
                <div class="category-icon">🎵</div>
                <div class="category-name">Musique</div>
                <div class="category-count">124 événements</div>
            </div>
            <div class="category-card" data-category="theatre">
                <div class="category-icon">🎭</div>
                <div class="category-name">Théâtre</div>
                <div class="category-count">87 événements</div>
            </div>
            <div class="category-card" data-category="exposition">
                <div class="category-icon">🖼️</div>
                <div class="category-name">Exposition</div>
                <div class="category-count">56 événements</div>
            </div>
            <div class="category-card" data-category="cinema">
                <div class="category-icon">🎬</div>
                <div class="category-name">Cinéma</div>
                <div class="category-count">42 événements</div>
            </div>
            <div class="category-card" data-category="danse">
                <div class="category-icon">=</div>
                <div class="category-name">Danse</div>
                <div class="category-count">31 événements</div>
            </div>
            <div class="category-card" data-category="conference">
                <div class="category-icon">🎤</div>
                <div class="category-name">Conférence</div>
                <div class="category-count">28 événements</div>
            </div>
            <div class="category-card" data-category="festival">
                <div class="category-icon">🎪</div>
                <div class="category-name">Festival</div>
                <div class="category-count">15 événements</div>
            </div>
            <div class="category-card" data-category="litterature">
                <div class="category-icon">📚</div>
                <div class="category-name">Littérature</div>
                <div class="category-count">19 événements</div>
            </div>
        </div>

        <!-- Results -->
        <div class="results-header">
            <div class="results-count">
                <span id="resultsCount">402</span> événements trouvés
            </div>
            <div class="sort-options">
                <label>Trier par:</label>
                <select class="filter-select" id="sortBy">
                    <option value="relevance">Pertinence</option>
                    <option value="date">Date</option>
                    <option value="price">Prix</option>
                    <option value="distance">Distance</option>
                </select>
            </div>
        </div>

        <div class="events-grid" id="eventsGrid">
            <!-- Events will be loaded here -->
        </div>

        <!-- Map -->
        <div class="map-container">
            <i class="fas fa-map-marked-alt" style="font-size: 3rem; margin-bottom: 1rem;"></i>
            <div>Carte interactive bientôt disponible</div>
        </div>
    </div>

    <script>
        // Category selection
        document.querySelectorAll('.category-card').forEach(card => {
            card.addEventListener('click', function() {
                this.classList.toggle('active');
                filterEvents();
            });
        });

        // Filter change
        document.querySelectorAll('.filter-select').forEach(select => {
            select.addEventListener('change', filterEvents);
        });

        function performSearch() {
            const searchTerm = document.getElementById('searchInput').value;
            console.log('Searching for:', searchTerm);
            filterEvents();
        }

        function filterEvents() {
            const activeCategories = Array.from(document.querySelectorAll('.category-card.active'))
                .map(card => card.dataset.category);
            
            const filters = {
                location: document.getElementById('locationFilter').value,
                date: document.getElementById('dateFilter').value,
                price: document.getElementById('priceFilter').value,
                distance: document.getElementById('distanceFilter').value,
                categories: activeCategories
            };
            
            loadEvents(filters);
        }

        async function loadEvents(filters = {}) {
            const eventsGrid = document.getElementById('eventsGrid');
            
            // Mock events data
            const events = [
                {
                    id: 1,
                    title: "Concert Symphonique",
                    category: "musique",
                    date: "2024-02-20",
                    location: "Philharmonie de Paris",
                    price: 35,
                    emoji: "🎼"
                },
                {
                    id: 2,
                    title: "Exposition Van Gogh",
                    category: "exposition",
                    date: "2024-02-22",
                    location: "Musée d'Orsay",
                    price: 15,
                    emoji: "🖼️"
                },
                {
                    id: 3,
                    title: "Festival de Danse Contemporaine",
                    category: "danse",
                    date: "2024-02-25",
                    location: "Théâtre National",
                    price: 0,
                    emoji: "="
                }
            ];
            
            eventsGrid.innerHTML = events.map(event => createEventCard(event)).join('');
            document.getElementById('resultsCount').textContent = events.length;
        }

        function createEventCard(event) {
            const date = new Date(event.date);
            const dateStr = date.toLocaleDateString('fr-FR', { 
                day: 'numeric', 
                month: 'long'
            });
            
            return `
                <div class="event-card" onclick="window.location.href='event-details.php?id=${event.id}'">
                    <div class="event-image">
                        ${event.emoji}
                        ${event.price === 0 ? '<div class="event-badge">Gratuit</div>' : ''}
                    </div>
                    <div class="event-content">
                        <span class="event-category">${event.category}</span>
                        <h3 class="event-title">${event.title}</h3>
                        <div class="event-info">
                            <div class="event-info-item">
                                <i class="fas fa-calendar"></i>
                                <span>${dateStr}</span>
                            </div>
                            <div class="event-info-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>${event.location}</span>
                            </div>
                            ${event.price > 0 ? `
                                <div class="event-info-item">
                                    <i class="fas fa-euro-sign"></i>
                                    <span>${event.price}€</span>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        }

        // Initialize
        loadEvents();
    </script>
</body>
</html>