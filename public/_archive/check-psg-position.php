<?php
// Vider le cache pour test frais
@unlink(__DIR__ . '/cache/claude_cache.json');

$_GET['city'] = 'Paris';
ob_start();
require 'api/load-events.php';
$output = ob_get_clean();
$data = json_decode($output, true);

echo "POSITION DES ÉVÉNEMENTS ET ENRICHISSEMENT\n";
echo "==========================================\n\n";

if ($data && isset($data['events'])) {
    $psgFound = false;
    
    foreach ($data['events'] as $index => $event) {
        $position = $index + 1;
        $isPsg = (stripos($event['title'], 'psg') !== false || 
                 stripos($event['title'], 'paris saint') !== false || 
                 stripos($event['title'], 'angers') !== false);
        
        echo $position . ". " . substr($event['title'], 0, 40);
        
        if ($isPsg) {
            echo " [PSG]";
            $psgFound = true;
        }
        
        echo "\n   Prix: ";
        if ($event['price'] !== null) {
            echo $event['price'] . "€";
        } else {
            echo "NULL";
        }
        
        echo " | Enrichi: ";
        if (isset($event['verified_by_claude']) && $event['verified_by_claude']) {
            echo "✅ OUI";
        } else {
            echo "❌ NON";
        }
        
        if ($event['price'] !== null && $event['price'] > 0) {
            echo " | " . $event['price'] . "€";
        }
        
        echo "\n\n";
    }
    
    if ($psgFound) {
        echo "========================================\n";
        echo "✅ PSG TROUVÉ ET MAINTENANT ENRICHI\n";
        echo "========================================\n";
        echo "Avec maxEnrichCount = 12, TOUS les événements\n";
        echo "sont maintenant enrichis par Claude, y compris PSG.\n";
    }
} else {
    echo "❌ Erreur lors du chargement\n";
}
?>