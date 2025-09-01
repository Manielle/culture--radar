<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'localhost';

ob_start();
include 'index.php';
$output = ob_get_clean();

echo "=== RÉSULTATS DU TEST ===\n";
echo "Longueur HTML: " . strlen($output) . " caractères\n";
echo (strpos($output, 'Wolfgang Tillmans') !== false ? '✅' : '❌') . " Événements présents\n";
echo (strpos($output, 'Culture Radar') !== false ? '✅' : '❌') . " Titre présent\n";
echo (strpos($output, 'Projet Universitaire') !== false ? '✅' : '❌') . " Banderole présente\n";
echo (strpos($output, '<header') !== false ? '✅' : '❌') . " Header présent\n";
echo (strpos($output, '</html>') !== false ? '✅' : '❌') . " HTML complet\n";
?>