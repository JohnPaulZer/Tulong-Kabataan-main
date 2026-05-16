<?php

namespace App\Services\Uploads;

use App\Models\UploadSession;
use App\Services\Storage\R2StorageException;
use App\Services\Storage\R2StorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class ChunkUploadService
{
    public function __construct(
        private readonly FileValidationService $validator,
        private readonly FileMergeService $merger,
        private readonly ImageOptimizationService $imageOptimizer,
        private readonly R2StorageService $storage,
    ) {}

    public function init(array $data, string $userId): UploadSession
    {
        if (! (bool) config('chunk_upload.enabled', true)) {
            throw new InvalidArgumentException('Chunk uploads are currently disabled.');
        }

        $validated = $this->validator->validateInit($data);
        $this->guardActiveUploadLimit($userId);

        $session = UploadSession::create([
            'upload_id' => (string) Str::uuid(),
            'user_id' => $userId,
            'module' => $validated['module'],
            'original_file_name' => basename((string) ($data['fileName'] ?? 'upload')),
            'file_size' => $validated['file_size'],
            'mime_type' => $validated['mime'],
            'extension' => $validated['extension'],
            'total_chunks' => $validated['total_chunks'],
            'uploaded_chunks' => [],
            'status' => UploadSession::STATUS_INITIALIZED,
        ]);

        $this->merger->ensureSessionDirectory($session->upload_id);

        return $session;
    }

    public function storeChunk(UploadSession $session, UploadedFile $chunk, int $chunkIndex): UploadSession
    {
        $this->ensureMutable($session);
        $this->validator->validateChunk($chunk, $chunkIndex, (int) $session->total_chunks);

        $uploadedChunks = $session->uploadedChunkIndexes();
        if (in_array($chunkIndex, $uploadedChunks, true)) {
            return $session;
        }

        $target = $this->merger->chunkPath($session->upload_id, $chunkIndex);
        $this->merger->ensureSessionDirectory($session->upload_id);

        if (! $chunk->move(dirname($target), basename($target))) {
            throw new RuntimeException('Unable to save the upload chunk.');
        }

        $uploadedChunks[] = $chunkIndex;
        sort($uploadedChunks);

        $session->fill([
            'uploaded_chunks' => $uploadedChunks,
            'status' => UploadSession::STATUS_UPLOADING,
        ])->save();

        return $session;
    }

    public function complete(UploadSession $session): UploadSession
    {
        $this->ensureMutable($session);

        if (count($session->uploadedChunkIndexes()) !== (int) $session->total_chunks) {
            throw new InvalidArgumentException('Some upload chunks are still missing.');
        }

        $session->update(['status' => UploadSession::STATUS_PROCESSING, 'error_message' => null]);
        $mergedPath = null;

        try {
            $mergedPath = $this->merger->merge($session);
            $metadata = $this->validator->validateFinalFile(
                $mergedPath,
                (string) $session->module,
                (string) $session->original_file_name,
                (int) $session->file_size
            );

            $moduleConfig = $this->validator->moduleConfig((string) $session->module);
            $uploadedFile = $this->imageOptimizer->makeUploadedFile(
                $mergedPath,
                (string) $session->original_file_name,
                $metadata['mime']
            );

            $finalPath = $this->storage->upload($uploadedFile, (string) $moduleConfig['folder'], [
                'max_kb' => $moduleConfig['max_kb'] ?? null,
                'mimes' => $moduleConfig['mimes'] ?? [],
                'visibility' => $moduleConfig['visibility'] ?? 'public',
                'prefix' => $moduleConfig['prefix'] ?? null,
                'convert_to_webp' => $moduleConfig['convert_to_webp'] ?? true,
            ]);

            $session->fill([
                'status' => UploadSession::STATUS_COMPLETED,
                'final_file_url' => $finalPath,
                'completed_at' => now(),
                'error_message' => null,
            ])->save();

            $this->merger->deleteSessionDirectory($session->upload_id);

            return $session;
        } catch (R2StorageException|InvalidArgumentException $e) {
            $session->fill([
                'status' => UploadSession::STATUS_FAILED,
                'error_message' => $e->getMessage(),
            ])->save();

            Log::warning('[ChunkUpload] Completion failed', [
                'upload_id' => $session->upload_id,
                'user_id' => $session->user_id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        } catch (\Throwable $e) {
            $session->fill([
                'status' => UploadSession::STATUS_FAILED,
                'error_message' => 'The upload could not be processed.',
            ])->save();

            Log::error('[ChunkUpload] Unexpected completion failure', [
                'upload_id' => $session->upload_id,
                'user_id' => $session->user_id,
                'error' => $e::class,
            ]);

            throw new RuntimeException('The upload could not be processed.', 0, $e);
        } finally {
            if ($mergedPath && is_file($mergedPath)) {
                @unlink($mergedPath);
            }
        }
    }

    public function cancel(UploadSession $session): void
    {
        $session->update(['status' => UploadSession::STATUS_CANCELLED]);
        $this->merger->deleteSessionDirectory($session->upload_id);
    }

    public function cleanupExpired(): int
    {
        $cutoff = now()->subMinutes(max(1, (int) config('chunk_upload.cleanup_minutes', 60)));
        $sessions = UploadSession::whereIn('status', [
            UploadSession::STATUS_INITIALIZED,
            UploadSession::STATUS_UPLOADING,
            UploadSession::STATUS_PROCESSING,
            UploadSession::STATUS_FAILED,
        ])
            ->where('updated_at', '<', $cutoff)
            ->get();

        foreach ($sessions as $session) {
            $session->update(['status' => UploadSession::STATUS_EXPIRED]);
            $this->merger->deleteSessionDirectory((string) $session->upload_id);
        }

        return $sessions->count();
    }

    public function completedPathForUser(string $path, string $module, string $userId): ?string
    {
        if ($path === '') {
            return null;
        }

        $session = UploadSession::where('user_id', $userId)
            ->where('module', $module)
            ->where('status', UploadSession::STATUS_COMPLETED)
            ->where('final_file_url', $path)
            ->first();

        return $session ? (string) $session->final_file_url : null;
    }

    public function latestCompletedPathForUser(string $module, string $userId): ?string
    {
        $session = UploadSession::where('user_id', $userId)
            ->where('module', $module)
            ->where('status', UploadSession::STATUS_COMPLETED)
            ->whereNotNull('final_file_url')
            ->latest('updated_at')
            ->first();

        return $session ? (string) $session->final_file_url : null;
    }

    private function ensureMutable(UploadSession $session): void
    {
        if (! in_array($session->status, [
            UploadSession::STATUS_INITIALIZED,
            UploadSession::STATUS_UPLOADING,
            UploadSession::STATUS_FAILED,
        ], true)) {
            throw new InvalidArgumentException('This upload session cannot be changed.');
        }
    }

    private function guardActiveUploadLimit(string $userId): void
    {
        $this->cleanupExpired();

        $maxActive = max(1, (int) config('chunk_upload.max_active_uploads_per_user', 5));
        $active = UploadSession::where('user_id', $userId)
            ->whereIn('status', [
                UploadSession::STATUS_INITIALIZED,
                UploadSession::STATUS_UPLOADING,
                UploadSession::STATUS_PROCESSING,
            ])
            ->count();

        if ($active >= $maxActive) {
            throw new InvalidArgumentException('You have too many active uploads. Please finish or cancel one first.');
        }
    }
}
