# 🤖 Guide d'Automatisation Culture Radar

## Comment ça marche AUTOMATIQUEMENT ?

### 1️⃣ Installation unique (1 seule fois)

```bash
# Exécuter UNE SEULE FOIS pour tout configurer
bash /root/culture-radar/cron/setup-cron.sh
```

✅ **C'est tout !** Après cette commande, tout est automatique.

### 2️⃣ Ce qui se passe ensuite AUTOMATIQUEMENT

Le système Linux (cron) va :

- **Chaque matin à 6h00** → Lancer le scraping
- **Chaque dimanche à 3h00** → Nettoyer les vieux événements  
- **Chaque jour à 9h00** → Générer un rapport

**Vous n'avez RIEN à faire !** Ça tourne tout seul en arrière-plan.

## 📊 Vérifier que ça fonctionne

### Voir si c'est bien configuré :
```bash
crontab -l
```

Vous devriez voir :
```
0 6 * * * /usr/bin/php /root/culture-radar/cron/scrape-events.php >> /root/culture-radar/cron/logs/cron.log 2>&1
0 3 * * 0 /usr/bin/php /root/culture-radar/cron/cleanup.php >> /root/culture-radar/cron/logs/cleanup.log 2>&1
0 9 * * * /usr/bin/php /root/culture-radar/cron/daily-report.php >> /root/culture-radar/cron/logs/report.log 2>&1
```

### Voir les logs (ce qui s'est passé) :
```bash
# Voir le dernier scraping
tail -f /root/culture-radar/cron/logs/cron.log

# Voir tous les logs d'aujourd'hui
cat /root/culture-radar/cron/logs/scraping_$(date +%Y-%m-%d).log
```

## 🧪 Tester MAINTENANT (sans attendre demain)

### Test rapide :
```bash
# Juste voir si ça marche (sans sauvegarder)
php /root/culture-radar/cron/manual-scrape.php --test

# Vraiment scraper maintenant
php /root/culture-radar/cron/manual-scrape.php
```

## ❓ FAQ

### "Est-ce que je dois laisser mon PC allumé ?"
- **Sur un serveur** : Non, ça tourne 24/7
- **Sur votre PC local** : Oui, il doit être allumé à 6h du matin
- **Solution** : Utilisez un VPS/serveur cloud (5€/mois)

### "Comment je sais si ça a marché ?"
```bash
# Voir le nombre d'événements dans la base
mysql -u root -p culture_radar -e "SELECT COUNT(*) as total FROM events WHERE DATE(last_scraped) = CURDATE();"
```

### "Je veux changer l'heure du scraping"
```bash
# Éditer le cron
crontab -e

# Changer le "0 6" (6h00) par exemple en "0 8" (8h00)
# Format : minute heure jour mois jour_semaine
```

## 🔄 Le cycle automatique

```
6h00 → Scraping
      ↓
   [Google Events API]
      ↓
   Récupère ~1000 événements
      ↓
   Stocke dans MySQL
      ↓
9h00 → Rapport par email
      ↓
   Votre site affiche les nouveaux événements
      ↓
Dimanche 3h00 → Nettoyage automatique
```

## 🚀 Pour les environnements de production

### Sur un VPS Ubuntu/Debian :
```bash
# S'assurer que cron est installé et actif
sudo apt-get install cron
sudo systemctl enable cron
sudo systemctl start cron
```

### Avec Docker :
```dockerfile
# Ajouter dans Dockerfile
RUN apt-get update && apt-get install -y cron
COPY cron/setup-cron.sh /setup-cron.sh
RUN bash /setup-cron.sh
CMD cron && tail -f /var/log/cron.log
```

### Avec systemd (alternative moderne) :
```bash
# Créer un timer systemd
sudo nano /etc/systemd/system/culture-scrape.timer

[Unit]
Description=Culture Radar Daily Scraping

[Timer]
OnCalendar=daily
OnCalendar=06:00
Persistent=true

[Install]
WantedBy=timers.target
```

## 📱 Notifications (optionnel)

### Recevoir une notification sur votre téléphone :
```php
// Ajouter dans scrape-events.php
if ($stats['new_events'] > 0) {
    // Telegram
    $telegram_url = "https://api.telegram.org/bot{TOKEN}/sendMessage";
    $message = "✅ Scraping terminé : {$stats['new_events']} nouveaux événements";
    file_get_contents($telegram_url . "?chat_id={CHAT_ID}&text=" . urlencode($message));
}
```

## 🔴 Résumé SIMPLE

1. **Une fois :** `bash setup-cron.sh`
2. **C'est fini !** Ça tourne tout seul
3. **Vérifier :** `tail -f cron/logs/cron.log`

---

**⚡ EN BREF : Vous lancez setup-cron.sh UNE FOIS, puis vous n'avez plus RIEN à faire !**