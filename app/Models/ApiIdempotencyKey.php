<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiIdempotencyKey extends Model
{
    protected $fillable = [
        'user_id',
        'key',
        'route',
        'request_hash',
        'response_status',
        'response_body',
        'resource_type',
        'resource_id',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'response_body' => 'array',
            'expires_at' => 'datetime',
        ];
    }
}
