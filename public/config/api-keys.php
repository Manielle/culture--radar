<?php
/**
 * Configuration des clés API
 * Utilise les variables d'environnement pour la sécurité
 */

require_once dirname(__DIR__) . '/includes/env.php';

return [
    'claude' => [
        'key' => Env::get('CLAUDE_API_KEY', ''),
        'model' => Env::get('CLAUDE_MODEL', 'claude-3-5-sonnet-20241022'),
        'api_url' => 'https://api.anthropic.com/v1/messages',
        'enabled' => !empty(Env::get('CLAUDE_API_KEY')) && !Env::isDemoMode()
    ],
    
    'openai' => [
        'key' => Env::get('OPENAI_API_KEY', ''),
        'enabled' => !empty(Env::get('OPENAI_API_KEY'))
    ],
    
    'google_maps' => [
        'key' => Env::get('GOOGLE_MAPS_KEY', ''),
        'enabled' => !empty(Env::get('GOOGLE_MAPS_KEY'))
    ],
    
    'serpapi' => [
        'key' => Env::get('SERPAPI_KEY', ''),
        'enabled' => !empty(Env::get('SERPAPI_KEY')) && !Env::isDemoMode()
    ],
    
    // Mode demo pour économiser les appels API
    'demo_mode' => Env::isDemoMode()
];
?>