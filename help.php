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
    <meta name="description" content="Centre d'aide Culture Radar - Trouvez des r�ponses � vos questions sur l'utilisation de notre plateforme de d�couverte d'�v�nements culturels.">
    
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
                        Trouvez rapidement des r�ponses � vos questions sur Culture Radar
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
                    <h2 class="section-title">Cat�gories d'aide</h2>
                    <p class="section-subtitle">S�lectionnez une cat�gorie pour trouver des r�ponses</p>
                </div>

                <div class="categories-grid">
                    <div class="category-card help-category" data-category="account">
                        <div class="category-icon">=d</div>
                        <div class="category-info">
                            <h3>Mon compte</h3>
                            <p>Inscription, connexion, param�tres de profil</p>
                        </div>
                        <div class="category-arrow">�</div>
                    </div>

                    <div class="category-card help-category" data-category="discover">
                        <div class="category-icon">=</div>
                        <div class="category-info">
                            <h3>D�couverte d'�v�nements</h3>
                            <p>Recherche, filtres, recommandations</p>
                        </div>
                        <div class="category-arrow">�</div>
                    </div>

                    <div class="category-card help-category" data-category="recommendations">
                        <div class="category-icon">></div>
                        <div class="category-info">
                            <h3>Recommandations IA</h3>
                            <p>Comment fonctionne notre syst�me de recommandations</p>
                        </div>
                        <div class="category-arrow">�</div>
                    </div>

                    <div class="category-card help-category" data-category="technical">
                        <div class="category-icon">�</div>
                        <div class="category-info">
                            <h3>Probl�mes techniques</h3>
                            <p>Bugs, erreurs, probl�mes de connexion</p>
                        </div>
                        <div class="category-arrow">�</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Questions Fr�quentes</h2>
                </div>

                <!-- Account FAQs -->
                <div class="faq-category" id="account-faqs">
                    <h3 class="faq-category-title">=d Mon compte</h3>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>Comment cr�er un compte Culture Radar ?</h4>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>Pour cr�er un compte, cliquez sur "S'inscrire" en haut � droite de la page. Remplissez le formulaire avec votre nom, email et mot de passe. Confirmez votre inscription via l'email de validation que vous recevrez.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>J'ai oubli� mon mot de passe, comment le r�cup�rer ?</h4>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>Cliquez sur "Mot de passe oubli� ?" sur la page de connexion. Entrez votre adresse email et vous recevrez un lien pour r�initialiser votre mot de passe.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>Comment modifier mes pr�f�rences culturelles ?</h4>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>Une fois connect�, allez dans votre Dashboard puis cliquez sur "Param�tres". Vous pourrez y modifier vos cat�gories pr�f�r�es, votre localisation et d'autres pr�f�rences.</p>
                        </div>
                    </div>
                </div>

                <!-- Discovery FAQs -->
                <div class="faq-category" id="discover-faqs">
                    <h3 class="faq-category-title">= D�couverte d'�v�nements</h3>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>Comment rechercher des �v�nements dans ma ville ?</h4>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>Utilisez la barre de recherche en sp�cifiant votre ville, ou activez la g�olocalisation pour d�couvrir automatiquement les �v�nements pr�s de chez vous. Vous pouvez aussi filtrer par distance dans les options de recherche.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>Comment utiliser les filtres de recherche ?</h4>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>Les filtres vous permettent d'affiner votre recherche par cat�gorie (concerts, expositions, th��tre...), date, prix et distance. Cliquez sur l'ic�ne filtres pour acc�der � toutes les options.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>Puis-je sauvegarder des �v�nements pour plus tard ?</h4>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>Oui ! Cliquez sur le cSur sur chaque �v�nement pour l'ajouter � vos favoris. Retrouvez tous vos �v�nements sauvegard�s dans votre Dashboard.</p>
                        </div>
                    </div>
                </div>

                <!-- AI Recommendations FAQs -->
                <div class="faq-category" id="recommendations-faqs">
                    <h3 class="faq-category-title">> Recommandations IA</h3>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>Comment fonctionnent les recommandations personnalis�es ?</h4>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>Notre IA analyse vos pr�f�rences, vos interactions pass�es et les �v�nements que vous avez aim�s pour vous proposer des recommandations personnalis�es. Plus vous utilisez la plateforme, plus les suggestions deviennent pr�cises.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>Pourquoi certaines recommandations ne me plaisent pas ?</h4>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>L'IA apprend de vos interactions. Utilisez les boutons "J'aime" et "Je n'aime pas" pour am�liorer les futures recommandations. Vous pouvez aussi mettre � jour vos pr�f�rences dans vos param�tres.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>Comment am�liorer mes recommandations ?</h4>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>Interagissez avec les �v�nements (likes, favoris, participations), mettez � jour r�guli�rement vos pr�f�rences et donnez des avis apr�s avoir assist� � des �v�nements.</p>
                        </div>
                    </div>
                </div>

                <!-- Technical FAQs -->
                <div class="faq-category" id="technical-faqs">
                    <h3 class="faq-category-title">� Probl�mes techniques</h3>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>Le site ne se charge pas correctement, que faire ?</h4>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>Essayez de vider le cache de votre navigateur, d�sactiver les extensions, ou utiliser un autre navigateur. Si le probl�me persiste, contactez notre support technique.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>L'application est-elle compatible avec mon navigateur ?</h4>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>Culture Radar est optimis� pour Chrome, Firefox, Safari et Edge (versions r�centes). Pour une meilleure exp�rience, nous recommandons de maintenir votre navigateur � jour.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>Comment signaler un bug ou un probl�me ?</h4>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>Utilisez notre formulaire de contact en d�crivant le probl�me rencontr�, votre navigateur et les �tapes pour reproduire le bug. Notre �quipe technique vous r�pondra rapidement.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Support -->
        <section class="section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Besoin d'aide personnalis�e ?</h2>
                    <p class="section-subtitle">Notre �quipe support est l� pour vous aider</p>
                </div>

                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">=�</div>
                        <h3 class="feature-title">Email Support</h3>
                        <p class="feature-description">
                            �crivez-nous � support@cultureradar.fr<br>
                            R�ponse sous 24h en moyenne
                        </p>
                        <a href="contact.php" class="btn-primary" style="margin-top: 1rem;">Nous contacter</a>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">=�</div>
                        <h3 class="feature-title">Chat en ligne</h3>
                        <p class="feature-description">
                            Assistance en temps r�el<br>
                            Disponible de 9h � 18h
                        </p>
                        <button class="btn-primary" style="margin-top: 1rem;" onclick="openChat()">D�marrer le chat</button>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">=�</div>
                        <h3 class="feature-title">Documentation</h3>
                        <p class="feature-description">
                            Guides d�taill�s et tutoriels<br>
                            pour ma�triser la plateforme
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
        alert('Chat en ligne bient�t disponible ! En attendant, n\'h�sitez pas � nous contacter par email.');
    }
    </script>
</body>
</html>