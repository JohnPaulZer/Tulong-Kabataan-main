<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class AdminAccount extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'admin_accounts';

    protected $fillable = [
        'username',
        'email',
        'password',
        'reset_token',
        'reset_token_expiry',
    ];

    protected $hidden = [
        'password',
        'reset_token',
    ];

    protected $casts = [
        'reset_token_expiry' => 'datetime',
    ];

    public function getAdminIdAttribute()
    {
        return $this->attributes['_id'] ?? $this->getKey();
    }
}
