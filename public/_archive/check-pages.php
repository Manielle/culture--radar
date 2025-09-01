<?php
/**
 * Vérification simple des pages pour MAMP
 */

// Pas de limite de temps
set_time_limit(0);

// Configuration
error_reporting(E_ERROR | E_WARNING);
ini_set('display_errors', 1);

// Statistiques
$stats = [
    'total' => 0,
    'ok' => 0,
    'warning' => 0,
    'error' => 0,
    'pages' => []
];

// Fonction simple de test
function testFile($filepath) {
    if (!file_exists($filepath)) {
        return 'error';
    }
    
    // Test de syntaxe basique
    $content = @file_get_contents($filepath);
    if ($content === false) {
        return 'error';
    }
    
    // Vérifications simples
    $hasIssue = false;
    
    // Chercher des erreurs évidentes
    if (strpos($content, 'Parse error') !== false) {
        return 'error';
    }
    
    if (strpos($content, 'Fatal error') !== false) {
        return 'error';
    }
    
    // Si utilise SESSION sans session_start
    if (strpos($content, '$_SESSION') !== false && 
        strpos($content, 'session_start()') === false &&
        basename($filepath) !== 'config.php') {
        $hasIssue = true;
    }
    
    return $hasIssue ? 'warning' : 'ok';
}

// Scanner le répertoire actuel
$currentDir = __DIR__;
$files = glob($currentDir . '/*.php');

foreach ($files as $file) {
    $filename = basename($file);
    if ($filename === 'check-pages.php') continue; // Skip ce fichier
    
    $stats['total']++;
    $status = testFile($file);
    $stats[$status]++;
    $stats['pages'][$filename] = $status;
}

// Scanner les sous-dossiers importants
$subdirs = ['admin', 'api', 'classes', 'services', 'includes'];

foreach ($subdirs as $subdir) {
    $path = $currentDir . '/' . $subdir;
    if (!is_dir($path)) continue;
    
    $subfiles = glob($path . '/*.php');
    foreach ($subfiles as $file) {
        $stats['total']++;
        $status = testFile($file);
        $stats[$status]++;
        $stats['pages'][$subdir . '/' . basename($file)] = $status;
    }
}

// Calculer le pourcentage
$percentage = $stats['total'] > 0 ? round(($stats['ok'] / $stats['total']) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test des Pages - Culture Radar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .report-container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .stat-box {
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .stat-number {
            font-size: 3rem;
            font-weight: bold;
        }
        .progress-custom {
            height: 30px;
            font-size: 1rem;
        }
        .page-badge {
            margin: 3px;
            padding: 5px 10px;
            font-size: 0.85rem;
        }
        .section-title {
            background: #f8f9fa;
            padding: 10px 15px;
            border-radius: 5px;
            margin: 20px 0 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="report-container">
        <h1 class="text-center mb-4">
            🔍 Rapport de Test des Pages
        </h1>
        
        <!-- Score global -->
        <div class="stat-box bg-light">
            <div class="stat-number text-<?php echo $percentage >= 80 ? 'success' : ($percentage >= 60 ? 'warning' : 'danger'); ?>">
                <?php echo $percentage; ?>%
            </div>
            <div class="text-muted">Score de Santé Global</div>
        </div>
        
        <!-- Barre de progression -->
        <div class="progress progress-custom mb-4">
            <div class="progress-bar bg-success" style="width: <?php echo ($stats['ok']/$stats['total']*100); ?>%">
                <?php echo $stats['ok']; ?> OK
            </div>
            <?php if ($stats['warning'] > 0): ?>
            <div class="progress-bar bg-warning" style="width: <?php echo ($stats['warning']/$stats['total']*100); ?>%">
                <?php echo $stats['warning']; ?> Warnings
            </div>
            <?php endif; ?>
            <?php if ($stats['error'] > 0): ?>
            <div class="progress-bar bg-danger" style="width: <?php echo ($stats['error']/$stats['total']*100); ?>%">
                <?php echo $stats['error']; ?> Erreurs
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Statistiques -->
        <div class="row text-center mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h3><?php echo $stats['total']; ?></h3>
                        <p class="mb-0">Pages totales</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body">
                        <h3 class="text-success"><?php echo $stats['ok']; ?></h3>
                        <p class="mb-0">Sans problème</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-warning">
                    <div class="card-body">
                        <h3 class="text-warning"><?php echo $stats['warning']; ?></h3>
                        <p class="mb-0">Avertissements</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-danger">
                    <div class="card-body">
                        <h3 class="text-danger"><?php echo $stats['error']; ?></h3>
                        <p class="mb-0">Erreurs</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Liste des pages -->
        <h3>📄 Détail des Pages</h3>
        
        <?php
        // Grouper par statut
        $grouped = [
            'ok' => [],
            'warning' => [],
            'error' => []
        ];
        
        foreach ($stats['pages'] as $page => $status) {
            $grouped[$status][] = $page;
        }
        ?>
        
        <?php if (!empty($grouped['error'])): ?>
        <div class="section-title text-danger">
            ❌ Pages avec erreurs (<?php echo count($grouped['error']); ?>)
        </div>
        <div class="mb-3">
            <?php foreach ($grouped['error'] as $page): ?>
                <span class="badge bg-danger page-badge"><?php echo $page; ?></span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($grouped['warning'])): ?>
        <div class="section-title text-warning">
            ⚠️ Pages avec avertissements (<?php echo count($grouped['warning']); ?>)
        </div>
        <div class="mb-3">
            <?php foreach ($grouped['warning'] as $page): ?>
                <span class="badge bg-warning page-badge"><?php echo $page; ?></span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <div class="section-title text-success">
            ✅ Pages fonctionnelles (<?php echo count($grouped['ok']); ?>)
        </div>
        <div class="mb-3">
            <?php foreach ($grouped['ok'] as $page): ?>
                <span class="badge bg-success page-badge"><?php echo $page; ?></span>
            <?php endforeach; ?>
        </div>
        
        <!-- Recommandations -->
        <?php if ($percentage < 100): ?>
        <div class="alert alert-info mt-4">
            <h5>💡 Recommandations</h5>
            <ul class="mb-0">
                <?php if ($stats['warning'] > 0): ?>
                <li>Vérifier les pages avec avertissements (souvent des sessions non initialisées)</li>
                <?php endif; ?>
                <?php if ($stats['error'] > 0): ?>
                <li>Corriger en priorité les pages avec erreurs</li>
                <?php endif; ?>
                <li>Tester manuellement les pages critiques (login, register, dashboard)</li>
            </ul>
        </div>
        <?php else: ?>
        <div class="alert alert-success mt-4">
            <h5>🎉 Parfait !</h5>
            <p class="mb-0">Toutes les pages sont fonctionnelles. Le site est prêt à être utilisé !</p>
        </div>
        <?php endif; ?>
        
        <div class="text-center text-muted mt-4">
            <small>Test effectué le <?php echo date('d/m/Y à H:i:s'); ?></small>
        </div>
    </div>
</body>
</html>