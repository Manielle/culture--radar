<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Load configuration
require_once __DIR__ . '/config.php';

$debug = [];
$error = '';
$success = '';

// Test database connection first
try {
    $dbConfig = Config::database();
    $debug[] = "DB Config: Host={$dbConfig['host']}, Port={$dbConfig['port']}, DB={$dbConfig['name']}";
    
    $dsn = "mysql:host=" . $dbConfig['host'] . ";port=" . $dbConfig['port'] . ";dbname=" . $dbConfig['name'] . ";charset=" . $dbConfig['charset'];
    $debug[] = "DSN: $dsn";
    
    $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $debug[] = "✅ Connexion DB réussie";
    
    // Check tables exist
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->fetch()) {
        $debug[] = "✅ Table 'users' existe";
    } else {
        $debug[] = "❌ Table 'users' manquante!";
    }
    
    $stmt = $pdo->query("SHOW TABLES LIKE 'user_profiles'");
    if ($stmt->fetch()) {
        $debug[] = "✅ Table 'user_profiles' existe";
    } else {
        $debug[] = "❌ Table 'user_profiles' manquante!";
    }
    
} catch (PDOException $e) {
    $debug[] = "❌ Erreur DB: " . $e->getMessage();
    $error = "Impossible de se connecter à la base de données";
}

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $debug[] = "📝 Formulaire soumis";
    
    // Verify CSRF token
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf_token']) {
        $error = 'Erreur de vérification de sécurité (CSRF).';
        $debug[] = "❌ CSRF invalide";
    } else {
        $debug[] = "✅ CSRF valide";
        
        $name = trim($_POST['name'] ?? '');
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        $debug[] = "Données: name='$name', email='$email'";
        
        // Validation
        if (empty($name) || empty($email) || empty($password)) {
            $error = 'Veuillez remplir tous les champs obligatoires.';
            $debug[] = "❌ Champs manquants";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Adresse email invalide.';
            $debug[] = "❌ Email invalide";
        } elseif (strlen($password) < 8) {
            $error = 'Le mot de passe doit contenir au moins 8 caractères.';
            $debug[] = "❌ Mot de passe trop court";
        } elseif ($password !== $confirmPassword) {
            $error = 'Les mots de passe ne correspondent pas.';
            $debug[] = "❌ Mots de passe différents";
        } else {
            try {
                $debug[] = "🔍 Vérification si l'email existe...";
                
                // Check if email already exists
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                
                if ($stmt->fetch()) {
                    $error = 'Cette adresse email est déjà utilisée.';
                    $debug[] = "❌ Email déjà utilisé";
                } else {
                    $debug[] = "✅ Email disponible";
                    
                    // Create user account
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $debug[] = "🔐 Mot de passe hashé";
                    
                    $stmt = $pdo->prepare("
                        INSERT INTO users (name, email, password, accepts_newsletter, is_active, created_at) 
                        VALUES (?, ?, ?, 0, 1, NOW())
                    ");
                    
                    $debug[] = "📝 Insertion utilisateur...";
                    $stmt->execute([$name, $email, $hashedPassword]);
                    $userId = $pdo->lastInsertId();
                    $debug[] = "✅ Utilisateur créé avec ID: $userId";
                    
                    // Create user profile
                    $stmt = $pdo->prepare("
                        INSERT INTO user_profiles (user_id, preferences, location, budget_max, created_at) 
                        VALUES (?, '{}', '', 0, NOW())
                    ");
                    $stmt->execute([$userId]);
                    $debug[] = "✅ Profil utilisateur créé";
                    
                    $success = "✅ Compte créé avec succès! ID: $userId";
                    
                    // Set session
                    $_SESSION['user_id'] = $userId;
                    $_SESSION['user_name'] = $name;
                    $_SESSION['user_email'] = $email;
                    $debug[] = "✅ Session créée";
                }
            } catch (PDOException $e) {
                $error = 'Erreur SQL: ' . $e->getMessage();
                $debug[] = "❌ Erreur SQL détaillée: " . $e->getMessage();
                $debug[] = "Code erreur: " . $e->getCode();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .debug {
            background: #1e293b;
            color: #10b981;
            padding: 20px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            margin: 20px 0;
            white-space: pre-wrap;
        }
        .error {
            background: #fee2e2;
            color: #dc2626;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .success {
            background: #d1fae5;
            color: #065f46;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        input, button {
            display: block;
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            background: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background: #45a049;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Debug Registration</h1>
        
        <?php if ($error): ?>
            <div class="error">❌ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
            <p><a href="/onboarding.php">➡️ Continuer vers l'onboarding</a></p>
        <?php endif; ?>
        
        <div class="debug">Debug Log:
<?php echo implode("\n", $debug); ?>
        </div>
        
        <h2>Test d'inscription</h2>
        <form method="POST">
            <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <label>Nom:</label>
            <input type="text" name="name" value="Test User" required>
            
            <label>Email:</label>
            <input type="email" name="email" value="test<?php echo rand(1000,9999); ?>@example.com" required>
            
            <label>Mot de passe:</label>
            <input type="password" name="password" value="TestPass123!" required>
            
            <label>Confirmer le mot de passe:</label>
            <input type="password" name="confirm_password" value="TestPass123!" required>
            
            <button type="submit" name="register">Créer le compte</button>
        </form>
        
        <hr>
        <p><a href="/register.php">➡️ Page d'inscription normale</a></p>
        <p><a href="/test-config-load.php">➡️ Test configuration</a></p>
    </div>
</body>
</html>