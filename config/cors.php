<?php

return [
    'paths' => ['api/*', 'broadcasting/auth', 'chatbot/*', 'events/updates', 'stats', 'administrator/*', 'livewire/*'],
    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
    'allowed_origins' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('CORS_ALLOWED_ORIGINS', env('APP_URL', 'http://127.0.0.1:8000')))
    ))),
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['Content-Type', 'X-Requested-With', 'X-CSRF-TOKEN', 'X-Socket-ID', 'X-TK-Signature', 'X-TK-Timestamp'],
    'exposed_headers' => ['Retry-After', 'X-RateLimit-Limit', 'X-RateLimit-Remaining'],
    'max_age' => 600,
    'supports_credentials' => true,
];
