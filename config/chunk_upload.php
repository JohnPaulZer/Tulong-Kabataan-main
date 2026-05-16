<?php

return [
    'enabled' => env('CHUNK_UPLOAD_ENABLED', true),
    'chunk_size_mb' => (int) env('CHUNK_UPLOAD_SIZE_MB', 3),
    'temp_dir' => env('CHUNK_UPLOAD_TEMP_DIR', 'storage/app/chunks'),
    'max_file_size_mb' => (int) env('CHUNK_UPLOAD_MAX_FILE_SIZE_MB', 25),
    'allowed_types' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('CHUNK_UPLOAD_ALLOWED_TYPES', 'jpg,jpeg,png,webp,pdf'))
    ))),
    'cleanup_minutes' => (int) env('CHUNK_UPLOAD_CLEANUP_MINUTES', 60),
    'max_active_uploads_per_user' => (int) env('CHUNK_UPLOAD_MAX_ACTIVE_PER_USER', 5),
    'max_parallel_uploads' => (int) env('CHUNK_UPLOAD_MAX_PARALLEL', 3),
    'retry_attempts' => (int) env('CHUNK_UPLOAD_RETRY_ATTEMPTS', 3),

    'modules' => [
        'profile_photo' => [
            'folder' => 'profile_photos',
            'mimes' => ['image/jpeg', 'image/png', 'image/webp'],
            'max_kb' => 25600,
        ],
        'kyc_id' => [
            'folder' => 'kyc_ids',
            'mimes' => ['image/jpeg', 'image/png', 'image/webp'],
            'max_kb' => 25600,
        ],
        'kyc_face' => [
            'folder' => 'kyc_faces',
            'mimes' => ['image/jpeg', 'image/png', 'image/webp'],
            'max_kb' => 25600,
        ],
        'kyc_selfie' => [
            'folder' => 'kyc_selfies',
            'mimes' => ['image/jpeg', 'image/png', 'image/webp'],
            'max_kb' => 25600,
        ],
        'campaign_featured' => [
            'folder' => 'campaign_featured',
            'mimes' => ['image/jpeg', 'image/png', 'image/webp'],
            'max_kb' => (int) env('CAMPAIGN_IMAGE_MAX_SIZE_KB', 15360),
        ],
        'campaign_qr' => [
            'folder' => 'campaign_qr',
            'mimes' => ['image/jpeg', 'image/png', 'image/webp'],
            'max_kb' => (int) env('CAMPAIGN_IMAGE_MAX_SIZE_KB', 15360),
        ],
        'campaign_image' => [
            'folder' => 'campaign_images',
            'mimes' => ['image/jpeg', 'image/png', 'image/webp'],
            'max_kb' => (int) env('CAMPAIGN_IMAGE_MAX_SIZE_KB', 15360),
        ],
        'campaign_update' => [
            'folder' => 'campaign_updates',
            'mimes' => ['image/jpeg', 'image/png', 'image/webp'],
            'max_kb' => 25600,
        ],
        'donation_proof' => [
            'folder' => 'donation_proofs',
            'mimes' => ['image/jpeg', 'image/png', 'image/webp'],
            'max_kb' => 25600,
        ],
        'manual_donation_proof' => [
            'folder' => 'manual_donation_proofs',
            'mimes' => ['image/jpeg', 'image/png', 'image/webp'],
            'max_kb' => 25600,
        ],
        'impact_report_photo' => [
            'folder' => 'impact_report_photos',
            'mimes' => ['image/jpeg', 'image/png', 'image/webp'],
            'max_kb' => 25600,
        ],
        'page_media' => [
            'folder' => 'page_media',
            'mimes' => ['image/jpeg', 'image/png', 'image/webp'],
            'max_kb' => 25600,
        ],
        'document' => [
            'folder' => 'documents',
            'mimes' => ['application/pdf'],
            'max_kb' => 25600,
            'convert_to_webp' => false,
        ],
    ],
];
