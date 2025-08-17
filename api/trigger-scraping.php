<?php
/**
 * Endpoint pour déclencher le scraping via une URL
 * Peut être appelé par un service cron externe
 */

// Vérifier la clé secrète pour sécuriser l'endpoint
$secret = $_GET['secret'] ?? '';
if ($secret !== 'votre_cle_secrete_ici_234kj23h4') {
    http_response_code(403);
    die('Unauthorized');
}

// Définir un timeout long pour le scraping
set_time_limit(300); // 5 minutes

// Logger l'appel
error_log('[' . date('Y-m-d H:i:s') . '] Scraping triggered via API');

// Inclure et exécuter le script de scraping
require_once __DIR__ . '/../cron/scrape-events.php';

// Retourner un statut
header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'message' => 'Scraping completed',
    'timestamp' => date('Y-m-d H:i:s')
]);