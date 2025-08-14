<?php
/**
 * Header component for Culture Radar
 * Includes navigation and user session handling
 */

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<header class="site-header">
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="index.php" class="brand-link">
                    <img src="logo-192x192.png" alt="Culture Radar Logo" class="brand-logo">
                    <span class="brand-text">Culture Radar</span>
                </a>
            </div>
            
            <div class="nav-menu">
                <a href="index.php" class="nav-link <?php echo $currentPage == 'index.php' ? 'active' : ''; ?>">
                    Accueil
                </a>
                
                <a href="discover.php" class="nav-link <?php echo $currentPage == 'discover.php' ? 'active' : ''; ?>">
                    Découvrir
                </a>
                
                <?php if ($isLoggedIn): ?>
                    <a href="dashboard.php" class="nav-link <?php echo $currentPage == 'dashboard.php' ? 'active' : ''; ?>">
                        Dashboard
                    </a>
                <?php endif; ?>
            </div>
            
            <div class="nav-actions">
                <?php if ($isLoggedIn): ?>
                    <div class="user-menu">
                        <span class="user-name">Bonjour, <?php echo htmlspecialchars($userName); ?></span>
                        <a href="logout.php" class="btn btn-outline">Déconnexion</a>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline <?php echo $currentPage == 'login.php' ? 'active' : ''; ?>">
                        Connexion
                    </a>
                    <a href="register.php" class="btn btn-primary <?php echo $currentPage == 'register.php' ? 'active' : ''; ?>">
                        S'inscrire
                    </a>
                <?php endif; ?>
            </div>
            
            <button class="nav-toggle" aria-label="Toggle navigation">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>
</header>

<style>
.site-header {
    background: #fff;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    position: sticky;
    top: 0;
    z-index: 1000;
}

.navbar {
    padding: 0;
}

.nav-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 70px;
}

.nav-brand {
    display: flex;
    align-items: center;
}

.brand-link {
    display: flex;
    align-items: center;
    text-decoration: none;
    font-size: 1.5rem;
    font-weight: 700;
    color: #1a1a2e;
}

.brand-logo {
    width: 32px;
    height: 32px;
    margin-right: 10px;
    object-fit: contain;
}

.brand-text {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.nav-menu {
    display: flex;
    align-items: center;
    gap: 30px;
    flex: 1;
    justify-content: center;
}

.nav-link {
    text-decoration: none;
    color: #4a5568;
    font-weight: 500;
    transition: color 0.3s;
    padding: 10px;
    border-radius: 6px;
}

.nav-link:hover,
.nav-link.active {
    color: #667eea;
    background: rgba(102, 126, 234, 0.1);
}

.nav-actions {
    display: flex;
    align-items: center;
    gap: 15px;
}

.btn {
    padding: 10px 20px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s;
    display: inline-block;
    border: none;
    cursor: pointer;
}

.btn-outline {
    color: #667eea;
    border: 2px solid #667eea;
    background: transparent;
}

.btn-outline:hover {
    background: #667eea;
    color: white;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
}

.user-menu {
    display: flex;
    align-items: center;
    gap: 15px;
}

.user-name {
    color: #4a5568;
    font-weight: 500;
}

.nav-toggle {
    display: none;
    flex-direction: column;
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 5px;
}

.nav-toggle span {
    width: 25px;
    height: 3px;
    background: #4a5568;
    margin: 3px 0;
    transition: 0.3s;
    border-radius: 3px;
}

@media (max-width: 768px) {
    .nav-menu,
    .nav-actions {
        display: none;
    }
    
    .nav-toggle {
        display: flex;
    }
    
    .nav-container {
        padding: 0 15px;
    }
}
</style>