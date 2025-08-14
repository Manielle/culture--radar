<?php
session_start();

// Set page title for header
$pageTitle = "À propos - Culture Radar";
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <meta name="description" content="Découvrez Culture Radar, votre boussole culturelle intelligente pour découvrir les événements qui vous correspondent en France.">
    
    <!-- Favicon -->
    <?php include 'includes/favicon.php'; ?>
    
    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Background Animation -->
    <div class="animated-bg">
        <div class="stars"></div>
        <div class="floating-shapes"></div>
    </div>

    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Hero Section -->
        <section class="hero hero-small">
            <div class="container">
                <div class="hero-content">
                    <h1 class="gradient-text">À propos de Culture Radar</h1>
                    <p class="hero-subtitle">
                        Votre boussole culturelle intelligente qui révolutionne la découverte d'événements culturels en France
                    </p>
                </div>
            </div>
        </section>

        <!-- Mission Section -->
        <section class="section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Notre Mission</h2>
                    <p class="section-subtitle">
                        Démocratiser l'accès à la culture en rendant la découverte d'événements plus intuitive et personnalisée
                    </p>
                </div>

                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">📡</div>
                        <h3 class="feature-title">Recommandations Intelligentes</h3>
                        <p class="feature-description">
                            Notre intelligence artificielle analyse vos préférences pour vous proposer des événements qui correspondent vraiment à vos goûts.
                        </p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon"><</div>
                        <h3 class="feature-title">Couverture Nationale</h3>
                        <p class="feature-description">
                            Découvrez des événements dans toute la France grâce à notre partenariat avec OpenAgenda et nos sources locales.
                        </p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">📱</div>
                        <h3 class="feature-title">Expérience Moderne</h3>
                        <p class="feature-description">
                            Interface intuitive et responsive qui s'adapte à tous vos appareils pour découvrir la culture partout.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Story Section -->
        <section class="section">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <h2 class="section-title">Notre Histoire</h2>
                        <p style="color: rgba(255, 255, 255, 0.8); line-height: 1.8; margin-bottom: 1.5rem;">
                            Culture Radar est né d'un constat simple : il est difficile de découvrir des événements culturels qui correspondent vraiment à nos goûts et à notre localisation. Trop souvent, nous passons à côté d'événements fantastiques simplement parce que nous n'en avons pas connaissance.
                        </p>
                        <p style="color: rgba(255, 255, 255, 0.8); line-height: 1.8; margin-bottom: 1.5rem;">
                            Notre équipe de passionnés de culture et de technologie a donc créé une plateforme qui utilise l'intelligence artificielle pour personnaliser les recommandations et rendre la découverte culturelle plus accessible à tous.
                        </p>
                        <p style="color: rgba(255, 255, 255, 0.8); line-height: 1.8;">
                            Aujourd'hui, Culture Radar aide des milliers d'utilisateurs à découvrir leur prochaine expérience culturelle préférée.
                        </p>
                    </div>
                    <div class="col-md-6">
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-number">10K+</div>
                                <div class="stat-label">Événements référencés</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number">50+</div>
                                <div class="stat-label">Villes couvertes</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number">5K+</div>
                                <div class="stat-label">Utilisateurs actifs</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number">95%</div>
                                <div class="stat-label">Satisfaction utilisateur</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Team Section -->
        <section class="section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Notre Équipe</h2>
                    <p class="section-subtitle">
                        Une équipe passionnée alliant expertise technologique et amour de la culture
                    </p>
                </div>

                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">=h=»</div>
                        <h3 class="feature-title">Équipe Technique</h3>
                        <p class="feature-description">
                            Développeurs et data scientists spécialisés dans l'intelligence artificielle et les technologies web modernes.
                        </p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">🎭</div>
                        <h3 class="feature-title">Experts Culturels</h3>
                        <p class="feature-description">
                            Critiques, journalistes culturels et passionnés qui enrichissent notre compréhension de l'écosystème culturel.
                        </p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">🎨</div>
                        <h3 class="feature-title">Designers UX/UI</h3>
                        <p class="feature-description">
                            Créateurs d'expériences utilisateur intuitives et esthétiques qui rendent la culture accessible à tous.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Values Section -->
        <section class="section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Nos Valeurs</h2>
                </div>

                <div class="categories-grid">
                    <div class="category-card">
                        <div class="category-icon">♿</div>
                        <div class="category-info">
                            <h3>Accessibilité</h3>
                            <p>Rendre la culture accessible à tous, peu importe le budget ou la localisation</p>
                        </div>
                    </div>

                    <div class="category-card">
                        <div class="category-icon">📡</div>
                        <div class="category-info">
                            <h3>Personnalisation</h3>
                            <p>Chaque utilisateur mérite des recommandations adaptées à ses goûts uniques</p>
                        </div>
                    </div>

                    <div class="category-card">
                        <div class="category-icon"><</div>
                        <div class="category-info">
                            <h3>Excellence</h3>
                        <p>Qualité et innovation dans chaque fonctionnalité que nous développons</p>
                        </div>
                    </div>

                    <div class="category-card">
                        <div class="category-icon">♿</div>
                        <div class="category-info">
                            <h3>Innovation</h3>
                            <p>Utiliser la technologie pour transformer l'expérience culturelle</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="container">
                <h2>Rejoignez l'aventure Culture Radar</h2>
                <p>Découvrez dès maintenant votre prochaine expérience culturelle</p>
                <div class="hero-cta">
                    <a href="register.php" class="cta-button">
                        <span>🚀</span>
                        Commencer gratuitement
                    </a>
                    <a href="discover.php" class="btn-secondary">
                        <span>🚀</span>
                        Explorer les événements
                    </a>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <style>
    .hero-small {
        min-height: 60vh;
        padding-top: 120px;
    }
    
    .hero-small .hero-content {
        max-width: 800px;
    }
    
    .main-content {
        margin-top: 0;
    }
    
    .row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        align-items: center;
    }
    
    .col-md-6 {
        display: flex;
        flex-direction: column;
    }
    
    @media (max-width: 768px) {
        .row {
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        
        .hero-small {
            min-height: 40vh;
            padding-top: 100px;
        }
    }
    </style>

    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
</body>
</html>