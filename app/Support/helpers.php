<?php

use App\Services\Storage\R2StorageService;
use Illuminate\Support\Str;

if (! function_exists('file_url')) {
    /**
     * Resolve a stored file reference to a viewable URL.
     *
     * Handles three cases so blade templates do not need to care where a file
     * was originally stored:
     *
     *   1. Empty / null        -> returns the fallback (or null).
     *   2. Absolute URL        -> returned unchanged (Google avatars, etc.).
     *   3. R2 object key       -> resolved via R2StorageService::url().
     *   4. Legacy local path   -> resolved via asset('storage/...') so older
     *                             rows migrated before the R2 cutover still render.
     *
     * @param  string|array|null  $keyOrUrl
     * @param  string|null        $fallback
     */
    function file_url($keyOrUrl, ?string $fallback = null): ?string
    {
        if (is_array($keyOrUrl)) {
            $keyOrUrl = $keyOrUrl[0] ?? null;
        }

        if (empty($keyOrUrl)) {
            return $fallback;
        }

        if (Str::startsWith($keyOrUrl, ['http://', 'https://'])) {
            return $keyOrUrl;
        }

        /** @var R2StorageService $service */
        $service = app(R2StorageService::class);

        // Treat the key as an R2 object when an R2 public URL is configured and
        // the key starts with one of the configured module folder prefixes. This
        // preserves backward compatibility with legacy local storage paths.
        $r2Configured = ! empty(config('r2.public_url')) || ! empty(config('filesystems.disks.r2.endpoint'));
        $folderPrefixes = array_values((array) config('r2.folders', []));

        $matchesR2Folder = false;
        foreach ($folderPrefixes as $prefix) {
            if ($prefix !== '' && Str::startsWith($keyOrUrl, $prefix)) {
                $matchesR2Folder = true;
                break;
            }
        }

        if ($r2Configured && $matchesR2Folder) {
            return $service->url($keyOrUrl) ?: $fallback;
        }

        // Legacy local storage fallback.
        return asset('storage/' . ltrim($keyOrUrl, '/'));
    }
}
