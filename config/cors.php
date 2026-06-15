<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://sampahdetector.my.id',
        'http://localhost:8080',
        'http://localhost:3000',
        'http://127.0.0.1:8080',
    ],

    'allowed_origins_patterns' => [
        '#^http://localhost:\d+$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 86400,

    'supports_credentials' => false,

];
