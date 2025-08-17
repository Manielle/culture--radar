<?php
/**
 * Service pour récupérer les événements via Google Events API (SerpAPI)
 */

class GoogleEventsService {
    private $apiKey = 'b56aa6ec92f9f569f50f671e5133d46d5131c74c260086c37f5222bf489f2d4d';
    private $baseUrl = 'https://serpapi.com/search.json';
    
    /**
     * Recherche des événements par ville
     */
    public function getEventsByCity($city, $params = []) {
        // Selon la doc, la location doit être dans la query (q)
        $defaultParams = [
            'engine' => 'google_events',
            'q' => 'Events in ' . $city,  // Format recommandé par la doc
            'hl' => 'en',  // Anglais pour de meilleurs résultats
            'gl' => 'us',  // IMPORTANT: gl=us fonctionne mieux même pour Paris!
            'api_key' => $this->apiKey
        ];
        
        // Fusionner avec les paramètres personnalisés
        $queryParams = array_merge($defaultParams, $params);
        
        // Construire l'URL
        $url = $this->baseUrl . '?' . http_build_query($queryParams);
        
        // Faire la requête
        $response = $this->makeRequest($url);
        
        if ($response && isset($response['events_results'])) {
            return $this->formatEvents($response['events_results'], $city);
        }
        
        return [];
    }
    
    /**
     * Recherche d'événements par catégorie
     */
    public function getEventsByCategory($category, $city = 'Paris') {
        $categoryQueries = [
            'concert' => 'concerts musique',
            'theater' => 'théâtre spectacles',
            'museum' => 'musées expositions',
            'festival' => 'festivals',
            'cinema' => 'cinéma films',
            'sport' => 'sport match',
            'workshop' => 'ateliers cours',
            'conference' => 'conférences talks'
        ];
        
        $query = $categoryQueries[$category] ?? $category;
        
        return $this->getEventsByCity($city, ['query' => $query]);
    }
    
    /**
     * Recherche d'événements du jour
     */
    public function getTodayEvents($city = 'Paris') {
        return $this->getEventsByCity($city, [
            'htichips' => 'date:today'  // Utiliser le filtre officiel
        ]);
    }
    
    /**
     * Recherche d'événements du weekend
     */
    public function getWeekendEvents($city = 'Paris') {
        return $this->getEventsByCity($city, [
            'htichips' => 'date:weekend'  // Filtre pour le weekend
        ]);
    }
    
    /**
     * Recherche d'événements gratuits
     */
    public function getFreeEvents($city = 'Paris') {
        // Pour les événements gratuits, on ajoute "free" dans la query
        return $this->getEventsByCity($city, [
            'q' => 'Free events in ' . $city
        ]);
    }
    
    /**
     * Recherche d'événements en ligne
     */
    public function getOnlineEvents($city = 'Paris') {
        return $this->getEventsByCity($city, [
            'htichips' => 'event_type:Virtual-Event'  // Événements virtuels
        ]);
    }
    
    /**
     * Formater les événements pour l'application
     */
    private function formatEvents($events, $city) {
        $formattedEvents = [];
        
        foreach ($events as $event) {
            $formattedEvent = [
                'id' => $this->generateEventId($event),
                'title' => $event['title'] ?? 'Événement sans titre',
                'description' => $event['description'] ?? '',
                'date' => $this->parseDate($event),
                'time' => $event['when'] ?? '',
                'venue' => $this->parseVenue($event),
                'address' => $event['address'] ?? [],
                'city' => $city,
                'price' => $this->parsePrice($event),
                'image' => $event['image'] ?? null,
                'link' => $event['link'] ?? null,
                'source' => 'Google Events',
                'category' => $this->detectCategory($event)
            ];
            
            $formattedEvents[] = $formattedEvent;
        }
        
        return $formattedEvents;
    }
    
    /**
     * Générer un ID unique pour l'événement
     */
    private function generateEventId($event) {
        $title = $event['title'] ?? '';
        $dateStr = '';
        if (isset($event['date'])) {
            if (is_array($event['date'])) {
                $dateStr = json_encode($event['date']);
            } else {
                $dateStr = $event['date'];
            }
        }
        return 'google-' . md5($title . $dateStr);
    }
    
    /**
     * Parser la date de l'événement
     */
    private function parseDate($event) {
        if (isset($event['date'])) {
            // Si c'est une date simple
            if (isset($event['date']['start_date'])) {
                return $event['date']['start_date'];
            }
            // Si c'est une période
            if (isset($event['date']['when'])) {
                return $event['date']['when'];
            }
        }
        
        // Fallback sur le champ 'when'
        if (isset($event['when'])) {
            return $event['when'];
        }
        
        return date('Y-m-d');
    }
    
