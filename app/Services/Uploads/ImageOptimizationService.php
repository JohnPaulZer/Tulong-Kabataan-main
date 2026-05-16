<?php

namespace App\Services\Uploads;

use Illuminate\Http\UploadedFile;

class ImageOptimizationService
{
    public function makeUploadedFile(string $path, string $originalName, string $mime): UploadedFile
    {
        return new UploadedFile(
            $path,
            $this->safeOriginalName($originalName),
            $mime,
            UPLOAD_ERR_OK,
            true
        );
    }

    private function safeOriginalName(string $originalName): string
    {
        $base = pathinfo($originalName, PATHINFO_FILENAME) ?: 'upload';
        $extension = pathinfo($originalName, PATHINFO_EXTENSION) ?: 'bin';
        $base = preg_replace('/[^A-Za-z0-9_-]+/', '-', $base) ?: 'upload';
        $extension = preg_replace('/[^A-Za-z0-9]+/', '', strtolower($extension)) ?: 'bin';

        return trim($base, '-') . '.' . $extension;
    }
}
