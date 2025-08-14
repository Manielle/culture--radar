/**
 * Enhanced Events Manager with Weather and Transport
 */

class EnhancedEventsManager {
    constructor() {
        this.enhancedApiUrl = '/api/enhanced-events.php';
        this.userLocation = null;
        this.geoLocationEnabled = false;
        this.cache = new Map();
        this.loadingStates = new Map();
    }

    /**
     * Initialize with geolocation if available
     */
    async initialize() {
        try {
            if (navigator.geolocation) {
                const position = await this.getCurrentPosition();
                this.userLocation = `${position.coords.latitude},${position.coords.longitude}`;
                this.geoLocationEnabled = true;
                console.log('Geolocation enabled');
            }
        } catch (error) {
            console.log('Geolocation not available or denied');
        }
    }

    /**
     * Get current position
     */
    getCurrentPosition() {
        return new Promise((resolve, reject) => {
            navigator.geolocation.getCurrentPosition(resolve, reject, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 300000 // 5 minutes
            });
        });
    }

    /**
     * Get enhanced events with weather and transport
     */
    async getEnhancedEvents(params = {}) {
        const {
            city = 'Paris',
            category = null,
            limit = 10,
            useCache = true
        } = params;

        const cacheKey = `enhanced_events_${city}_${category}_${limit}_${this.userLocation || 'no_location'}`;
        
        if (useCache && this.isCacheValid(cacheKey)) {
            return this.cache.get(cacheKey).data;
        }

        try {
            this.setLoading('enhanced-events', true);
            
            const url = new URL(this.enhancedApiUrl, window.location.origin);
            url.searchParams.set('action', 'enhanced-list');
            url.searchParams.set('city', city);
            url.searchParams.set('limit', limit);
            
            if (category) {
                url.searchParams.set('category', category);
            }
            
            if (this.userLocation) {
                url.searchParams.set('user_location', this.userLocation);
            }

            const response = await fetch(url);
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Erreur lors de la récupération des événements enrichis');
            }

            // Cache for 10 minutes (shorter because of dynamic data)
            this.cache.set(cacheKey, {
                data: data.data,
                timestamp: Date.now()
            });

            return data.data;

        } catch (error) {
            console.error('Erreur getEnhancedEvents:', error);
            this.showError(`Impossible de charger les événements: ${error.message}`);
            return [];
        } finally {
            this.setLoading('enhanced-events', false);
        }
    }

    /**
     * Get weather for a specific city/date
     */
    async getWeather(city, date = null) {
        try {
            const url = new URL(this.enhancedApiUrl, window.location.origin);
            url.searchParams.set('action', 'weather-only');
            url.searchParams.set('city', city);
            
            if (date) {
                url.searchParams.set('date', date);
            }

            const response = await fetch(url);
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message);
            }

            return data.data;

        } catch (error) {
            console.error('Erreur getWeather:', error);
            return null;
        }
    }

    /**
     * Get transport info for a destination
     */
    async getTransport(destination, city = 'Paris') {
        try {
            const url = new URL(this.enhancedApiUrl, window.location.origin);
            url.searchParams.set('action', 'transport-only');
            url.searchParams.set('destination', destination);
            url.searchParams.set('city', city);
            
            if (this.userLocation) {
                url.searchParams.set('user_location', this.userLocation);
            }

            const response = await fetch(url);
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message);
            }

            return data.data;

        } catch (error) {
            console.error('Erreur getTransport:', error);
            return null;
        }
    }

    /**
     * Test all APIs
     */
    async testAPIs() {
        try {
            const url = new URL(this.enhancedApiUrl, window.location.origin);
            url.searchParams.set('action', 'test-apis');

            const response = await fetch(url);
            const data = await response.json();

            return data;

        } catch (error) {
            console.error('Erreur testAPIs:', error);
            return {
                success: false,
                message: error.message
            };
        }
    }

    /**
     * Display enhanced events with weather and transport
     */
    displayEnhancedEvents(events, containerId = 'events-container') {
        const container = document.getElementById(containerId);
        if (!container) {
            console.error(`Container ${containerId} not found`);
            return;
        }

        if (!events || events.length === 0) {
            container.innerHTML = `
                <div class="no-events">
                    <i class="fas fa-calendar-times"></i>
                    <h3>Aucun événement trouvé</h3>
                    <p>Essayez de modifier vos critères de recherche</p>
                </div>
            `;
            return;
        }

        const eventsHTML = events.map(event => this.createEnhancedEventCard(event)).join('');
        container.innerHTML = eventsHTML;

        this.attachEventListeners(container);
    }

    /**
     * Create enhanced event card with weather and transport
     */
    createEnhancedEventCard(event) {
        const baseCard = this.createEventCard(event);
        
        // Add weather info
        let weatherHTML = '';
        if (event.weather && event.weather.type !== 'unavailable') {
            weatherHTML = `
                <div class="event-weather">
                    <div class="weather-info">
                        <span class="weather-icon">${event.weather.icon}</span>
                        <span class="weather-temp">${event.weather.temperature}°C</span>
                        <span class="weather-desc">${event.weather.description}</span>
                    </div>
                    ${event.weather.recommendations ? `<div class="weather-rec">${event.weather.recommendations}</div>` : ''}
                </div>
            `;
        }
        
        // Add transport info
        let transportHTML = '';
        if (event.transport && event.transport.public_transport) {
            const transport = event.transport.public_transport;
            
            if (transport.available && transport.duration) {
                transportHTML = `
                    <div class="event-transport">
                        <div class="transport-info">
                            <i class="fas fa-subway"></i>
                            <span>${transport.duration}</span>
                        </div>
                    </div>
                `;
            } else if (transport.general_info) {
                transportHTML = `
                    <div class="event-transport">
                        <div class="transport-info">
                            <i class="fas fa-info-circle"></i>
                            <span>Transports disponibles</span>
                        </div>
                    </div>
                `;
            }
        }
        
        // Insert weather and transport info into the base card
        const enhancedCard = baseCard.replace(
            '</div><!-- End of event content -->',
            `${weatherHTML}${transportHTML}</div><!-- End of enhanced event content -->`
        );
        
        return enhancedCard;
    }

    /**
     * Create basic event card (fallback if no base EventsManager)
     */
    createEventCard(event) {
        const eventDate = event.date_start ? new Date(event.date_start).toLocaleDateString('fr-FR') : 'Date à définir';
        const eventPrice = event.is_free ? 'Gratuit' : (event.price ? `${event.price}€` : 'Prix non spécifié');
        
        return `
            <div class="event-card enhanced" data-event-id="${event.id}">
                <div class="event-header">
                    ${event.image_url ? `<img src="${event.image_url}" alt="${event.title}" class="event-image">` : ''}
                    <div class="event-category">${event.category}</div>
                </div>
                <div class="event-content">
                    <h3 class="event-title">${event.title}</h3>
                    <p class="event-description">${event.description.substring(0, 150)}...</p>
                    <div class="event-details">
                        <div class="event-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>${event.venue_name}, ${event.city}</span>
                        </div>
                        <div class="event-date">
                            <i class="fas fa-calendar"></i>
                            <span>${eventDate}</span>
                        </div>
                        <div class="event-price">
                            <i class="fas fa-euro-sign"></i>
                            <span>${eventPrice}</span>
                        </div>
                    </div>
                </div>
                <div class="event-footer">
                    <a href="${event.external_url}" target="_blank" class="btn btn-primary">
                        <i class="fas fa-external-link-alt"></i>
                        Voir détails
                    </a>
                </div>
            </div><!-- End of event content -->
        `;
    }

    /**
     * Set loading state
     */
    setLoading(key, isLoading) {
        this.loadingStates.set(key, isLoading);
        
        // Update UI loading indicators
        const loadingElements = document.querySelectorAll(`[data-loading="${key}"]`);
        loadingElements.forEach(el => {
            if (isLoading) {
                el.classList.add('loading');
            } else {
                el.classList.remove('loading');
            }
        });
    }

    /**
     * Show error message
     */
    showError(message) {
        // Try to find existing notification system or create simple alert
        if (typeof showNotification === 'function') {
            showNotification(message, 'error');
        } else {
            console.error(message);
            
            // Create simple error notification
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-notification';
            errorDiv.style.cssText = `
                position: fixed;
                top: 100px;
                right: 20px;
                background: #f44336;
                color: white;
                padding: 1rem;
                border-radius: 8px;
                z-index: 1000;
                max-width: 300px;
            `;
            errorDiv.textContent = message;
            
            document.body.appendChild(errorDiv);
            
            setTimeout(() => {
                if (errorDiv.parentNode) {
                    errorDiv.parentNode.removeChild(errorDiv);
                }
            }, 5000);
        }
    }

    /**
     * Attach event listeners
     */
    attachEventListeners(container) {
        // Add click listeners for event cards
        const eventCards = container.querySelectorAll('.event-card');
        eventCards.forEach(card => {
            card.addEventListener('click', (e) => {
                if (!e.target.closest('a, button')) {
                    const eventId = card.dataset.eventId;
                    this.handleEventClick(eventId);
                }
            });
        });
    }

    /**
     * Handle event card click
     */
    handleEventClick(eventId) {
        console.log('Event clicked:', eventId);
        // Implement event detail view or other actions
    }

    /**
     * Check if cache is valid (shorter duration for enhanced data)
     */
    isCacheValid(key) {
        const cached = this.cache.get(key);
        const shortCacheDuration = 10 * 60 * 1000; // 10 minutes for enhanced data
        return cached && (Date.now() - cached.timestamp < shortCacheDuration);
    }
}

// Global initialization
window.enhancedEventsManager = new EnhancedEventsManager();

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', async function() {
    await window.enhancedEventsManager.initialize();
    
    // Auto-test APIs on page load (optional)
    if (window.location.search.includes('test-apis')) {
        const testResults = await window.enhancedEventsManager.testAPIs();
        console.log('API Test Results:', testResults);
    }
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = EnhancedEventsManager;
}