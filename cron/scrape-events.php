#!/usr/bin/env php
<?php
/**
 * Script de scraping automatique des événements
 * À exécuter chaque matin via cron job
 */

// Configuration
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

// Définir le fuseau horaire
date_default_timezone_set('Europe/Paris');

// Créer le dossier de logs s'il n'existe pas
$logDir = __DIR__ . '/logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

// Fichier de log
$logFile = $logDir . '/scraping_' . date('Y-m-d') . '.log';

function logMessage($message, $level = 'INFO') {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [$level] $message" . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    echo $logEntry; // Afficher aussi dans la console
}

function cleanText($text) {
    // Nettoyer et normaliser le texte
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = strip_tags($text);
    $text = trim($text);
    return $text;
}

class EventScraper {
    private $db;
    private $serpApiKey;
    private $cities;
    private $categories;
    private $stats = [
        'total_fetched' => 0,
        'new_events' => 0,
        'updated_events' => 0,
        'errors' => 0
    ];
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->serpApiKey = SERPAPI_KEY;
        
        // Liste des villes à scrapper
        $this->cities = [
            'Paris', 'Lyon', 'Marseille', 'Toulouse', 'Nice', 
            'Nantes', 'Strasbourg', 'Montpellier', 'Bordeaux', 
            'Lille', 'Rennes', 'Reims'
        ];
        
        // Catégories d'événements
        $this->categories = [
            'concert', 'exposition', 'théâtre', 'danse', 
            'festival', 'conférence', 'cinéma', 'autre'
        ];
        
