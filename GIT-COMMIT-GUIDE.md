# 📝 Guide pour commiter les changements sur Git

## Fichiers ajoutés/modifiés aujourd'hui :

### 🔧 Système de scraping automatique
- `cron/scrape-events.php` - Script principal de scraping
- `cron/cleanup.php` - Nettoyage hebdomadaire
- `cron/daily-report.php` - Rapport quotidien
- `cron/manual-scrape.php` - Test manuel
- `cron/setup-cron.sh` - Configuration Linux
- `cron/README.md` - Documentation

### 🗄️ Base de données
- `setup-database.sql` - Structure des tables

### 🚂 Configuration Railway
- `railway.json` - Configuration des cron jobs
- `railway.toml` - Config alternative
- `RAILWAY-SETUP.md` - Guide Railway

### 🪟 Scripts Windows
- `LANCER-SCRAPING-WINDOWS.bat` - Script Windows
- `cron/windows-setup.bat` - Setup Windows
- `cron/run-scraping-windows.ps1` - PowerShell

### 🔌 API Endpoints
- `api/trigger-scraping.php` - Endpoint pour déclencher le scraping
- `api/google-events.php` - Intégration Google Events (déjà existant)

### 🔄 GitHub Actions
- `.github/workflows/daily-scraping.yml` - Automation GitHub
- `.github/workflows/railway-cron.yml` - Railway + GitHub

### 📚 Documentation
- `AUTOMATISATION.md` - Guide d'automatisation

## Commandes Git à exécuter :

```bash
# 1. Aller dans votre dossier
cd C:\Users\InessDJABA\Desktop\Manouk\culture-radar

# 2. Voir le statut
git status

# 3. Ajouter tous les nouveaux fichiers
git add cron/
git add setup-database.sql
git add railway.json railway.toml
git add RAILWAY-SETUP.md AUTOMATISATION.md
git add LANCER-SCRAPING-WINDOWS.bat
git add api/trigger-scraping.php
git add .github/workflows/
git add GIT-COMMIT-GUIDE.md

# Ou tout ajouter d'un coup :
git add .

# 4. Créer le commit
git commit -m "✨ Add automated event scraping system

- Implement daily scraping with SerpAPI Google Events
- Add database structure for events storage
- Configure Railway cron jobs
- Add Windows batch scripts for local testing
- Setup GitHub Actions for automation
- Create cleanup and reporting scripts
- Add comprehensive documentation"

# 5. Pousser sur GitHub
git push origin main
# ou
git push
```

## ⚠️ Avant de push, vérifiez :

### Fichiers à NE PAS commiter :
```bash
# Créez/vérifiez .gitignore
echo "cron/logs/" >> .gitignore
echo "cron/reports/" >> .gitignore
echo "*.log" >> .gitignore
echo ".env" >> .gitignore
echo "config.local.php" >> .gitignore
```

### Sécurité - Remplacez les clés sensibles :
1. Ouvrez `api/trigger-scraping.php`
2. Changez `votre_cle_secrete_ici_234kj23h4` par une vraie clé secrète
3. Stockez la vraie clé API SerpAPI dans Railway (pas dans le code)

## 🚀 Après le push :

1. **Railway va automatiquement** :
   - Détecter le nouveau `railway.json`
   - Activer les cron jobs
   - Redéployer votre app

2. **Vérifiez dans Railway** :
   - Dashboard → Deployments
   - Vous devriez voir un nouveau déploiement

3. **Testez** :
   - Allez sur votre site
   - Essayez : `https://votre-app.railway.app/api/trigger-scraping.php?secret=VOTRE_CLE`

## 📋 Commandes simplifiées (copiez-collez) :

```bash
# Tout en une fois
cd C:\Users\InessDJABA\Desktop\Manouk\culture-radar
git add .
git status
git commit -m "Add automated event scraping system with Railway cron jobs"
git push
```

## ❓ Problèmes possibles :

### "fatal: not a git repository"
```bash
git init
git remote add origin https://github.com/VOTRE_USERNAME/culture-radar.git
```

### "error: failed to push"
```bash
git pull origin main --rebase
git push
```

### "Changes not staged"
```bash
git add -A
git status
```

---

Prêt à commiter ? Lancez les commandes dans l'ordre ! 🚀