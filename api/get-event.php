<?php
/**
 * Get single event details by ID
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$eventId = $_GET['id'] ?? 1;

// Define all available events
$allEvents = [
    1 => [
        'id' => 1,
        'title' => 'Exposition Impressionniste - Musée d\'Orsay',
        'category' => 'exposition',
        'description' => 'Découvrez les chefs-d\'œuvre de l\'impressionnisme français dans cette exposition exceptionnelle.',
        'long_description' => 'Cette exposition rassemble plus de 100 œuvres majeures de l\'impressionnisme français. Vous pourrez admirer des tableaux de Monet, Renoir, Degas, Cézanne et bien d\'autres. L\'exposition retrace l\'histoire de ce mouvement artistique révolutionnaire qui a changé notre façon de voir la peinture. Des audioguides sont disponibles en plusieurs langues.',
        'venue_name' => 'Musée d\'Orsay',
        'address' => '1 Rue de la Légion d\'Honneur, 75007 Paris',
        'location' => 'Paris 7e',
        'start_date' => date('Y-m-d H:i:s', strtotime('+1 day')),
        'end_date' => date('Y-m-d H:i:s', strtotime('+90 days')),
        'price' => 16.00,
        'price_details' => [
            'Tarif normal' => 16.00,
            'Tarif réduit' => 13.00,
            'Jeune (18-25 ans)' => 0.00,
            'Gratuit -18 ans' => 0.00
        ],
        'is_free' => false,
        'organizer' => 'Musée d\'Orsay',
        'website' => 'https://www.musee-orsay.fr',
        'tags' => ['art', 'musée', 'impressionnisme', 'peinture']
    ],
    2 => [
        'id' => 2,
        'title' => 'Concert Jazz au Sunset',
        'category' => 'musique',
        'description' => 'Une soirée jazz exceptionnelle avec le quartet de Marcus Miller.',
        'long_description' => 'Le légendaire bassiste Marcus Miller revient au Sunset-Sunside avec son nouveau quartet pour une soirée unique. Au programme : les grands classiques du jazz fusion et les nouvelles compositions de son dernier album. Une expérience musicale inoubliable dans l\'intimité du plus célèbre club de jazz parisien.',
        'venue_name' => 'Sunset-Sunside',
        'address' => '60 Rue des Lombards, 75001 Paris',
        'location' => 'Paris 1er',
        'start_date' => date('Y-m-d 21:00:00', strtotime('+2 days')),
        'price' => 25.00,
        'price_details' => [
            'Tarif normal' => 25.00,
            'Tarif adhérent' => 20.00
        ],
        'is_free' => false,
        'organizer' => 'Sunset-Sunside Jazz Club',
        'tags' => ['jazz', 'concert', 'musique live']
    ],
    3 => [
        'id' => 3,
        'title' => 'Théâtre: Cyrano de Bergerac',
        'category' => 'theatre',
        'description' => 'La célèbre pièce d\'Edmond Rostand dans une mise en scène moderne.',
        'long_description' => 'Une nouvelle mise en scène audacieuse de ce classique du théâtre français. Le metteur en scène Thomas Jolly transpose l\'action dans un futur proche, donnant une nouvelle dimension à cette histoire d\'amour intemporelle. Avec une troupe de 20 comédiens et des décors spectaculaires.',
        'venue_name' => 'Théâtre Mogador',
        'address' => '25 Rue de Mogador, 75009 Paris',
        'location' => 'Paris 9e',
        'start_date' => date('Y-m-d 20:30:00', strtotime('+3 days')),
        'price' => 45.00,
        'price_details' => [
            'Orchestre' => 65.00,
            'Mezzanine' => 45.00,
            'Balcon' => 35.00
        ],
        'is_free' => false,
        'organizer' => 'Théâtre Mogador',
        'tags' => ['théâtre', 'classique', 'drame']
    ],
    4 => [
        'id' => 4,
        'title' => 'Festival Street Art - Belleville',
        'category' => 'festival',
        'description' => 'Parcours artistique gratuit dans les rues de Belleville.',
        'long_description' => 'Le quartier de Belleville se transforme en galerie à ciel ouvert ! Plus de 50 artistes urbains investissent les murs, les façades et les espaces publics. Performances live, ateliers participatifs et visites guidées gratuites tout au long du week-end. Une célébration de l\'art urbain sous toutes ses formes.',
        'venue_name' => 'Quartier Belleville',
        'address' => 'Belleville, 75020 Paris',
        'location' => 'Paris 20e',
        'start_date' => date('Y-m-d', strtotime('next saturday')),
        'price' => 0,
        'is_free' => true,
        'organizer' => 'Association Belleville en Vue',
        'tags' => ['art urbain', 'gratuit', 'plein air', 'festival']
    ],
    5 => [
        'id' => 5,
        'title' => 'Projection Cinéma Plein Air',
        'category' => 'cinema',
        'description' => 'Projection gratuite de films cultes sous les étoiles.',
        'long_description' => 'Le cinéma en plein air de La Villette présente cette semaine •Les Tontons Flingueurs•, un classique du cinéma français. Arrivez tôt pour profiter des food trucks et de l\'ambiance festive. Transats et couvertures disponibles sur place (location payante) ou apportez votre équipement.',
        'venue_name' => 'Parc de la Villette',
        'address' => '211 Avenue Jean Jaurès, 75019 Paris',
        'location' => 'Paris 19e',
        'start_date' => date('Y-m-d 21:30:00', strtotime('+5 days')),
        'price' => 0,
        'is_free' => true,
        'organizer' => 'La Villette',
        'tags' => ['cinéma', 'gratuit', 'plein air']
    ],
    6 => [
        'id' => 6,
        'title' => 'Atelier Poterie Créative',
        'category' => 'atelier',
        'description' => 'Initiez-vous à la poterie avec un artisan professionnel.',
        'long_description' => 'Découvrez l\'art de la poterie dans cet atelier de 3 heures. Vous apprendrez les techniques de base du tournage, du modelage et de l\'émaillage. Chaque participant repartira avec sa création. Tout le matériel est fourni, tablier inclus. Idéal pour les débutants.',
        'venue_name' => 'Atelier des Arts',
        'address' => '15 Rue Saint-Maur, 75011 Paris',
        'location' => 'Paris 11e',
        'start_date' => date('Y-m-d 14:00:00', strtotime('next sunday')),
        'price' => 35.00,
        'is_free' => false,
        'organizer' => 'Atelier des Arts',
        'tags' => ['atelier', 'artisanat', 'créatif']
    ],
    7 => [
        'id' => 7,
        'title' => 'Visite Guidée: Paris Médiéval',
        'category' => 'heritage',
        'description' => 'Découvrez l\'histoire médiévale de Paris.',
        'long_description' => 'Remontez le temps jusqu\'au Moyen Âge avec cette visite guidée de 2 heures. De Notre-Dame à la Sainte-Chapelle, en passant par les ruelles médiévales du Marais, découvrez les vestiges de Paris au temps des rois et des chevaliers. Votre guide conférencier vous révélera les secrets de cette époque fascinante.',
        'venue_name' => 'Île de la Cité',
        'address' => 'Parvis Notre-Dame, 75004 Paris',
        'location' => 'Paris 4e',
        'start_date' => date('Y-m-d 10:00:00', strtotime('+4 days')),
        'price' => 12.00,
        'is_free' => false,
        'organizer' => 'Paris Historique',
        'tags' => ['patrimoine', 'histoire', 'visite guidée']
    ],
    8 => [
        'id' => 8,
        'title' => 'Spectacle de Danse Contemporaine',
        'category' => 'danse',
        'description' => 'Performance de danse moderne par la compagnie Pina Bausch.',
        'long_description' => 'La célèbre compagnie Tanztheater Wuppertal Pina Bausch présente •Café Müller•, une œuvre emblématique du répertoire. Cette pièce explore les thèmes de la solitude et de la communication à travers une chorégraphie puissante et émouvante. Une expérience théâtrale unique qui transcende les frontières entre danse et théâtre.',
        'venue_name' => 'Théâtre de la Ville',
        'address' => '2 Place du Châtelet, 75004 Paris',
        'location' => 'Paris 4e',
        'start_date' => date('Y-m-d 20:00:00', strtotime('+6 days')),
        'price' => 30.00,
        'is_free' => false,
        'organizer' => 'Théâtre de la Ville',
        'tags' => ['danse', 'contemporain', 'spectacle']
    ]
];

// Common details for all events
$commonDetails = [
    'capacity' => rand(100, 2500),
    'available_spots' => rand(20, 500),
    'contact_email' => 'contact@culture-radar.fr',
    'contact_phone' => '+33 1 42 85 67 89',
    'accessibility' => [
        'Accès PMR' => true,
        'Parking' => true,
        'Transports publics' => true,
        'Toilettes accessibles' => true
    ],
    'amenities' => [
        'Wi-Fi gratuit',
        'Vestiaire'
    ],
    'reviews' => [
        [
            'user' => 'Marie L.',
            'rating' => 5,
            'comment' => 'Événement exceptionnel ! Je recommande vivement.',
            'date' => date('Y-m-d', strtotime('-2 days'))
        ],
        [
            'user' => 'Pierre M.',
            'rating' => 4,
            'comment' => 'Très belle expérience, organisation parfaite.',
            'date' => date('Y-m-d', strtotime('-5 days'))
        ]
    ],
    'weather_forecast' => [
        'temperature' => rand(15, 25),
        'condition' => 'Partiellement nuageux',
        'precipitation' => rand(0, 30),
        'wind' => rand(5, 20)
    ],
    'transport_options' => [
        ['type' => 'Métro', 'line' => 'Ligne 1', 'station' => 'Châtelet', 'duration' => '10 min à pied'],
        ['type' => 'Bus', 'line' => '38', 'station' => 'République', 'duration' => '5 min à pied'],
        ['type' => 'RER', 'line' => 'A', 'station' => 'Châtelet-Les Halles', 'duration' => '8 min à pied']
    ]
];

// Get the requested event
if (isset($allEvents[$eventId])) {
    $event = array_merge($allEvents[$eventId], $commonDetails);
    echo json_encode([
        'success' => true,
        'event' => $event
    ]);
} else {
    // Return default event if ID not found
    $event = array_merge($allEvents[1], $commonDetails);
    $event['id'] = $eventId;
    echo json_encode([
        'success' => true,
        'event' => $event
    ]);
}
?>