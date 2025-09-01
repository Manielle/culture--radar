<?php
// Simple connection test
header('Content-Type: text/plain');
echo "Connection successful!\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n";
echo "Port: " . $_SERVER['SERVER_PORT'] . "\n";
echo "Remote: " . $_SERVER['REMOTE_ADDR'] . "\n";
?>