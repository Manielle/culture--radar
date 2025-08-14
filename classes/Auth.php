<?php
/**
 * Authentication System for Culture Radar
 * Handles user registration, login, and session management
 */

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/db.php';

class Auth {
    private $db;
    private $sessionTimeout = 3600; // 1 hour
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->initSession();
    }
    
    private function initSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Session timeout check
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $this->sessionTimeout)) {
            $this->logout();
        }
        $_SESSION['last_activity'] = time();
    }
    
    /**
     * Register a new user
     */
    public function register($data) {
        try {
            // Validate input
            $errors = $this->validateRegistration($data);
            if (!empty($errors)) {
                return ['success' => false, 'errors' => $errors];
            }
            
            // Check if email exists
            if ($this->emailExists($data['email'])) {
                return ['success' => false, 'message' => 'Cette adresse email est déjà utilisée'];
            }
            
            // Hash password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // Insert user
            $stmt = $this->db->prepare("
                INSERT INTO users (name, email, password, accepts_newsletter, created_at) 
                VALUES (:name, :email, :password, :newsletter, NOW())
            ");
            
            $stmt->execute([
                ':name' => $data['name'],
                ':email' => $data['email'],
                ':password' => $hashedPassword,
                ':newsletter' => isset($data['newsletter']) ? 1 : 0
            ]);
            
            $userId = $this->db->lastInsertId();
            
            // Create user profile
            $stmt = $this->db->prepare("
                INSERT INTO user_profiles (user_id, location, preferences, created_at) 
                VALUES (:user_id, :location, :preferences, NOW())
            ");
            
            // Use provided preferences or defaults
            $userPreferences = [
                'categories' => $data['preferences'] ?? [],
                'budget_max' => 0,
                'notification_enabled' => true,
                'accessibility_required' => false
            ];
            
            $stmt->execute([
                ':user_id' => $userId,
                ':location' => $data['location'] ?? '',
                ':preferences' => json_encode($userPreferences)
            ]);
            
            // Auto-login after registration
            $this->createSession($userId, $data['name'], $data['email']);
            
            return [
                'success' => true, 
                'message' => 'Inscription réussie!',
                'user_id' => $userId
            ];
            
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de l\'inscription'];
        }
    }
    
    /**
     * Login user
     */
    public function login($email, $password, $remember = false) {
        try {
            $stmt = $this->db->prepare("
                SELECT id, name, email, password, is_active, onboarding_completed 
                FROM users 
                WHERE email = :email
            ");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                return ['success' => false, 'message' => 'Email ou mot de passe incorrect'];
            }
            
            if (!$user['is_active']) {
                return ['success' => false, 'message' => 'Votre compte a été désactivé'];
            }
            
            if (!password_verify($password, $user['password'])) {
                return ['success' => false, 'message' => 'Email ou mot de passe incorrect'];
            }
            
            // Update last login
            $stmt = $this->db->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
            $stmt->execute([':id' => $user['id']]);
            
            // Create session
            $this->createSession($user['id'], $user['name'], $user['email']);
            
            // Set remember me cookie if requested
            if ($remember) {
                $this->setRememberMeCookie($user['id']);
            }
            
            return [
                'success' => true,
                'message' => 'Connexion réussie!',
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'onboarding_completed' => $user['onboarding_completed']
                ]
            ];
            
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur de connexion'];
        }
    }
    
    /**
     * Logout user
     */
    public function logout() {
        // Destroy session
        $_SESSION = [];
        
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Remove remember me cookie
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }
        
        session_destroy();
        
        return ['success' => true, 'message' => 'Déconnexion réussie'];
    }
    
    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        if (isset($_SESSION['user_id'])) {
            return true;
        }
        
        // Check remember me cookie
        if (isset($_COOKIE['remember_token'])) {
            return $this->loginWithRememberToken($_COOKIE['remember_token']);
        }
        
        return false;
    }
    
    /**
     * Get current user
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        try {
            $stmt = $this->db->prepare("
                SELECT u.*, up.preferences, up.location, up.budget_max, up.onboarding_completed as profile_completed
                FROM users u
                LEFT JOIN user_profiles up ON u.id = up.user_id
                WHERE u.id = :id
            ");
            $stmt->execute([':id' => $_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && $user['preferences']) {
                $user['preferences'] = json_decode($user['preferences'], true);
            }
            
            return $user;
            
        } catch (PDOException $e) {
            error_log("Get current user error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Update user profile
     */
    public function updateProfile($userId, $data) {
        try {
            // Update user table
            if (isset($data['name'])) {
                $stmt = $this->db->prepare("UPDATE users SET name = :name WHERE id = :id");
                $stmt->execute([':name' => $data['name'], ':id' => $userId]);
            }
            
            // Update user_profiles table
            $updates = [];
            $params = [':user_id' => $userId];
            
            if (isset($data['location'])) {
                $updates[] = "location = :location";
                $params[':location'] = $data['location'];
            }
            
            if (isset($data['budget_max'])) {
                $updates[] = "budget_max = :budget_max";
                $params[':budget_max'] = $data['budget_max'];
            }
            
            if (isset($data['preferences'])) {
                $updates[] = "preferences = :preferences";
                $params[':preferences'] = json_encode($data['preferences']);
            }
            
            if (isset($data['onboarding_completed'])) {
                $updates[] = "onboarding_completed = :onboarding";
                $params[':onboarding'] = $data['onboarding_completed'] ? 1 : 0;
            }
            
            if (!empty($updates)) {
                $sql = "UPDATE user_profiles SET " . implode(', ', $updates) . " WHERE user_id = :user_id";
                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
            }
            
            return ['success' => true, 'message' => 'Profil mis à jour'];
            
        } catch (PDOException $e) {
            error_log("Update profile error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la mise à jour'];
        }
    }
    
    /**
     * Check if user has role
     */
    public function hasRole($role) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        // For now, simple role check - can be expanded
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
    }
    
    /**
     * Private helper methods
     */
    private function validateRegistration($data) {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = 'Le nom est requis';
        }
        
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email invalide';
        }
        
        if (empty($data['password']) || strlen($data['password']) < 8) {
            $errors[] = 'Le mot de passe doit contenir au moins 8 caractères';
        }
        
        // Only check password confirmation if it's provided
        if (isset($data['password_confirm']) && $data['password'] !== $data['password_confirm']) {
            $errors[] = 'Les mots de passe ne correspondent pas';
        }
        
        return $errors;
    }
    
    private function emailExists($email) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetchColumn() > 0;
    }
    
    private function createSession($userId, $name, $email) {
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['last_activity'] = time();
    }
    
    private function setRememberMeCookie($userId) {
        $token = bin2hex(random_bytes(32));
        $expiry = time() + (30 * 24 * 60 * 60); // 30 days
        
        // Store token in database (you'd need a remember_tokens table)
        // For now, just set the cookie
        setcookie('remember_token', $token, $expiry, '/', '', true, true);
    }
    
    private function loginWithRememberToken($token) {
        // Implementation would check token in database
        // For now, return false
        return false;
    }
}