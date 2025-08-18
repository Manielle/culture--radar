<?php
/**
 * Culture Radar Setup Test Script
 * Tests all components and provides setup status
 */

require_once __DIR__ . '/config.php';

header('Content-Type: text/html; charset=utf-8');

$tests = [];
$errors = [];

// Test 1: Configuration
$tests['config'] = [
    'name' => 'Configuration',
    'status' => false,
    'message' => ''
];

if (file_exists('.env')) {
    $tests['config']['status'] = true;
    $tests['config']['message'] = '✅ .env file found and loaded';
} else {
    $tests['config']['message'] = '❌ .env file not found';
    $errors[] = 'Configuration file missing';
}

// Test 2: Database Connection
$tests['database'] = [
    'name' => 'Database Connection',
    'status' => false,
    'message' => ''
];

try {
    $dbConfig = Config::database();
    $host = str_replace(':' . $dbConfig['port'], '', $dbConfig['host']);
    $dsn = "mysql:host=" . $host . ";port=" . $dbConfig['port'] . ";dbname=" . $dbConfig['name'] . ";charset=" . $dbConfig['charset'];
    $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Test a simple query
    $stmt = $pdo->query("SELECT 1");
    
    $tests['database']['status'] = true;
    $tests['database']['message'] = '✅ Database connected successfully';
} catch(PDOException $e) {
    $tests['database']['message'] = '❌ Database connection failed: ' . $e->getMessage();
    $errors[] = 'Database not accessible. Run setup-database.php first.';
}

// Test 3: Tables exist
$tests['tables'] = [
    'name' => 'Database Tables',
    'status' => false,
    'message' => ''
];

if ($tests['database']['status']) {
    try {
        $requiredTables = ['users', 'user_profiles', 'events', 'venues'];
        $existingTables = [];
        
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($requiredTables as $table) {
            if (in_array($table, $tables)) {
                $existingTables[] = $table;
            }
        }
        
        if (count($existingTables) == count($requiredTables)) {
            $tests['tables']['status'] = true;
            $tests['tables']['message'] = '✅ All required tables exist: ' . implode(', ', $existingTables);
        } else {
            $missing = array_diff($requiredTables, $existingTables);
            $tests['tables']['message'] = '⚠️ Missing tables: ' . implode(', ', $missing);
            $errors[] = 'Some database tables are missing. Run setup-database.php.';
        }
    } catch(Exception $e) {
        $tests['tables']['message'] = '❌ Could not check tables: ' . $e->getMessage();
    }
} else {
    $tests['tables']['message'] = '⏭️ Skipped (database not connected)';
}

// Test 4: API Keys
$tests['apis'] = [
    'name' => 'API Keys',
    'status' => false,
    'message' => ''
];

$apiKeys = [
    'OpenAgenda' => Config::get('OPENAGENDA_API_KEY'),
    'OpenWeatherMap' => Config::get('OPENWEATHER_API_KEY'),
    'Google Maps' => Config::get('GOOGLE_MAPS_API_KEY'),
    'RATP' => Config::get('RATP_API_KEY')
];

$configuredApis = [];
foreach ($apiKeys as $name => $key) {
    if (!empty($key) && $key !== 'your_' . strtolower(str_replace(' ', '_', $name)) . '_api_key_here') {
        $configuredApis[] = $name;
    }
}

if (count($configuredApis) == count($apiKeys)) {
    $tests['apis']['status'] = true;
    $tests['apis']['message'] = '✅ All APIs configured: ' . implode(', ', $configuredApis);
} else {
    $tests['apis']['message'] = '⚠️ APIs configured: ' . (empty($configuredApis) ? 'None' : implode(', ', $configuredApis));
}

// Test 5: File Structure
$tests['files'] = [
    'name' => 'File Structure',
    'status' => false,
    'message' => ''
];

$requiredDirs = ['assets', 'includes', 'services', 'api', 'admin', 'cache'];
$missingDirs = [];

foreach ($requiredDirs as $dir) {
    if (!is_dir(__DIR__ . '/' . $dir)) {
        $missingDirs[] = $dir;
    }
}

if (empty($missingDirs)) {
    $tests['files']['status'] = true;
    $tests['files']['message'] = '✅ All required directories exist';
} else {
    $tests['files']['message'] = '⚠️ Missing directories: ' . implode(', ', $missingDirs);
}

// Test 6: OpenAgenda Service
$tests['openagenda'] = [
    'name' => 'OpenAgenda Service',
    'status' => false,
    'message' => ''
];

