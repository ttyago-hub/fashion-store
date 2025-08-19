<?php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'register', 'login', 'storage/*'], // ⚡ AGREGAR 'storage/*'
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'],
    'allowed_headers' => ['*'],
    'supports_credentials' => true,
];