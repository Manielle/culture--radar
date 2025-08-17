#!/bin/bash

# Script de configuration du cron job pour le scraping automatique
# À exécuter une seule fois pour configurer le système

echo "=== Configuration du cron job Culture Radar ==="

# Créer les dossiers nécessaires
mkdir -p /root/culture-radar/cron/logs
chmod 755 /root/culture-radar/cron/logs

# Rendre le script de scraping exécutable
chmod +x /root/culture-radar/cron/scrape-events.php

# Sauvegarder le crontab actuel
crontab -l > /tmp/current_cron 2>/dev/null || true

# Vérifier si le job existe déjà
if grep -q "scrape-events.php" /tmp/current_cron; then
    echo "⚠️  Le cron job existe déjà. Mise à jour..."
    # Retirer l'ancien job
    grep -v "scrape-events.php" /tmp/current_cron > /tmp/new_cron
else
    cp /tmp/current_cron /tmp/new_cron 2>/dev/null || touch /tmp/new_cron
fi

# Ajouter le nouveau cron job
# Exécution tous les jours à 6h00 du matin
echo "0 6 * * * /usr/bin/php /root/culture-radar/cron/scrape-events.php >> /root/culture-radar/cron/logs/cron.log 2>&1" >> /tmp/new_cron

# Optionnel: Ajouter d'autres jobs
# Nettoyage hebdomadaire des anciens événements (dimanche à 3h)
echo "0 3 * * 0 /usr/bin/php /root/culture-radar/cron/cleanup.php >> /root/culture-radar/cron/logs/cleanup.log 2>&1" >> /tmp/new_cron

# Rapport quotidien par email (à 9h)
echo "0 9 * * * /usr/bin/php /root/culture-radar/cron/daily-report.php >> /root/culture-radar/cron/logs/report.log 2>&1" >> /tmp/new_cron

# Installer le nouveau crontab
crontab /tmp/new_cron

# Nettoyer
rm /tmp/current_cron /tmp/new_cron 2>/dev/null || true

echo "✅ Cron job configuré avec succès!"
echo ""
echo "Jobs programmés:"
echo "  - Scraping quotidien: tous les jours à 6h00"
echo "  - Nettoyage hebdomadaire: dimanche à 3h00"
echo "  - Rapport quotidien: tous les jours à 9h00"
echo ""
echo "Pour vérifier les jobs:"
echo "  crontab -l"
echo ""
echo "Pour voir les logs:"
echo "  tail -f /root/culture-radar/cron/logs/cron.log"
echo ""
echo "Pour tester manuellement le scraping:"
echo "  php /root/culture-radar/cron/scrape-events.php"