<?php

namespace App\Http\Controllers;

use App\Models\UploadSession;
use App\Services\Uploads\ChunkUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChunkUploadController
{
    public function __construct(private readonly ChunkUploadService $uploads)
    {
    }

    public function init(Request $request): JsonResponse
    {
        $data = $request->validate([
            'fileName' => 'required|string|max:255',
            'fileSize' => 'required|integer|min:1',
            'fileType' => 'required|string|max:150',
            'totalChunks' => 'required|integer|min:1',
            'chunkSize' => 'nullable|integer|min:1',
            'module' => 'required|string|max:80',
        ]);

        try {
            $session = $this->uploads->init($data, (string) $request->user()->getAuthIdentifier());
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            Log::error('[ChunkUpload] Init failed', ['error' => $e::class]);
            return response()->json(['success' => false, 'message' => 'Upload could not be started.'], 500);
        }

        return response()->json([
            'success' => true,
            'uploadId' => $session->upload_id,
            'chunkSize' => max(1, (int) config('chunk_upload.chunk_size_mb', 3)) * 1024 * 1024,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'uploadId' => 'required|string',
            'chunkIndex' => 'required|integer|min:0',
            'totalChunks' => 'required|integer|min:1',
            'chunkSize' => 'required|integer|min:1',
            'fileName' => 'required|string|max:255',
            'fileSize' => 'required|integer|min:1',
            'fileType' => 'required|string|max:150',
            'module' => 'required|string|max:80',
            'checksum' => 'nullable|string|max:128',
            'chunk' => 'required|file',
        ]);

        $session = $this->findOwnedSession($request, $data['uploadId']);
        if (! $session) {
            return response()->json(['success' => false, 'message' => 'Upload session was not found.'], 404);
        }

        if ((string) $session->module !== (string) $data['module']) {
            return response()->json(['success' => false, 'message' => 'Upload session does not match this file.'], 422);
        }

        try {
            $session = $this->uploads->storeChunk($session, $request->file('chunk'), (int) $data['chunkIndex']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            Log::warning('[ChunkUpload] Chunk store failed', [
                'upload_id' => $data['uploadId'],
                'chunk_index' => $data['chunkIndex'],
                'error' => $e::class,
            ]);

            return response()->json(['success' => false, 'message' => 'Upload chunk could not be saved.'], 500);
        }

        return response()->json([
            'success' => true,
            'uploadId' => $session->upload_id,
            'chunkIndex' => (int) $data['chunkIndex'],
            'uploadedChunks' => count($session->uploadedChunkIndexes()),
            'totalChunks' => (int) $session->total_chunks,
        ]);
    }

    public function complete(Request $request): JsonResponse
    {
        $data = $request->validate([
            'uploadId' => 'required|string',
            'module' => 'required|string|max:80',
        ]);

        $session = $this->findOwnedSession($request, $data['uploadId']);
        if (! $session) {
            return response()->json(['success' => false, 'message' => 'Upload session was not found.'], 404);
        }

        if ((string) $session->module !== (string) $data['module']) {
            return response()->json(['success' => false, 'message' => 'Upload session does not match this file.'], 422);
        }

        if ($session->status === UploadSession::STATUS_COMPLETED) {
            return response()->json([
                'success' => true,
                'uploadId' => $session->upload_id,
                'path' => $session->final_file_url,
                'url' => file_url($session->final_file_url),
            ]);
        }

        try {
            $session = $this->uploads->complete($session);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Upload could not be processed. Please retry.'], 500);
        }

        return response()->json([
            'success' => true,
            'uploadId' => $session->upload_id,
            'path' => $session->final_file_url,
            'url' => file_url($session->final_file_url),
        ]);
    }

    public function cancel(Request $request, string $uploadId): JsonResponse
    {
        $session = $this->findOwnedSession($request, $uploadId);
        if (! $session) {
            return response()->json(['success' => true]);
        }

        $this->uploads->cancel($session);

        return response()->json(['success' => true]);
    }

    private function findOwnedSession(Request $request, string $uploadId): ?UploadSession
    {
        return UploadSession::where('upload_id', $uploadId)
            ->where('user_id', (string) $request->user()->getAuthIdentifier())
            ->first();
    }
}
