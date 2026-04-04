<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class FrontendPassword extends Model
{
    protected $fillable = [
        'name',
        'password',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function matches(string $plainPassword): bool
    {
        return Hash::check($plainPassword, $this->password);
    }
}
