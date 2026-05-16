<?php

namespace App\Jobs;

use App\Services\Uploads\ChunkUploadService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class UploadCleanupJob implements ShouldQueue
{
    use Queueable;

    public function handle(ChunkUploadService $uploads): void
    {
        $count = $uploads->cleanupExpired();

        if ($count > 0) {
            Log::info('[ChunkUpload] Expired upload sessions cleaned.', ['count' => $count]);
        }
    }
}
