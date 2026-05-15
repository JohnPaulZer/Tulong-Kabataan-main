<?php

/*
|--------------------------------------------------------------------------
| Automated ID Verification Configuration
|--------------------------------------------------------------------------
|
| Centralized config for the automated ID verification pipeline. Every
| value is environment-driven so the pipeline can be tuned per deployment
| without touching code. The provider is pluggable: switching providers
| only requires changing ID_VERIFICATION_PROVIDER and supplying the
| matching API key.
|
| Scoring thresholds:
|   - score >= auto_approve_score   -> account is auto-verified
|   - score >= manual_review_score  -> queued for admin review
|   - score <  manual_review_score  -> rejected or asked to re-upload
|
*/

return [

    // Active provider. Supported: didit, ocr_space, google_vision, none.
    // Setting "none" disables external API calls and falls back to local
    // image quality + duplicate-hash checks only (sends to manual review).
    'provider' => env('ID_VERIFICATION_PROVIDER', 'ocr_space'),

    // Whether automated review is active at all. When false the upload
    // flow behaves exactly like the legacy manual flow (status: pending).
    'enabled' => filter_var(env('ID_VERIFICATION_ENABLED', true), FILTER_VALIDATE_BOOLEAN),

    // Per-user attempt limits — enforced inside the service in addition
    // to the route-level throttle:upload middleware.
    'max_attempts_per_day' => (int) env('ID_VERIFICATION_MAX_ATTEMPTS_PER_DAY', 5),

    // Liveness / face match. Only meaningful when the provider supports
    // them (Didit). When true and the provider does not support liveness,
    // submissions are routed to manual review instead of auto-approve.
    'require_liveness' => filter_var(env('ID_REQUIRE_LIVENESS', false), FILTER_VALIDATE_BOOLEAN),

    // File constraints — these are validated in addition to the global
    // R2 storage validation. Values are intentionally tighter than R2.
    'file' => [
        'max_size_mb'   => (int) env('ID_MAX_FILE_SIZE_MB', 5),
        'allowed_types' => array_values(array_filter(array_map('trim', explode(',',
            (string) env('ID_ALLOWED_TYPES', 'jpg,jpeg,png,webp')
        )))),
        'allowed_mimes' => [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/webp',
        ],
    ],

    // Score thresholds. Comments inside IdScoringService explain how
    // each individual check contributes to the final score.
    'scoring' => [
        'auto_approve'   => (int) env('ID_AUTO_APPROVE_SCORE', 85),
        'manual_review'  => (int) env('ID_MANUAL_REVIEW_SCORE', 60),
        // Fine-grained per-check weights. Total caps at 100.
        'weights' => [
            'name'            => 35,
            'birthdate'       => 25,
            'id_type'         => 15,
            'address'         => 10,
            'image_quality'   => 10,
            'expiry_valid'    => 5,
        ],
    ],

    // Image quality thresholds. These are deliberately loose to avoid
    // false rejections of valid IDs photographed under normal conditions.
    'quality' => [
        'min_width'        => 400,
        'min_height'       => 250,
        'min_brightness'   => 35,   // mean pixel value 0-255
        'max_brightness'   => 235,
        'min_sharpness'    => 12.0, // simple Laplacian-like variance
        'sample_pixels'    => 4000, // pixels sampled for sharpness/brightness
    ],

    // Per-provider settings.
    'providers' => [
        'didit' => [
            'api_key'       => env('DIDIT_API_KEY'),
            'workflow_id'   => env('DIDIT_WORKFLOW_ID'),
            'base_url'      => env('DIDIT_BASE_URL', 'https://verification.didit.me'),
            'webhook_secret' => env('DIDIT_WEBHOOK_SECRET'),
            'timeout'       => (int) env('DIDIT_TIMEOUT', 30),
        ],
        'ocr_space' => [
            'api_key'  => env('OCR_SPACE_API_KEY'),
            'base_url' => env('OCR_SPACE_BASE_URL', 'https://api.ocr.space/parse/image'),
            // OCR Engine 2 is much better for IDs, both engines are free.
            'engine'   => (int) env('OCR_SPACE_ENGINE', 2),
            'language' => env('OCR_SPACE_LANGUAGE', 'eng'),
            'timeout'  => (int) env('OCR_SPACE_TIMEOUT', 30),
            // OCR.Space free tier accepts files up to 1MB. Larger files are
            // resized down before being sent to keep us inside the free tier.
            'max_upload_kb' => (int) env('OCR_SPACE_MAX_UPLOAD_KB', 1024),
        ],
        'google_vision' => [
            'api_key'  => env('GOOGLE_VISION_API_KEY'),
            'base_url' => env('GOOGLE_VISION_BASE_URL', 'https://vision.googleapis.com/v1/images:annotate'),
            'timeout'  => (int) env('GOOGLE_VISION_TIMEOUT', 30),
        ],
    ],

    // Per-provider quota guard — defends free-tier monthly limits.
    // Each provider key here matches a key in providers[] above. Set to
    // 0 to disable a window (e.g. when using a paid plan with no cap).
    'quotas' => [
        'didit' => [
            // Didit's free plan gives 500 verifications/month. We set the
            // cap at 500 and when exhausted, auto-fallback to OCR.Space.
            'daily'   => (int) env('ID_QUOTA_DIDIT_DAILY', 30),
            'monthly' => (int) env('ID_QUOTA_DIDIT_MONTHLY', 500),
        ],
        'ocr_space' => [
            // OCR.Space free tier: 25,000 calls/month, ~500/day soft cap.
            // We sit comfortably below to leave headroom for retries.
            'daily'   => (int) env('ID_QUOTA_OCR_SPACE_DAILY', 400),
            'monthly' => (int) env('ID_QUOTA_OCR_SPACE_MONTHLY', 20000),
        ],
        'google_vision' => [
            // Google Vision free tier: 1,000 units/month.
            'daily'   => (int) env('ID_QUOTA_GOOGLE_VISION_DAILY', 50),
            'monthly' => (int) env('ID_QUOTA_GOOGLE_VISION_MONTHLY', 900),
        ],
    ],

    // Words that strongly suggest a fake / sample / template ID.
    'fraud_keywords' => [
        'sample', 'specimen', 'template', 'fake', 'duplicate copy',
        'for testing', 'test only', 'do not accept', 'mock', 'dummy',
    ],

    // Text patterns used to identify the ID type from OCR output.
    // Patterns are matched case-insensitively against normalized text.
    'id_type_patterns' => [
        'philid' => [
            'republic of the philippines',
            'philippine identification',
            'pambansang pagkakakilanlan',
            'philsys',
            'philid',
            'national id',
            'psn',
            'pcn',
        ],
        'drivers_license' => [
            'land transportation office',
            'driver\'s license',
            'drivers license',
            'license no',
            'restriction',
            'dl code',
            'expiration date',
            'lto',
        ],
    ],
];
