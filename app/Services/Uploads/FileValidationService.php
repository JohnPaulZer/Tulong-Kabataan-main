<?php

namespace App\Services\Uploads;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use InvalidArgumentException;

class FileValidationService
{
    private const EXECUTABLE_EXTENSIONS = [
        'bat', 'cmd', 'com', 'dll', 'exe', 'js', 'jar', 'msi', 'php', 'ps1', 'scr', 'sh', 'vbs',
    ];

    private const MIME_BY_EXTENSION = [
        'jpg' => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png' => ['image/png'],
        'webp' => ['image/webp'],
        'pdf' => ['application/pdf'],
    ];

    public function validateInit(array $data): array
    {
        $module = (string) ($data['module'] ?? '');
        $moduleConfig = $this->moduleConfig($module);
        $extension = $this->safeExtension((string) ($data['fileName'] ?? ''));
        $mime = strtolower((string) ($data['fileType'] ?? ''));
        $fileSize = (int) ($data['fileSize'] ?? 0);
        $totalChunks = (int) ($data['totalChunks'] ?? 0);

        if ($fileSize <= 0) {
            throw new InvalidArgumentException('The selected file is empty.');
        }

        $maxBytes = $this->maxBytes($moduleConfig);
        if ($fileSize > $maxBytes) {
            throw new InvalidArgumentException('The selected file is too large.');
        }

        if ($totalChunks <= 0 || $totalChunks > 10000) {
            throw new InvalidArgumentException('The upload chunk count is invalid.');
        }

        $this->validateExtensionAndMime($extension, $mime, $moduleConfig);

        return [
            'module' => $module,
            'extension' => $extension,
            'mime' => $mime,
            'file_size' => $fileSize,
            'total_chunks' => $totalChunks,
        ];
    }

    public function validateChunk(UploadedFile $chunk, int $chunkIndex, int $totalChunks): void
    {
        if (! $chunk->isValid() || $chunk->getSize() <= 0) {
            throw new InvalidArgumentException('The upload chunk is invalid.');
        }

        if ($chunkIndex < 0 || $chunkIndex >= $totalChunks) {
            throw new InvalidArgumentException('The upload chunk index is invalid.');
        }

        $maxChunkBytes = max(1, (int) config('chunk_upload.chunk_size_mb', 3)) * 1024 * 1024;
        if ($chunk->getSize() > ($maxChunkBytes + 1024 * 1024)) {
            throw new InvalidArgumentException('The upload chunk is too large.');
        }
    }

    public function validateFinalFile(string $path, string $module, string $originalName, int $expectedBytes): array
    {
        if (! is_file($path) || filesize($path) === false || filesize($path) <= 0) {
            throw new InvalidArgumentException('The uploaded file could not be read.');
        }

        $actualBytes = (int) filesize($path);
        if ($actualBytes !== $expectedBytes) {
            throw new InvalidArgumentException('The uploaded file size does not match.');
        }

        $moduleConfig = $this->moduleConfig($module);
        if ($actualBytes > $this->maxBytes($moduleConfig)) {
            throw new InvalidArgumentException('The uploaded file is too large.');
        }

        $extension = $this->safeExtension($originalName);
        $mime = $this->detectMime($path);
        $this->validateExtensionAndMime($extension, $mime, $moduleConfig);
        $this->validateMagicBytes($path, $extension, $mime);

        return [
            'extension' => $extension,
            'mime' => $mime,
            'bytes' => $actualBytes,
        ];
    }

    public function moduleConfig(string $module): array
    {
        $modules = (array) config('chunk_upload.modules', []);
        if (! isset($modules[$module])) {
            throw new InvalidArgumentException('This upload type is not supported.');
        }

        return $modules[$module];
    }

    public function safeExtension(string $fileName): string
    {
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $extension = preg_replace('/[^a-z0-9]/', '', $extension) ?: '';

        if ($extension === '' || in_array($extension, self::EXECUTABLE_EXTENSIONS, true)) {
            throw new InvalidArgumentException('This file type is not allowed.');
        }

        return $extension;
    }

    private function validateExtensionAndMime(string $extension, string $mime, array $moduleConfig): void
    {
        $allowedExtensions = array_map('strtolower', (array) config('chunk_upload.allowed_types', []));
        if (! in_array($extension, $allowedExtensions, true)) {
            throw new InvalidArgumentException('This file extension is not allowed.');
        }

        $allowedMimes = array_map('strtolower', (array) ($moduleConfig['mimes'] ?? []));
        if (! in_array($mime, $allowedMimes, true)) {
            throw new InvalidArgumentException('This file type is not allowed.');
        }

        if (isset(self::MIME_BY_EXTENSION[$extension]) && ! in_array($mime, self::MIME_BY_EXTENSION[$extension], true)) {
            throw new InvalidArgumentException('The file extension does not match its type.');
        }
    }

    private function validateMagicBytes(string $path, string $extension, string $mime): void
    {
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new InvalidArgumentException('The uploaded file could not be inspected.');
        }

        $bytes = fread($handle, 16) ?: '';
        fclose($handle);

        $hex = bin2hex($bytes);
        $valid = match ($extension) {
            'jpg', 'jpeg' => Str::startsWith($hex, 'ffd8ff'),
            'png' => Str::startsWith($hex, '89504e470d0a1a0a'),
            'webp' => Str::startsWith($bytes, 'RIFF') && substr($bytes, 8, 4) === 'WEBP',
            'pdf' => Str::startsWith($bytes, '%PDF-'),
            default => false,
        };

        if (! $valid) {
            throw new InvalidArgumentException('The uploaded file signature is invalid.');
        }

        if ($extension !== 'pdf' && ! Str::startsWith($mime, 'image/')) {
            throw new InvalidArgumentException('Only image files are allowed here.');
        }
    }

    private function detectMime(string $path): string
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $finfo ? finfo_file($finfo, $path) : null;
        if ($finfo) {
            finfo_close($finfo);
        }

        return strtolower((string) ($mime ?: 'application/octet-stream'));
    }

    private function maxBytes(array $moduleConfig): int
    {
        $moduleMaxKb = (int) ($moduleConfig['max_kb'] ?? 0);
        $globalMaxMb = max(1, (int) config('chunk_upload.max_file_size_mb', 25));
        $moduleMaxBytes = $moduleMaxKb > 0 ? $moduleMaxKb * 1024 : PHP_INT_MAX;

        return min($globalMaxMb * 1024 * 1024, $moduleMaxBytes);
    }
}
