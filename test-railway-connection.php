<?php
/**
 * Page de test pour vérifier la connexion MySQL sur Railway
 * Accédez à : https://votre-app.railway.app/test-railway-connection.php
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Connexion Railway MySQL</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }
        .status {
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        pre {
            background: #f4f4f4;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #667eea;
            color: white;
        }
        .check-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            margin: 5px 0;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-success {
            background: #28a745;
            color: white;
        }
        .badge-danger {
            background: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Test de Connexion Railway MySQL</h1>
        
        <?php
        // Détection de l'environnement
        $isRailway = getenv('RAILWAY_ENVIRONMENT') !== false || getenv('MYSQL_URL') !== false;
        ?>
        
        <div class="status <?php echo $isRailway ? 'success' : 'warning'; ?>">
            <strong>Environnement détecté :</strong> 
            <?php echo $isRailway ? '✅ Railway' : '⚠️ Local'; ?>
        </div>
        
        <h2>📊 Variables d'Environnement MySQL</h2>
        <table>
            <thead>
                <tr>
                    <th>Variable</th>
                    <th>Valeur</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $mysqlVars = [
                    'MYSQL_URL' => 'URL complète de connexion',
                    'MYSQLHOST' => 'Hostname MySQL',
                    'MYSQLPORT' => 'Port MySQL',
                    'MYSQLUSER' => 'Utilisateur MySQL',
                    'MYSQLPASSWORD' => 'Mot de passe MySQL',
                    'MYSQLDATABASE' => 'Nom de la base'
                ];
                
                foreach ($mysqlVars as $var => $description) {
                    $value = getenv($var);
                    $hasValue = $value !== false && $value !== '';
                    
                    // Masquer les infos sensibles
                    if ($var === 'MYSQLPASSWORD' && $hasValue) {
                        $displayValue = str_repeat('*', 8) . substr($value, -4);
                    } elseif ($var === 'MYSQL_URL' && $hasValue) {
                        $parsed = parse_url($value);
                        $displayValue = $parsed['scheme'] . '://***:***@' . $parsed['host'] . ':' . $parsed['port'] . $parsed['path'];
                    } else {
                        $displayValue = $hasValue ? $value : 'Non définie';
                    }
                    
                    echo "<tr>";
                    echo "<td><strong>{$var}</strong><br><small>{$description}</small></td>";
                    echo "<td><code>{$displayValue}</code></td>";
                    echo "<td>" . ($hasValue ? '<span class="badge badge-success">✓</span>' : '<span class="badge badge-danger">✗</span>') . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
        
        <h2>🔌 Test de Connexion</h2>
        <?php
        if (getenv('MYSQL_URL')) {
            try {
                // Tentative de connexion avec MYSQL_URL
                $pdo = new PDO(getenv('MYSQL_URL'), null, null, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]);
                
                // Test simple
                $result = $pdo->query("SELECT VERSION() as version, NOW() as time")->fetch();
                ?>
                <div class="status success">
                    <h3>✅ Connexion Réussie!</h3>
                    <p><strong>MySQL Version:</strong> <?php echo $result['version']; ?></p>
                    <p><strong>Heure serveur:</strong> <?php echo $result['time']; ?></p>
                </div>
                
                <h3>📋 Tables dans la base de données</h3>
                <?php
                $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                if (count($tables) > 0) {
                    echo "<ul>";
                    foreach ($tables as $table) {
                        $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
                        echo "<li><strong>{$table}</strong> - {$count} enregistrements</li>";
                    }
                    echo "</ul>";
                } else {
                    echo '<div class="status warning">⚠️ Aucune table trouvée. Exécutez le script d\'initialisation.</div>';
                }
                
            } catch (PDOException $e) {
                ?>
                <div class="status error">
                    <h3>❌ Erreur de Connexion</h3>
                    <p><?php echo htmlspecialchars($e->getMessage()); ?></p>
                </div>
                <?php
            }
        } else {
            ?>
            <div class="status warning">
                <h3>⚠️ Variables MySQL non détectées</h3>
                <p>Les services ne semblent pas être liés sur Railway.</p>
                <h4>Comment lier les services :</h4>
                <ol>
                    <li>Allez dans votre dashboard Railway</li>
                    <li>Cliquez sur votre Web Service</li>
                    <li>Onglet "Variables" → "Add Reference Variable"</li>
                    <li>Sélectionnez votre service MySQL</li>
                    <li>Les variables seront automatiquement injectées</li>
                </ol>
            </div>
            <?php
        }
        ?>
        
        <h2>✅ Checklist de Configuration</h2>
        <div class="check-item">
            <span>Service MySQL créé sur Railway</span>
            <span class="badge <?php echo getenv('MYSQLDATABASE') ? 'badge-success' : 'badge-danger'; ?>">
                <?php echo getenv('MYSQLDATABASE') ? '✓' : '✗'; ?>
            </span>
        </div>
        <div class="check-item">
            <span>Services liés (Web ↔ MySQL)</span>
            <span class="badge <?php echo getenv('MYSQL_URL') ? 'badge-success' : 'badge-danger'; ?>">
                <?php echo getenv('MYSQL_URL') ? '✓' : '✗'; ?>
            </span>
        </div>
        <div class="check-item">
            <span>Connexion à la base de données</span>
            <span class="badge <?php echo isset($pdo) && $pdo ? 'badge-success' : 'badge-danger'; ?>">
                <?php echo isset($pdo) && $pdo ? '✓' : '✗'; ?>
            </span>
        </div>
        
        <h2>🛠️ Informations Système</h2>
        <pre><?php
        echo "PHP Version: " . PHP_VERSION . "\n";
        echo "Extensions chargées:\n";
        echo "- PDO: " . (extension_loaded('pdo') ? '✓' : '✗') . "\n";
        echo "- PDO MySQL: " . (extension_loaded('pdo_mysql') ? '✓' : '✗') . "\n";
        echo "- MySQLi: " . (extension_loaded('mysqli') ? '✓' : '✗') . "\n";
        ?></pre>
        
        <div class="status info">
            <h4>📝 Prochaines étapes :</h4>
            <?php if (!getenv('MYSQL_URL')): ?>
                <p>1. Liez vos services dans Railway Dashboard</p>
            <?php elseif (empty($tables)): ?>
                <p>1. Exécutez le script d'initialisation : <code>php scripts/init-railway-db.php</code></p>
            <?php else: ?>
                <p>✅ Tout est configuré ! Votre application est prête.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>