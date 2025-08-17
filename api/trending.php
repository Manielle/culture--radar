<?php
  header('Content-Type: application/json');
  error_reporting(0);
  echo json_encode([
      'success' => true,
      'trending' => [
          ['id' => 101, 'title' => 'Festival Music', 'category' => 'festival', 'venue_name' => 'Parc', 'city' => 'Paris',
   'price' => 0, 'is_free' => true, 'interaction_count' => 1250],
          ['id' => 102, 'title' => 'Expo Art', 'category' => 'art', 'venue_name' => 'Musée', 'city' => 'Paris', 'price'
  => 15, 'is_free' => false, 'interaction_count' => 890],
          ['id' => 103, 'title' => 'Concert Rock', 'category' => 'music', 'venue_name' => 'Salle Concert', 'city' =>
  'Paris', 'price' => 35, 'is_free' => false, 'interaction_count' => 756],
          ['id' => 104, 'title' => 'Stand-up', 'category' => 'theater', 'venue_name' => 'Comedy Club', 'city' => 'Paris',
   'price' => 20, 'is_free' => false, 'interaction_count' => 445],
          ['id' => 105, 'title' => 'Marché Noël', 'category' => 'market', 'venue_name' => 'Place', 'city' => 'Paris',
  'price' => 0, 'is_free' => true, 'interaction_count' => 623],
          ['id' => 106, 'title' => 'Atelier', 'category' => 'workshop', 'venue_name' => 'Centre', 'city' => 'Paris',
  'price' => 25, 'is_free' => false, 'interaction_count' => 234],
          ['id' => 107, 'title' => 'Projection', 'category' => 'cinema', 'venue_name' => 'Cinéma', 'city' => 'Paris',
  'price' => 10, 'is_free' => false, 'interaction_count' => 389],
          ['id' => 108, 'title' => 'Spectacle', 'category' => 'dance', 'venue_name' => 'Théâtre', 'city' => 'Paris',
  'price' => 0, 'is_free' => true, 'interaction_count' => 267]
      ]
  ]);
  ?>