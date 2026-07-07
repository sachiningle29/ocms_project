<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:4173',      // Vite dev server
        'http://127.0.0.1:4173',      // optional, for direct IP access
        'http://localhost:8001',
        'http://localhost:8080',
        'http://localhost:8000',
        'http://127.0.0.1:8000',
        'http://10.205.151.96:4173',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
