<?php
/**
 * UTF-8 Encoding Setup
 * Include this file at the beginning of each PHP script to ensure proper encoding
 */

// Set internal encoding to UTF-8
mb_internal_encoding('UTF-8');

// Set default charset for HTML output
ini_set('default_charset', 'UTF-8');

// Ensure database connections use UTF-8
if (!defined('DB_CHARSET')) {
    define('DB_CHARSET', 'utf8mb4');
}

// Set locale to French UTF-8
setlocale(LC_ALL, 'fr_FR.UTF-8', 'fr_FR.utf8', 'fr_FR', 'french');

// Function to ensure proper UTF-8 output
function utf8_output($text) {
    if (!mb_check_encoding($text, 'UTF-8')) {
        return mb_convert_encoding($text, 'UTF-8', 'auto');
    }
    return $text;
}
?>