try {
    require_once __DIR__ . '/services/OpenAgendaService.php';
    $openAgendaService = new OpenAgendaService();
    
    $testEvents = $openAgendaService->getEventsByLocation([
        'city' => 'Paris',
        'additional' => ['size' => 1]
    ]);
    
    if (!empty($testEvents)) {
        $tests['openagenda']['status'] = true;
        $tests['openagenda']['message'] = '✅ OpenAgenda service working (fetched ' . count($testEvents) . ' events)';
    } else {
        $tests['openagenda']['message'] = '⚠️ OpenAgenda returned no events (using mock data)';
    }
} catch (Exception $e) {
    $tests['openagenda']['message'] = '❌ OpenAgenda service error: ' . $e->getMessage();
}

// Test 7: Page Links
$tests['pages'] = [
    'name' => 'Page Structure',
    'status' => false,
    'message' => ''
];

$pages = [
    'index.php' => 'Homepage',
    'login.php' => 'Login',
    'register.php' => 'Register',
    'dashboard.php' => 'Dashboard',
    'discover.php' => 'Discover',
    'onboarding.php' => 'Onboarding'
];

$existingPages = [];
foreach ($pages as $file => $name) {
    if (file_exists(__DIR__ . '/' . $file)) {
        $existingPages[] = $name;
    }
}

if (count($existingPages) == count($pages)) {
    $tests['pages']['status'] = true;
    $tests['pages']['message'] = '✅ All pages exist: ' . implode(', ', $existingPages);
} else {
    $tests['pages']['message'] = '⚠️ Found pages: ' . implode(', ', $existingPages);
}

// Calculate overall status
$totalTests = count($tests);
$passedTests = count(array_filter($tests, function($test) { return $test['status']; }));
$overallStatus = ($passedTests == $totalTests) ? 'ready' : (($passedTests > 0) ? 'partial' : 'not-ready');

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Culture Radar - Setup Test</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2em;
            margin-bottom: 10px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            margin-top: 10px;
        }
        
        .status-ready {
            background: #10b981;
            color: white;
        }
        
        .status-partial {
            background: #f59e0b;
            color: white;
        }
        
        .status-not-ready {
            background: #ef4444;
            color: white;
        }
        
        .content {
            padding: 30px;
        }
        
        .test-item {
            padding: 20px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .test-item:last-child {
            border-bottom: none;
        }
        
        .test-status {
            font-size: 24px;
        }
        
        .test-details {
            flex: 1;
        }
        
        .test-name {
            font-weight: 600;
            color: #111827;
            margin-bottom: 5px;
        }
        
        .test-message {
            color: #6b7280;
            font-size: 14px;
        }
        
        .actions {
            padding: 30px;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
        }
        
        .button {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            margin-right: 10px;
            margin-bottom: 10px;
            transition: background 0.2s;
        }
        
        .button:hover {
            background: #5a67d8;
        }
        
        .button-secondary {
            background: #6b7280;
        }
        
        .button-secondary:hover {
            background: #4b5563;
        }
        
        .error-box {
            background: #fef2f2;
            border: 1px solid #fee2e2;
            border-radius: 6px;
            padding: 15px;
            margin-top: 20px;
            color: #991b1b;
        }
        
        .error-box h3 {
            margin-bottom: 10px;
            color: #dc2626;
        }
        
        .error-box ul {
            margin-left: 20px;
        }
        
        .success-box {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            padding: 15px;
            margin-top: 20px;
            color: #14532d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Culture Radar Setup Test</h1>
            <div class="status-badge status-<?php echo $overallStatus; ?>">
                <?php 
                if ($overallStatus == 'ready') {
                    echo '✅ Ready to Use';
                } elseif ($overallStatus == 'partial') {
                    echo '⚠️ Partially Configured';
                } else {
                    echo '❌ Setup Required';
                }
                ?>
            </div>
            <p style="margin-top: 10px; opacity: 0.9;">
                <?php echo $passedTests; ?> / <?php echo $totalTests; ?> tests passed
            </p>
        </div>
        
        <div class="content">
            <?php foreach ($tests as $test): ?>
            <div class="test-item">
                <div class="test-status">
                    <?php echo $test['status'] ? '✅' : '❌'; ?>
                </div>
                <div class="test-details">
                    <div class="test-name"><?php echo $test['name']; ?></div>
                    <div class="test-message"><?php echo $test['message']; ?></div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (!empty($errors)): ?>
            <div class="error-box">
                <h3>Action Required:</h3>
                <ul>
                    <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <?php if ($overallStatus == 'ready'): ?>
            <div class="success-box">
                <h3>🎉 Setup Complete!</h3>
                <p>Your Culture Radar installation is ready. You can now access all features.</p>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="actions">
            <?php if (!$tests['database']['status'] || !$tests['tables']['status']): ?>
            <a href="setup-database.php" class="button">Setup Database</a>
            <?php endif; ?>
            
            <a href="index.php" class="button">Go to Homepage</a>
            <a href="test-openagenda.php" class="button button-secondary">Test OpenAgenda API</a>
            <a href="login.php" class="button button-secondary">Login Page</a>
            <a href="register.php" class="button button-secondary">Register Page</a>
        </div>
    </div>
</body>
</html>