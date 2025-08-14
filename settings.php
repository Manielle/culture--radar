<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/classes/Auth.php';

$auth = new Auth();

if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user = $auth->getCurrentUser();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres - Culture Radar</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <?php include 'includes/favicon.php'; ?>
    
    <style>
        .settings-container {
            min-height: 100vh;
            background: #0a0a0f;
            padding-top: 80px;
        }
        
        .settings-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 3rem;
        }
        
        .settings-sidebar {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
            height: fit-content;
        }
        
        .settings-nav {
            list-style: none;
            padding: 0;
        }
        
        .settings-nav-item {
            margin-bottom: 0.5rem;
        }
        
        .settings-nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .settings-nav-link:hover {
            background: rgba(255, 255, 255, 0.05);
            color: white;
        }
        
        .settings-nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .settings-content {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 2rem;
        }
        
        .settings-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .settings-title {
            color: white;
            font-size: 1.8rem;
            font-weight: 600;
        }
        
        .settings-section {
            margin-bottom: 2rem;
        }
        
        .settings-section-title {
            color: white;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #667eea;
            background: rgba(255, 255, 255, 0.08);
        }
        
        .form-select {
            width: 100%;
            padding: 0.75rem 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            cursor: pointer;
        }
        
        .form-checkbox {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: rgba(255, 255, 255, 0.8);
        }
        
        .btn-save {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
        }
        
        .btn-save:hover {
            transform: translateY(-2px);
        }
        
        .btn-cancel {
            background: transparent;
            color: rgba(255, 255, 255, 0.7);
            padding: 0.75rem 2rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            margin-left: 1rem;
        }
        
        .btn-cancel:hover {
            background: rgba(255, 255, 255, 0.05);
            color: white;
        }
        
        @media (max-width: 768px) {
            .settings-wrapper {
                grid-template-columns: 1fr;
            }
            
            .settings-sidebar {
                display: none;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="settings-container">
        <div class="settings-wrapper">
            <aside class="settings-sidebar">
                <ul class="settings-nav">
                    <li class="settings-nav-item">
                        <a href="#profile" class="settings-nav-link active">
                            <i class="fas fa-user"></i>
                            <span>Profil</span>
                        </a>
                    </li>
                    <li class="settings-nav-item">
                        <a href="#preferences" class="settings-nav-link">
                            <i class="fas fa-sliders-h"></i>
                            <span>Préférences</span>
                        </a>
                    </li>
                    <li class="settings-nav-item">
                        <a href="#notifications" class="settings-nav-link">
                            <i class="fas fa-bell"></i>
                            <span>Notifications</span>
                        </a>
                    </li>
                    <li class="settings-nav-item">
                        <a href="#privacy" class="settings-nav-link">
                            <i class="fas fa-lock"></i>
                            <span>Confidentialité</span>
                        </a>
                    </li>
                    <li class="settings-nav-item">
                        <a href="#account" class="settings-nav-link">
                            <i class="fas fa-cog"></i>
                            <span>Compte</span>
                        </a>
                    </li>
                </ul>
            </aside>
            
            <main class="settings-content">
                <div class="settings-header">
                    <h1 class="settings-title">Paramètres du profil</h1>
                </div>
                
                <form id="settingsForm">
                    <div class="settings-section">
                        <h2 class="settings-section-title">Informations personnelles</h2>
                        
                        <div class="form-group">
                            <label class="form-label">Nom</label>
                            <input type="text" class="form-input" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" />
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-input" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" />
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Téléphone</label>
                            <input type="tel" class="form-input" placeholder="+33 6 12 34 56 78" />
                        </div>
                    </div>
                    
                    <div class="settings-section">
                        <h2 class="settings-section-title">Localisation</h2>
                        
                        <div class="form-group">
                            <label class="form-label">Ville</label>
                            <select class="form-select">
                                <option>Paris</option>
                                <option>Lyon</option>
                                <option>Marseille</option>
                                <option>Toulouse</option>
                                <option>Bordeaux</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Rayon de recherche</label>
                            <select class="form-select">
                                <option>5 km</option>
                                <option>10 km</option>
                                <option>20 km</option>
                                <option>50 km</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="settings-section">
                        <h2 class="settings-section-title">Préférences de notification</h2>
                        
                        <div class="form-group">
                            <label class="form-checkbox">
                                <input type="checkbox" checked>
                                <span>Nouveaux événements dans ma zone</span>
                            </label>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-checkbox">
                                <input type="checkbox" checked>
                                <span>Rappels d'événements sauvegardés</span>
                            </label>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-checkbox">
                                <input type="checkbox">
                                <span>Newsletter hebdomadaire</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-save">Enregistrer les modifications</button>
                        <button type="button" class="btn-cancel" onclick="window.location.href='dashboard.php'">Annuler</button>
                    </div>
                </form>
            </main>
        </div>
    </div>
    
    <script>
        // Gestion de la navigation dans les paramètres
        document.querySelectorAll('.settings-nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.settings-nav-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });
        
        // Gestion du formulaire
        document.getElementById('settingsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Paramètres enregistrés avec succès !');
        });
    </script>
</body>
</html>