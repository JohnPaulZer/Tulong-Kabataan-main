<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IdentityStatus extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'identity_statuses';

    protected $fillable = [
        'user_id',
        'status',
    ];

    public function getStatusIdAttribute()
    {
        return (string) ($this->attributes['_id'] ?? $this->getKey());
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', '_id');
    }
}
