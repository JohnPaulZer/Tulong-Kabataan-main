<?php

return [
    'rate_limits' => [
        'public' => [
            'max_attempts' => (int) env('RATE_LIMIT_PUBLIC_MAX', 120),
            'decay_minutes' => (int) env('RATE_LIMIT_PUBLIC_DECAY', 1),
        ],
        'api' => [
            'max_attempts' => (int) env('RATE_LIMIT_API_MAX', 60),
            'decay_minutes' => (int) env('RATE_LIMIT_API_DECAY', 1),
        ],
        'auth' => [
            'max_attempts' => (int) env('RATE_LIMIT_AUTH_MAX', 8),
            'decay_minutes' => (int) env('RATE_LIMIT_AUTH_DECAY', 1),
        ],
        'admin' => [
            'max_attempts' => (int) env('RATE_LIMIT_ADMIN_MAX', 60),
            'decay_minutes' => (int) env('RATE_LIMIT_ADMIN_DECAY', 1),
        ],
        'payment' => [
            'max_attempts' => (int) env('RATE_LIMIT_PAYMENT_MAX', 10),
            'decay_minutes' => (int) env('RATE_LIMIT_PAYMENT_DECAY', 1),
        ],
        'upload' => [
            'max_attempts' => (int) env('RATE_LIMIT_UPLOAD_MAX', 12),
            'decay_minutes' => (int) env('RATE_LIMIT_UPLOAD_DECAY', 1),
        ],
        'chunk_upload' => [
            'max_attempts' => (int) env('RATE_LIMIT_CHUNK_UPLOAD_MAX', 240),
            'decay_minutes' => (int) env('RATE_LIMIT_CHUNK_UPLOAD_DECAY', 1),
        ],
        'chatbot' => [
            'max_attempts' => (int) env('RATE_LIMIT_CHATBOT_MAX', 12),
            'decay_minutes' => (int) env('RATE_LIMIT_CHATBOT_DECAY', 1),
        ],
        'webhook' => [
            'max_attempts' => (int) env('RATE_LIMIT_WEBHOOK_MAX', 60),
            'decay_minutes' => (int) env('RATE_LIMIT_WEBHOOK_DECAY', 1),
        ],
    ],

    'headers' => [
        'enabled' => (bool) env('SECURITY_HEADERS_ENABLED', true),
        'hsts_enabled' => (bool) env('SECURITY_HSTS_ENABLED', true),
        'hsts_max_age' => (int) env('SECURITY_HSTS_MAX_AGE', 31536000),
        'csp_enabled' => (bool) env('SECURITY_CSP_ENABLED', true),
        'csp' => env('SECURITY_CSP', ''),
        'csp_extra_connect' => env('SECURITY_CSP_EXTRA_CONNECT', ''),
        'csp_extra_img' => env('SECURITY_CSP_EXTRA_IMG', ''),
    ],

    'cors' => [
        'allowed_origins' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('CORS_ALLOWED_ORIGINS', env('APP_URL', 'http://127.0.0.1:8000')))
        ))),
    ],

    'webhooks' => [
        'secret' => env('WEBHOOK_SECRET'),
        'signature_header' => env('WEBHOOK_SIGNATURE_HEADER', 'X-TK-Signature'),
        'timestamp_header' => env('WEBHOOK_TIMESTAMP_HEADER', 'X-TK-Timestamp'),
        'tolerance_seconds' => (int) env('WEBHOOK_TOLERANCE_SECONDS', 300),
    ],
];
