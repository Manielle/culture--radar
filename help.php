<?php
session_start();

// Set page title for header
$pageTitle = "Centre d'aide - Culture Radar";
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <meta name="description" content="Centre d'aide Culture Radar - Trouvez des réponses à vos questions sur l'utilisation de notre plateforme de découverte d'événements culturels.">
    
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
                    <h1 class="gradient-text">Centre d'aide</h1>
                    <p class="hero-subtitle">
                        Trouvez rapidement des réponses à vos questions sur Culture Radar
                    </p>
                </div>
            </div>
        </section>

        <!-- Search Help -->
        <section class="quick-search">
            <div class="container">
                <div class="search-form">
                    <div class="search-input-group">
                        <input type="text" class="search-input" placeholder="Rechercher dans l'aide..." id="helpSearch">
                        <button class="search-button" type="button">
                            <span>=</span>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Categories -->
        <section class="section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Catégories d'aide</h2>
                    <p class="section-subtitle">Sélectionnez une catégorie pour trouver des réponses</p>
                </div>

                <div class="categories-grid">
                    <div class="category-card help-category" data-category="account">
                        <div class="category-icon">=d</div>
                        <div class="category-info">
                            <h3>Mon compte</h3>
                            <p>Inscription, connexion, paramètres de profil</p>
                        </div>
                        <div class="category-arrow">’</div>
                    </div>

                    <div class="category-card help-category" data-category="discover">
                        <div class="category-icon">=</div>
                        <div class="category-info">
                            <h3>Découverte d'événements</h3>
                            <p>Recherche, filtres, recommandations</p>
                        </div>
                        <div class="category-arrow">’</div>
                    </div>

                    <div class="category-card help-category" data-category="recommendations">
                        <div class="category-icon">></div>
                        <div class="category-info">
                            <h3>Recommandations IA</h3>
                            <p>Comment fonctionne notre système de recommandations</p>
                        </div>
                        <div class="category-arrow">’</div>
                    </div>

                    <div class="category-card help-category" data-category="technical">
                        <div class="category-icon">™</div>
                        <div class="category-info">
                            <h3>Problèmes techniques</h3>
                            <p>Bugs, erreurs, problèmes de connexion</p>
                        </div>
                        <div class="category-arrow">’</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Questions Fréquentes</h2>
                </div>

                <!-- Account FAQs -->
                <div class="faq-category" id="account-faqs">
                    <h3 class="faq-category-title">=d Mon compte</h3>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>Comment créer un compte Culture Radar ?</h4>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>Pour créer un compte, cliquez sur "S'inscrire" en haut à droite de la page. Remplissez le formulaire avec votre nom, email et mot de passe. Confirmez votre inscription via l'email de validation que vous recevrez.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>J'ai oublié mon mot de passe, comment le récupérer ?</h4>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>Cliquez sur "Mot de passe oublié ?" sur la page de connexion. Entrez votre adresse email et vous recevrez un lien pour réinitialiser votre mot de passe.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>Comment modifier mes préférences culturelles ?</h4>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>Une fois connecté, allez dans votre Dashboard puis cliquez sur "Paramètres". Vous pourrez y modifier vos catégories préférées, votre localisation et d'autres préférences.</p>
                        </div>
                    </div>
                </div>

                <!-- Discovery FAQs -->
                <div class="faq-category" id="discover-faqs">
                    <h3 class="faq-category-title">= Découverte d'événements</h3>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>Comment rechercher des événements dans ma ville ?</h4>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>Utilisez la barre de recherche en spécifiant votre ville, ou activez la géolocalisation pour découvrir automatiquement les événements près de chez vous. Vous pouvez aussi filtrer par distance dans les options de recherche.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>Comment utiliser les filtres de recherche ?</h4>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>Les filtres vous permettent d'affiner votre recherche par catégorie (concerts, expositions, théâtre...), date, prix et distance. Cliquez sur l'icône filtres pour accéder à toutes les options.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>Puis-je sauvegarder des événements pour plus tard ?</h4>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>Oui ! Cliquez sur le cSur sur chaque événement pour l'ajouter à vos favoris. Retrouvez tous vos événements sauvegardés dans votre Dashboard.</p>
                        </div>
                    </div>
                </div>

                <!-- AI Recommendations FAQs -->
                <div class="faq-category" id="recommendations-faqs">
                    <h3 class="faq-category-title">> Recommandations IA</h3>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>Comment fonctionnent les recommandations personnalisées ?</h4>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>Notre IA analyse vos préférences, vos interactions passées et les événements que vous avez aimés pour vous proposer des recommandations personnalisées. Plus vous utilisez la plateforme, plus les suggestions deviennent précises.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>Pourquoi certaines recommandations ne me plaisent pas ?</h4>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>L'IA apprend de vos interactions. Utilisez les boutons "J'aime" et "Je n'aime pas" pour améliorer les futures recommandations. Vous pouvez aussi mettre à jour vos préférences dans vos paramètres.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>Comment améliorer mes recommandations ?</h4>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>Interagissez avec les événements (likes, favoris, participations), mettez à jour régulièrement vos préférences et donnez des avis après avoir assisté à des événements.</p>
                        </div>
                    </div>
                </div>

                <!-- Technical FAQs -->
                <div class="faq-category" id="technical-faqs">
                    <h3 class="faq-category-title">™ Problèmes techniques</h3>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>Le site ne se charge pas correctement, que faire ?</h4>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>Essayez de vider le cache de votre navigateur, désactiver les extensions, ou utiliser un autre navigateur. Si le problème persiste, contactez notre support technique.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>L'application est-elle compatible avec mon navigateur ?</h4>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>Culture Radar est optimisé pour Chrome, Firefox, Safari et Edge (versions récentes). Pour une meilleure expérience, nous recommandons de maintenir votre navigateur à jour.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>Comment signaler un bug ou un problème ?</h4>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>Utilisez notre formulaire de contact en décrivant le problème rencontré, votre navigateur et les étapes pour reproduire le bug. Notre équipe technique vous répondra rapidement.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Support -->
        <section class="section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Besoin d'aide personnalisée ?</h2>
                    <p class="section-subtitle">Notre équipe support est là pour vous aider</p>
                </div>

                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">=ç</div>
                        <h3 class="feature-title">Email Support</h3>
                        <p class="feature-description">
                            Écrivez-nous à support@cultureradar.fr<br>
                            Réponse sous 24h en moyenne
                        </p>
                        <a href="contact.php" class="btn-primary" style="margin-top: 1rem;">Nous contacter</a>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">=¬</div>
                        <h3 class="feature-title">Chat en ligne</h3>
                        <p class="feature-description">
                            Assistance en temps réel<br>
                            Disponible de 9h à 18h
                        </p>
                        <button class="btn-primary" style="margin-top: 1rem;" onclick="openChat()">Démarrer le chat</button>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">=Ú</div>
                        <h3 class="feature-title">Documentation</h3>
                        <p class="feature-description">
                            Guides détaillés et tutoriels<br>
                            pour maîtriser la plateforme
                        </p>
                        <a href="#" class="btn-primary" style="margin-top: 1rem;">Voir les guides</a>
                    </div>
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
    
    .help-category {
        cursor: pointer;
        transition: var(--transition);
    }
    
    .help-category:hover {
        transform: translateY(-5px);
    }
    
    .faq-category {
        margin-bottom: 3rem;
    }
    
    .faq-category-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: white;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid rgba(255, 255, 255, 0.1);
    }
    
    .faq-item {
        background: var(--glass);
        border: 1px solid var(--glass-border);
        border-radius: var(--border-radius);
        margin-bottom: 1rem;
        backdrop-filter: blur(20px);
        overflow: hidden;
        transition: var(--transition);
    }
    
    .faq-item:hover {
        background: rgba(255, 255, 255, 0.15);
    }
    
    .faq-question {
        padding: 1.5rem;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .faq-question h4 {
        color: white;
        font-weight: 600;
        margin: 0;
        flex: 1;
    }
    
    .faq-toggle {
        color: rgba(255, 255, 255, 0.7);
        font-size: 1.5rem;
        font-weight: bold;
        transition: var(--transition);
        margin-left: 1rem;
    }
    
    .faq-item.active .faq-toggle {
        transform: rotate(45deg);
        color: #667eea;
    }
    
    .faq-answer {
        padding: 0 1.5rem;
        max-height: 0;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .faq-item.active .faq-answer {
        padding: 0 1.5rem 1.5rem 1.5rem;
        max-height: 500px;
    }
    
    .faq-answer p {
        color: rgba(255, 255, 255, 0.8);
        line-height: 1.6;
        margin: 0;
    }
    
    @media (max-width: 768px) {
        .hero-small {
            min-height: 40vh;
            padding-top: 100px;
        }
        
        .faq-question {
            padding: 1rem;
        }
        
        .faq-item.active .faq-answer {
            padding: 0 1rem 1rem 1rem;
        }
    }
    </style>

    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
    <script>
    // FAQ Toggle Functionality
    document.addEventListener('DOMContentLoaded', function() {
        const faqItems = document.querySelectorAll('.faq-item');
        
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            
            question.addEventListener('click', () => {
                const isActive = item.classList.contains('active');
                
                // Close all other FAQ items
                faqItems.forEach(otherItem => {
                    otherItem.classList.remove('active');
                });
                
                // Toggle current item
                if (!isActive) {
                    item.classList.add('active');
                }
            });
        });
        
        // Help search functionality
        const helpSearch = document.getElementById('helpSearch');
        if (helpSearch) {
            helpSearch.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const faqItems = document.querySelectorAll('.faq-item');
                
                faqItems.forEach(item => {
                    const question = item.querySelector('.faq-question h4').textContent.toLowerCase();
                    const answer = item.querySelector('.faq-answer p').textContent.toLowerCase();
                    
                    if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = searchTerm ? 'none' : 'block';
                    }
                });
            });
        }
        
        // Category filtering
        const helpCategories = document.querySelectorAll('.help-category');
        helpCategories.forEach(category => {
            category.addEventListener('click', function() {
                const categoryName = this.dataset.category;
                const categorySection = document.getElementById(categoryName + '-faqs');
                
                if (categorySection) {
                    categorySection.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    });
    
    function openChat() {
        alert('Chat en ligne bientôt disponible ! En attendant, n\'hésitez pas à nous contacter par email.');
    }
    </script>
</body>
</html>