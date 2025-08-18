<?php
// Absolute minimal PHP test - no includes, no requires
echo "<!DOCTYPE html>";
echo "<html><head><title>Test</title></head>";
echo "<body style='font-family:Arial;padding:20px;'>";
echo "<h1>Culture Radar - Test Minimal</h1>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";
echo "<p>Time: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>Port: " . ($_SERVER['SERVER_PORT'] ?? 'unknown') . "</p>";
echo "<p>Railway: " . (getenv('RAILWAY_ENVIRONMENT') ? 'Yes' : 'No') . "</p>";
echo "</body></html>";
?>