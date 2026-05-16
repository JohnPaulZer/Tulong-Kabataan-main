<?php

namespace App\Services\Storage;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

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

        $preparedFile = $this->prepareFileForUpload($file, $options);
        $folder   = $this->resolveFolder($folderKey, $options['prefix'] ?? null);
        $filename = $this->generateFilename($file, $preparedFile['extension']);
        $key      = trim($folder, '/') . '/' . $filename;
        $stream   = null;

        try {
            $disk    = $this->disk();
            $stream  = fopen($preparedFile['path'], 'r');
            if ($stream === false) {
                throw new \RuntimeException('Unable to read prepared upload file.');
            }

            $success = $disk->put($key, $stream, [
                'visibility'  => $options['visibility'] ?? 'public',
                'ContentType' => $preparedFile['mime'],
            ]);
        } catch (\Throwable $e) {
            Log::error('[R2] Upload failed', [
                'folder'  => $folder,
                'key'     => $key,
                'error' => $e::class,
            ]);
            throw new R2StorageException('Failed to upload file to storage. Please try again.', 0, $e);
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }

            if ($preparedFile['temporary']) {
                @unlink($preparedFile['path']);
            }
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
                'error' => $e::class,
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

        // External URLs pass through; managed R2 URLs are rebuilt from their key.
        if (Str::startsWith($key, ['http://', 'https://'])) {
            $managedKey = $this->managedUrlToKey($key);
            if ($managedKey === null) {
                return $key;
            }

            $key = $managedKey;
        }

        $publicBase = $this->publicBaseUrl();
        if ($publicBase !== '') {
            return $publicBase . '/' . ltrim($key, '/');
        }

        try {
            return $this->disk()->url($key);
        } catch (\Throwable $e) {
            Log::warning('[R2] URL generation failed', ['key' => $key, 'error' => $e::class]);
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
            Log::warning('[R2] Read failed', ['key' => $key, 'error' => $e::class]);
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

    protected function prepareFileForUpload(UploadedFile $file, array $options): array
    {
        if (! $this->shouldConvertImageToWebp($file, $options)) {
            $path = $file->getRealPath();
            if (! $path) {
                throw new R2StorageException('Uploaded file could not be read.');
            }

            return [
                'path'      => $path,
                'mime'      => $file->getMimeType(),
                'extension' => strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'bin'),
                'temporary' => false,
            ];
        }

        return [
            'path'      => $this->convertImageToWebp($file, $options),
            'mime'      => 'image/webp',
            'extension' => 'webp',
            'temporary' => true,
        ];
    }

    protected function shouldConvertImageToWebp(UploadedFile $file, array $options): bool
    {
        if (($options['convert_to_webp'] ?? true) === false) {
            return false;
        }

        $enabled = filter_var(config('r2.webp.enabled', true), FILTER_VALIDATE_BOOLEAN);
        if (! $enabled) {
            return false;
        }

        $mime = $file->getMimeType();

        return is_string($mime) && Str::startsWith($mime, 'image/');
    }

    protected function convertImageToWebp(UploadedFile $file, array $options): string
    {
        $inputPath = $file->getRealPath();
        if (! $inputPath || ! is_file($inputPath)) {
            throw new R2StorageException('Uploaded image could not be read for WebP conversion.');
        }

        $script = (string) config('r2.webp.script');
        if ($script === '' || ! is_file($script)) {
            throw new R2StorageException('WebP converter script is missing.');
        }

        $outputPath = tempnam(sys_get_temp_dir(), 'tk_webp_');
        if ($outputPath === false) {
            throw new R2StorageException('Unable to create a temporary WebP file.');
        }

        $process = new Process([
            (string) config('r2.webp.node_binary', 'node'),
            $script,
            $inputPath,
            $outputPath,
            json_encode($this->webpConversionOptions($options), JSON_THROW_ON_ERROR),
        ]);
        $process->setTimeout((int) config('r2.webp.timeout', 60));
        $process->run();

        if (! $process->isSuccessful()) {
            @unlink($outputPath);
            Log::error('[R2] WebP conversion failed', [
                'file' => $file->getClientOriginalName(),
                'mime' => $file->getMimeType(),
                'error' => trim($process->getErrorOutput() ?: $process->getOutput()),
            ]);
            throw new R2StorageException('Failed to convert image to WebP. Please try another image.');
        }

        $convertedBytes = filesize($outputPath);
        if ($convertedBytes === false || $convertedBytes <= 0) {
            @unlink($outputPath);
            throw new R2StorageException('WebP conversion produced an empty file.');
        }

        $maxKb = $options['max_kb'] ?? (int) config('r2.validation.max_size_kb', 7168);
        if ($maxKb > 0 && $convertedBytes > ($maxKb * 1024)) {
            @unlink($outputPath);
            throw new R2StorageException("Converted WebP file is too large. Maximum allowed size is {$maxKb} KB.");
        }

        return $outputPath;
    }

    protected function webpConversionOptions(array $options): array
    {
        $conversionOptions = [
            'maxWidth' => max(1, (int) config('r2.webp.max_width', 2048)),
            'maxHeight' => max(1, (int) config('r2.webp.max_height', 2048)),
            'maxQuality' => (float) config('r2.webp.max_quality', 0.82),
            'minQuality' => (float) config('r2.webp.min_quality', 0.45),
        ];

        $maxKb = $options['max_kb'] ?? (int) config('r2.validation.max_size_kb', 7168);
        if ($maxKb > 0) {
            $conversionOptions['targetBytes'] = $maxKb * 1024;
        }

        return $conversionOptions;
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

    protected function generateFilename(UploadedFile $file, ?string $extension = null): string
    {
        $ext = strtolower($extension ?: $file->getClientOriginalExtension() ?: $file->extension() ?: 'bin');
        return Str::ulid()->toBase32() . '_' . Str::random(6) . '.' . preg_replace('/[^a-z0-9]/', '', $ext);
    }

    /**
     * Convert a full URL back to an object key when possible, otherwise return as-is.
     */
    protected function normalizeToKey(string $keyOrUrl): string
    {
        if (! Str::startsWith($keyOrUrl, ['http://', 'https://'])) {
            return $this->stripLeadingBucket($keyOrUrl);
        }

        $managedKey = $this->managedUrlToKey($keyOrUrl);
        if ($managedKey !== null) {
            return $managedKey;
        }

        // Fallback: strip scheme+host so the remaining path acts as a key.
        $parsed = parse_url($keyOrUrl);
        return isset($parsed['path']) ? $this->stripLeadingBucket(ltrim($parsed['path'], '/')) : $keyOrUrl;
    }

    protected function managedUrlToKey(string $url): ?string
    {
        foreach (array_filter([$this->rawPublicBaseUrl(), $this->publicBaseUrl()]) as $publicBase) {
            if (Str::startsWith($url, $publicBase)) {
                return $this->stripLeadingBucket(ltrim(Str::after($url, $publicBase), '/'));
            }
        }

        return null;
    }

    protected function stripLeadingBucket(string $key): string
    {
        $bucket = trim((string) config('r2.bucket'), '/');
        $key = ltrim($key, '/');

        if ($bucket !== '' && Str::startsWith($key, $bucket . '/')) {
            return Str::after($key, $bucket . '/');
        }

        return $key;
    }

    protected function publicBaseUrl(): string
    {
        $publicBase = $this->rawPublicBaseUrl();
        if ($publicBase === '') {
            return '';
        }

        $bucket = trim((string) config('r2.bucket'), '/');
        if ($bucket === '') {
            return $publicBase;
        }

        $path = trim((string) parse_url($publicBase, PHP_URL_PATH), '/');
        if ($path === $bucket) {
            $scheme = parse_url($publicBase, PHP_URL_SCHEME);
            $host = parse_url($publicBase, PHP_URL_HOST);
            $port = parse_url($publicBase, PHP_URL_PORT);

            if ($scheme && $host) {
                return $scheme . '://' . $host . ($port ? ':' . $port : '');
            }
        }

        return $publicBase;
    }

    protected function rawPublicBaseUrl(): string
    {
        return rtrim((string) config('r2.public_url'), '/');
    }
}
