<?php
/**
 * Footer component for Culture Radar
 */
?>

<footer class="site-footer">
    <div class="footer-container">
        <div class="footer-content">
            <div class="footer-section">
                <h3 class="footer-title">Culture Radar</h3>
                <p class="footer-description">
                    Votre boussole culturelle intelligente pour découvrir les événements qui vous correspondent.
                </p>
                <div class="social-links">
                    <a href="#" aria-label="Facebook"><span>📘</span></a>
                    <a href="#" aria-label="Twitter"><span>🐦</span></a>
                    <a href="#" aria-label="Instagram"><span>📷</span></a>
                </div>
            </div>
            
            <div class="footer-section">
                <h4 class="footer-subtitle">Découvrir</h4>
                <ul class="footer-links">
                    <li><a href="events.php">Tous les événements</a></li>
                    <li><a href="venues.php">Lieux culturels</a></li>
                    <li><a href="artists.php">Artistes</a></li>
                    <li><a href="calendar.php">Calendrier</a></li>
                </ul>
                <h4 class="footer-subtitle" style="margin-top: 20px;">Villes</h4>
                <ul class="footer-links">
                    <li><a href="events.php?city=paris">Paris</a></li>
                    <li><a href="events.php?city=lyon">Lyon</a></li>
                    <li><a href="events.php?city=marseille">Marseille</a></li>
                    <li><a href="events.php?city=toulouse">Toulouse</a></li>
                    <li><a href="events.php?city=bordeaux">Bordeaux</a></li>
                    <li><a href="events.php?city=lille">Lille</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4 class="footer-subtitle">Ressources</h4>
                <ul class="footer-links">
                    <li><a href="about.php">À propos</a></li>
                    <li><a href="help.php">Centre d'aide</a></li>
                    <li><a href="blog.php">Blog</a></li>
                    <li><a href="partners.php">Partenaires</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4 class="footer-subtitle">Légal</h4>
                <ul class="footer-links">
                    <li><a href="privacy.php">Confidentialité</a></li>
                    <li><a href="terms.php">Conditions d'utilisation</a></li>
                    <li><a href="legal.php">Mentions légales</a></li>
                    <li><a href="cookies.php">Cookies</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Culture Radar. Tous droits réservés.</p>
            <p class="footer-tech">Propulsé par IA & OpenAgenda</p>
        </div>
    </div>
</footer>

<style>
.site-footer {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    color: #fff;
    padding: 60px 0 20px;
    margin-top: 80px;
}

.footer-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.footer-content {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr;
    gap: 40px;
    margin-bottom: 40px;
}

.footer-section {
    display: flex;
    flex-direction: column;
}

.footer-title {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 15px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.footer-subtitle {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 15px;
    color: #fff;
}

.footer-description {
    color: #a0a0a0;
    line-height: 1.6;
    margin-bottom: 20px;
}

.social-links {
    display: flex;
    gap: 15px;
}

.social-links a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    text-decoration: none;
    transition: all 0.3s;
}

.social-links a:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    transform: translateY(-3px);
}

.social-links span {
    font-size: 1.2rem;
}

.footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links li {
    margin-bottom: 10px;
}

.footer-links a {
    color: #a0a0a0;
    text-decoration: none;
    transition: color 0.3s;
}

.footer-links a:hover {
    color: #667eea;
}

.footer-bottom {
    padding-top: 30px;
    border-top: 1px solid rgba(255,255,255,0.1);
    text-align: center;
    color: #a0a0a0;
}

.footer-tech {
    margin-top: 10px;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .footer-content {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .site-footer {
        padding: 40px 0 20px;
        margin-top: 40px;
    }
}
</style>