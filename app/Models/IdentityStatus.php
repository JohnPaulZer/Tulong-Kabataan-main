<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class IdentityStatus extends Model
{
    use HasFactory;

    protected $table = 'identity_statuses';
    protected $primaryKey = 'status_id';   // custom PK
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'status',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'user_id'
        );
    }

}
