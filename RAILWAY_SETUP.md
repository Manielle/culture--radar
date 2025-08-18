# Guide de Configuration MySQL sur Railway pour Culture Radar

## 📋 Prérequis
- Compte Railway (railway.app)
- Repository GitHub avec votre code
- Railway CLI (optionnel mais recommandé)

## 🚀 Configuration étape par étape

### 1. Création du projet sur Railway

1. Connectez-vous à [Railway.app](https://railway.app)
2. Cliquez sur **"New Project"**
3. Sélectionnez **"Deploy from GitHub repo"**
4. Autorisez Railway à accéder à votre repository GitHub
5. Sélectionnez le repository `culture-radar`

### 2. Ajout de MySQL

1. Dans votre projet Railway, cliquez sur **"New Service"**
2. Sélectionnez **"Database"** → **"MySQL"**
3. Railway va automatiquement créer une instance MySQL avec les variables d'environnement

### 3. Variables d'environnement MySQL

Railway génère automatiquement ces variables :
- `MYSQLHOST` - Hostname de votre base de données
- `MYSQLPORT` - Port (généralement 3306)
- `MYSQLUSER` - Nom d'utilisateur (root)
- `MYSQLPASSWORD` - Mot de passe généré
- `MYSQLDATABASE` - Nom de la base de données
- `MYSQL_URL` - URL de connexion complète

### 4. Configuration des variables d'API

Dans Railway, ajoutez ces variables d'environnement :

```bash
OPENAGENDA_API_KEY=votre_cle_api
PARIS_OPEN_DATA_KEY=votre_cle_api
SERP_API_KEY=b56aa6ec92f9f569f50f671e5133d46d5131c74c260086c37f5222bf489f2d4d
OPENWEATHER_API_KEY=votre_cle_api
```

Pour ajouter des variables :
1. Cliquez sur votre service web
2. Onglet **"Variables"**
3. Cliquez sur **"New Variable"**
4. Ajoutez chaque paire clé/valeur

### 5. Déploiement de l'application

Railway utilise le `Dockerfile.railway` pour construire votre application :

```dockerfile
FROM php:8.1-apache

# Installation des extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Configuration Apache
RUN a2enmod rewrite
COPY . /var/www/html/

# Permissions
RUN chown -R www-data:www-data /var/www/html
```

### 6. Initialisation de la base de données

#### Option A : Via Railway CLI

```bash
# Installation de Railway CLI
npm install -g @railway/cli

# Connexion à votre projet
railway login
railway link

# Exécution du script d'initialisation
railway run php scripts/init-railway-db.php
```

#### Option B : Via le Shell Railway

1. Dans Railway, cliquez sur votre service web
2. Onglet **"Settings"** → **"Shell"**
3. Exécutez :
```bash
php scripts/init-railway-db.php
```

### 7. Vérification du déploiement

1. Vérifiez le health check :
   ```
   https://votre-app.railway.app/health.php
   ```

2. Testez la connexion :
   ```
   https://votre-app.railway.app/config-railway.php?debug=1
   ```

## 🔧 Configuration dans votre code

Le fichier `config-railway.php` détecte automatiquement l'environnement Railway :

```php
// Détection automatique
$isRailway = getenv('MYSQLHOST') !== false;

if ($isRailway) {
    // Configuration Railway
    define('DB_HOST', getenv('MYSQLHOST'));
    define('DB_NAME', getenv('MYSQLDATABASE'));
    // ...
} else {
    // Configuration locale
    define('DB_HOST', 'localhost');
    // ...
}
```

## 📝 Utilisation dans votre code

### Connexion à la base de données

```php
require_once 'config-railway.php';

// Obtenir une connexion PDO
$pdo = getDatabaseConnection();

// Exemple de requête
$stmt = $pdo->query("SELECT * FROM events");
$events = $stmt->fetchAll();
```

### Test de connexion

```php
if (testDatabaseConnection()) {
    echo "Connexion réussie!";
} else {
    echo "Erreur de connexion";
}
```

## 🐛 Dépannage

### Erreur 502 Bad Gateway

1. Vérifiez les logs :
   - Railway Dashboard → Service → Logs
   
2. Vérifiez la connexion MySQL :
   - Accédez à `/health.php`
   
3. Vérifiez les variables d'environnement :
   - Toutes les variables MySQL sont-elles présentes ?

### Base de données non accessible

1. Vérifiez que MySQL est déployé et actif
2. Vérifiez les variables d'environnement
3. Testez avec le script :
   ```bash
   railway run php -r "var_dump(getenv('MYSQLHOST'));"
   ```

### Tables non créées

Exécutez le script d'initialisation :
```bash
railway run php scripts/init-railway-db.php
```

## 📊 Monitoring

Railway offre un monitoring intégré :
1. **Metrics** : CPU, RAM, Network
2. **Logs** : Temps réel et historique
3. **Deployments** : Historique des déploiements

## 🔐 Sécurité

1. **Ne jamais commiter** les credentials dans le code
2. Utilisez toujours les **variables d'environnement**
3. Activez **2FA** sur votre compte Railway
4. Limitez les **IP autorisées** si possible

## 📚 Ressources

- [Documentation Railway](https://docs.railway.app)
- [Railway CLI](https://docs.railway.app/develop/cli)
- [Support Railway](https://help.railway.app)

## ✅ Checklist de déploiement

- [ ] Projet créé sur Railway
- [ ] MySQL ajouté au projet
- [ ] Variables d'environnement configurées
- [ ] Code poussé sur GitHub
- [ ] Déploiement réussi
- [ ] Base de données initialisée
- [ ] Health check fonctionnel
- [ ] Site accessible publiquement

---

Pour toute question, consultez la documentation Railway ou contactez le support.