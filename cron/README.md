# Système de Scraping Automatique - Culture Radar

## 📋 Vue d'ensemble

Ce système permet de récupérer automatiquement les événements culturels depuis plusieurs sources (Google Events, OpenAgenda, etc.) et de les stocker dans une base de données MySQL.

## 🚀 Installation

### 1. Créer la base de données

```bash
mysql -u root -p < setup-database.sql
```

### 2. Configurer le cron job

```bash
chmod +x setup-cron.sh
./setup-cron.sh
```

Cela configurera les tâches suivantes :
- **Scraping quotidien** : 6h00 du matin
- **Nettoyage hebdomadaire** : Dimanche 3h00
- **Rapport quotidien** : 9h00 du matin

### 3. Configurer les clés API

Éditer `/root/culture-radar/config.php` :

```php
define('SERPAPI_KEY', 'votre_clé_serpapi');
define('ADMIN_EMAIL', 'admin@example.com'); // Pour les rapports
```

## 📁 Structure des fichiers

```
cron/
├── scrape-events.php      # Script principal de scraping
├── cleanup.php            # Nettoyage des anciens événements
├── daily-report.php       # Génération du rapport quotidien
├── manual-scrape.php      # Lancement manuel pour tests
├── setup-cron.sh          # Configuration du cron
├── logs/                  # Dossier des logs
│   ├── scraping_*.log     # Logs de scraping
│   ├── cleanup_*.log      # Logs de nettoyage
│   └── report_*.log       # Logs des rapports
└── reports/               # Rapports HTML générés
    └── report_*.html      # Rapports quotidiens
```

## 🔧 Utilisation

### Lancer manuellement le scraping

```bash
# Scrapper toutes les villes
php manual-scrape.php

# Scrapper une ville spécifique
php manual-scrape.php --city=Paris

# Mode test (sans enregistrer)
php manual-scrape.php --test --verbose

# Aide
php manual-scrape.php --help
```

### Voir les logs

```bash
# Logs en temps réel
tail -f logs/scraping_*.log

# Derniers logs
cat logs/scraping_$(date +%Y-%m-%d).log
```

### Vérifier le cron

```bash
# Voir les jobs configurés
crontab -l

# Éditer les jobs
crontab -e

# Voir les logs système du cron
grep CRON /var/log/syslog
```

## 📊 Base de données

### Tables principales

- **events** : Stockage des événements
- **scraping_logs** : Historique des scraping
- **event_sources** : Sources de données configurées
- **event_categories** : Catégories d'événements

### Requêtes utiles

```sql
-- Événements du jour
SELECT * FROM events 
WHERE DATE(start_date) = CURDATE() 
AND is_active = 1 
ORDER BY ai_score DESC;

-- Statistiques par ville
SELECT city, COUNT(*) as total 
FROM events 
WHERE is_active = 1 
GROUP BY city;

-- Derniers scraping
SELECT * FROM scraping_logs 
ORDER BY created_at DESC 
LIMIT 10;
```

## 🔍 Sources de données

### 1. Google Events (SerpAPI)
- **API** : https://serpapi.com/google-events-api
- **Limite** : 100 requêtes/mois (plan gratuit)
- **Données** : Événements Google avec lieu, date, prix

### 2. OpenAgenda (À configurer)
- **API** : https://openagenda.com/api
- **Clé requise** : Demander sur leur site
- **Données** : Événements culturels français

### 3. Eventbrite (À implémenter)
- **API** : https://www.eventbrite.com/platform/api
- **OAuth requis** : Configuration avancée

## 📈 Monitoring

### Dashboard de statistiques

Créer une page PHP pour visualiser :
- Nombre d'événements par jour
- Taux de succès du scraping
- Événements populaires
- Couverture par ville

### Alertes

Configurer des alertes email si :
- Le scraping échoue
- Moins de X événements trouvés
- Erreurs répétées

## 🐛 Dépannage

### Le cron ne s'exécute pas

```bash
# Vérifier que cron est actif
service cron status

# Redémarrer cron
service cron restart

# Vérifier les permissions
chmod +x cron/*.php
```

### Erreurs de base de données

```bash
# Vérifier MySQL
mysql -u root -p -e "SHOW DATABASES;"

# Réparer les tables
mysql -u root -p culture_radar -e "REPAIR TABLE events;"
```

### API rate limiting

- Utiliser plusieurs clés API
- Espacer les requêtes (sleep)
- Implémenter un cache

## 📝 Maintenance

### Tâches régulières

1. **Quotidien** : Vérifier les logs de scraping
2. **Hebdomadaire** : Nettoyer les anciens événements
3. **Mensuel** : Optimiser les tables MySQL
4. **Trimestriel** : Purger les vieux logs

### Commandes utiles

```bash
# Backup de la base
mysqldump -u root -p culture_radar > backup_$(date +%Y%m%d).sql

# Restaurer un backup
mysql -u root -p culture_radar < backup_20240215.sql

# Nettoyer les logs de plus de 30 jours
find logs/ -name "*.log" -mtime +30 -delete
```

## 🚀 Améliorations futures

- [ ] Ajouter plus de sources (Eventbrite, Facebook Events)
- [ ] Machine Learning pour le scoring des événements
- [ ] Détection automatique de doublons
- [ ] API GraphQL pour les requêtes frontend
- [ ] Websockets pour les mises à jour temps réel
- [ ] Docker pour le déploiement

## 📧 Support

Pour toute question ou problème :
1. Vérifier les logs dans `/cron/logs/`
2. Consulter cette documentation
3. Tester avec `manual-scrape.php --test`

---

*Culture Radar - Système de scraping automatique v1.0*