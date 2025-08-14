<?php
/**
 * Recommendations API Endpoint
 * Provides personalized event recommendations
 */

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/classes/Auth.php';
require_once dirname(__DIR__) . '/classes/RecommendationEngine.php';

$auth = new Auth();
$response = ['success' => false, 'message' => 'Invalid request'];

// Get request parameters
$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true) ?? [];

// Get current user if logged in
$currentUser = null;
if ($auth->isLoggedIn()) {
    $currentUser = $auth->getCurrentUser();
}

$recommender = new RecommendationEngine($currentUser ? $currentUser['id'] : null);

switch ($action) {
    case 'personalized':
        if (!$currentUser) {
            $response = [
                'success' => false,
                'message' => 'Authentication required for personalized recommendations'
            ];
            break;
        }
        
        $params = [
            'limit' => $_GET['limit'] ?? 10,
            'filters' => [
                'lat' => $_GET['lat'] ?? null,
                'lng' => $_GET['lng'] ?? null,
                'date' => $_GET['date'] ?? null,
                'categories' => isset($_GET['categories']) ? explode(',', $_GET['categories']) : []
            ],
            'include_reasons' => true
        ];
        
        $recommendations = $recommender->getRecommendations($params);
        
        $response = [
            'success' => true,
            'recommendations' => $recommendations,
            'user_preferences' => $currentUser['preferences'] ?? []
        ];
        break;
        
    case 'public':
        // Public recommendations without login
        $params = [
            'location' => $_GET['location'] ?? 'Paris',
            'categories' => isset($_GET['categories']) ? explode(',', $_GET['categories']) : [],
            'limit' => $_GET['limit'] ?? 10
        ];
        
        $recommendations = $recommender->getPublicRecommendations($params);
        
        $response = [
            'success' => true,
            'recommendations' => $recommendations
        ];
        break;
        
    case 'trending':
        $location = $_GET['location'] ?? 'Paris';
        $limit = $_GET['limit'] ?? 6;
        
        $trending = $recommender->getTrendingEvents($location, $limit);
        
        $response = [
            'success' => true,
            'trending' => $trending
        ];
        break;
        
    case 'similar':
        // Get similar events to a specific event
        $eventId = $_GET['event_id'] ?? null;
        
        if (!$eventId) {
            $response = ['success' => false, 'message' => 'Event ID required'];
            break;
        }
        
        // This would be implemented in RecommendationEngine
        $response = [
            'success' => true,
            'message' => 'Similar events feature coming soon'
        ];
        break;
        
    case 'feedback':
        if (!$currentUser) {
            $response = ['success' => false, 'message' => 'Authentication required'];
            break;
        }
        
        // Record user feedback for learning
        $eventId = $input['event_id'] ?? null;
        $action = $input['action'] ?? null; // 'like', 'dislike', 'attend', 'skip'
        
        if (!$eventId || !$action) {
            $response = ['success' => false, 'message' => 'Event ID and action required'];
            break;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Record interaction
            $stmt = $db->prepare(•
                INSERT INTO user_event_history (user_id, event_id, action, created_at)
                VALUES (:user_id, :event_id, :action, NOW())
                ON DUPLICATE KEY UPDATE action = :action, updated_at = NOW()
            •);
            
            $stmt->execute([
                ':user_id' => $currentUser['id'],
                ':event_id' => $eventId,
                ':action' => $action
            ]);
            
            $response = [
                'success' => true,
                'message' => 'Feedback recorded successfully'
            ];
            
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => 'Failed to record feedback'
            ];
        }
        break;
        
    case 'categories':
        // Get available event categories
        $categories = [
            ['id' => 'musique', 'name' => 'Musique', 'icon' => '🎵'],
            ['id' => 'theatre', 'name' => 'Théâtre', 'icon' => '🎭'],
            ['id' => 'exposition', 'name' => 'Exposition', 'icon' => '🖼️'],
            ['id' => 'cinema', 'name' => 'Cinéma', 'icon' => '🎬'],
            ['id' => 'danse', 'name' => 'Danse', 'icon' => '💃'],
            ['id' => 'conference', 'name' => 'Conférence', 'icon' => '🎤'],
            ['id' => 'festival', 'name' => 'Festival', 'icon' => '🎪'],
            ['id' => 'litterature', 'name' => 'Littérature', 'icon' => '📚']
        ];
        
        $response = [
            'success' => true,
            'categories' => $categories
        ];
        break;
        
    default:
        $response = [
            'success' => false,
            'message' => 'Unknown action'
        ];
}

echo json_encode($response);
exit;