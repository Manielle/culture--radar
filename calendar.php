<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/classes/Auth.php';

$auth = new Auth();

if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user = $auth->getCurrentUser();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier - CultureRadar</title>
    
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
        
        .calendar-header {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .month-navigation {
            display: flex;
            align-items: center;
            gap: 2rem;
        }
        
        .month-nav-btn {
            width: 40px;
            height: 40px;
            border: none;
            background: #f7fafc;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4a5568;
        }
        
        .month-nav-btn:hover {
            background: #e2e8f0;
            color: #667eea;
        }
        
        .current-month {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2d3748;
        }
        
        .view-options {
            display: flex;
            gap: 0.5rem;
        }
        
        .view-btn {
            padding: 0.5rem 1rem;
            background: #f7fafc;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
            color: #4a5568;
        }
        
        .view-btn:hover {
            background: #e2e8f0;
        }
        
        .view-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .calendar-grid {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        .weekdays {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            margin-bottom: 1rem;
        }
        
        .weekday {
            text-align: center;
            font-weight: 600;
            color: #718096;
            padding: 0.5rem;
            font-size: 0.9rem;
        }
        
        .days-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background: #e2e8f0;
        }
        
        .calendar-day {
            background: white;
            min-height: 100px;
            padding: 0.5rem;
            position: relative;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .calendar-day:hover {
            background: #f7fafc;
        }
        
        .calendar-day.other-month {
            background: #f7fafc;
            color: #cbd5e0;
        }
        
        .calendar-day.today {
            background: #f0f4ff;
        }
        
        .day-number {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .day-events {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        
        .day-event {
            padding: 0.25rem 0.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 4px;
            font-size: 0.75rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .event-list {
            margin-top: 2rem;
        }
        
        .event-list-header {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #2d3748;
        }
        
        .event-list-items {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .event-list-item {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            display: flex;
            gap: 1rem;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .event-list-item:hover {
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }
        
        .event-time {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.5rem;
            border-radius: 8px;
            font-weight: 600;
            min-width: 80px;
            text-align: center;
        }
        
        .event-details {
            flex: 1;
        }
        
        .event-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: #2d3748;
        }
        
        .event-location {
            color: #718096;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .nav-menu {
                display: none;
            }
            
            .calendar-header {
                flex-direction: column;
                gap: 1rem;
            }
            
            .calendar-day {
                min-height: 80px;
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
                <a href="calendar.php" class="nav-item active">Calendrier</a>
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
        <div class="calendar-header">
            <div class="month-navigation">
                <button class="month-nav-btn" id="prevMonth">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <h2 class="current-month" id="currentMonth">Janvier 2024</h2>
                <button class="month-nav-btn" id="nextMonth">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <div class="view-options">
                <button class="view-btn active" data-view="month">Mois</button>
                <button class="view-btn" data-view="week">Semaine</button>
                <button class="view-btn" data-view="list">Liste</button>
            </div>
        </div>

        <div class="calendar-grid">
            <div class="weekdays">
                <div class="weekday">Lun</div>
                <div class="weekday">Mar</div>
                <div class="weekday">Mer</div>
                <div class="weekday">Jeu</div>
                <div class="weekday">Ven</div>
                <div class="weekday">Sam</div>
                <div class="weekday">Dim</div>
            </div>
            <div class="days-grid" id="daysGrid">
                <!-- Calendar days will be generated here -->
            </div>
        </div>

        <div class="event-list">
            <h3 class="event-list-header">Événements à venir</h3>
            <div class="event-list-items" id="eventList">
                <!-- Event list will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        let currentDate = new Date();
        let currentMonth = currentDate.getMonth();
        let currentYear = currentDate.getFullYear();
        
        const monthNames = [
            'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
            'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
        ];

        function generateCalendar(month, year) {
            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const daysInPrevMonth = new Date(year, month, 0).getDate();
            
            const adjustedFirstDay = firstDay === 0 ? 6 : firstDay - 1;
            
            const daysGrid = document.getElementById('daysGrid');
            daysGrid.innerHTML = '';
            
            // Previous month days
            for (let i = adjustedFirstDay - 1; i >= 0; i--) {
                const day = daysInPrevMonth - i;
                const dayDiv = createDayElement(day, true);
                daysGrid.appendChild(dayDiv);
            }
            
            // Current month days
            for (let day = 1; day <= daysInMonth; day++) {
                const dayDiv = createDayElement(day, false, year, month, day);
                daysGrid.appendChild(dayDiv);
            }
            
            // Next month days
            const totalCells = daysGrid.children.length;
            const remainingCells = 42 - totalCells;
            for (let day = 1; day <= remainingCells; day++) {
                const dayDiv = createDayElement(day, true);
                daysGrid.appendChild(dayDiv);
            }
            
            // Update month display
            document.getElementById('currentMonth').textContent = 
                `${monthNames[month]} ${year}`;
        }

        function createDayElement(day, isOtherMonth, year, month, dayNum) {
            const dayDiv = document.createElement('div');
            dayDiv.className = 'calendar-day';
            
            if (isOtherMonth) {
                dayDiv.classList.add('other-month');
            }
            
            // Check if today
            const today = new Date();
            if (!isOtherMonth && year === today.getFullYear() && 
                month === today.getMonth() && dayNum === today.getDate()) {
                dayDiv.classList.add('today');
            }
            
            dayDiv.innerHTML = `
                <div class="day-number">${day}</div>
                <div class="day-events" id="events-${year}-${month}-${day}"></div>
            `;
            
            // Add sample events
            if (!isOtherMonth && Math.random() > 0.7) {
                const eventsDiv = dayDiv.querySelector('.day-events');
                eventsDiv.innerHTML = '<div class="day-event">Événement</div>';
            }
            
            dayDiv.addEventListener('click', () => {
                if (!isOtherMonth) {
                    showDayEvents(year, month, dayNum);
                }
            });
            
            return dayDiv;
        }

        function showDayEvents(year, month, day) {
            console.log(`Showing events for ${day}/${month + 1}/${year}`);
            // Implement day events modal or navigation
        }

        // Navigation
        document.getElementById('prevMonth').addEventListener('click', () => {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            generateCalendar(currentMonth, currentYear);
        });

        document.getElementById('nextMonth').addEventListener('click', () => {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            generateCalendar(currentMonth, currentYear);
        });

        // View switching
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                // Implement view switching logic
            });
        });

        // Load upcoming events
        function loadUpcomingEvents() {
            const eventList = document.getElementById('eventList');
            
            // Sample events
            const events = [
                { time: '14:00', title: 'Concert de Jazz', location: 'Salle Pleyel' },
                { time: '19:30', title: 'Théâtre: Le Malade Imaginaire', location: 'Comédie Française' },
                { time: '20:00', title: 'Exposition Nocturne', location: 'Grand Palais' }
            ];
            
            eventList.innerHTML = events.map(event => `
                <div class="event-list-item" onclick="window.location.href='event-details.php'">
                    <div class="event-time">${event.time}</div>
                    <div class="event-details">
                        <div class="event-title">${event.title}</div>
                        <div class="event-location">
                            <i class="fas fa-map-marker-alt"></i> ${event.location}
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // Initialize
        generateCalendar(currentMonth, currentYear);
        loadUpcomingEvents();
    </script>
</body>
</html>