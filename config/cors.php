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
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'], // semua route API akan menggunakan CORS

    'allowed_methods' => ['*'], // izinkan semua method (GET, POST, dll)

    'allowed_origins' =>  ['http://127.0.0.1:8001'], // izinkan semua domain frontend

    'allowed_headers' => ['*'], // izinkan semua header

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
