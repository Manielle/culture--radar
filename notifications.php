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

// Mock notifications data
$notifications = [
    [
        'id' => 1,
        'type' => 'event_reminder',
        'title' => 'Rappel d\'événement',
        'message' => 'Festival de Jazz au Parc de la Villette commence dans 2 heures',
        'created_at' => '2024-01-15 18:00:00',
        'read' => false,
        'icon' => 'fas fa-music',
        'color' => 'primary'
    ],
    [
        'id' => 2,
        'type' => 'recommendation',
        'title' => 'Nouvelle recommandation',
        'message' => 'Exposition Monet au Musée d\'Orsay correspond à vos goûts',
        'created_at' => '2024-01-15 15:30:00',
        'read' => false,
        'icon' => 'fas fa-palette',
        'color' => 'success'
    ],
    [
        'id' => 3,
        'type' => 'event_update',
        'title' => 'Mise à jour d\'événement',
        'message' => 'L\'heure du spectacle "Hamlet" a été modifiée à 20h30',
        'created_at' => '2024-01-15 12:15:00',
        'read' => true,
        'icon' => 'fas fa-theater-masks',
        'color' => 'warning'
    ],
    [
        'id' => 4,
        'type' => 'system',
        'title' => 'Système',
        'message' => 'Votre profil a été mis à jour avec succès',
        'created_at' => '2024-01-14 16:45:00',
        'read' => true,
        'icon' => 'fas fa-check-circle',
        'color' => 'accent'
    ],
    [
        'id' => 5,
        'type' => 'new_events',
        'title' => 'Nouveaux événements',
        'message' => '5 nouveaux événements culturels ajoutés dans votre région',
        'created_at' => '2024-01-14 09:20:00',
        'read' => false,
        'icon' => 'fas fa-plus-circle',
        'color' => 'secondary'
    ]
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - CultureRadar</title>
    
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
            max-width: 1000px;
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
        }
        
        .notifications-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .notifications-count {
            color: #718096;
            font-size: 1.1rem;
        }
        
        .mark-all-read {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: transform 0.3s;
        }
        
        .mark-all-read:hover {
            transform: translateY(-2px);
        }
        
        .notifications-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .notification-item {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s;
            position: relative;
            cursor: pointer;
        }
        
        .notification-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }
        
        .notification-item.unread {
            border-left: 4px solid #667eea;
            background: #f0f4ff;
        }
        
        .notification-item.unread::before {
            content: '';
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #667eea;
        }
        
        .notification-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 0.75rem;
        }
        
        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
        }
        
        .notification-icon.primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .notification-icon.success {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        }
        
        .notification-icon.warning {
            background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
        }
        
        .notification-icon.accent {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .notification-icon.secondary {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .notification-content {
            flex: 1;
        }
        
        .notification-title {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.25rem;
        }
        
        .notification-message {
            color: #718096;
            line-height: 1.5;
        }
        
        .notification-time {
            color: #a0aec0;
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }
        
        .notification-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .notification-item:hover .notification-actions {
            opacity: 1;
        }
        
        .notification-action {
            background: #f7fafc;
            border: none;
            padding: 0.25rem 0.75rem;
            border-radius: 6px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .notification-action:hover {
            background: #e2e8f0;
        }
        
        .notification-action.primary {
            background: #667eea;
            color: white;
        }
        
        .notification-action.primary:hover {
            background: #5a6fd8;
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
        
        .filter-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            overflow-x: auto;
        }
        
        .filter-tab {
            padding: 0.5rem 1rem;
            background: white;
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
        
        @media (max-width: 768px) {
            .nav-menu {
                display: none;
            }
            
            .notifications-header {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }
            
            .notification-header {
                flex-wrap: wrap;
            }
            
            .filter-tabs {
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
                <a href="recommendations.php" class="nav-item">Recommandations</a>
            </div>
            <div class="user-menu">
                <a href="notifications.php" class="nav-item active">
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
            <h1 class="page-title">Notifications</h1>
            <p class="page-subtitle">Restez informé de vos événements et recommandations</p>
        </div>

        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <div class="filter-tab active" data-filter="all">
                Toutes
            </div>
            <div class="filter-tab" data-filter="unread">
                Non lues
            </div>
            <div class="filter-tab" data-filter="event_reminder">
                Rappels
            </div>
            <div class="filter-tab" data-filter="recommendation">
                Recommandations
            </div>
            <div class="filter-tab" data-filter="system">
                Système
            </div>
        </div>

        <div class="notifications-header">
            <div class="notifications-count">
                <?php 
                $unreadCount = count(array_filter($notifications, function($n) { return !$n['read']; }));
                echo count($notifications) . ' notifications • ' . $unreadCount . ' non lues';
                ?>
            </div>
            <button class="mark-all-read" onclick="markAllRead()">
                <i class="fas fa-check"></i> Tout marquer comme lu
            </button>
        </div>

        <div class="notifications-list" id="notificationsList">
            <?php foreach ($notifications as $notification): ?>
                <div class="notification-item <?php echo !$notification['read'] ? 'unread' : ''; ?>" 
                     data-id="<?php echo $notification['id']; ?>" 
                     data-type="<?php echo $notification['type']; ?>">
                    <div class="notification-header">
                        <div class="notification-icon <?php echo $notification['color']; ?>">
                            <i class="<?php echo $notification['icon']; ?>"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-title"><?php echo htmlspecialchars($notification['title']); ?></div>
                            <div class="notification-message"><?php echo htmlspecialchars($notification['message']); ?></div>
                            <div class="notification-time">
                                <i class="fas fa-clock"></i>
                                <?php 
                                $time = new DateTime($notification['created_at']);
                                echo $time->format('d/m/Y à H:i');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="notification-actions">
                        <?php if (!$notification['read']): ?>
                            <button class="notification-action primary" onclick="markAsRead(<?php echo $notification['id']; ?>)">
                                Marquer comme lu
                            </button>
                        <?php endif; ?>
                        <button class="notification-action" onclick="deleteNotification(<?php echo $notification['id']; ?>)">
                            Supprimer
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        // Filter functionality
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                const filter = this.dataset.filter;
                filterNotifications(filter);
            });
        });

        function filterNotifications(filter) {
            const notifications = document.querySelectorAll('.notification-item');
            
            notifications.forEach(notification => {
                const type = notification.dataset.type;
                const isUnread = notification.classList.contains('unread');
                
                let show = false;
                
                if (filter === 'all') {
                    show = true;
                } else if (filter === 'unread') {
                    show = isUnread;
                } else {
                    show = type === filter;
                }
                
                notification.style.display = show ? 'block' : 'none';
            });
        }

        function markAsRead(id) {
            const notification = document.querySelector(`[data-id="${id}"]`);
            notification.classList.remove('unread');
            
            // Update the notification actions
            const actions = notification.querySelector('.notification-actions');
            actions.innerHTML = `
                <button class="notification-action" onclick="deleteNotification(${id})">
                    Supprimer
                </button>
            `;
            
            updateNotificationCount();
        }

        function markAllRead() {
            document.querySelectorAll('.notification-item.unread').forEach(notification => {
                notification.classList.remove('unread');
                
                const actions = notification.querySelector('.notification-actions');
                const id = notification.dataset.id;
                actions.innerHTML = `
                    <button class="notification-action" onclick="deleteNotification(${id})">
                        Supprimer
                    </button>
                `;
            });
            
            updateNotificationCount();
        }

        function deleteNotification(id) {
            const notification = document.querySelector(`[data-id="${id}"]`);
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            
            setTimeout(() => {
                notification.remove();
                updateNotificationCount();
                
                // Check if no notifications left
                if (document.querySelectorAll('.notification-item').length === 0) {
                    showEmptyState();
                }
            }, 300);
        }

        function updateNotificationCount() {
            const total = document.querySelectorAll('.notification-item').length;
            const unread = document.querySelectorAll('.notification-item.unread').length;
            
            document.querySelector('.notifications-count').textContent = 
                `${total} notifications • ${unread} non lues`;
        }

        function showEmptyState() {
            document.getElementById('notificationsList').innerHTML = `
                <div class="empty-state">
                    <div class="empty-state-icon">🔔</div>
                    <div class="empty-state-title">Aucune notification</div>
                    <p>Vous êtes à jour ! Toutes vos notifications ont été traitées.</p>
                </div>
            `;
        }

        // Real-time notifications (mock)
        setInterval(() => {
            // This would normally fetch new notifications from the server
            // For demo purposes, we'll just show a console message
            console.log('Checking for new notifications...');
        }, 30000);
    </script>
</body>
</html>