<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/classes/Auth.php';

$auth = new Auth();

// Check if user is logged in
if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user = $auth->getCurrentUser();

// Mock personalized recommendations data
$recommendations = [
    [
        'id' => 1,
        'title' => 'Festival de Jazz au Parc de la Villette',
        'category' => 'Musique',
        'description' => 'Découvrez les plus grands artistes de jazz dans un cadre exceptionnel.',
        'venue_name' => 'Parc de la Villette',
        'location' => 'Paris 19e',
        'start_date' => '2024-02-15 20:00:00',
        'price' => 45.00,
        'is_free' => false,
        'ai_score' => 95,
        'match_reasons' => ['Vous aimez le jazz', 'Proche de votre localisation', 'Prix abordable'],
        'image_url' => null,
        'featured' => true
    ],
    [
        'id' => 2,
        'title' => 'Exposition Monet - Les Nymphéas',
        'category' => 'Art',
        'description' => 'Une exposition exceptionnelle dédiée aux célèbres Nymphéas de Claude Monet.',
        'venue_name' => 'Musée de l\'Orangerie',
        'location' => 'Paris 1er',
        'start_date' => '2024-02-10 10:00:00',
        'price' => 16.00,
        'is_free' => false,
        'ai_score' => 92,
        'match_reasons' => ['Vous visitez souvent les musées', 'Art impressionniste', 'Durée flexible'],
        'image_url' => null,
        'featured' => false
    ],
    [
        'id' => 3,
        'title' => 'Spectacle de Danse Contemporaine',
        'category' => 'Danse',
        'description' => 'Une création originale mêlant danse contemporaine et nouvelles technologies.',
        'venue_name' => 'Théâtre de la Bastille',
        'location' => 'Paris 11e',
        'start_date' => '2024-02-20 19:30:00',
        'price' => 35.00,
        'is_free' => false,
        'ai_score' => 88,
        'match_reasons' => ['Vous appréciez les arts du spectacle', 'Innovation artistique', 'Soirée recommandée'],
        'image_url' => null,
        'featured' => false
    ],
    [
        'id' => 4,
        'title' => 'Conférence sur l\'Intelligence Artificielle',
        'category' => 'Conférence',
        'description' => 'Rencontre avec des experts en IA et découverte des dernières innovations.',
        'venue_name' => 'Université Sorbonne',
        'location' => 'Paris 5e',
        'start_date' => '2024-02-12 14:00:00',
        'price' => 0.00,
        'is_free' => true,
        'ai_score' => 85,
        'match_reasons' => ['Événement gratuit', 'Sujet d\'actualité', 'Proche de vous'],
        'image_url' => null,
        'featured' => false
    ],
    [
        'id' => 5,
        'title' => 'Marché Nocturne des Créateurs',
        'category' => 'Artisanat',
        'description' => 'Découvrez les créations uniques d\'artistes locaux dans une ambiance conviviale.',
        'venue_name' => 'Place des Vosges',
        'location' => 'Paris 4e',
        'start_date' => '2024-02-18 18:00:00',
        'price' => 0.00,
        'is_free' => true,
        'ai_score' => 82,
        'match_reasons' => ['Événement gratuit', 'Artisanat local', 'Ambiance unique'],
        'image_url' => null,
        'featured' => false
    ],
    [
        'id' => 6,
        'title' => 'Concert de Musique Classique',
        'category' => 'Musique',
        'description' => 'Soirée exceptionnelle avec l\'Orchestre de Paris interprétant Beethoven.',
        'venue_name' => 'Philharmonie de Paris',
        'location' => 'Paris 19e',
        'start_date' => '2024-02-25 20:30:00',
        'price' => 65.00,
        'is_free' => false,
        'ai_score' => 90,
        'match_reasons' => ['Musique classique', 'Orchestre renommé', 'Acoustique exceptionnelle'],
        'image_url' => null,
        'featured' => true
    ]
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recommandations - CultureRadar</title>
    
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
            min-height: 100vh;
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
        
        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }
        
        .page-subtitle {
            color: #718096;
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .ai-info-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .ai-info-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .ai-info-description {
            opacity: 0.9;
            margin-bottom: 1rem;
        }
        
        .ai-stats {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-top: 1rem;
        }
        
        .ai-stat {
            text-align: center;
        }
        
        .ai-stat-value {
            font-size: 2rem;
            font-weight: 700;
            display: block;
        }
        
        .ai-stat-label {
            font-size: 0.85rem;
            opacity: 0.8;
        }
        
        .filter-section {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        .filter-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            overflow-x: auto;
        }
        
        .filter-tab {
            padding: 0.5rem 1rem;
            background: #f7fafc;
            border: 2px solid #e2e8f0;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
            white-space: nowrap;
        }
        
        .filter-tab:hover {
            border-color: #667eea;
        }
        
        .filter-tab.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: transparent;
        }
        
        .sort-options {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .sort-label {
            font-weight: 500;
            color: #4a5568;
        }
        
        .sort-select {
            padding: 0.5rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            background: white;
            cursor: pointer;
        }
        
        .recommendations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .recommendation-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s;
            position: relative;
        }
        
        .recommendation-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        
        .recommendation-card.featured {
            border: 2px solid #667eea;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.2);
        }
        
        .featured-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            z-index: 1;
        }
        
        .recommendation-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
            color: white;
            position: relative;
        }
        
        .recommendation-category {
            background: rgba(255,255,255,0.2);
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-block;
            margin-bottom: 1rem;
        }
        
        .recommendation-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            line-height: 1.3;
        }
        
        .ai-score-display {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-top: 1rem;
        }
        
        .ai-score-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            font-weight: 700;
        }
        
        .ai-score-text {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .recommendation-content {
            padding: 2rem;
        }
        
        .recommendation-description {
            color: #718096;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        
        .recommendation-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            color: #718096;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .match-reasons {
            margin-bottom: 1.5rem;
        }
        
        .match-reasons-title {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .match-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        .match-tag {
            background: #f0f4ff;
            color: #667eea;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .recommendation-actions {
            display: flex;
            gap: 1rem;
            justify-content: space-between;
            align-items: center;
        }
        
        .price-display {
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .price-free {
            color: #48bb78;
        }
        
        .price-paid {
            color: #2d3748;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }
        
        .btn-outline {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }
        
        .btn-outline:hover {
            background: #667eea;
            color: white;
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #718096;
        }
        
        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            opacity: 0.5;
        }
        
        .empty-state-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .load-more {
            text-align: center;
            margin-top: 2rem;
        }
        
        .load-more-btn {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
            padding: 1rem 2rem;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .load-more-btn:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .nav-menu {
                display: none;
            }
            
            .recommendations-grid {
                grid-template-columns: 1fr;
            }
            
            .ai-stats {
                gap: 1rem;
            }
            
            .sort-options {
                flex-direction: column;
                align-items: stretch;
                gap: 0.5rem;
            }
            
            .recommendation-details {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }
            
            .recommendation-actions {
                flex-direction: column;
                gap: 1rem;
            }
            
            .action-buttons {
                width: 100%;
            }
            
            .btn {
                flex: 1;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">📡 CultureRadar</div>
            <div class="nav-menu">
                <a href="dashboard.php" class="nav-item">Découvrir</a>
                <a href="my-events.php" class="nav-item">Mes événements</a>
                <a href="calendar.php" class="nav-item">Calendrier</a>
                <a href="explore.php" class="nav-item">Explorer</a>
                <a href="trending.php" class="nav-item">Tendances</a>
                <a href="recommendations.php" class="nav-item active">Recommandations</a>
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
        <div class="page-header">
            <h1 class="page-title">Recommandations Personnalisées</h1>
            <p class="page-subtitle">Découvrez des événements culturels sélectionnés spécialement pour vous par notre intelligence artificielle</p>
        </div>

        <!-- AI Information Card -->
        <div class="ai-info-card">
            <div class="ai-info-title">> Intelligence Artificielle CultureRadar</div>
            <div class="ai-info-description">
                Nos algorithmes analysent vos préférences, votre historique et votre localisation pour vous proposer les événements les plus pertinents.
            </div>
            <div class="ai-stats">
                <div class="ai-stat">
                    <span class="ai-stat-value"><?php echo count($recommendations); ?></span>
                    <span class="ai-stat-label">Recommandations</span>
                </div>
                <div class="ai-stat">
                    <span class="ai-stat-value">89%</span>
                    <span class="ai-stat-label">Précision</span>
                </div>
                <div class="ai-stat">
                    <span class="ai-stat-value">1.2k</span>
                    <span class="ai-stat-label">Utilisateurs satisfaits</span>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="filter-section">
            <div class="filter-tabs">
                <div class="filter-tab active" data-filter="all">
                    Toutes
                </div>
                <div class="filter-tab" data-filter="high-score">
                    Score élevé (90%+)
                </div>
                <div class="filter-tab" data-filter="free">
                    Gratuit
                </div>
                <div class="filter-tab" data-filter="featured">
                    Mis en avant
                </div>
                <div class="filter-tab" data-filter="this-week">
                    Cette semaine
                </div>
            </div>
            
            <div class="sort-options">
                <span class="sort-label">Trier par :</span>
                <select class="sort-select" id="sortSelect">
                    <option value="score">Score de correspondance</option>
                    <option value="date">Date</option>
                    <option value="price">Prix</option>
                    <option value="popularity">Popularité</option>
                </select>
            </div>
        </div>

        <!-- Recommendations Grid -->
        <div class="recommendations-grid" id="recommendationsGrid">
            <?php foreach ($recommendations as $recommendation): ?>
                <div class="recommendation-card <?php echo $recommendation['featured'] ? 'featured' : ''; ?>" 
                     data-score="<?php echo $recommendation['ai_score']; ?>"
                     data-price="<?php echo $recommendation['is_free'] ? 0 : $recommendation['price']; ?>"
                     data-category="<?php echo strtolower($recommendation['category']); ?>">
                    
                    <?php if ($recommendation['featured']): ?>
                        <div class="featured-badge">⭐ Recommandé</div>
                    <?php endif; ?>
                    
                    <div class="recommendation-header">
                        <div class="recommendation-category">
                            <?php 
                            $categoryIcons = [
                                'Musique' => '<µ',
                                'Art' => '<¨',
                                'Danse' => '=',
                                'Conférence' => '<¤',
                                'Artisanat' => '=à'
                            ];
                            echo ($categoryIcons[$recommendation['category']] ?? '🎭') . ' ' . $recommendation['category'];
                            ?>
                        </div>
                        
                        <h3 class="recommendation-title"><?php echo htmlspecialchars($recommendation['title']); ?></h3>
                        
                        <div class="ai-score-display">
                            <div class="ai-score-circle">
                                <?php echo $recommendation['ai_score']; ?>%
                            </div>
                            <div>
                                <div class="ai-score-text">Correspondance IA</div>
                                <div style="font-size: 0.8rem; opacity: 0.8;">Basée sur vos préférences</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="recommendation-content">
                        <p class="recommendation-description"><?php echo htmlspecialchars($recommendation['description']); ?></p>
                        
                        <div class="recommendation-details">
                            <div class="detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo htmlspecialchars($recommendation['venue_name']); ?>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-city"></i>
                                <?php echo htmlspecialchars($recommendation['location']); ?>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-calendar"></i>
                                <?php echo date('d/m/Y', strtotime($recommendation['start_date'])); ?>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-clock"></i>
                                <?php echo date('H:i', strtotime($recommendation['start_date'])); ?>
                            </div>
                        </div>
                        
                        <div class="match-reasons">
                            <div class="match-reasons-title">
                                <i class="fas fa-lightbulb"></i>
                                Pourquoi cette recommandation ?
                            </div>
                            <div class="match-tags">
                                <?php foreach ($recommendation['match_reasons'] as $reason): ?>
                                    <span class="match-tag"><?php echo htmlspecialchars($reason); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="recommendation-actions">
                            <div class="price-display <?php echo $recommendation['is_free'] ? 'price-free' : 'price-paid'; ?>">
                                <?php if ($recommendation['is_free']): ?>
                                    <i class="fas fa-gift"></i> Gratuit
                                <?php else: ?>
                                    <i class="fas fa-euro-sign"></i> <?php echo number_format($recommendation['price'], 2); ?> €
                                <?php endif; ?>
                            </div>
                            
                            <div class="action-buttons">
                                <button class="btn btn-outline" onclick="saveRecommendation(<?php echo $recommendation['id']; ?>)">
                                    <i class="fas fa-heart"></i> Sauvegarder
                                </button>
                                <a href="event-details.php?id=<?php echo $recommendation['id']; ?>" class="btn btn-primary">
                                    <i class="fas fa-info-circle"></i> Voir détails
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Load More Button -->
        <div class="load-more">
            <button class="load-more-btn" onclick="loadMoreRecommendations()">
                <i class="fas fa-plus"></i> Charger plus de recommandations
            </button>
        </div>
    </div>

    <script>
        // Filter functionality
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                const filter = this.dataset.filter;
                filterRecommendations(filter);
            });
        });

        // Sort functionality
        document.getElementById('sortSelect').addEventListener('change', function() {
            sortRecommendations(this.value);
        });

        function filterRecommendations(filter) {
            const cards = document.querySelectorAll('.recommendation-card');
            
            cards.forEach(card => {
                let show = true;
                
                switch (filter) {
                    case 'high-score':
                        show = parseInt(card.dataset.score) >= 90;
                        break;
                    case 'free':
                        show = parseFloat(card.dataset.price) === 0;
                        break;
                    case 'featured':
                        show = card.classList.contains('featured');
                        break;
                    case 'this-week':
                        // This would normally check dates
                        show = true;
                        break;
                    case 'all':
                    default:
                        show = true;
                        break;
                }
                
                card.style.display = show ? 'block' : 'none';
            });
        }

        function sortRecommendations(criteria) {
            const grid = document.getElementById('recommendationsGrid');
            const cards = Array.from(grid.children);
            
            cards.sort((a, b) => {
                switch (criteria) {
                    case 'score':
                        return parseInt(b.dataset.score) - parseInt(a.dataset.score);
                    case 'price':
                        return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                    case 'date':
                        // This would normally sort by actual dates
                        return 0;
                    case 'popularity':
                        // This would normally sort by popularity metrics
                        return Math.random() - 0.5;
                    default:
                        return 0;
                }
            });
            
            cards.forEach(card => grid.appendChild(card));
        }

        function saveRecommendation(id) {
            // Simulate API call
            const button = event.target.closest('button');
            const originalText = button.innerHTML;
            
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sauvegarde...';
            button.disabled = true;
            
            setTimeout(() => {
                button.innerHTML = '<i class="fas fa-check"></i> Sauvegardé';
                button.style.background = '#48bb78';
                button.style.color = 'white';
                button.style.border = '2px solid #48bb78';
                
                // Show success notification
                showNotification('Événement ajouté aux favoris !', 'success');
            }, 1000);
        }

        function loadMoreRecommendations() {
            const button = document.querySelector('.load-more-btn');
            const originalText = button.innerHTML;
            
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Chargement...';
            button.disabled = true;
            
            // Simulate loading more recommendations
            setTimeout(() => {
                button.innerHTML = originalText;
                button.disabled = false;
                
                showNotification('Plus de recommandations seront bientôt disponibles !', 'info');
            }, 1500);
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 100px;
                right: 20px;
                background: ${type === 'success' ? '#48bb78' : type === 'error' ? '#f56565' : '#667eea'};
                color: white;
                padding: 1rem 1.5rem;
                border-radius: 12px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.15);
                z-index: 1000;
                transform: translateX(100%);
                transition: transform 0.3s ease;
                max-width: 400px;
            `;
            
            notification.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                <span style="margin-left: 0.5rem;">${message}</span>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => notification.style.transform = 'translateX(0)', 100);
            
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => document.body.removeChild(notification), 300);
            }, 4000);
        }

        // Initialize with default sort
        sortRecommendations('score');
    </script>
</body>
</html>