<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class UploadSession extends Model
{
    use HasFactory;

    public const STATUS_INITIALIZED = 'initialized';
    public const STATUS_UPLOADING = 'uploading';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_EXPIRED = 'expired';

    protected $connection = 'mongodb';
    protected $collection = 'upload_sessions';

    protected $fillable = [
        'upload_id',
        'user_id',
        'module',
        'original_file_name',
        'file_size',
        'mime_type',
        'extension',
        'total_chunks',
        'uploaded_chunks',
        'status',
        'final_file_url',
        'error_message',
        'created_at',
        'updated_at',
        'completed_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'total_chunks' => 'integer',
        'uploaded_chunks' => 'array',
        'completed_at' => 'datetime',
    ];

    public function uploadedChunkIndexes(): array
    {
        return array_values(array_unique(array_map('intval', $this->uploaded_chunks ?? [])));
    }
}
