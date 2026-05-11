<?php

namespace App\Services\Storage;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Centralized Cloudflare R2 storage service.
 *
 * This is the ONLY place in the app that talks to R2 directly.
 * Every controller, Livewire component, or job that uploads, replaces,
 * reads, or deletes a file must go through this service so that:
 *
 *   - Folder structure stays consistent (driven by config/r2.php)
 *   - Filenames are always unique & safe
 *   - Validation rules are enforced centrally
 *   - Failures raise a consistent exception type the callers can catch
 *   - The persisted value is always the object KEY (e.g. "users/profile/abc.jpg"),
 *     never a full URL, so URLs can be regenerated if the public domain changes.
 */
class R2StorageService
{
    /**
     * Upload an uploaded file to R2 and return the stored object key.
     *
     * @param  UploadedFile  $file          The validated upload from the request.
     * @param  string        $folderKey     A key from config('r2.folders') OR a raw folder path.
     * @param  array         $options       [
     *                                        'mimes'      => ['image/jpeg', ...],  // override allowed MIME list
     *                                        'max_kb'     => 2048,                 // override max size
     *                                        'visibility' => 'public'|'private',   // R2 visibility
     *                                        'prefix'     => 'optional-subdir',    // extra folder below the base
     *                                      ]
     * @return string                       The object key stored in R2 (save this in the DB).
     *
     * @throws R2StorageException           On validation or upload failure.
     */
    public function upload(UploadedFile $file, string $folderKey, array $options = []): string
    {
        if (! $file->isValid()) {
            throw new R2StorageException('Uploaded file is invalid or was not received properly.');
        }

        $this->validate($file, $options);

        $folder   = $this->resolveFolder($folderKey, $options['prefix'] ?? null);
        $filename = $this->generateFilename($file);
        $key      = trim($folder, '/') . '/' . $filename;

        try {
            $disk    = $this->disk();
            $stream  = fopen($file->getRealPath(), 'r');
            $success = $disk->put($key, $stream, [
                'visibility'  => $options['visibility'] ?? 'public',
                'ContentType' => $file->getMimeType(),
            ]);
            if (is_resource($stream)) {
                fclose($stream);
            }
        } catch (\Throwable $e) {
            Log::error('[R2] Upload failed', [
                'folder'  => $folder,
                'key'     => $key,
                'message' => $e->getMessage(),
            ]);
            throw new R2StorageException('Failed to upload file to storage. Please try again.', 0, $e);
        }

        if ($success === false) {
            throw new R2StorageException('Storage rejected the upload. Please try again.');
        }

        return $key;
    }

    /**
     * Replace an existing file on R2. Uploads the new file first, then deletes
     * the old key. If the upload fails, the old file is left intact so the DB
     * value remains valid.
     */
    public function replace(UploadedFile $file, ?string $previousKey, string $folderKey, array $options = []): string
    {
        $newKey = $this->upload($file, $folderKey, $options);

        if ($previousKey) {
            $this->delete($previousKey);
        }

        return $newKey;
    }

    /**
     * Delete an object from R2. Accepts either a stored object key or a full URL
     * (in which case the URL is normalized back to a key if possible). Missing
     * files are treated as success so delete calls are idempotent.
     */
    public function delete(?string $keyOrUrl): bool
    {
        if (empty($keyOrUrl)) {
            return true;
        }

        $key = $this->normalizeToKey($keyOrUrl);

        try {
            $disk = $this->disk();
            if (! $disk->exists($key)) {
                return true;
            }

            return (bool) $disk->delete($key);
        } catch (\Throwable $e) {
            Log::warning('[R2] Delete failed', [
                'key'     => $key,
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Return a publicly accessible URL for a stored object key. When R2_PUBLIC_URL
     * is configured, the result is prefixed with that domain. Otherwise, the S3
     * adapter generates a URL from the endpoint + bucket.
     *
     * Returns null on empty input so callers can chain safely.
     */
    public function url(?string $key): ?string
    {
        if (empty($key)) {
            return null;
        }

        // Already a full URL — pass through.
        if (Str::startsWith($key, ['http://', 'https://'])) {
            return $key;
        }

        $publicBase = rtrim((string) config('r2.public_url'), '/');
        if ($publicBase !== '') {
            return $publicBase . '/' . ltrim($key, '/');
        }

        try {
            return $this->disk()->url($key);
        } catch (\Throwable $e) {
            Log::warning('[R2] URL generation failed', ['key' => $key, 'message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Read the raw contents of an object from R2. Returns null if the file is
     * missing or unreadable. Useful for server-side processing / re-serving.
     */
    public function get(?string $key): ?string
    {
        if (empty($key)) {
            return null;
        }

        try {
            $disk = $this->disk();
            return $disk->exists($key) ? $disk->get($key) : null;
        } catch (\Throwable $e) {
            Log::warning('[R2] Read failed', ['key' => $key, 'message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Check whether a given object key exists in R2.
     */
    public function exists(?string $key): bool
    {
        if (empty($key)) {
            return false;
        }

        try {
            return $this->disk()->exists($this->normalizeToKey($key));
        } catch (\Throwable $e) {
            return false;
        }
    }

    // ---------------------------------------------------------------------
    // Internal helpers
    // ---------------------------------------------------------------------

    protected function disk()
    {
        return Storage::disk(config('r2.disk', 'r2'));
    }

    protected function validate(UploadedFile $file, array $options): void
    {
        $maxKb = $options['max_kb'] ?? (int) config('r2.validation.max_size_kb', 7168);
        if ($maxKb > 0 && ($file->getSize() / 1024) > $maxKb) {
            throw new R2StorageException("File is too large. Maximum allowed size is {$maxKb} KB.");
        }

        $allowed = $options['mimes']
            ?? array_merge(
                (array) config('r2.validation.image_mimes', []),
                (array) config('r2.validation.document_mimes', [])
            );

        if (! empty($allowed) && ! in_array($file->getMimeType(), $allowed, true)) {
            throw new R2StorageException('Unsupported file type: ' . $file->getMimeType());
        }
    }

    protected function resolveFolder(string $folderKey, ?string $extraPrefix = null): string
    {
        $folders = (array) config('r2.folders', []);
        $base = $folders[$folderKey] ?? $folderKey;
        $base = trim($base, '/');

        if ($extraPrefix) {
            $base .= '/' . trim($extraPrefix, '/');
        }

        return $base;
    }

    protected function generateFilename(UploadedFile $file): string
    {
        $ext = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'bin');
        return Str::ulid()->toBase32() . '_' . Str::random(6) . '.' . preg_replace('/[^a-z0-9]/', '', $ext);
    }

    /**
     * Convert a full URL back to an object key when possible, otherwise return as-is.
     */
    protected function normalizeToKey(string $keyOrUrl): string
    {
        if (! Str::startsWith($keyOrUrl, ['http://', 'https://'])) {
            return $keyOrUrl;
        }

        $publicBase = rtrim((string) config('r2.public_url'), '/');
        if ($publicBase !== '' && Str::startsWith($keyOrUrl, $publicBase)) {
            return ltrim(Str::after($keyOrUrl, $publicBase), '/');
        }

        // Fallback: strip scheme+host so the remaining path acts as a key.
        $parsed = parse_url($keyOrUrl);
        return isset($parsed['path']) ? ltrim($parsed['path'], '/') : $keyOrUrl;
    }
}
