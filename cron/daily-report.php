#!/usr/bin/env php
<?php
/**
 * Script de rapport quotidien
 * Envoie un résumé des événements scrappés par email
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

date_default_timezone_set('Europe/Paris');

$logFile = __DIR__ . '/logs/report_' . date('Y-m-d') . '.log';

function logMessage($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] $message" . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    echo $logEntry;
}

try {
    $db = Database::getInstance()->getConnection();
    
    logMessage("=== Génération du rapport quotidien ===");
    
    // 1. Statistiques du dernier scraping
    $sql = "SELECT * FROM scraping_logs 
            WHERE run_date = CURDATE() 
            ORDER BY created_at DESC 
            LIMIT 1";
    $lastRun = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
    
    // 2. Statistiques globales
    $sql = "SELECT 
                COUNT(*) as total_events,
                COUNT(DISTINCT city) as total_cities,
                COUNT(DISTINCT category) as total_categories,
                SUM(is_free) as free_events,
                AVG(ai_score) as avg_score
            FROM events 
            WHERE is_active = 1";
    $globalStats = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
    
    // 3. Top événements du jour
    $sql = "SELECT title, city, venue_name, start_date, ai_score
            FROM events 
            WHERE is_active = 1 
            AND DATE(start_date) = CURDATE()
            ORDER BY ai_score DESC 
            LIMIT 10";
    $topEvents = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    
    // 4. Statistiques par ville
    $sql = "SELECT city, COUNT(*) as count 
            FROM events 
            WHERE is_active = 1 
            GROUP BY city 
            ORDER BY count DESC";
    $cityStats = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    
    // Construire le rapport HTML
    $report = "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Rapport Culture Radar - " . date('d/m/Y') . "</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        h1 { color: #667eea; }
        h2 { color: #333; border-bottom: 2px solid #667eea; padding-bottom: 5px; }
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin: 20px 0; }
        .stat-box { background: #f5f5f5; padding: 15px; border-radius: 8px; }
        .stat-value { font-size: 24px; font-weight: bold; color: #667eea; }
        .stat-label { color: #666; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #667eea; color: white; }
        .success { color: #48bb78; }
        .warning { color: #f6ad55; }
    </style>
</head>
<body>
    <h1>📊 Rapport Culture Radar - " . date('d/m/Y') . "</h1>";
    
    if ($lastRun) {
        $report .= "
    <h2>Dernier Scraping</h2>
    <div class='stats-grid'>
        <div class='stat-box'>
            <div class='stat-value'>" . $lastRun['total_fetched'] . "</div>
            <div class='stat-label'>Événements récupérés</div>
        </div>
        <div class='stat-box'>
            <div class='stat-value class=\"success\"'>" . $lastRun['new_events'] . "</div>
            <div class='stat-label'>Nouveaux événements</div>
        </div>
        <div class='stat-box'>
            <div class='stat-value'>" . $lastRun['updated_events'] . "</div>
            <div class='stat-label'>Mises à jour</div>
        </div>
    </div>";
    }
    
    $report .= "
    <h2>Statistiques Globales</h2>
    <div class='stats-grid'>
        <div class='stat-box'>
            <div class='stat-value'>" . number_format($globalStats['total_events']) . "</div>
            <div class='stat-label'>Total événements actifs</div>
        </div>
        <div class='stat-box'>
            <div class='stat-value'>" . $globalStats['total_cities'] . "</div>
            <div class='stat-label'>Villes couvertes</div>
        </div>
        <div class='stat-box'>
            <div class='stat-value'>" . number_format($globalStats['free_events']) . "</div>
            <div class='stat-label'>Événements gratuits</div>
        </div>
    </div>";
    
    if (!empty($topEvents)) {
        $report .= "
    <h2>Top Événements du Jour</h2>
    <table>
        <tr>
            <th>Événement</th>
            <th>Ville</th>
            <th>Lieu</th>
            <th>Heure</th>
            <th>Score</th>
        </tr>";
        
        foreach ($topEvents as $event) {
            $time = date('H:i', strtotime($event['start_date']));
            $report .= "
        <tr>
            <td>" . htmlspecialchars($event['title']) . "</td>
            <td>" . $event['city'] . "</td>
            <td>" . htmlspecialchars($event['venue_name']) . "</td>
            <td>" . $time . "</td>
            <td>" . $event['ai_score'] . "</td>
        </tr>";
        }
        $report .= "</table>";
    }
    
    $report .= "
    <h2>Répartition par Ville</h2>
    <table>
        <tr>
            <th>Ville</th>
            <th>Nombre d'événements</th>
            <th>Pourcentage</th>
        </tr>";
    
    $total = $globalStats['total_events'];
    foreach ($cityStats as $city) {
        $percentage = round(($city['count'] / $total) * 100, 1);
        $report .= "
        <tr>
            <td>" . $city['city'] . "</td>
            <td>" . $city['count'] . "</td>
            <td>" . $percentage . "%</td>
        </tr>";
    }
    
    $report .= "
    </table>
    
    <p style='margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px;'>
        Rapport généré automatiquement le " . date('d/m/Y à H:i:s') . "<br>
        Culture Radar - Votre boussole culturelle intelligente
    </p>
</body>
</html>";
    
    // Sauvegarder le rapport
    $reportFile = __DIR__ . '/reports/report_' . date('Y-m-d') . '.html';
    if (!is_dir(__DIR__ . '/reports')) {
        mkdir(__DIR__ . '/reports', 0755, true);
    }
    file_put_contents($reportFile, $report);
    logMessage("Rapport HTML sauvegardé: $reportFile");
    
    // Envoyer par email si configuré
    if (defined('ADMIN_EMAIL') && ADMIN_EMAIL) {
        $to = ADMIN_EMAIL;
        $subject = "Rapport Culture Radar - " . date('d/m/Y');
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: Culture Radar <noreply@culture-radar.fr>' . "\r\n";
        
        if (mail($to, $subject, $report, $headers)) {
            logMessage("Rapport envoyé par email à: $to");
        } else {
            logMessage("Erreur lors de l'envoi de l'email");
        }
    }
    
    logMessage("=== Rapport généré avec succès ===");
    
} catch (Exception $e) {
    logMessage("ERREUR: " . $e->getMessage());
    exit(1);
}