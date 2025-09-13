<?php
// Global API configuration
return [
    'base_url' => '/bcash/public/api',  // No spaces, simpler URL
    'allowed_origins' => [
        'http://localhost:3000',
        'http://127.0.0.1:3000',
        'http://localhost',
        'http://127.0.0.1'
    ],
    'cookie_domain' => 'localhost',
    'use_secure_cookies' => false  // Set to true in production with HTTPS
];
