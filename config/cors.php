<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    // 'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'paths' => ['api/*', '/menu', '/sanctum/csrf-cookie', '/*', 'storage/*', 'order', '/order', '/kds/*'],
    'allowed_methods' => ['GET','POST','PUT','PATCH','DELETE','OPTIONS'],
    'allowed_origins' => ['http://localhost:3000','http://127.0.0.1:3000','http://localhost:8000','http://10.0.2.2:8000', 'http://10.0.2.2:8081', '10.0.2.2:8081', 'http://10.0.2.2:8000', 'http://10.0.2.2:8082', '10.0.2.2:8082', 'http://10.0.2.2:8000', 'http://localhost:8082', 'localhost:8082'],
    'allowed_headers' => ['*'],
    'exposed_headers' => ['X-XSRF-TOKEN'],
    'supports_credentials' => true,
];
