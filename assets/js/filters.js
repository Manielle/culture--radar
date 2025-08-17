/**
 * Event Filters Handler
 * Manages filtering by city, date, price, and proximity
 */

(function() {
    // Prevent redeclaration if already loaded
    if (window.EventFilters) {
        console.log('EventFilters already defined, skipping');
        return;
    }

    class EventFilters {
    constructor() {
        this.currentFilter = 'all';
        this.currentCity = this.detectUserCity();
        this.events = [];
        this.filteredEvents = [];
        this.init();
    }

    init() {
        this.setupFilterButtons();
        this.setupCitySelector();
        this.updateTimeDisplay();
        this.loadEvents();
        
        // Update time every minute
        setInterval(() => this.updateTimeDisplay(), 60000);
    }
    
    updateTimeDisplay() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('fr-FR', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        
        // Update demo location time
        const demoTime = document.querySelector('.demo-time');
        if (demoTime) {
            demoTime.textContent = timeStr;
        }
        
        // Update demo city
        const demoCity = document.querySelector('.demo-city');
        if (demoCity) {
            demoCity.textContent = this.currentCity;
        }
    }

    detectUserCity() {
        // Get from localStorage or default to Paris
        const savedCity = localStorage.getItem('userCity');
        if (savedCity) return savedCity;

        // Try to detect from browser geolocation (async)
        if ('geolocation' in navigator) {
            navigator.geolocation.getCurrentPosition(
                (position) => this.reverseGeocode(position.coords),
                () => console.log('Geolocation denied, using default city')
            );
        }
        
        return 'Paris'; // Default city
    }

    async reverseGeocode(coords) {
        try {
            // Use a reverse geocoding service to get city from coordinates
            const response = await fetch(`/api/geocode.php?lat=${coords.latitude}&lng=${coords.longitude}`);
            const data = await response.json();
            if (data.city) {
                this.currentCity = data.city;
                localStorage.setItem('userCity', data.city);
                this.updateCityDisplay();
                this.loadEvents();
            }
        } catch (error) {
            console.error('Reverse geocoding failed:', error);
        }
    }

    setupFilterButtons() {
        const filterChips = document.querySelectorAll('.filter-chip');
        
        filterChips.forEach(chip => {
            chip.addEventListener('click', (e) => {
                e.preventDefault();
                
                // Update active state
                filterChips.forEach(c => c.classList.remove('active'));
                chip.classList.add('active');
                
                // Apply filter
                const filterType = chip.dataset.filter;
                this.applyFilter(filterType);
            });
        });
    }

    setupCitySelector() {
        // Add city selector to the search form if it doesn't exist
        const searchForm = document.querySelector('.search-form');
        if (!searchForm) return;

        const existingSelector = searchForm.querySelector('.city-selector');
        if (existingSelector) return;

        const citySelector = document.createElement('div');
        citySelector.className = 'city-selector';
        citySelector.innerHTML = `
            <style>
                .city-selector {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 15px;
                    margin: 20px auto;
                    padding: 12px 20px;
                    background: rgba(102, 126, 234, 0.1);
                    border-radius: 12px;
                    border: 1px solid rgba(102, 126, 234, 0.3);
                    max-width: fit-content;
                }
                
                .city-selector label {
                    color: white;
                    font-size: 15px;
                    font-weight: 500;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }
                
                .city-selector select {
                    background: rgba(255, 255, 255, 0.1);
                    border: 1px solid rgba(255, 255, 255, 0.3);
                    color: white;
                    padding: 8px 12px;
                    border-radius: 8px;
                    cursor: pointer;
                    font-size: 15px;
                    font-weight: 500;
                    transition: all 0.3s;
                }
                
                .city-selector select:hover {
                    background: rgba(255, 255, 255, 0.15);
                    border-color: #667eea;
                }
                
                .city-selector select:focus {
                    outline: none;
                    border-color: #667eea;
                    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
                }
                
                .city-selector select option {
                    background: #1a1a1a;
                    color: white;
                    padding: 8px;
                }

                .location-indicator {
                    display: inline-flex;
                    align-items: center;
                    gap: 5px;
                    color: #48bb78;
                    font-size: 13px;
                    animation: pulse 2s infinite;
                }
                
                @keyframes pulse {
                    0%, 100% { opacity: 1; }
                    50% { opacity: 0.6; }
                }
            </style>
            <label for="citySelect">
                <i class="fas fa-map-marker-alt"></i> Ville:
            </label>
            <select id="citySelect" name="city">
                <option value="Paris" ${this.currentCity === 'Paris' ? 'selected' : ''}>Paris</option>
                <option value="Lyon" ${this.currentCity === 'Lyon' ? 'selected' : ''}>Lyon</option>
                <option value="Marseille" ${this.currentCity === 'Marseille' ? 'selected' : ''}>Marseille</option>
                <option value="Toulouse" ${this.currentCity === 'Toulouse' ? 'selected' : ''}>Toulouse</option>
                <option value="Nice" ${this.currentCity === 'Nice' ? 'selected' : ''}>Nice</option>
                <option value="Nantes" ${this.currentCity === 'Nantes' ? 'selected' : ''}>Nantes</option>
                <option value="Strasbourg" ${this.currentCity === 'Strasbourg' ? 'selected' : ''}>Strasbourg</option>
                <option value="Montpellier" ${this.currentCity === 'Montpellier' ? 'selected' : ''}>Montpellier</option>
                <option value="Bordeaux" ${this.currentCity === 'Bordeaux' ? 'selected' : ''}>Bordeaux</option>
                <option value="Lille" ${this.currentCity === 'Lille' ? 'selected' : ''}>Lille</option>
                <option value="Rennes" ${this.currentCity === 'Rennes' ? 'selected' : ''}>Rennes</option>
                <option value="Reims" ${this.currentCity === 'Reims' ? 'selected' : ''}>Reims</option>
            </select>
            <span class="location-indicator">
                <i class="fas fa-location-crosshairs"></i>
                <span id="locationStatus">Détection auto</span>
            </span>
        `;

        // Insert after the search input group
        const searchInputGroup = searchForm.querySelector('.search-input-group');
        if (searchInputGroup) {
            searchInputGroup.insertAdjacentElement('afterend', citySelector);
        } else {
            searchForm.appendChild(citySelector);
        }

        // Handle city change
        const citySelect = document.getElementById('citySelect');
        citySelect.addEventListener('change', (e) => {
            this.currentCity = e.target.value;
            localStorage.setItem('userCity', this.currentCity);
            this.loadEvents();
        });
    }

    updateCityDisplay() {
        const citySelect = document.getElementById('citySelect');
        if (citySelect) {
            citySelect.value = this.currentCity;
        }
        
        const locationStatus = document.getElementById('locationStatus');
        if (locationStatus) {
            locationStatus.textContent = `${this.currentCity} détecté`;
        }
    }

    async loadEvents() {
        try {
            // Show loading state
            this.showLoading();

            // Try Google Events API first
            const googleResponse = await fetch(`/api/google-events.php?city=${encodeURIComponent(this.currentCity)}&filter=${this.currentFilter}&limit=50`);
            const googleData = await googleResponse.json();

            if (googleData.success && googleData.events && googleData.events.length > 0) {
                console.log(`Loaded ${googleData.events.length} events from Google Events for ${this.currentCity}`);
                this.events = googleData.events;
                this.applyFilter(this.currentFilter);
                return;
            }

            // Fallback to OpenAgenda API
            const response = await fetch(`/api/real-events.php?city=${encodeURIComponent(this.currentCity)}&limit=50`);
            const data = await response.json();

            if (data.success && data.events) {
                this.events = data.events;
                this.applyFilter(this.currentFilter);
            } else {
                // Fallback to mock data if both APIs fail
                this.loadMockEvents();
            }
        } catch (error) {
            console.error('Failed to load events:', error);
            this.loadMockEvents();
        }
    }

    loadMockEvents() {
        // Generate mock events for the selected city
        const categories = ['Concert', 'Exposition', 'Théâtre', 'Danse', 'Cinéma', 'Conférence'];
        const venues = {
            'Paris': ['Olympia', 'Zénith', 'Louvre', 'Orsay', 'Philharmonie', 'Opéra'],
            'Lyon': ['Halle Tony Garnier', 'Opéra de Lyon', 'Musée des Confluences', 'Transbordeur'],
            'Marseille': ['Le Silo', 'Opéra de Marseille', 'MUCEM', 'Friche la Belle de Mai'],
            'Toulouse': ['Zénith de Toulouse', 'Théâtre du Capitole', 'Les Abattoirs', 'La Cave Poésie'],
            'Nice': ['Acropolis', 'Opéra de Nice', 'MAMAC', 'Théâtre National'],
            'Nantes': ['Zénith de Nantes', 'Lieu Unique', 'Musée d\'Arts', 'Stereolux'],
            'Strasbourg': ['Zénith de Strasbourg', 'Opéra du Rhin', 'Musée d\'Art Moderne', 'La Laiterie'],
            'Montpellier': ['Zénith Sud', 'Opéra Comédie', 'Musée Fabre', 'Rockstore'],
            'Bordeaux': ['Arkéa Arena', 'Opéra de Bordeaux', 'CAPC', 'Rocher de Palmer'],
            'Lille': ['Zénith de Lille', 'Opéra de Lille', 'Palais des Beaux-Arts', 'Aéronef'],
            'Rennes': ['Le Liberté', 'Opéra de Rennes', 'Musée des Beaux-Arts', 'Antipode'],
            'Reims': ['La Cartonnerie', 'Opéra de Reims', 'Musée Saint-Remi', 'Le Manège']
        };

        const cityVenues = venues[this.currentCity] || venues['Paris'];
        const today = new Date();
        
        this.events = [];
        for (let i = 0; i < 20; i++) {
            const eventDate = new Date(today);
            eventDate.setDate(today.getDate() + Math.floor(Math.random() * 30));
            
            const isFree = Math.random() > 0.7;
            const price = isFree ? 0 : Math.floor(Math.random() * 50) + 10;
            
            this.events.push({
                id: `mock-${i}`,
                title: `${categories[Math.floor(Math.random() * categories.length)]} ${i + 1}`,
                venue_name: cityVenues[Math.floor(Math.random() * cityVenues.length)],
                city: this.currentCity,
                start_date: eventDate.toISOString(),
                category: categories[Math.floor(Math.random() * categories.length)].toLowerCase(),
                price: price,
                is_free: isFree,
                distance: Math.random() * 20, // Distance in km
                ai_score: Math.floor(Math.random() * 30) + 70
            });
        }

        this.applyFilter(this.currentFilter);
    }

    applyFilter(filterType) {
        this.currentFilter = filterType;
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        
        const weekendStart = new Date(today);
        const daysUntilSaturday = (6 - today.getDay()) % 7;
        weekendStart.setDate(today.getDate() + (daysUntilSaturday || 7));
        
        const weekendEnd = new Date(weekendStart);
        weekendEnd.setDate(weekendStart.getDate() + 1);

        switch (filterType) {
            case 'today':
                this.filteredEvents = this.events.filter(event => {
                    const eventDate = new Date(event.start_date);
                    eventDate.setHours(0, 0, 0, 0);
                    return eventDate.getTime() === today.getTime();
                });
                break;
                
            case 'weekend':
                this.filteredEvents = this.events.filter(event => {
                    const eventDate = new Date(event.start_date);
                    eventDate.setHours(0, 0, 0, 0);
                    return eventDate >= weekendStart && eventDate <= weekendEnd;
                });
                break;
                
            case 'free':
                this.filteredEvents = this.events.filter(event => event.is_free || event.price === 0);
                break;
                
            case 'nearby':
                // Filter events within 5km
                this.filteredEvents = this.events.filter(event => {
                    return event.distance !== undefined && event.distance <= 5;
                });
                break;
                
            case 'all':
            default:
                this.filteredEvents = [...this.events];
                break;
        }

        // Sort by AI score (relevance)
        this.filteredEvents.sort((a, b) => (b.ai_score || 0) - (a.ai_score || 0));
        
        this.displayEvents();
    }

    showLoading() {
        const container = document.querySelector('.demo-events') || document.querySelector('.results-grid');
        if (!container) return;

        container.innerHTML = `
            <div style="text-align: center; padding: 2rem; color: rgba(255, 255, 255, 0.6);">
                <i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                <p>Chargement des événements à ${this.currentCity}...</p>
            </div>
        `;
    }

    displayEvents() {
        // Find containers
        const demoContainer = document.getElementById('demo-events-container');
        const resultsContainer = document.querySelector('.results-grid');
        
        // Update both if they exist, prioritize results container on search page
        const containers = [];
        if (resultsContainer) containers.push(resultsContainer);
        else if (demoContainer) containers.push(demoContainer);
        
        if (containers.length === 0) return;
        
        const container = containers[0];

        if (this.filteredEvents.length === 0) {
            container.innerHTML = `
                <div style="text-align: center; padding: 2rem; color: rgba(255, 255, 255, 0.6);">
                    <i class="fas fa-calendar-xmark" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                    <p>Aucun événement trouvé pour ces critères à ${this.currentCity}</p>
                    <button onclick="window.eventFilters.applyFilter('all')" style="margin-top: 1rem; padding: 0.5rem 1rem; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 8px; color: white; cursor: pointer;">
                        Voir tous les événements
                    </button>
                </div>
            `;
            return;
        }

        // Determine how many events to show
        const maxEvents = container.id === 'demo-events-container' ? 3 : 10;
        
        // Display events
        const eventsHTML = this.filteredEvents.slice(0, maxEvents).map(event => {
            const eventDate = new Date(event.start_date);
            const now = new Date();
            const isToday = eventDate.toDateString() === now.toDateString();
            const isTomorrow = eventDate.toDateString() === new Date(now.getTime() + 86400000).toDateString();
            
            let dateStr;
            if (isToday) {
                dateStr = `Aujourd'hui ${eventDate.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })}`;
            } else if (isTomorrow) {
                dateStr = `Demain ${eventDate.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })}`;
            } else {
                dateStr = eventDate.toLocaleDateString('fr-FR', { 
                    day: 'numeric', 
                    month: 'short',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            // Choose an emoji based on category
            const categoryEmojis = {
                'concert': '🎵',
                'musique': '🎶',
                'exposition': '🎨',
                'théâtre': '🎭',
                'danse': '💃',
                'cinéma': '🎬',
                'conférence': '🎤',
                'festival': '🎪',
                'autre': '✨'
            };
            
            const emoji = categoryEmojis[event.category?.toLowerCase()] || '📍';

            return `
                <div class="event-card demo-event" data-event-id="${event.id}" style="cursor: pointer;">
                    <div class="event-category-tag">${event.category || 'Événement'}</div>
                    <h3 class="event-title">${emoji} ${event.title}</h3>
                    <div class="event-meta">
                        <span><i class="fas fa-location-dot"></i> ${event.venue_name || event.city}</span>
                        <span><i class="fas fa-clock"></i> ${dateStr}</span>
                        ${event.is_free || event.price === 0 || event.price === null ? 
                            '<span style="color: #48bb78;"><i class="fas fa-gift"></i> Gratuit</span>' : 
                            event.price ? `<span><i class="fas fa-euro-sign"></i> ${event.price}€</span>` : ''
                        }
                        ${event.venue_rating ? 
                            `<span><i class="fas fa-star" style="color: #fbbf24;"></i> ${event.venue_rating}</span>` : ''
                        }
                        ${event.distance !== undefined && event.distance <= 10 ? 
                            `<span><i class="fas fa-walking"></i> ${event.distance < 1 ? Math.round(event.distance * 1000) + ' m' : event.distance.toFixed(1) + ' km'}</span>` : ''
                        }
                        ${event.ai_score && event.ai_score >= 80 ? 
                            `<span class="match-score"><i class="fas fa-heart"></i> Recommandé</span>` : ''
                        }
                    </div>
                    ${event.ticket_links && event.ticket_links.length > 0 ? 
                        `<div class="event-actions" style="margin-top: 10px;">
                            <a href="${event.ticket_links[0].url}" target="_blank" style="color: #667eea; text-decoration: none; font-size: 0.9rem;">
                                <i class="fas fa-ticket"></i> Billets disponibles
                            </a>
                        </div>` : ''
                    }
                </div>
            `;
        }).join('');

        container.innerHTML = eventsHTML;

        // Update results count if on search page
        const resultsHeader = document.querySelector('.results-header p');
        if (resultsHeader) {
            resultsHeader.textContent = `${this.filteredEvents.length} événement${this.filteredEvents.length > 1 ? 's' : ''} trouvé${this.filteredEvents.length > 1 ? 's' : ''} à ${this.currentCity}`;
        }

        // Add click handlers
        container.querySelectorAll('.event-card').forEach((card, index) => {
            card.addEventListener('click', () => {
                const eventId = card.dataset.eventId;
                // Store event data in sessionStorage for the details page
                if (this.filteredEvents[index]) {
                    sessionStorage.setItem('event_' + eventId, JSON.stringify(this.filteredEvents[index]));
                }
                window.location.href = `/event-details.php?id=${encodeURIComponent(eventId)}`;
            });
        });
    }
    } // End of EventFilters class
    
    // Make EventFilters available globally
    window.EventFilters = EventFilters;
    
    // Initialize filters when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            if (!window.eventFilters) {
                window.eventFilters = new EventFilters();
            }
        });
    } else {
        if (!window.eventFilters) {
            window.eventFilters = new EventFilters();
        }
    }
})(); // End of IIFE