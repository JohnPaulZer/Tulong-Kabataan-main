<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminAccount extends Model
{
    protected $table = 'admin_accounts';
    protected $primaryKey = 'admin_id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'admin_id',
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
}
