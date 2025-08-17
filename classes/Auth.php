<?php
/**
 * Simple Auth class that works without database
 * For Railway deployment
 */

class Auth {
    private $db = null;
    
    public function __construct() {
        // Try to get database but don't fail if not available
        try {
            if (file_exists(__DIR__ . '/../includes/db.php')) {
                // Don't actually include it to avoid connection errors
                // require_once __DIR__ . '/../includes/db.php';
                // $this->db = Database::getInstance();
            }
        } catch (Exception $e) {
            // Continue without database
            error_log("Auth: Database not available - " . $e->getMessage());
        }
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'] ?? null,
                'name' => $_SESSION['user_name'] ?? 'Utilisateur',
                'email' => $_SESSION['user_email'] ?? null
            ];
        }
        return null;
    }
    
    public function login($email, $password) {
        // Mock login for demo
        if ($email === 'demo@culture-radar.fr' && $password === 'demo') {
            $_SESSION['user_id'] = 1;
            $_SESSION['user_name'] = 'Demo User';
            $_SESSION['user_email'] = $email;
            return true;
        }
        return false;
    }
    
    public function logout() {
        session_destroy();
        return true;
    }
    
    public function register($data) {
        // Mock registration
        return true;
    }
}