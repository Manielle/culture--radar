<?php
/**
 * Authentication API Endpoints
 * Handles login, registration, and profile updates
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors directly, log them
ini_set('log_errors', 1);

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/classes/Auth.php';

// Handle CORS for API requests (restricted to same origin)
$allowed_origins = [
    'http://localhost:8888',
    'https://culture-radar.fr',
    Config::get('APP_URL')
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins)) {
    header(•Access-Control-Allow-Origin: $origin•);
    header('Access-Control-Allow-Credentials: true');
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Max-Age: 86400');
    exit(0);
}

// Initialize Auth with error handling
try {
    $auth = new Auth();
} catch (Exception $e) {
    error_log(•Auth initialization error: • . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de connexion à la base de données. Vérifiez que le serveur MySQL est démarré.',
        'debug' => Config::isDebug() ? $e->getMessage() : null
    ]);
    exit;
}

$response = ['success' => false, 'message' => 'Invalid request'];

// Get request data
$input = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? $input['action'] ?? '';

// Log the request for debugging
error_log(•Auth API request - Action: $action, Input: • . json_encode($input));

switch ($action) {
    case 'register':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $response = ['success' => false, 'message' => 'Method not allowed'];
            break;
        }
        
        // Handle both confirmPassword and password_confirm field names
        $passwordConfirm = $input['confirmPassword'] ?? $input['password_confirm'] ?? '';
        
        $response = $auth->register([
            'name' => $input['name'] ?? '',
            'email' => $input['email'] ?? '',
            'password' => $input['password'] ?? '',
            'password_confirm' => $passwordConfirm,
            'location' => $input['location'] ?? '',
            'newsletter' => $input['newsletter'] ?? false,
            'preferences' => $input['preferences'] ?? []
        ]);
        break;
        
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $response = ['success' => false, 'message' => 'Method not allowed'];
            break;
        }
        
        $response = $auth->login(
            $input['email'] ?? '',
            $input['password'] ?? '',
            $input['remember'] ?? false
        );
        break;
        
    case 'logout':
        $response = $auth->logout();
        break;
        
    case 'check':
        $response = [
            'success' => true,
            'logged_in' => $auth->isLoggedIn(),
            'user' => $auth->getCurrentUser()
        ];
        break;
        
    case 'update_profile':
        if (!$auth->isLoggedIn()) {
            $response = ['success' => false, 'message' => 'Non authentifié'];
            break;
        }
        
        $currentUser = $auth->getCurrentUser();
        $response = $auth->updateProfile($currentUser['id'], $input);
        break;
        
    case 'update_preferences':
        if (!$auth->isLoggedIn()) {
            $response = ['success' => false, 'message' => 'Non authentifié'];
            break;
        }
        
        $currentUser = $auth->getCurrentUser();
        $preferences = [
            'categories' => $input['categories'] ?? [],
            'budget_max' => $input['budget_max'] ?? 0,
            'notification_enabled' => $input['notification_enabled'] ?? true,
            'accessibility_required' => $input['accessibility_required'] ?? false,
            'transport_mode' => $input['transport_mode'] ?? 'all',
            'max_distance' => $input['max_distance'] ?? 10
        ];
        
        $response = $auth->updateProfile($currentUser['id'], [
            'preferences' => $preferences,
            'onboarding_completed' => true
        ]);
        
        // Also update onboarding status in users table
        if ($response['success']) {
            try {
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare(•UPDATE users SET onboarding_completed = 1 WHERE id = :id•);
                $stmt->execute([':id' => $currentUser['id']]);
            } catch (Exception $e) {
                error_log(•Failed to update onboarding status: • . $e->getMessage());
            }
        }
        break;
        
    case 'delete_account':
        if (!$auth->isLoggedIn()) {
            $response = ['success' => false, 'message' => 'Non authentifié'];
            break;
        }
        
        // GDPR compliance - allow users to delete their account
        $currentUser = $auth->getCurrentUser();
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Soft delete - mark as inactive
            $stmt = $db->prepare(•UPDATE users SET is_active = 0, email = CONCAT(email, '_deleted_', UNIX_TIMESTAMP()) WHERE id = :id•);
            $stmt->execute([':id' => $currentUser['id']]);
            
            $auth->logout();
            
            $response = ['success' => true, 'message' => 'Compte supprimé avec succès'];
        } catch (Exception $e) {
            $response = ['success' => false, 'message' => 'Erreur lors de la suppression du compte'];
        }
        break;
        
    default:
        $response = ['success' => false, 'message' => 'Action non reconnue'];
}

echo json_encode($response);
exit;