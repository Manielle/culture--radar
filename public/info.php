<?php
/**
 * PHP Info page for debugging
 * Shows PHP configuration and environment variables
 */

// Basic info
echo "<h1>Culture Radar - PHP Info</h1>";
echo "<h2>Basic Information</h2>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";
echo "<p>Server Port: " . ($_SERVER['SERVER_PORT'] ?? 'unknown') . "</p>";
echo "<p>Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'unknown') . "</p>";
echo "<p>Script Name: " . ($_SERVER['SCRIPT_NAME'] ?? 'unknown') . "</p>";

// Railway environment variables
echo "<h2>Railway Environment Variables</h2>";
echo "<ul>";
echo "<li>PORT: " . getenv('PORT') . "</li>";
echo "<li>RAILWAY_ENVIRONMENT: " . getenv('RAILWAY_ENVIRONMENT') . "</li>";
echo "<li>RAILWAY_STATIC_URL: " . getenv('RAILWAY_STATIC_URL') . "</li>";
echo "<li>RAILWAY_DEPLOYMENT_ID: " . getenv('RAILWAY_DEPLOYMENT_ID') . "</li>";
echo "</ul>";

// MySQL environment variables
echo "<h2>MySQL Configuration</h2>";
echo "<ul>";
echo "<li>MYSQL_URL: " . (getenv('MYSQL_URL') ? 'Set' : 'Not set') . "</li>";
echo "<li>MYSQLHOST: " . getenv('MYSQLHOST') . "</li>";
echo "<li>MYSQLPORT: " . getenv('MYSQLPORT') . "</li>";
echo "<li>MYSQLDATABASE: " . getenv('MYSQLDATABASE') . "</li>";
echo "<li>MYSQLUSER: " . getenv('MYSQLUSER') . "</li>";
echo "</ul>";

// Links to test pages
echo "<h2>Test Pages</h2>";
echo "<ul>";
echo '<li><a href="/test.html">Static HTML Test</a></li>';
echo '<li><a href="/health.php">Health Check (JSON)</a></li>';
echo '<li><a href="/index.php">Main Application</a></li>';
echo '<li><a href="/phpinfo.php">Full PHP Info</a></li>';
echo "</ul>";
?>