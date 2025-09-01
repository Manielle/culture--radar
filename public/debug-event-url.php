<?php
// Debug pour comprendre pourquoi l'URL n'est pas correcte

require_once 'services/VenueLinksService.php';

// Les données de l'événement Wolfgang Tillmans
$eventData = 'eyJ0aXRsZSI6IldvbGZnYW5nIFRpbGxtYW5zIC0gRGVybmlcdTAwZThyZSBleHBvc2l0aW9uIGF2YW50IGZlcm1ldHVyZSIsImRlc2NyaXB0aW9uIjoiQ2FydGUgYmxhbmNoZSBcdTAwZTAgbCdhcnRpc3RlIGFsbGVtYW5kIFdvbGZnYW5nIFRpbGxtYW5zIHBvdXIgdW5lIGV4cG9zaXRpb24gXHUwMGU5dlx1MDBlOW5lbWVudCBxdWkgY2xcdTAwZjR0dXJlcmEgbGEgcHJvZ3JhbW1hdGlvbiBkdSBDZW50cmUgUG9tcGlkb3UgYXZhbnQgc2EgZmVybWV0dXJlIHBvdXIgclx1MDBlOW5vdmF0aW9uLiIsImNhdGVnb3J5IjoiRXhwb3NpdGlvbiIsInZlbnVlX25hbWUiOiJDZW50cmUgUG9tcGlkb3UiLCJhZGRyZXNzIjoiUGxhY2UgR2Vvcmdlcy1Qb21waWRvdSwgNzUwMDQgUGFyaXMiLCJjaXR5IjoiUGFyaXMiLCJkYXRlX2Rpc3BsYXkiOiIxMyBqdWluIC0gMjIgc2VwdGVtYnJlIDIwMjUiLCJwcmljZSI6MTQsImlzX2ZyZWUiOmZhbHNlLCJzb3VyY2VfdXJsIjoiaHR0cHM6XC9cL3d3dy5jZW50cmVwb21waWRvdS5mclwvZnJcL3Byb2dyYW1tZVwvYWdlbmRhXC9ldmVuZW1lbnRcL25TbGNiTVoiLCJjb25maWRlbmNlX2xldmVsIjoiaGlnaCIsIm5vdGVzIjoiRGVybmlcdTAwZThyZSBleHBvc2l0aW9uIGF2YW50IGZlcm1ldHVyZSBwb3VyIHJcdTAwZTlub3ZhdGlvbiBqdXNxdSdlbiAyMDMwIiwiaWQiOiJoeWJyaWRfZjRjMzY0ZjgxODMyMTVjMGYzNWU2MWVjNWVmMGViM2QiLCJzb3VyY2UiOiJoeWJyaWRfc2VycGFwaV9jbGF1ZGUifQ==';

// Décoder
$event = json_decode(base64_decode($eventData), true);

// Simuler exactement ce qui se passe dans event-details-hybrid.php
$venue = $event['venue_name'] ?? 'Lieu non spécifié';
$sourceUrl = $event['source_url'] ?? '';
$venueUrl = VenueLinksService::getVenueUrl($venue);

echo "<!DOCTYPE html><html><head><title>Debug URL</title>";
echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">';
echo "</head><body style='padding: 40px;'>";

echo "<h1>🔍 Debug de l'URL pour Wolfgang Tillmans</h1>";

echo "<div class='alert alert-info'>";
echo "<h3>1. Données de l'événement décodées :</h3>";
echo "<pre>";
echo "venue_name: " . htmlspecialchars($venue) . "\n";
echo "source_url originale: " . htmlspecialchars($sourceUrl) . "\n";
echo "</pre>";
echo "</div>";

echo "<div class='alert alert-warning'>";
echo "<h3>2. VenueLinksService retourne :</h3>";
echo "<pre>";
echo "venueUrl: " . htmlspecialchars($venueUrl) . "\n";
echo "</pre>";
echo "</div>";

echo "<div class='alert alert-danger'>";
echo "<h3>3. Logique actuelle dans event-details-hybrid.php :</h3>";
echo "<pre>";
echo "if (empty(\$sourceUrl) || \$sourceUrl === '#' || \$sourceUrl === '') {\n";
echo "    // Condition: " . (empty($sourceUrl) || $sourceUrl === '#' || $sourceUrl === '' ? "TRUE" : "FALSE") . "\n";
echo "    if (\$venueUrl) {\n";
echo "        \$sourceUrl = \$venueUrl;\n";
echo "    }\n";
echo "}\n";
echo "</pre>";
echo "</div>";

// Appliquer la logique
$finalUrl = $sourceUrl;
if (empty($sourceUrl) || $sourceUrl === '#' || $sourceUrl === '') {
    if ($venueUrl) {
        $finalUrl = $venueUrl;
    }
}

echo "<div class='alert " . ($finalUrl === $sourceUrl ? "alert-success" : "alert-danger") . "'>";
echo "<h3>4. Résultat final :</h3>";
echo "<pre>";
echo "URL finale utilisée: " . htmlspecialchars($finalUrl) . "\n";
echo "Est-ce correct? " . ($finalUrl === 'https://www.centrepompidou.fr/fr/programme/agenda/evenement/nSlcbMZ' ? 'OUI ✅' : 'NON ❌') . "\n";
echo "</pre>";
echo "</div>";

echo "<h3>5. Test du lien :</h3>";
echo "<a href='" . htmlspecialchars($finalUrl) . "' target='_blank' class='btn btn-primary'>Ouvrir l'URL finale</a>";

echo "<hr>";
echo "<h3>6. Test direct de la page event-details-hybrid.php :</h3>";
echo "<a href='event-details-hybrid.php?data=" . $eventData . "' class='btn btn-warning'>Voir la page de l'événement</a>";

echo "</body></html>";
?>