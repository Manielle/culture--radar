<?php
require_once 'services/VenueLinksService.php';

// Décoder l'URL que vous avez fournie
$data = 'eyJ0aXRsZSI6IkNvbmNlcnQgSmF6eiAtIFNhbWVkaSBzb2lyIiwiZGVzY3JpcHRpb24iOiJTb2lyw6llIGphenogZXhjZXB0aW9ubmVsbGUgYXZlYyBkZXMgYXJ0aXN0ZXMgbG9jYXV4LiIsImNhdGVnb3J5IjoiTXVzaXF1ZSIsInZlbnVlX25hbWUiOiJMZSBTdW5zZXQiLCJhZGRyZXNzIjoiNjAgcnVlIGRlcyBMb21iYXJkcywgNzUwMDEgUGFyaXMiLCJjaXR5IjoiUGFyaXMiLCJkYXRlX2Rpc3BsYXkiOiIzMCBBdWd1c3QgMjAyNSIsInN0YXJ0X2RhdGUiOiIyMDI1LTA4LTMwIDIwOjAwOjAwIiwicHJpY2UiOjI1LCJpc19mcmVlIjpmYWxzZSwiaWQiOiJrbm93bl8zMzk4MjUyYWI4YTcyY2UxYjM5NGFlYmE0ZTJmYWVjMCIsInNvdXJjZSI6Imtub3duX2V2ZW50cyIsImNvbmZpZGVuY2UiOiJoaWdoIiwiZGV0YWlsX3VybCI6ImV2ZW50LWRldGFpbHMtaHlicmlkLnBocD9kYXRhPWV5SjBhWFJzWlNJNklrTnZibU5sY25RZ1NtRjZlaUF0SUZOaGJXVmthU0J6YjJseUlpd2laR1Z6WTNKcGNIUnBiMjRpT2lKVGIybHl3NmxsSUdwaGVub2daWGhqWlhCMGFXOXVibVZzYkdVZ1lYWmxZeUJrWlhNZ1lYSjBhWE4wWlhNZ2JHOWpZWFY0TGlJc0ltTmhkR1ZuYjNKNUlqb2lUWFZ6YVhGMVpTSXNJblpsYm5WbFgyNWhiV1VpT2lKTVpTQlRkVzV6WlhRaUxDSmhaR1J5WlhOeklqb2lOakFnY25WbElHUmxjeUJNYjIxaVlYSmtjeXdnTnpVd01ERWdVR0Z5YVhNaUxDSmphWFI1SWpvaVVHRnlhWE1pTENKa1lYUmxYMlJwYzNCc1lYa2lPaUl6TUNCQmRXZDFjM1FnTWpBeU5TSXNJbk4wWVhKMFgyUmhkR1VpT2lJeU1ESTFMVEE0TFRNd0lESXdPakF3T2pBd0lpd2ljSEpwWTJVaU9qSTFMQ0pwYzE5bWNtVmxJanBtWVd4elpTd2lhV1FpT2lKcmJtOTNibDh6TXprNE1qVXlZV0k0WVRjeVkyVXhZak01TkdGbFltRTBaVEptWVdWak1DSXNJbk52ZFhKalpTSTZJbXR1YjNkdVgyVjJaVzUwY3lJc0ltTnZibVpwWkdWdVkyVWlPaUpvYVdkb0luMD0iLCJzb3VyY2VfdXJsIjoiIyIsIm9yZ2FuaXplciI6IkxlIFN1bnNldCIsImNvbnRhY3RfcGhvbmUiOiJDb250YWN0IHN1ciBwbGFjZSIsImxvbmdfZGVzY3JpcHRpb24iOiJTb2lyw6llIGphenogZXhjZXB0aW9ubmVsbGUgYXZlYyBkZXMgYXJ0aXN0ZXMgbG9jYXV4LiIsImNhcGFjaXR5IjoyMDAsImF2YWlsYWJsZV9zcG90cyI6MTEwLCJpbWFnZSI6Imh0dHBzOi8vaW1hZ2VzLnVuc3BsYXNoLmNvbS9waG90by0xNDkzMjI1NDU3MTI0LWEzZWIxNjFmZmE1Zj93PTQwMCZoPTMwMCZmaXQ9Y3JvcCJ9';

$event = json_decode(base64_decode($data), true);

echo "<h1>Debug Venue Link</h1>";
echo "<pre>";
echo "Événement décodé:\n";
print_r($event);

echo "\n\nRecherche du lieu:\n";
$venue = $event['venue_name'] ?? '';
echo "Nom du lieu: '$venue'\n";

$venueUrl = VenueLinksService::getVenueUrl($venue);
echo "URL trouvée: $venueUrl\n";

$sourceUrl = $event['source_url'] ?? '';
echo "\nURL source originale: '$sourceUrl'\n";

// Test de la logique
if (empty($sourceUrl) || $sourceUrl === '#' || $sourceUrl === '') {
    echo "→ URL source vide ou '#', on utilise l'URL du lieu\n";
    if ($venueUrl) {
        $sourceUrl = $venueUrl;
        echo "→ Nouvelle URL: $sourceUrl\n";
    }
}

echo "\n\nRésultat final:\n";
echo "URL qui sera utilisée pour le bouton 'Plus d'infos': $sourceUrl\n";
echo "</pre>";

echo "<h2>Test du lien</h2>";
echo "<a href='$sourceUrl' target='_blank' class='btn btn-primary' style='padding: 10px 20px; background: #FF6B6B; color: white; text-decoration: none; border-radius: 5px; display: inline-block;'>Tester le lien vers le site officiel</a>";

echo "<h2>Autres lieux testés:</h2>";
$testVenues = ['Le Sunset', 'le sunset', 'Sunset Sunside', 'Philharmonie de Paris', 'Opéra Garnier'];
echo "<ul>";
foreach ($testVenues as $v) {
    $url = VenueLinksService::getVenueUrl($v);
    echo "<li>$v → $url</li>";
}
echo "</ul>";
?>