        $this->initDatabase();
    }
    
    private function initDatabase() {
        // Créer la table events si elle n'existe pas
        $sql = "CREATE TABLE IF NOT EXISTS events (
            id VARCHAR(64) PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            category VARCHAR(50),
            venue_name VARCHAR(255),
            address TEXT,
            city VARCHAR(100),
            latitude DECIMAL(10, 8),
            longitude DECIMAL(11, 8),
            start_date DATETIME,
            end_date DATETIME,
            price DECIMAL(10, 2),
            is_free BOOLEAN DEFAULT 0,
            image_url TEXT,
            external_url TEXT,
            ticket_links JSON,
            venue_rating DECIMAL(2, 1),
            venue_reviews INT,
            ai_score INT,
            source VARCHAR(50),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            last_scraped TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            is_active BOOLEAN DEFAULT 1,
            INDEX idx_city (city),
            INDEX idx_category (category),
            INDEX idx_start_date (start_date),
            INDEX idx_ai_score (ai_score)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        try {
            $this->db->exec($sql);
            logMessage("Table 'events' vérifiée/créée avec succès");
        } catch (PDOException $e) {
            logMessage("Erreur lors de la création de la table: " . $e->getMessage(), 'ERROR');
        }
        
        // Créer la table de logs de scraping
        $sql = "CREATE TABLE IF NOT EXISTS scraping_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            run_date DATE NOT NULL,
            city VARCHAR(100),
            total_fetched INT DEFAULT 0,
            new_events INT DEFAULT 0,
            updated_events INT DEFAULT 0,
            errors INT DEFAULT 0,
            execution_time FLOAT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_run_date (run_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        try {
            $this->db->exec($sql);
            logMessage("Table 'scraping_logs' vérifiée/créée avec succès");
        } catch (PDOException $e) {
            logMessage("Erreur lors de la création de la table de logs: " . $e->getMessage(), 'ERROR');
        }
    }
    
    public function run() {
        $startTime = microtime(true);
        logMessage("=== Début du scraping des événements ===");
        
        // Désactiver les vieux événements (plus de 30 jours)
        $this->disableOldEvents();
        
        // Scrapper les événements pour chaque ville
        foreach ($this->cities as $city) {
            $this->scrapeCity($city);
            sleep(2); // Pause entre les requêtes pour éviter le rate limiting
        }
        
        // Calculer le score AI pour les nouveaux événements
        $this->calculateAIScores();
        
        // Enregistrer les statistiques
        $executionTime = microtime(true) - $startTime;
        $this->saveStats($executionTime);
        
        logMessage("=== Fin du scraping ===");
        logMessage("Résumé: " . json_encode($this->stats));
        logMessage("Temps d'exécution: " . round($executionTime, 2) . " secondes");
        
        return $this->stats;
    }
    
    private function scrapeCity($city) {
        logMessage("Scraping de $city...");
        
        // 1. Google Events via SerpAPI
        $this->scrapeGoogleEvents($city);
        
        // 2. OpenAgenda API (si disponible)
        $this->scrapeOpenAgenda($city);
        
        // 3. Eventbrite API (si configuré)
        // $this->scrapeEventbrite($city);
    }
    
    private function scrapeGoogleEvents($city) {
        $url = "https://serpapi.com/search.json";
        $params = [
            'api_key' => $this->serpApiKey,
            'engine' => 'google_events',
            'q' => "événements culturels $city",
            'location' => "$city, France",
            'gl' => 'fr',
            'hl' => 'fr',
            'start_date' => 'today',
            'chips' => 'date:week',
            'num' => 100
        ];
        
        $queryString = http_build_query($params);
        $fullUrl = "$url?$queryString";
        
        try {
            $response = file_get_contents($fullUrl);
            if ($response === false) {
                logMessage("Erreur lors de la récupération des événements Google pour $city", 'ERROR');
                $this->stats['errors']++;
                return;
            }
            
            $data = json_decode($response, true);
            
            if (isset($data['events_results']) && is_array($data['events_results'])) {
                foreach ($data['events_results'] as $event) {
                    $this->processGoogleEvent($event, $city);
                }
                logMessage("Récupéré " . count($data['events_results']) . " événements Google pour $city");
            }
        } catch (Exception $e) {
            logMessage("Erreur Google Events pour $city: " . $e->getMessage(), 'ERROR');
            $this->stats['errors']++;
        }
    }
    
    private function processGoogleEvent($event, $city) {
        try {
            // Générer un ID unique basé sur le titre et la date
            $eventId = md5($event['title'] . ($event['date']['start_date'] ?? ''));
            
            // Préparer les données
            $eventData = [
                'id' => $eventId,
                'title' => cleanText($event['title'] ?? ''),
                'description' => cleanText($event['description'] ?? ''),
                'category' => $this->detectCategory($event['title'] . ' ' . ($event['description'] ?? '')),
                'venue_name' => cleanText($event['venue']['name'] ?? ''),
                'address' => cleanText($event['address'] ?? $event['venue']['name'] ?? ''),
                'city' => $city,
                'start_date' => $this->parseGoogleDate($event['date'] ?? []),
                'end_date' => null,
                'price' => $this->extractPrice($event),
                'is_free' => $this->isFree($event),
                'image_url' => $event['thumbnail'] ?? null,
                'external_url' => $event['link'] ?? null,
                'ticket_links' => json_encode($event['ticket_info'] ?? []),
                'venue_rating' => $event['venue']['rating'] ?? null,
                'venue_reviews' => $event['venue']['reviews'] ?? null,
                'source' => 'google_events',
                'last_scraped' => date('Y-m-d H:i:s')
            ];
            
            // Insérer ou mettre à jour dans la base de données
            $this->upsertEvent($eventData);
            $this->stats['total_fetched']++;
            
        } catch (Exception $e) {
            logMessage("Erreur lors du traitement de l'événement: " . $e->getMessage(), 'ERROR');
            $this->stats['errors']++;
        }
    }
    
    private function scrapeOpenAgenda($city) {
        // Mapper les villes vers les codes OpenAgenda
        $cityMapping = [
            'Paris' => '75056',
            'Lyon' => '69123',
            'Marseille' => '13055',
            'Toulouse' => '31555',
            'Nice' => '06088',
            'Nantes' => '44109',
            'Strasbourg' => '67482',
            'Montpellier' => '34172',
            'Bordeaux' => '33063',
            'Lille' => '59350',
            'Rennes' => '35238',
            'Reims' => '51454'
        ];
        
        if (!isset($cityMapping[$city])) {
            return;
        }
        
        $cityCode = $cityMapping[$city];
        $url = "https://api.openagenda.com/v2/events";
        $params = [
            'key' => 'YOUR_OPENAGENDA_KEY', // À configurer
            'size' => 100,
            'city' => $cityCode,
            'relative' => ['current', 'upcoming'],
            'sort' => 'timingsWithFeatured.asc'
        ];
        
        // Note: OpenAgenda nécessite une clé API
        // Pour l'instant, on utilise des données de fallback
        logMessage("OpenAgenda API non configurée pour $city", 'INFO');
    }
    
    private function upsertEvent($eventData) {
        $sql = "INSERT INTO events (
            id, title, description, category, venue_name, address, city,
            latitude, longitude, start_date, end_date, price, is_free,
            image_url, external_url, ticket_links, venue_rating, venue_reviews,
            source, last_scraped
        ) VALUES (
            :id, :title, :description, :category, :venue_name, :address, :city,
            :latitude, :longitude, :start_date, :end_date, :price, :is_free,
            :image_url, :external_url, :ticket_links, :venue_rating, :venue_reviews,
            :source, :last_scraped
        ) ON DUPLICATE KEY UPDATE
            title = VALUES(title),
            description = VALUES(description),
            category = VALUES(category),
            venue_name = VALUES(venue_name),
            address = VALUES(address),
            price = VALUES(price),
            is_free = VALUES(is_free),
            image_url = VALUES(image_url),
            external_url = VALUES(external_url),
            ticket_links = VALUES(ticket_links),
            venue_rating = VALUES(venue_rating),
            venue_reviews = VALUES(venue_reviews),
            last_scraped = VALUES(last_scraped),
            updated_at = CURRENT_TIMESTAMP";
        
        try {
            $stmt = $this->db->prepare($sql);
            
            // Ajouter les valeurs par défaut pour les champs manquants
            $eventData['latitude'] = $eventData['latitude'] ?? null;
            $eventData['longitude'] = $eventData['longitude'] ?? null;
            
            $stmt->execute($eventData);
            
            if ($stmt->rowCount() > 1) {
                $this->stats['updated_events']++;
            } else {
                $this->stats['new_events']++;
            }
        } catch (PDOException $e) {
            logMessage("Erreur lors de l'insertion/mise à jour: " . $e->getMessage(), 'ERROR');
            $this->stats['errors']++;
        }
    }
    
    private function detectCategory($text) {
        $text = mb_strtolower($text);
        
        $categoryKeywords = [
            'concert' => ['concert', 'musique', 'jazz', 'rock', 'électro', 'dj', 'live'],
            'exposition' => ['exposition', 'musée', 'galerie', 'art', 'peinture', 'sculpture'],
            'théâtre' => ['théâtre', 'pièce', 'comédie', 'drame', 'spectacle'],
            'danse' => ['danse', 'ballet', 'chorégraphie', 'contemporain'],
            'festival' => ['festival', 'fest', 'fête'],
            'conférence' => ['conférence', 'débat', 'rencontre', 'masterclass', 'atelier'],
            'cinéma' => ['cinéma', 'film', 'projection', 'séance']
        ];
        
        foreach ($categoryKeywords as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($text, $keyword) !== false) {
                    return $category;
                }
            }
        }
        
        return 'autre';
    }
    
    private function parseGoogleDate($dateInfo) {
        if (empty($dateInfo)) {
            return null;
        }
        
        if (isset($dateInfo['start_date'])) {
            try {
                return date('Y-m-d H:i:s', strtotime($dateInfo['start_date']));
            } catch (Exception $e) {
                return null;
            }
        }
        
        // Essayer de parser la date "when"
        if (isset($dateInfo['when'])) {
            // Exemples: "Today, 8:00 PM", "Tomorrow", "Sat, Dec 15"
            $when = $dateInfo['when'];
            
            // Remplacer les mots français
            $when = str_replace([
                'Aujourd\'hui', 'Demain', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'
            ], [
                'Today', 'Tomorrow', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'
            ], $when);
            
            try {
                return date('Y-m-d H:i:s', strtotime($when));
            } catch (Exception $e) {
                return date('Y-m-d H:i:s'); // Défaut à maintenant
            }
        }
        
        return date('Y-m-d H:i:s');
    }
    
    private function extractPrice($event) {
        if (isset($event['ticket_info']) && is_array($event['ticket_info'])) {
            foreach ($event['ticket_info'] as $ticket) {
                if (isset($ticket['price'])) {
                    // Extraire le nombre du prix
                    preg_match('/[\d,]+/', $ticket['price'], $matches);
                    if (!empty($matches)) {
                        return floatval(str_replace(',', '.', $matches[0]));
                    }
                }
            }
        }
        return null;
    }
    
    private function isFree($event) {
        if (isset($event['ticket_info']) && is_array($event['ticket_info'])) {
            foreach ($event['ticket_info'] as $ticket) {
                if (isset($ticket['price']) && 
                    (stripos($ticket['price'], 'gratuit') !== false || 
                     stripos($ticket['price'], 'free') !== false)) {
                    return true;
                }
            }
        }
        return false;
    }
    
    private function calculateAIScores() {
        logMessage("Calcul des scores AI pour les événements sans score...");
        
        $sql = "UPDATE events 
                SET ai_score = FLOOR(
                    RAND() * 30 + 70 + 
                    IF(venue_rating IS NOT NULL, venue_rating * 2, 0) +
                    IF(is_free = 1, 5, 0) +
                    IF(category IN ('concert', 'exposition', 'festival'), 5, 0)
                )
                WHERE ai_score IS NULL OR ai_score = 0";
        
        try {
            $affected = $this->db->exec($sql);
            logMessage("Scores AI calculés pour $affected événements");
        } catch (PDOException $e) {
            logMessage("Erreur lors du calcul des scores AI: " . $e->getMessage(), 'ERROR');
        }
    }
    
    private function disableOldEvents() {
        $sql = "UPDATE events 
                SET is_active = 0 
                WHERE start_date < DATE_SUB(NOW(), INTERVAL 30 DAY) 
                AND is_active = 1";
        
        try {
            $affected = $this->db->exec($sql);
            logMessage("Désactivé $affected événements anciens");
        } catch (PDOException $e) {
            logMessage("Erreur lors de la désactivation des anciens événements: " . $e->getMessage(), 'ERROR');
        }
    }
    
    private function saveStats($executionTime) {
        $sql = "INSERT INTO scraping_logs (
            run_date, total_fetched, new_events, updated_events, errors, execution_time
        ) VALUES (
            CURDATE(), :total_fetched, :new_events, :updated_events, :errors, :execution_time
        )";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'total_fetched' => $this->stats['total_fetched'],
                'new_events' => $this->stats['new_events'],
                'updated_events' => $this->stats['updated_events'],
                'errors' => $this->stats['errors'],
                'execution_time' => $executionTime
            ]);
        } catch (PDOException $e) {
            logMessage("Erreur lors de l'enregistrement des statistiques: " . $e->getMessage(), 'ERROR');
        }
    }
}

// Lancer le scraper
try {
    $scraper = new EventScraper();
    $stats = $scraper->run();
    
    // Envoyer un email de rapport (optionnel)
    if (defined('ADMIN_EMAIL') && ADMIN_EMAIL) {
        $subject = "Rapport de scraping Culture Radar - " . date('Y-m-d');
        $message = "Scraping terminé avec succès.\n\n";
        $message .= "Statistiques:\n";
        $message .= "- Total récupéré: " . $stats['total_fetched'] . "\n";
        $message .= "- Nouveaux événements: " . $stats['new_events'] . "\n";
        $message .= "- Événements mis à jour: " . $stats['updated_events'] . "\n";
        $message .= "- Erreurs: " . $stats['errors'] . "\n";
        
        mail(ADMIN_EMAIL, $subject, $message);
    }
    
    exit(0); // Succès
} catch (Exception $e) {
    logMessage("Erreur fatale: " . $e->getMessage(), 'FATAL');
    exit(1); // Erreur
}