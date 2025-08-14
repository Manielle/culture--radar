<?php
// Sécurité HTTP headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: no-referrer');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');

session_start();

// Load configuration
require_once __DIR__ . '/config.php';

// Initialize database connection
try {
    $dbConfig = Config::database();
    $dsn = "mysql:host=" . $dbConfig['host'] . ";dbname=" . $dbConfig['name'] . ";charset=" . $dbConfig['charset'];
    $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Database doesn't exist, we'll create it later
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO & Meta -->
    <title>Culture Radar - Votre boussole culturelle intelligente | Découverte culturelle IA</title>
    <meta name="description" content="Découvrez les trésors culturels cachés de votre ville avec Culture Radar. Intelligence artificielle + géolocalisation pour des recommandations culturelles personnalisées. 50,000+ explorateurs nous font confiance.">
    <meta name="keywords" content="culture, événements, intelligence artificielle, recommandations culturelles, spectacles, expositions, géolocalisation culture">
    
    <!-- Open Graph / Social -->
    <meta property="og:title" content="Culture Radar - Votre boussole culturelle révolutionnaire">
    <meta property="og:description" content="L'IA qui révolutionne la découverte culturelle. Trouvez instantanément les événements qui vous correspondent.">
    <meta property="og:image" content="/assets/og-image.jpg">
    <meta property="og:url" content="https://culture-radar.fr/">
    
    <?php include 'includes/favicon.php'; ?>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Skip to content for accessibility -->
    <a href="#main-content" class="skip-to-content">Aller au contenu principal</a>
    
    <!-- Animated Background -->
    <div class="animated-bg" aria-hidden="true">
        <div class="stars"></div>
        <div class="floating-shapes"></div>
    </div>
    
    <!-- Header -->
    <header class="header" role="banner">
        <nav class="nav" role="navigation" aria-label="Navigation principale">
            <a href="/" class="logo" aria-label="Culture Radar - Retour à l'accueil">
                <img src="/logo culture radar.PNG" alt="Culture Radar Logo" class="logo-icon" style="height: 40px; width: auto; margin-right: 10px;">
                Culture Radar
            </a>
            
            <ul class="nav-links" role="menubar">
                <li role="none"><a href="#discover" role="menuitem">Découvrir</a></li>
                <li role="none"><a href="#categories" role="menuitem">Catégories</a></li>
                <li role="none"><a href="#features" role="menuitem">Fonctionnalités</a></li>
                <li role="none"><a href="#how" role="menuitem">Comment ça marche</a></li>
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
    
    <!-- Main Content -->
    <main id="main-content">
        <!-- Hero Section -->
        <section class="hero" role="banner" aria-labelledby="hero-title">
            <div class="hero-content">
                <div class="hero-badge" role="text">
                    <i class="fas fa-sparkles"></i> Intelligence artificielle culturelle
                </div>
                
                <h1 id="hero-title" class="font-bold">
                    Votre boussole culturelle<br>
                    <span class="gradient-text">révolutionnaire</span>
                </h1>
                
                <p class="hero-subtitle">
                    Découvrez les trésors culturels cachés de votre ville grâce à l'intelligence artificielle. 
                    Des recommandations ultra-personnalisées qui transforment votre exploration urbaine.
                </p>
                
                <div class="hero-cta">
                    <?php if($isLoggedIn): ?>
                        <a href="/discover.php" class="btn-primary">
                            <i class="fas fa-compass"></i> Explorer maintenant
                        </a>
                        <a href="/dashboard.php" class="btn-secondary">
                            <i class="fas fa-user"></i> Mon espace
                        </a>
                    <?php else: ?>
                        <a href="/register.php" class="btn-primary">
                            <i class="fas fa-rocket"></i> Découvrir maintenant
                        </a>
                        <a href="#demo" class="btn-secondary">
                            <i class="fas fa-play"></i> Voir la démo
                        </a>
                    <?php endif; ?>
                </div>
                
                <!-- Live Demo Preview -->
                <div class="hero-demo" role="region" aria-labelledby="demo-title">
                    <div class="demo-header">
                        <h2 id="demo-title" class="sr-only">Aperçu en temps réel</h2>
                        <span class="location-tag">
                            <i class="fas fa-map-marker-alt"></i> Paris 11e • Maintenant
                        </span>
                        <span class="time-tag">Personnalisé pour vous</span>
                    </div>
                    
                    <div class="demo-events" role="list">
                        <div class="event-card demo-event" role="listitem">
                            <div class="event-category-tag">Exposition</div>
                            <h3 class="event-title">✨ Expo Photo "Paris Nocturne"</h3>
                            <div class="event-meta">
                                <span><i class="fas fa-location-dot"></i> Galerie Temps d'Art</span>
                                <span><i class="fas fa-euro-sign"></i> Gratuit</span>
                                <span><i class="fas fa-walking"></i> 5 min</span>
                                <span class="match-score">97% match</span>
                            </div>
                        </div>
                        
                        <div class="event-card demo-event" role="listitem">
                            <div class="event-category-tag">Concert</div>
                            <h3 class="event-title">🎷 Concert Jazz Intimiste</h3>
                            <div class="event-meta">
                                <span><i class="fas fa-location-dot"></i> Le Sunset</span>
                                <span><i class="fas fa-clock"></i> 20h30</span>
                                <span><i class="fas fa-euro-sign"></i> 15€</span>
                                <span class="match-score">94% match</span>
                            </div>
                        </div>
                        
                        <div class="event-card demo-event" role="listitem">
                            <div class="event-category-tag">Théâtre</div>
                            <h3 class="event-title">🎭 Théâtre d'Impro</h3>
                            <div class="event-meta">
                                <span><i class="fas fa-location-dot"></i> Café Théâtre</span>
                                <span><i class="fas fa-clock"></i> 21h</span>
                                <span><i class="fas fa-euro-sign"></i> 12€</span>
                                <span class="match-score">89% match</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Quick Search Section -->
        <section class="quick-search" id="discover">
            <div class="container">
                <h2 class="section-title">Que cherchez-vous aujourd'hui ?</h2>
                
                <form class="search-form" action="/search.php" method="GET">
                    <div class="search-input-group">
                        <input type="text" name="q" placeholder="Rechercher un événement, un lieu, un artiste..." 
                               class="search-input" aria-label="Recherche">
                        <button type="submit" class="search-button" aria-label="Rechercher">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    
                    <div class="quick-filters">
                        <button type="button" class="filter-chip active" data-filter="all">Tout</button>
                        <button type="button" class="filter-chip" data-filter="today">Aujourd'hui</button>
                        <button type="button" class="filter-chip" data-filter="weekend">Ce week-end</button>
                        <button type="button" class="filter-chip" data-filter="free">Gratuit</button>
                        <button type="button" class="filter-chip" data-filter="nearby">À proximité</button>
                    </div>
                </form>
            </div>
        </section>
        
        <!-- Categories Section -->
        <section id="categories" class="categories-section" role="region" aria-labelledby="categories-title">
            <div class="container">
                <div class="section-header">
                    <h2 id="categories-title" class="section-title">Explorer par catégorie</h2>
                    <p class="section-subtitle">Découvrez la richesse culturelle de votre ville</p>
                </div>
                
                <div class="categories-grid">
                    <a href="/category.php?type=theater" class="category-card theater">
                        <div class="category-icon">🎭</div>
                        <div class="category-info">
                            <h3>Spectacles & Théâtre</h3>
                            <p>Pièces, comédies musicales, one-man-shows</p>
                        </div>
                        <div class="category-arrow">→</div>
                    </a>
                    
                    <a href="/category.php?type=music" class="category-card music">
                        <div class="category-icon">🎵</div>
                        <div class="category-info">
                            <h3>Musique & Concerts</h3>
                            <p>Jazz, classique, électro, world music</p>
                        </div>
                        <div class="category-arrow">→</div>
                    </a>
                    
                    <a href="/category.php?type=museum" class="category-card museum">
                        <div class="category-icon">🖼️</div>
                        <div class="category-info">
                            <h3>Expositions & Musées</h3>
                            <p>Art contemporain, collections permanentes</p>
                        </div>
                        <div class="category-arrow">→</div>
                    </a>
                    
                    <a href="/category.php?type=heritage" class="category-card heritage">
                        <div class="category-icon">🏛️</div>
                        <div class="category-info">
                            <h3>Patrimoine & Visites</h3>
                            <p>Histoire, architecture, visites guidées</p>
                        </div>
                        <div class="category-arrow">→</div>
                    </a>
                    
                    <a href="/category.php?type=cinema" class="category-card cinema">
                        <div class="category-icon">🎬</div>
                        <div class="category-info">
                            <h3>Cinéma & Projections</h3>
                            <p>Films d'auteur, séances spéciales</p>
                        </div>
                        <div class="category-arrow">→</div>
                    </a>
                    
                    <a href="/category.php?type=workshop" class="category-card workshop">
                        <div class="category-icon">🎨</div>
                        <div class="category-info">
                            <h3>Ateliers & Rencontres</h3>
                            <p>Cours, masterclass, échanges culturels</p>
                        </div>
                        <div class="category-arrow">→</div>
                    </a>
                </div>
            </div>
        </section>
        
        <!-- Features Section -->
        <section id="features" class="features" role="region" aria-labelledby="features-title">
            <div class="container">
                <div class="section-header">
                    <h2 id="features-title" class="section-title scroll-reveal">
                        Pourquoi Culture Radar révolutionne la découverte ?
                    </h2>
                    <p class="section-subtitle scroll-reveal">
                        Une approche innovante qui combine IA, données ouvertes et passion culturelle
                    </p>
                </div>
                
                <div class="features-grid">
                    <article class="feature-card scroll-reveal">
                        <div class="feature-icon">
                            <i class="fas fa-brain"></i>
                        </div>
                        <h3 class="feature-title">IA Prédictive Avancée</h3>
                        <p class="feature-description">
                            Notre algorithme analyse vos préférences et votre contexte pour prédire 
                            avec précision les expériences culturelles qui vous passionneront.
                        </p>
                    </article>
                    
                    <article class="feature-card scroll-reveal">
                        <div class="feature-icon">
                            <i class="fas fa-gem"></i>
                        </div>
                        <h3 class="feature-title">Trésors Cachés</h3>
                        <p class="feature-description">
                            Découvrez les pépites culturelles invisibles : galeries indépendantes, 
                            concerts secrets, expositions confidentielles hors des circuits touristiques.
                        </p>
                    </article>
                    
                    <article class="feature-card scroll-reveal">
                        <div class="feature-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h3 class="feature-title">Temps Réel Intelligent</h3>
                        <p class="feature-description">
                            Adaptation instantanée selon la météo, les transports, vos disponibilités 
                            et même votre humeur du moment pour des suggestions toujours pertinentes.
                        </p>
                    </article>
                    
                    <article class="feature-card scroll-reveal">
                        <div class="feature-icon">
                            <i class="fas fa-universal-access"></i>
                        </div>
                        <h3 class="feature-title">Inclusion Universelle</h3>
                        <p class="feature-description">
                            Accessibilité complète avec informations PMR, audiodescription, 
                            langue des signes et adaptation aux besoins spécifiques.
                        </p>
                    </article>
                    
                    <article class="feature-card scroll-reveal">
                        <div class="feature-icon">
                            <i class="fas fa-sparkles"></i>
                        </div>
                        <h3 class="feature-title">Curateur Personnel</h3>
                        <p class="feature-description">
                            Votre assistant culturel apprend continuellement de vos goûts pour 
                            vous surprendre avec des découvertes alignées sur vos passions.
                        </p>
                    </article>
                    
                    <article class="feature-card scroll-reveal">
                        <div class="feature-icon">
                            <i class="fas fa-network-wired"></i>
                        </div>
                        <h3 class="feature-title">Écosystème Complet</h3>
                        <p class="feature-description">
                            Connectez-vous avec d'autres passionnés, partagez vos découvertes 
                            et créez votre communauté culturelle personnalisée.
                        </p>
                    </article>
                </div>
            </div>
        </section>
        
        <!-- How it Works -->
        <section id="how" class="how-it-works" role="region" aria-labelledby="how-title">
            <div class="container">
                <div class="section-header">
                    <h2 id="how-title" class="section-title scroll-reveal">La magie en 3 étapes</h2>
                    <p class="section-subtitle scroll-reveal">
                        Simple, rapide et terriblement efficace
                    </p>
                </div>
                
                <div class="steps">
                    <article class="step scroll-reveal">
                        <div class="step-number">1</div>
                        <h3 class="step-title">Créez votre profil culturel</h3>
                        <p class="step-description">
                            En 2 minutes, notre questionnaire intelligent comprend vos goûts, 
                            vos envies et vos contraintes pour créer votre ADN culturel unique.
                        </p>
                    </article>
                    
                    <article class="step scroll-reveal">
                        <div class="step-number">2</div>
                        <h3 class="step-title">Recevez vos recommandations</h3>
                        <p class="step-description">
                            L'IA analyse en temps réel des milliers d'événements pour vous proposer 
                            exactement ce qui vous correspond, où que vous soyez.
                        </p>
                    </article>
                    
                    <article class="step scroll-reveal">
                        <div class="step-number">3</div>
                        <h3 class="step-title">Vivez et partagez</h3>
                        <p class="step-description">
                            Réservez en un clic, invitez vos amis et enrichissez votre carnet culturel. 
                            Plus vous explorez, plus les suggestions deviennent précises.
                        </p>
                    </article>
                </div>
            </div>
        </section>
        
        <!-- Stats Section -->
        <section class="stats-section">
            <div class="container">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number" data-count="50000">0</div>
                        <div class="stat-label">Explorateurs actifs</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" data-count="12000">0</div>
                        <div class="stat-label">Événements référencés</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" data-count="95">0</div>
                        <div class="stat-label">% de satisfaction</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" data-count="24">0</div>
                        <div class="stat-label">Villes couvertes</div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- CTA Section -->
        <section class="cta-section" role="region" aria-labelledby="cta-title">
            <div class="container">
                <h2 id="cta-title">Prêt pour une révolution culturelle ?</h2>
                <p>Rejoignez les milliers d'explorateurs qui redécouvrent leur ville</p>
                <a href="/register.php" class="btn-white">
                    <i class="fas fa-rocket"></i> Commencer l'aventure
                </a>
            </div>
        </section>
    </main>
    
    <!-- Footer -->
    <footer id="footer" class="footer" role="contentinfo">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Culture Radar</h3>
                <p>La révolution de la découverte culturelle. Votre boussole intelligente vers l'art, 
                   la culture et l'émerveillement.</p>
                <div class="social-links">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
            
            <div class="footer-section">
                <h3>Découvrir</h3>
                <a href="/events.php">Tous les événements</a>
                <a href="/venues.php">Lieux culturels</a>
                <a href="/artists.php">Artistes</a>
                <a href="/calendar.php">Calendrier</a>
            </div>
            
            <div class="footer-section">
                <h3>Ressources</h3>
                <a href="/about.php">À propos</a>
                <a href="/help.php">Centre d'aide</a>
                <a href="/blog.php">Blog</a>
                <a href="/partners.php">Partenaires</a>
            </div>
            
            <div class="footer-section">
                <h3>Légal</h3>
                <a href="/privacy.php">Confidentialité</a>
                <a href="/terms.php">Conditions d'utilisation</a>
                <a href="/legal.php">Mentions légales</a>
                <a href="/cookies.php">Cookies</a>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2024 Culture Radar. Projet étudiant fictif - Aucune transaction réelle.</p>
        </div>
    </footer>
    
    <!-- Cookie Banner -->
    <div id="cookie-banner" class="cookie-banner" role="dialog" aria-labelledby="cookie-title">
        <div class="cookie-content">
            <h3 id="cookie-title">🍪 Respect de votre vie privée</h3>
            <p>Nous utilisons des cookies pour améliorer votre expérience et personnaliser vos recommandations culturelles.</p>
            <div class="cookie-buttons">
                <button onclick="acceptAllCookies()" class="btn-primary">Accepter tout</button>
                <button onclick="rejectCookies()" class="btn-secondary">Refuser</button>
                <a href="/privacy.php" class="cookie-link">Personnaliser</a>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
</body>
</html>