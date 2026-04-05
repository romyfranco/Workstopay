<?php

namespace App\Models;

use App\Enums\VehicleWorkStatus;
use Illuminate\Database\Eloquent\Model;

class VehicleWork extends Model
{
    protected $fillable = [
        'plate',
        'vehicle',
        'job_description',
        'comment',
        'job_photos',
        'status',
        'paid',
        'payment_sent',
    ];

    protected function casts(): array
    {
        return [
            'job_photos' => 'array',
            'status' => VehicleWorkStatus::class,
            'paid' => 'boolean',
            'payment_sent' => 'boolean',
        ];
    }
}