    /**
     * Parser le lieu de l'événement
     */
    private function parseVenue($event) {
        if (isset($event['venue'])) {
            if (is_array($event['venue'])) {
                return $event['venue']['name'] ?? 'Lieu non spécifié';
            }
            return $event['venue'];
        }
        
        if (isset($event['address']) && is_array($event['address'])) {
            return $event['address'][0] ?? 'Lieu non spécifié';
        }
        
        return 'Lieu non spécifié';
    }
    
    /**
     * Parser le prix
     */
    private function parsePrice($event) {
        // Chercher des indices de gratuité
        $text = strtolower($event['title'] ?? '') . ' ' . strtolower($event['description'] ?? '');
        
        if (strpos($text, 'gratuit') !== false || strpos($text, 'free') !== false) {
            return ['is_free' => true, 'price' => 0, 'price_text' => 'Gratuit'];
        }
        
        // Chercher un prix dans le ticket_info
        if (isset($event['ticket_info'])) {
            if (is_array($event['ticket_info']) && isset($event['ticket_info'][0]['price'])) {
                $price = $event['ticket_info'][0]['price'];
                return [
                    'is_free' => false,
                    'price' => floatval(preg_replace('/[^0-9.]/', '', $price)),
                    'price_text' => $price
                ];
            }
        }
        
        return ['is_free' => null, 'price' => null, 'price_text' => 'Prix non spécifié'];
    }
    
    /**
     * Détecter la catégorie de l'événement
     */
    private function detectCategory($event) {
        $text = strtolower($event['title'] ?? '') . ' ' . strtolower($event['description'] ?? '');
        
        $categories = [
            'concert' => ['concert', 'musique', 'music', 'live', 'dj'],
            'theater' => ['théâtre', 'spectacle', 'pièce', 'comédie', 'drame'],
            'museum' => ['musée', 'exposition', 'galerie', 'art'],
            'cinema' => ['cinéma', 'film', 'projection', 'séance'],
            'sport' => ['sport', 'match', 'compétition', 'course'],
            'festival' => ['festival', 'fête', 'salon'],
            'workshop' => ['atelier', 'cours', 'stage', 'formation'],
            'conference' => ['conférence', 'débat', 'rencontre', 'talk']
        ];
        
        foreach ($categories as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($text, $keyword) !== false) {
                    return $category;
                }
            }
        }
        
        return 'other';
    }
    
    /**
     * Faire une requête HTTP
     */
    private function makeRequest($url) {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'User-Agent: CultureRadar/1.0'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        if ($error) {
            error_log("GoogleEventsService Error: " . $error);
            return null;
        }
        
        if ($httpCode !== 200) {
            error_log("GoogleEventsService HTTP Error: " . $httpCode);
            return null;
        }
        
        return json_decode($response, true);
    }
    
    /**
     * Obtenir des événements de démonstration (fallback)
     */
    public function getDemoEvents($city = 'Paris') {
        return [
            [
                'id' => 'demo-1',
                'title' => 'Concert de Jazz au Sunset',
                'description' => 'Une soirée jazz exceptionnelle avec des artistes internationaux',
                'date' => date('Y-m-d'),
                'time' => '21:00',
                'venue' => 'Le Sunset-Sunside',
                'address' => ['60 Rue des Lombards', '75001 Paris'],
                'city' => $city,
                'price' => ['is_free' => false, 'price' => 25, 'price_text' => '25€'],
                'category' => 'concert',
                'source' => 'Demo'
            ],
            [
                'id' => 'demo-2',
                'title' => 'Exposition Monet',
                'description' => 'Les Nymphéas de Claude Monet',
                'date' => date('Y-m-d', strtotime('+1 day')),
                'time' => '10:00 - 18:00',
                'venue' => 'Musée de l\'Orangerie',
                'address' => ['Jardin des Tuileries', '75001 Paris'],
                'city' => $city,
                'price' => ['is_free' => false, 'price' => 12, 'price_text' => '12€'],
                'category' => 'museum',
                'source' => 'Demo'
            ],
            [
                'id' => 'demo-3',
                'title' => 'Théâtre: Le Malade Imaginaire',
                'description' => 'La célèbre pièce de Molière',
                'date' => date('Y-m-d', strtotime('+2 days')),
                'time' => '20:00',
                'venue' => 'Comédie-Française',
                'address' => ['1 Place Colette', '75001 Paris'],
                'city' => $city,
                'price' => ['is_free' => false, 'price' => 35, 'price_text' => '35€'],
                'category' => 'theater',
                'source' => 'Demo'
            ],
            [
                'id' => 'demo-4',
                'title' => 'Festival de Street Art',
                'description' => 'Découvrez les artistes urbains du moment',
                'date' => date('Y-m-d', strtotime('+3 days')),
                'time' => 'Toute la journée',
                'venue' => 'Belleville',
                'address' => ['Quartier Belleville', '75020 Paris'],
                'city' => $city,
                'price' => ['is_free' => true, 'price' => 0, 'price_text' => 'Gratuit'],
                'category' => 'festival',
                'source' => 'Demo'
            ]
        ];
    }
}
?>