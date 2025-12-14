<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    // Include API routes and Sanctum's CSRF endpoint. Add auth endpoints explicitly.
    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'login',
        'logout',
        'user',
    ],

    'allowed_methods' => ['*'],

    // Configure allowed origins via environment to support Vite dev and production.
    // Accept comma-separated list in FRONTEND_URLS. FRONTEND_URL kept for convenience.
    'allowed_origins' => array_filter(array_map('trim', explode(',', env('FRONTEND_URLS', env('FRONTEND_URL', 'http://localhost:5173'))))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Required for cookie-based auth with Sanctum
    'supports_credentials' => true,
];
