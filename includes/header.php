<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? ($_SESSION['user_name'] ?? 'User') : '';
$currentPage = basename($_SERVER['PHP_SELF'] ?? 'index.php');
?>

<header class="header" role="banner">
    <nav class="nav" role="navigation" aria-label="Navigation principale">
        <a href="/" class="logo" aria-label="Culture Radar - Retour à l'accueil">
            <?php if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/logo-culture-radar.png')): ?>
            <img src="/logo-culture-radar.png" alt="Culture Radar Logo" class="logo-icon" style="height: 40px; width: auto; margin-right: 10px;">
            <?php endif; ?>
            Culture Radar
        </a>
        
        <ul class="nav-links" role="menubar">
            <li role="none"><a href="/#discover" role="menuitem">Découvrir</a></li>
            <li role="none"><a href="/#categories" role="menuitem">Catégories</a></li>
            <li role="none"><a href="/#features" role="menuitem">Fonctionnalités</a></li>
            <li role="none"><a href="/#how" role="menuitem">Comment ça marche</a></li>
            <?php if($isLoggedIn): ?>
                <li role="none"><a href="/dashboard.php" role="menuitem">Mon Espace</a></li>
            <?php endif; ?>
        </ul>
        
        <div class="nav-actions">
            <?php if($isLoggedIn): ?>
                <div class="user-menu">
                    <button class="user-avatar" aria-label="Menu utilisateur">
                        <?php echo substr($userName, 0, 1); ?>
                    </button>
                    <div class="user-dropdown">
                        <a href="/dashboard.php">Mon tableau de bord</a>
                        <a href="/settings.php">Paramètres</a>
                        <a href="/logout.php">Déconnexion</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="/login.php" class="btn-secondary">Connexion</a>
                <a href="/register.php" class="cta-button">Commencer</a>
            <?php endif; ?>
        </div>
        
        <button class="mobile-menu-toggle" aria-label="Menu mobile">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </nav>
</header>