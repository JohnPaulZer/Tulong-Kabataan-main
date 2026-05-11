<?php

/*
|--------------------------------------------------------------------------
| Cloudflare R2 Configuration
|--------------------------------------------------------------------------
|
| Centralized configuration for the Cloudflare R2 object storage integration.
| R2 is fully S3-compatible so we reuse Laravel's S3 Flysystem driver under
| the hood. All values are driven by environment variables — nothing is
| hardcoded — so the same service works across local, staging and prod.
|
| The "folders" array defines logical buckets-within-the-bucket. Every
| feature that uploads files should reference one of these folder keys
| rather than hardcoding its own prefix, to keep the storage layout
| predictable and easy to browse.
|
*/

return [

    'account_id'        => env('R2_ACCOUNT_ID'),
    'access_key_id'     => env('R2_ACCESS_KEY_ID'),
    'secret_access_key' => env('R2_SECRET_ACCESS_KEY'),
    'bucket'            => env('R2_BUCKET_NAME'),
    'endpoint'          => env('R2_ENDPOINT'),
    'public_url'        => env('R2_PUBLIC_URL'),
    'region'            => env('R2_REGION', 'auto'),

    // Disk name registered in config/filesystems.php
    'disk'              => 'r2',

    // Logical folder prefixes per module/feature. Keep this as the single
    // source of truth so controllers never hardcode folder strings.
    'folders' => [
        'profile_photos'       => 'users/profile',
        'kyc_ids'              => 'users/kyc/ids',
        'kyc_faces'            => 'users/kyc/faces',
        'kyc_selfies'          => 'users/kyc/selfies',
        'campaign_featured'    => 'campaigns/featured',
        'campaign_qr'          => 'campaigns/qrcodes',
        'campaign_images'      => 'campaigns/images',
        'campaign_updates'     => 'campaigns/updates',
        'donation_proofs'      => 'donations/proofs',
        'manual_donation_proofs' => 'donations/manual-proofs',
        'event_photos'         => 'events/banners',
        'impact_report_photos' => 'admin/impact-reports',
        'admin_uploads'        => 'admin/uploads',
        'documents'            => 'documents',
    ],

    // Default validation policy applied when a caller does not override it.
    'validation' => [
        'max_size_kb'    => (int) env('R2_MAX_FILE_SIZE_KB', 7168),
        'image_mimes'    => ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml'],
        'document_mimes' => ['application/pdf'],
    ],

];
