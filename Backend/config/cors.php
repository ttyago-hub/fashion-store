<?php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'register', 'login'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost:5173', 'http://127.0.0.1:5173'],
    'allowed_headers' => ['*'],
    'supports_credentials' => true,
];