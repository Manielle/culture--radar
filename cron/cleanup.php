#!/usr/bin/env php
<?php
/**
 * Script de nettoyage hebdomadaire
 * Supprime les anciens événements et optimise la base de données
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

date_default_timezone_set('Europe/Paris');

$logFile = __DIR__ . '/logs/cleanup_' . date('Y-m-d') . '.log';

function logMessage($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] $message" . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    echo $logEntry;
}

try {
    $db = Database::getInstance()->getConnection();
    
    logMessage("=== Début du nettoyage hebdomadaire ===");
    
    // 1. Désactiver les événements passés depuis plus de 30 jours
    $sql = "UPDATE events 
            SET is_active = 0 
            WHERE start_date < DATE_SUB(NOW(), INTERVAL 30 DAY) 
            AND is_active = 1";
    $affected = $db->exec($sql);
    logMessage("Désactivé $affected événements anciens");
    
    // 2. Supprimer les événements de plus de 90 jours
    $sql = "DELETE FROM events 
            WHERE start_date < DATE_SUB(NOW(), INTERVAL 90 DAY)";
    $deleted = $db->exec($sql);
    logMessage("Supprimé $deleted événements très anciens");
    
    // 3. Nettoyer les logs de scraping de plus de 30 jours
    $sql = "DELETE FROM scraping_logs 
            WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)";
    $logsDeleted = $db->exec($sql);
    logMessage("Supprimé $logsDeleted anciens logs de scraping");
    
    // 4. Optimiser les tables
    $tables = ['events', 'scraping_logs'];
    foreach ($tables as $table) {
        $db->exec("OPTIMIZE TABLE $table");
        logMessage("Table '$table' optimisée");
    }
    
    // 5. Recalculer les scores AI pour les événements sans score
    $sql = "UPDATE events 
            SET ai_score = calculate_ai_score(category, venue_rating, is_free, venue_reviews)
            WHERE ai_score IS NULL OR ai_score = 0";
    $updated = $db->exec($sql);
    logMessage("Scores AI recalculés pour $updated événements");
    
    // 6. Statistiques finales
    $sql = "SELECT 
                COUNT(*) as total_active,
                COUNT(DISTINCT city) as cities,
                COUNT(DISTINCT category) as categories
            FROM events 
            WHERE is_active = 1";
    $stats = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
    
    logMessage("Statistiques après nettoyage:");
    logMessage("  - Événements actifs: " . $stats['total_active']);
    logMessage("  - Villes: " . $stats['cities']);
    logMessage("  - Catégories: " . $stats['categories']);
    
    logMessage("=== Nettoyage terminé avec succès ===");
    
} catch (Exception $e) {
    logMessage("ERREUR: " . $e->getMessage());
    exit(1);
}