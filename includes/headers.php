<?php
/**
 * Common HTTP headers for UTF-8 support
 * Include this at the top of every PHP file that outputs HTML
 */

// Set UTF-8 for PHP output
header('Content-Type: text/html; charset=UTF-8');

// Prevent character encoding issues
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

// Set locale for French with UTF-8
setlocale(LC_ALL, 'fr_FR.UTF-8', 'fr_FR.utf8', 'fr_FR', 'french');

// Ensure proper timezone
date_default_timezone_set('Europe/Paris');
?>