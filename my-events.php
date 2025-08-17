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
    <title>Mes événements - CultureRadar</title>
    
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
        
        .page-header {
            margin-bottom: 2rem;
        }
        
        .page-title {
            font-size: 2rem;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }
        
        .page-subtitle {
            color: #718096;
        }
        
        .tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 1rem;
        }
        
        .tab {
            padding: 0.5rem 1rem;
            background: none;
            border: none;
            color: #718096;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }
        
        .tab:hover {
            color: #667eea;
        }
        
        .tab.active {
            color: #667eea;
        }
        
        .tab.active::after {
            content: '';
            position: absolute;
            bottom: -1rem;
            left: 0;
            right: 0;
            height: 2px;
            background: #667eea;
        }
        
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
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
        
        .event-status-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .badge-upcoming {
            background: #c6f6d5;
            color: #22543d;
        }
        
        .badge-attended {
            background: #e9d8fd;
            color: #44337a;
        }
        
        .badge-saved {
            background: #fed7d7;
            color: #c53030;
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
        
        .event-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e2e8f0;
        }
        
        .event-action {
            flex: 1;
            padding: 0.5rem;
            background: #f7fafc;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-weight: 500;
            color: #4a5568;
        }
        
        .event-action:hover {
            background: #e2e8f0;
        }
        
        .event-action.primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        .empty-state-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #2d3748;
        }
        
        .empty-state-text {
            color: #718096;
            margin-bottom: 2rem;
        }
        
        .btn-explore {
            display: inline-block;
            padding: 0.75rem 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: transform 0.3s;
        }
        
        .btn-explore:hover {
            transform: translateY(-2px);
        }
        
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.25rem;
        }
        
        .stat-label {
            color: #718096;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .nav-menu {
                display: none;
            }
            
            .events-grid {
                grid-template-columns: 1fr;
            }
            
            .tabs {
                overflow-x: auto;
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
                <a href="my-events.php" class="nav-item active">Mes événements</a>
                <a href="calendar.php" class="nav-item">Calendrier</a>
                <a href="explore.php" class="nav-item">Explorer</a>
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
            <h1 class="page-title">Mes événements</h1>
            <p class="page-subtitle">Gérez vos événements sauvegardés, participations et historique</p>
        </div>

        <!-- Statistics -->
        <div class="stats-cards">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-bookmark"></i>
                </div>
                <div class="stat-value" id="savedCount">0</div>
                <div class="stat-label">Événements sauvegardés</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-value" id="upcomingCount">0</div>
                <div class="stat-label">À venir</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-history"></i>
                </div>
                <div class="stat-value" id="attendedCount">0</div>
                <div class="stat-label">Participations</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="stat-value" id="badgeCount">0</div>
                <div class="stat-label">Badges gagnés</div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab active" data-tab="saved">
                <i class="fas fa-bookmark"></i> Sauvegardés
            </button>
            <button class="tab" data-tab="upcoming">
                <i class="fas fa-calendar"></i> À venir
            </button>
            <button class="tab" data-tab="attended">
                <i class="fas fa-check-circle"></i> Participés
            </button>
            <button class="tab" data-tab="recommended">
                <i class="fas fa-star"></i> Recommandés pour vous
            </button>
        </div>

        <!-- Events Grid -->
        <div id="eventsContainer">
            <div class="events-grid" id="savedEvents">
                <!-- Saved events will be loaded here -->
            </div>

            <div class="events-grid" id="upcomingEvents" style="display: none;">
                <!-- Upcoming events will be loaded here -->
            </div>

            <div class="events-grid" id="attendedEvents" style="display: none;">
                <!-- Attended events will be loaded here -->
            </div>

            <div class="events-grid" id="recommendedEvents" style="display: none;">
                <!-- Recommended events will be loaded here -->
            </div>
        </div>

        <!-- Empty State (shown when no events) -->
        <div class="empty-state" id="emptyState" style="display: none;">
            <div class="empty-state-icon">📅</div>
            <h2 class="empty-state-title">Aucun événement pour le moment</h2>
            <p class="empty-state-text">Commencez à explorer et sauvegarder des événements qui vous intéressent</p>
            <a href="dashboard.php" class="btn-explore">Explorer les événements</a>
        </div>
    </div>

    <script>
        // Tab switching
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Update active tab
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                // Show corresponding content
                const tabName = this.dataset.tab;
                document.querySelectorAll('.events-grid').forEach(grid => {
                    grid.style.display = 'none';
                });
                
                const targetGrid = document.getElementById(tabName + 'Events');
                if (targetGrid) {
                    targetGrid.style.display = 'grid';
                }
                
                // Load content for the tab
                loadTabContent(tabName);
            });
        });

        function loadTabContent(tabName) {
            switch(tabName) {
                case 'saved':
                    loadSavedEvents();
                    break;
                case 'upcoming':
                    loadUpcomingEvents();
                    break;
                case 'attended':
                    loadAttendedEvents();
                    break;
                case 'recommended':
                    loadRecommendedEvents();
                    break;
            }
        }

        async function loadSavedEvents() {
            // Mock data for now - replace with actual API call
            const mockEvents = [
                {
                    id: 1,
                    title: "Festival de Jazz",
                    category: "musique",
                    date: "2024-02-15",
                    location: "Paris",
                    status: "saved",
                    emoji: "🎵"
                },
                {
                    id: 2,
                    title: "Exposition Impressionniste",
                    category: "exposition",
                    date: "2024-02-20",
                    location: "Musée d'Orsay",
                    status: "saved",
                    emoji: "🖼️"
                }
            ];
            
            displayEvents(mockEvents, 'savedEvents');
            updateStats();
        }

        async function loadUpcomingEvents() {
            // Load upcoming events
            const mockEvents = [
                {
                    id: 3,
                    title: "Pièce de Théâtre: Hamlet",
                    category: "theatre",
                    date: "2024-02-25",
                    location: "Théâtre de la Ville",
                    status: "upcoming",
                    emoji: "🎭"
                }
            ];
            
            displayEvents(mockEvents, 'upcomingEvents');
        }

        async function loadAttendedEvents() {
            // Load attended events
            const mockEvents = [
                {
                    id: 4,
                    title: "Concert Symphonique",
                    category: "musique",
                    date: "2024-01-15",
                    location: "Philharmonie de Paris",
                    status: "attended",
                    emoji: "🎼"
                }
            ];
            
            displayEvents(mockEvents, 'attendedEvents');
        }

        async function loadRecommendedEvents() {
            try {
                const response = await fetch('api/recommendations.php?action=personalized&limit=6');
                const data = await response.json();
                
                if (data.success && data.recommendations) {
                    displayEvents(data.recommendations, 'recommendedEvents');
                }
            } catch (error) {
                console.error('Error loading recommendations:', error);
            }
        }

        function displayEvents(events, containerId) {
            const container = document.getElementById(containerId);
            
            if (events.length === 0) {
                document.getElementById('emptyState').style.display = 'block';
                container.style.display = 'none';
                return;
            }
            
            document.getElementById('emptyState').style.display = 'none';
            
            container.innerHTML = events.map(event => createEventCard(event)).join('');
        }

        function createEventCard(event) {
            const date = new Date(event.date || event.start_date);
            const dateStr = date.toLocaleDateString('fr-FR', { 
                day: 'numeric', 
                month: 'long',
                year: 'numeric'
            });
            
            const statusBadge = {
                'saved': '<div class="event-status-badge badge-saved">Sauvegardé</div>',
                'upcoming': '<div class="event-status-badge badge-upcoming">À venir</div>',
                'attended': '<div class="event-status-badge badge-attended">Participé</div>'
            };
            
            return `
                <div class="event-card" data-event-id="${event.id}">
                    <div class="event-image">
                        ${event.emoji || '🎨'}
                        ${statusBadge[event.status] || ''}
                    </div>
                    <div class="event-content">
                        <span class="event-category">${event.category || 'Culture'}</span>
                        <h3 class="event-title">${event.title || event.name}</h3>
                        <div class="event-info">
                            <div class="event-info-item">
                                <i class="fas fa-calendar"></i>
                                <span>${dateStr}</span>
                            </div>
                            <div class="event-info-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>${event.location || event.venue_name || 'Paris'}</span>
                            </div>
                        </div>
                        <div class="event-actions">
                            <button class="event-action" onclick="removeEvent(${event.id})">
                                <i class="fas fa-times"></i> Retirer
                            </button>
                            <button class="event-action primary" onclick="viewEvent(${event.id})">
                                <i class="fas fa-info-circle"></i> Détails
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

        function removeEvent(eventId) {
            if (confirm('Êtes-vous sûr de vouloir retirer cet événement?')) {
                // API call to remove event
                console.log('Removing event:', eventId);
                // Reload current tab
                const activeTab = document.querySelector('.tab.active').dataset.tab;
                loadTabContent(activeTab);
            }
        }

        function viewEvent(eventId) {
            window.location.href = `event-details.php?id=${eventId}`;
        }

        function updateStats() {
            // Update statistics - mock data for now
            document.getElementById('savedCount').textContent = '12';
            document.getElementById('upcomingCount').textContent = '3';
            document.getElementById('attendedCount').textContent = '8';
            document.getElementById('badgeCount').textContent = '5';
        }

        // Initialize
        loadSavedEvents();
    </script>
</body>
</html>