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

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    // The framework default is 0 (no caching), so the browser sends a fresh
    // OPTIONS preflight before every single POST/PATCH/DELETE — an extra
    // full round-trip on every save/toggle/delete. That's barely noticeable
    // on localhost but adds real, repeated latency on a mobile connection.
    // 24h lets the browser reuse one preflight result for the session.
    'max_age' => 86400,

    'supports_credentials' => false,

];
