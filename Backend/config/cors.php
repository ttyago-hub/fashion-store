<?php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'register', 'login'],
    'allowed_methods' => ['*'],
     'allowed_origins' => ['*'],
    'allowed_headers' => ['*'],
    'supports_credentials' => true,
];