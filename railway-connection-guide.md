# Connexion Automatique MySQL ↔ Web App sur Railway

## 🎯 Méthode Simple : Référencement de Service

### 1. Dans le Dashboard Railway :

1. **Allez dans votre projet Railway**
2. Vous devriez voir 2 services :
   - 🌐 **Web Service** (votre app PHP)
   - 🗄️ **MySQL** (base de données)

### 2. Connecter les services :

#### Option A : Via l'Interface Graphique
1. **Cliquez sur votre Web Service**
2. Allez dans l'onglet **"Variables"**
3. Cliquez sur **"Add Reference Variable"**
4. Sélectionnez **MySQL** dans la liste
5. Railway va automatiquement ajouter TOUTES les variables MySQL

#### Option B : Via Railway CLI
```bash
railway link
railway service link mysql
```

### 3. Variables Auto-Injectées

Une fois connectés, Railway injecte automatiquement ces variables dans votre web service :
- `MYSQL_URL` - URL complète de connexion
- `MYSQLHOST` - Hostname interne
- `MYSQLPORT` - Port (3306)
- `MYSQLUSER` - Utilisateur
- `MYSQLPASSWORD` - Mot de passe
- `MYSQLDATABASE` - Nom de la base

## 🔄 Configuration PHP Simplifiée

```php
<?php
// config.php - Version ultra simple
// Railway injecte automatiquement les variables quand les services sont liés

// Détection automatique Railway
if (getenv('MYSQL_URL')) {
    // Railway a injecté l'URL MySQL
    $mysql_url = getenv('MYSQL_URL');
    
    // Parser l'URL pour obtenir les composants
    $url = parse_url($mysql_url);
    
    define('DB_HOST', $url['host']);
    define('DB_PORT', $url['port']);
    define('DB_USER', $url['user']);
    define('DB_PASS', $url['pass']);
    define('DB_NAME', ltrim($url['path'], '/'));
} else {
    // Configuration locale
    define('DB_HOST', 'localhost');
    define('DB_PORT', '8889');
    define('DB_USER', 'root');
    define('DB_PASS', 'root');
    define('DB_NAME', 'culture_radar');
}

// Connexion PDO simple
function getDB() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        return new PDO($dsn, DB_USER, DB_PASS);
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}
?>
```

## 🎮 Utilisation du Railway Dashboard

### Vue Graphique des Services Connectés :

```
┌─────────────────┐      Variables      ┌─────────────────┐
│                 │  ←───────────────→  │                 │
│   Web Service   │     Auto-Linked     │     MySQL       │
│   (PHP App)     │                     │    Database     │
│                 │                     │                 │
└─────────────────┘                     └─────────────────┘
         ↑                                       ↑
         │                                       │
    Déployé sur                             Provisionné
    Railway App                             Automatiquement
```

## ⚡ Avantages de la Connexion Native

1. **Zero Configuration** : Pas besoin de copier-coller les variables
2. **Sécurité** : Les credentials restent internes à Railway
3. **Mise à jour automatique** : Si MySQL change, les variables sont mises à jour
4. **Network interne** : Communication via réseau privé Railway (plus rapide)

## 🚀 Étapes Complètes

### 1️⃣ Créer les services
```bash
# Dans votre terminal
railway login
railway init
railway add mysql
railway add
```

### 2️⃣ Lier les services (Dashboard)
- Cliquez sur le Web Service
- Variables → Add Reference → MySQL
- ✅ Connecté !

### 3️⃣ Déployer
```bash
git push
# Railway déploie automatiquement
```

### 4️⃣ Vérifier la connexion
```bash
railway logs
# Ou visitez : https://votre-app.railway.app/health.php
```

## 🔍 Debugging

### Voir les variables injectées :
```php
// test-connection.php
<?php
echo "<h2>Variables Railway MySQL:</h2>";
echo "<pre>";
echo "MYSQL_URL: " . getenv('MYSQL_URL') . "\n";
echo "MYSQLHOST: " . getenv('MYSQLHOST') . "\n";
echo "MYSQLPORT: " . getenv('MYSQLPORT') . "\n";
echo "MYSQLDATABASE: " . getenv('MYSQLDATABASE') . "\n";
echo "</pre>";

// Test connexion
try {
    $pdo = new PDO(getenv('MYSQL_URL'));
    echo "<p style='color:green'>✅ Connexion MySQL réussie!</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Erreur: " . $e->getMessage() . "</p>";
}
?>
```

## 📝 Notes Importantes

- **Pas besoin de copier les variables** manuellement
- Les services doivent être **dans le même projet Railway**
- La connexion utilise le **réseau interne** Railway (plus rapide et sécurisé)
- Les variables sont **automatiquement synchronisées**

## 🆘 Commandes Utiles

```bash
# Voir tous les services
railway status

# Voir les variables d'un service
railway variables

# Se connecter à MySQL directement
railway connect mysql

# Logs en temps réel
railway logs --tail
```

---

C'est LA méthode la plus simple sur Railway ! Les services se parlent automatiquement une fois liés. 🎉