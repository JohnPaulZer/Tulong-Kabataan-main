<?php

namespace App\Services\Uploads;

use App\Models\UploadSession;
use RuntimeException;

class FileMergeService
{
    public function merge(UploadSession $session): string
    {
        $dir = $this->sessionDirectory($session->upload_id);
        $mergedPath = $dir . DIRECTORY_SEPARATOR . 'merged_' . $session->upload_id . '.part';
        $output = fopen($mergedPath, 'wb');

        if ($output === false) {
            throw new RuntimeException('Unable to prepare the uploaded file.');
        }

        try {
            for ($index = 0; $index < (int) $session->total_chunks; $index++) {
                $chunkPath = $this->chunkPath($session->upload_id, $index);
                if (! is_file($chunkPath)) {
                    throw new RuntimeException('A chunk is missing from this upload.');
                }

                $input = fopen($chunkPath, 'rb');
                if ($input === false) {
                    throw new RuntimeException('Unable to read an uploaded chunk.');
                }

                stream_copy_to_stream($input, $output);
                fclose($input);
            }
        } finally {
            fclose($output);
        }

        return $mergedPath;
    }

    public function chunkPath(string $uploadId, int $chunkIndex): string
    {
        return $this->sessionDirectory($uploadId) . DIRECTORY_SEPARATOR . $chunkIndex . '.chunk';
    }

    public function sessionDirectory(string $uploadId): string
    {
        $base = $this->baseDirectory();
        $safeUploadId = preg_replace('/[^A-Za-z0-9_-]/', '', $uploadId);

        return $base . DIRECTORY_SEPARATOR . $safeUploadId;
    }

    public function ensureSessionDirectory(string $uploadId): string
    {
        $dir = $this->sessionDirectory($uploadId);
        if (! is_dir($dir) && ! mkdir($dir, 0750, true) && ! is_dir($dir)) {
            throw new RuntimeException('Unable to create the upload workspace.');
        }

        return $dir;
    }

    public function deleteSessionDirectory(string $uploadId): void
    {
        $dir = $this->sessionDirectory($uploadId);
        if (! is_dir($dir)) {
            return;
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            $file->isDir() ? @rmdir($file->getPathname()) : @unlink($file->getPathname());
        }

        @rmdir($dir);
    }

    private function baseDirectory(): string
    {
        $configured = (string) config('chunk_upload.temp_dir', 'storage/app/chunks');
        $base = str_starts_with($configured, DIRECTORY_SEPARATOR) || preg_match('/^[A-Za-z]:[\\\\\/]/', $configured)
            ? $configured
            : base_path($configured);

        if (! is_dir($base) && ! mkdir($base, 0750, true) && ! is_dir($base)) {
            throw new RuntimeException('Unable to create the chunk upload directory.');
        }

        return $base;
    }
}
