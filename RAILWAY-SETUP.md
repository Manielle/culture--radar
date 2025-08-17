# 🚂 Configuration Railway pour Culture Radar

## Automatisation sur Railway

Railway gère l'automatisation différemment d'un serveur local. Voici comment configurer le scraping automatique :

## Option 1 : Railway Cron Jobs (Recommandé)

### 1. Dans votre dashboard Railway :

1. Allez dans votre projet Culture Radar
2. Cliquez sur **"Settings"** → **"Cron"**
3. Ajoutez un nouveau cron job :
   - **Name** : `Scraping Quotidien`
   - **Schedule** : `0 6 * * *`
   - **Command** : `php cron/scrape-events.php`

### 2. Variables d'environnement Railway :

Dans Railway, ajoutez ces variables :
- `SERPAPI_KEY` = votre clé API
- `MYSQL_URL` = URL de votre base de données Railway

## Option 2 : GitHub Actions (Gratuit)

Si votre code est sur GitHub :

1. Créez le dossier `.github/workflows/`
2. Ajoutez le fichier `railway-cron.yml` (déjà créé)
3. Dans GitHub → Settings → Secrets, ajoutez :
   - `DATABASE_URL` : L'URL MySQL de Railway
   - `SERPAPI_KEY` : Votre clé API

## Option 3 : Service Worker Railway

Créez un service séparé dans Railway :

```javascript
// worker.js
const cron = require('node-cron');
const { exec } = require('child_process');

// Scraping à 6h00
cron.schedule('0 6 * * *', () => {
  exec('php cron/scrape-events.php', (err, stdout) => {
    console.log('Scraping terminé:', stdout);
  });
});

console.log('Worker démarré - Scraping programmé pour 6h00');
```

## 🎯 Pour tester MAINTENANT sur Railway :

### Via Railway CLI :
```bash
# Installer Railway CLI
npm install -g @railway/cli

# Se connecter
railway login

# Lancer le scraping manuellement
railway run php cron/manual-scrape.php --test
```

### Via l'interface Railway :
1. Allez dans votre projet
2. Onglet **"Console"** ou **"Shell"**
3. Tapez : `php cron/manual-scrape.php --test`

## 📊 Voir les logs sur Railway :

1. Dashboard Railway → Votre projet
2. Onglet **"Logs"**
3. Filtrer par : `scrape-events`

## 🔧 Configuration de la base de données :

Railway fournit automatiquement MySQL. Pour initialiser :

```bash
# Dans la console Railway
mysql $MYSQL_URL < setup-database.sql
```

Ou utilisez phpMyAdmin de Railway pour importer le fichier SQL.

## ⚡ Résumé pour Railway :

1. **Pas besoin de cron local** - Railway gère ça
2. **Pas besoin de PHP local** - Tout est sur le cloud
3. **Configuration** dans Railway Dashboard → Settings → Cron
4. **Tests** via Railway Console
5. **Logs** dans Railway Dashboard → Logs

## 🚨 Important :

- Les cron jobs Railway peuvent être payants selon votre plan
- Alternative gratuite : GitHub Actions (limite 2000 min/mois)
- Pour développer localement : utilisez Railway CLI

---

**Besoin d'aide ?** La doc Railway : https://docs.railway.app/reference/cron-jobs