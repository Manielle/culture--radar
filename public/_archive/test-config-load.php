<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html><head><title>Test Config Load</title>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    .success { color: green; }
    .error { color: red; }
    pre { background: #f0f0f0; padding: 10px; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background: #4CAF50; color: white; }
</style></head><body>";

echo "<h1>Test du chargement de la configuration</h1>";

// Load config
require_once __DIR__ . '/config.php';

echo "<h2>Configuration de la base de données:</h2>";
$dbConfig = Config::database();
echo "<table>";
echo "<tr><th>Paramètre</th><th>Valeur</th></tr>";
foreach ($dbConfig as $key => $value) {
    echo "<tr><td>$key</td><td>" . ($key === 'pass' ? '****' : $value) . "</td></tr>";
}
echo "</table>";

echo "<h2>DSN généré:</h2>";
echo "<pre>" . Config::getDSN() . "</pre>";

echo "<h2>Test de connexion via Config::getPDO():</h2>";
try {
    $pdo = Config::getPDO();
    echo "<p class='success'>✅ Connexion réussie via Config::getPDO()</p>";
    
    // Test query
    $stmt = $pdo->query("SELECT DATABASE() as db, COUNT(*) as table_count FROM information_schema.tables WHERE table_schema = DATABASE()");
    $result = $stmt->fetch();
    echo "<p>Base de données: <strong>{$result['db']}</strong></p>";
    echo "<p>Nombre de tables: <strong>{$result['table_count']}</strong></p>";
    
    // Check user tables
    echo "<h3>Tables utilisateurs:</h3>";
    $tables = ['users', 'user_profiles'];
    echo "<ul>";
    foreach ($tables as $table) {
        $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM $table");
        $count = $stmt->fetch()['cnt'];
        echo "<li><strong>$table</strong>: $count enregistrements</li>";
    }
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<p class='error'>❌ Erreur: " . $e->getMessage() . "</p>";
}

echo "<h2>Variables d'environnement:</h2>";
echo "<table>";
echo "<tr><th>Variable</th><th>Valeur</th></tr>";
$envVars = ['DB_HOST', 'DB_PORT', 'DB_NAME', 'DB_USER', 'DB_PASS', 'IS_MAMP', 'IS_LOCALHOST'];
foreach ($envVars as $var) {
    $value = getenv($var);
    if ($value === false) {
        $value = isset($_ENV[$var]) ? $_ENV[$var] : '(non définie)';
    }
    echo "<tr><td>$var</td><td>" . ($var === 'DB_PASS' ? '****' : $value) . "</td></tr>";
}
echo "</table>";

echo "<h2>Fichiers de configuration:</h2>";
$files = [
    'config.php' => file_exists(__DIR__ . '/config.php'),
    'mamp-config.php' => file_exists(__DIR__ . '/mamp-config.php'),
    '.env' => file_exists(__DIR__ . '/.env'),
    'config/api-keys.php' => file_exists(__DIR__ . '/config/api-keys.php')
];
echo "<ul>";
foreach ($files as $file => $exists) {
    echo "<li>$file: " . ($exists ? "✅ Existe" : "❌ N'existe pas") . "</li>";
}
echo "</ul>";

echo "<hr>";
echo "<h2>Actions:</h2>";
echo "<p><a href='/register.php'>➡️ Tester l'inscription</a></p>";
echo "<p><a href='/test-registration-flow.php'>➡️ Test du flux d'inscription</a></p>";

echo "</body></html>";
?>