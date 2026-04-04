<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessLog extends Model
{
    protected $fillable = [
        'channel',
        'event',
        'user_id',
        'frontend_password_id',
        'user_role',
        'display_name',
        'ip_address',
        'user_agent',
        'context',
    ];

    protected function casts(): array
    {
        return [
            'context' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function frontendPassword(): BelongsTo
    {
        return $this->belongsTo(FrontendPassword::class);
    }
}
