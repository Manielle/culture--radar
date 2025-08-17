<?php
/**
 * Events Data API - Returns actual event data for dashboard
 * This is a temporary solution until real APIs are connected
 */

header('Content-Type: application/json');

// Sample realistic events data for different French cities
$events = [
    'Paris' => [
        [
            'id' => 1,
            'title' => 'Exposition Monet au Musée de l\'Orangerie',
            'category' => 'exposition',
            'venue_name' => 'Musée de l\'Orangerie',
            'address' => 'Jardin des Tuileries, 75001 Paris',
            'start_date' => date('Y-m-d H:i:s', strtotime('+1 day')),
            'end_date' => date('Y-m-d H:i:s', strtotime('+30 days')),
            'price' => 12.50,
            'is_free' => false,
            'description' => 'Découvrez les Nymphéas de Claude Monet dans un cadre exceptionnel.',
            'image_url' => 'https://www.musee-orangerie.fr/sites/default/files/atoms/image/nympheas.jpg',
            'ai_score' => 95,
            'tags' => ['art', 'impressionnisme', 'musée']
        ],
        [
            'id' => 2,
            'title' => 'Concert Jazz - New Morning',
            'category' => 'musique',
            'venue_name' => 'New Morning',
            'address' => '7-9 Rue des Petites Écuries, 75010 Paris',
            'start_date' => date('Y-m-d 20:00:00', strtotime('+2 days')),
            'price' => 25,
            'is_free' => false,
            'description' => 'Soirée jazz avec le quartet de Marcus Miller.',
            'ai_score' => 88,
            'tags' => ['jazz', 'concert', 'musique live']
        ],
        [
            'id' => 3,
            'title' => 'Théâtre: Le Malade Imaginaire',
            'category' => 'theatre',
            'venue_name' => 'Comédie-Française',
            'address' => '1 Place Colette, 75001 Paris',
            'start_date' => date('Y-m-d 20:30:00', strtotime('+3 days')),
            'price' => 35,
            'is_free' => false,
            'description' => 'La célèbre pièce de Molière dans une mise en scène moderne.',
            'ai_score' => 92,
            'tags' => ['théâtre', 'classique', 'molière']
        ],
        [
            'id' => 4,
            'title' => 'Festival Street Art Belleville',
            'category' => 'festival',
            'venue_name' => 'Quartier Belleville',
            'address' => 'Belleville, 75020 Paris',
            'start_date' => date('Y-m-d', strtotime('next saturday')),
            'price' => 0,
            'is_free' => true,
            'description' => 'Découvrez les œuvres de street art dans les rues de Belleville.',
            'ai_score' => 85,
            'tags' => ['art urbain', 'gratuit', 'plein air']
        ],
        [
            'id' => 5,
            'title' => 'Projection: Cinéma en Plein Air',
            'category' => 'cinema',
            'venue_name' => 'Parc de la Villette',
            'address' => '211 Avenue Jean Jaurès, 75019 Paris',
            'start_date' => date('Y-m-d 21:30:00', strtotime('+5 days')),
            'price' => 0,
            'is_free' => true,
            'description' => 'Projection gratuite de films cultes sous les étoiles.',
            'ai_score' => 90,
            'tags' => ['cinéma', 'gratuit', 'plein air']
        ],
        [
            'id' => 6,
            'title' => 'Atelier Poterie pour Débutants',
            'category' => 'atelier',
            'venue_name' => 'Atelier des Lumières',
            'address' => '38 Rue Saint-Maur, 75011 Paris',
            'start_date' => date('Y-m-d 14:00:00', strtotime('next sunday')),
            'price' => 45,
            'is_free' => false,
            'description' => 'Initiez-vous à la poterie avec un artisan professionnel.',
            'ai_score' => 78,
            'tags' => ['atelier', 'artisanat', 'créatif']
        ]
    ],
    'Lyon' => [
        [
            'id' => 7,
            'title' => 'Fête des Lumières - Préparatifs',
            'category' => 'festival',
            'venue_name' => 'Place Bellecour',
            'address' => 'Place Bellecour, 69002 Lyon',
            'start_date' => date('Y-m-d', strtotime('+10 days')),
            'price' => 0,
            'is_free' => true,
            'description' => 'Découvrez les installations lumineuses en avant-première.',
            'ai_score' => 94
        ],
        [
            'id' => 8,
            'title' => 'Opéra: Carmen',
            'category' => 'musique',
            'venue_name' => 'Opéra de Lyon',
            'address' => '1 Place de la Comédie, 69001 Lyon',
            'start_date' => date('Y-m-d 20:00:00', strtotime('+4 days')),
            'price' => 65,
            'is_free' => false,
            'description' => 'Le chef-d\'œuvre de Bizet dans une production spectaculaire.',
            'ai_score' => 91
        ]
    ],
    'Marseille' => [
        [
            'id' => 9,
            'title' => 'Exposition MUCEM: Méditerranée',
            'category' => 'exposition',
            'venue_name' => 'MUCEM',
            'address' => '7 Promenade Robert Laffont, 13002 Marseille',
            'start_date' => date('Y-m-d'),
            'price' => 11,
            'is_free' => false,
            'description' => 'Voyage à travers les cultures méditerranéennes.',
            'ai_score' => 89
        ]
    ]
];

// Get requested city
$city = $_GET['city'] ?? $_GET['location'] ?? 'Paris';
$category = $_GET['category'] ?? null;
$limit = intval($_GET['limit'] ?? 6);

// Get events for the city
$cityEvents = $events[$city] ?? $events['Paris'];

// Filter by category if specified
if ($category) {
    $cityEvents = array_filter($cityEvents, function($event) use ($category) {
        return $event['category'] === $category;
    });
}

// Limit results
$cityEvents = array_slice($cityEvents, 0, $limit);

// Return response
echo json_encode([
    'success' => true,
    'location' => $city,
    'total' => count($cityEvents),
    'events' => $cityEvents,
    'message' => empty($cityEvents) ? 'Aucun événement trouvé' : null
]);
?>