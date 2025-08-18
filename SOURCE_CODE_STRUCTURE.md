# Culture Radar - Structure du Code Source

## 📦 Archive Disponible
`culture-radar-source.tar.gz` (379 MB)

## 📁 Structure Principale

```
culture-radar/
├── 📄 **Fichiers Principaux**
│   ├── index.php                    # Point d'entrée principal
│   ├── config.php                   # Configuration avec gestion Railway/local
│   ├── config-railway.php           # Config spécifique Railway
│   ├── login.php                    # Page de connexion
│   ├── register.php                 # Inscription utilisateurs
│   ├── dashboard.php                # Tableau de bord utilisateur
│   ├── discover.php                 # Découverte d'événements
│   └── setup-database.php           # Installation base de données
│
├── 📂 **api/**                      # Endpoints API
│   ├── events-aggregator.php       # Agrégation multi-sources
│   ├── recommendations.php         # IA de recommandation
│   ├── paris-events.php           # Événements Paris
│   └── real-events.php            # Événements temps réel
│
├── 📂 **classes/**                  # Classes PHP
│   ├── RecommendationEngine.php   # Moteur IA (scoring multi-facteurs)
│   ├── BadgeSystem.php            # Système de gamification
│   └── WeatherTransportService.php # Services météo/transport
│
├── 📂 **services/**                 # Services externes
│   ├── OpenAgendaService.php      # API OpenAgenda
│   ├── GoogleMapsService.php      # Google Maps
│   ├── WeatherService.php         # Météo
│   └── TransportService.php       # Transport
│
├── 📂 **admin/**                    # Interface admin
│   ├── dashboard.php              # Dashboard admin
│   └── badges.php                 # Gestion badges
│
├── 📂 **organizer/**               # Interface organisateurs
│   ├── dashboard.php              # Dashboard organisateur
│   ├── events.php                 # Gestion événements
│   └── login.php                  # Connexion organisateur
│
├── 📂 **assets/**                   # Ressources front-end
│   ├── css/
│   │   └── style.css              # Styles principaux
│   └── js/
│       ├── main.js                # JavaScript principal
│       └── ai-recommendations.js  # IA côté client
│
├── 📂 **scripts/**                  # Scripts utilitaires
│   └── train_ai.php              # Entraînement modèle IA
│
├── 🐳 **Docker & Deployment**
│   ├── Dockerfile                 # Container local
│   ├── Dockerfile.railway         # Container Railway
│   ├── docker-compose.yml         # Stack locale
│   ├── railway.json              # Config Railway
│   └── nginx/nginx.conf          # Config serveur web
│
└── 📝 **Documentation**
    ├── DEPLOYMENT.md             # Guide déploiement
    ├── MAMP_SETUP_GUIDE.md      # Setup local
    └── fix-railway-vars.md       # Fix variables Railway
```

## 🔑 Fichiers Clés à Comprendre

### 1. **config.php** - Configuration Intelligente
- Détecte automatiquement l'environnement (Railway/Local)
- Gère les connexions base de données
- Configure les API keys

### 2. **RecommendationEngine.php** - Moteur IA
```php
// Système de scoring multi-facteurs
- Préférences utilisateur: 40%
- Proximité géographique: 25%
- Compatibilité prix: 15%
- Préférence temporelle: 10%
- Signaux sociaux: 5%
- Facteur nouveauté: 5%
```

### 3. **api/events-aggregator.php** - Agrégation
- Combine OpenAgenda, Paris Open Data, Google Events
- Cache intelligent
- Fallback sur données démo

### 4. **Database Schema** (setup-database.php)
- Tables: users, events, venues, preferences, recommendations
- Support multi-tenant (utilisateurs, organisateurs, admin)
- Système de badges et achievements

## 🚀 Variables d'Environnement Requises

```env
# Base de données
MYSQLHOST=centerbeam.proxy.rlwy.net
MYSQLPORT=48330
MYSQLDATABASE=railway
MYSQLUSER=root
MYSQLPASSWORD=tBixYXRKGkGAZuyxGHFZzaTxQAGXvJJH

# APIs
OPENAGENDA_API_KEY=b6cea4ca5dcf4054ae4dd58482b389a1
OPENWEATHERMAP_API_KEY=4f70ce6daf82c0e77d6128bc7fadf972
GOOGLE_MAPS_API_KEY=AIzaSyClDB39stO1egB2L1P3aRymydGjPZ72uNo
SERPAPI_KEY=b56aa6ec92f9f569f50f671e5133d46d5131c74c260086c37f5222bf489f2d4d
```

## 💡 Points Techniques Importants

### Sécurité
- Protection CSRF sur tous les formulaires
- Rate limiting (5 tentatives, 15 min lockout)
- Mots de passe hashés (bcrypt)
- PDO avec requêtes préparées

### Performance
- Cache multi-niveaux (events, weather, transport)
- Lazy loading des recommandations
- Compression gzip
- CDN pour les assets

### IA & Machine Learning
- Apprentissage continu des préférences
- Scoring en temps réel
- Adaptation comportementale
- Training script automatisé

## 📥 Extraction de l'Archive

```bash
# Extraire l'archive
tar -xzf culture-radar-source.tar.gz

# Installation locale (MAMP)
1. Copier dans /Applications/MAMP/htdocs/
2. Importer setup-database.sql
3. Configurer .env avec vos credentials

# Déploiement Railway
1. git push origin essai-safe
2. Configurer variables dans Railway
3. Accéder à l'URL publique
```

## 🔗 URLs Importantes

- **Production**: https://ias-b3-g7-paris.up.railway.app
- **Test DB**: /test-db-connection.php
- **Setup**: /setup-database.php
- **Admin**: /admin/dashboard.php

## 📞 Support

Pour toute question sur le code:
1. Vérifier config.php pour la configuration
2. Consulter les logs dans /logs/
3. Tester avec test-db-connection.php