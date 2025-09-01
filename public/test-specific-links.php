<?php
// Test des liens spécifiques vs génériques

// Créer un événement avec un lien spécifique
$testEvent = [
    'title' => 'Wolfgang Tillmans - Exposition au Centre Pompidou',
    'description' => 'Carte blanche à l\'artiste allemand',
    'category' => 'Exposition',
    'venue_name' => 'Centre Pompidou',
    'address' => 'Place Georges-Pompidou, 75004 Paris',
    'city' => 'Paris',
    'date_display' => '13 juin - 22 septembre 2025',
    'price' => 14,
    'is_free' => false,
    'source_url' => 'https://www.centrepompidou.fr/fr/programme/agenda/evenement/nSlcbMZ'
];

$eventData = base64_encode(json_encode($testEvent));

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Test Liens Spécifiques</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .test-card {
            background: white;
            padding: 20px;
            margin: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .url-display {
            background: #f4f4f4;
            padding: 10px;
            border-radius: 5px;
            word-break: break-all;
            font-family: monospace;
            font-size: 12px;
        }
    </style>
</head>
<body style="background: #f8f9fa; padding: 40px;">
    <div class="container">
        <h1>🔗 Test des Liens Spécifiques vs Génériques</h1>
        
        <div class="test-card">
            <h3>Exemple : Exposition Wolfgang Tillmans</h3>
            
            <h5>Données de l'événement :</h5>
            <ul>
                <li><strong>Titre :</strong> <?php echo $testEvent['title']; ?></li>
                <li><strong>Lieu :</strong> <?php echo $testEvent['venue_name']; ?></li>
                <li><strong>URL spécifique :</strong> 
                    <div class="url-display"><?php echo $testEvent['source_url']; ?></div>
                </li>
            </ul>
            
            <h5>Ce qui devrait se passer :</h5>
            <p class="success">✅ Le système doit garder l'URL spécifique de l'événement</p>
            <p>→ <code>https://www.centrepompidou.fr/fr/programme/agenda/evenement/nSlcbMZ</code></p>
            
            <h5>Ce qui ne doit PAS se passer :</h5>
            <p class="error">❌ Le système ne doit PAS remplacer par l'URL générique</p>
            <p>→ <code>https://www.centrepompidou.fr</code> (page d'accueil)</p>
            
            <div class="mt-4">
                <a href="event-details-hybrid.php?data=<?php echo $eventData; ?>" 
                   class="btn btn-primary btn-lg" target="_blank">
                    🧪 Tester cet événement
                </a>
                <p class="mt-2">
                    <small>Le bouton "Voir sur le site officiel" doit ouvrir la page spécifique de l'expo, pas la page d'accueil du Centre Pompidou</small>
                </p>
            </div>
        </div>
        
        <div class="test-card">
            <h3>Règles du système :</h3>
            <ol>
                <li>Si l'événement a une <strong>URL spécifique</strong> (comme une page d'événement), on la garde</li>
                <li>Si l'événement n'a <strong>pas d'URL</strong> ou juste "#", on met l'URL générale du lieu</li>
                <li>VenueLinksService ne doit intervenir que pour les événements sans URL</li>
            </ol>
            
            <h4>Exemples :</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Événement</th>
                        <th>source_url originale</th>
                        <th>Résultat attendu</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Expo Pompidou</td>
                        <td><code>.../evenement/nSlcbMZ</code></td>
                        <td class="success">Garder l'URL spécifique ✅</td>
                    </tr>
                    <tr>
                        <td>Concert Philharmonie</td>
                        <td><code>.../concert/123456</code></td>
                        <td class="success">Garder l'URL spécifique ✅</td>
                    </tr>
                    <tr>
                        <td>Jazz au Sunset</td>
                        <td><code>#</code></td>
                        <td>→ sunset-sunside.com (URL générale)</td>
                    </tr>
                    <tr>
                        <td>Théâtre sans URL</td>
                        <td><em>vide</em></td>
                        <td>→ Site du théâtre (URL générale)</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="test-card">
            <h3>Pour votre soutenance :</h3>
            <p>Le système est maintenant intelligent :</p>
            <ul>
                <li>Il préserve les liens directs vers les événements spécifiques</li>
                <li>Il ajoute automatiquement les sites officiels quand il n'y a pas de lien</li>
                <li>Les utilisateurs arrivent toujours sur la bonne page !</li>
            </ul>
        </div>
    </div>
</body>
</html>