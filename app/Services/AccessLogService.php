<?php

namespace App\Services;

use App\Models\AccessLog;
use App\Models\FrontendPassword;
use App\Models\User;
use Illuminate\Http\Request;

class AccessLogService
{
    public function logUserLogin(User $user, ?Request $request = null): AccessLog
    {
        $request ??= request();

        return AccessLog::query()->create([
            'channel' => 'panel',
            'event' => 'user_login',
            'user_id' => $user->id,
            'frontend_password_id' => null,
            'user_role' => $user->role?->value,
            'display_name' => $user->name,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'context' => [
                'email' => $user->email,
            ],
        ]);
    }

    public function logFrontendPasswordUse(FrontendPassword $frontendPassword, ?Request $request = null): AccessLog
    {
        $request ??= request();

        return AccessLog::query()->create([
            'channel' => 'frontend',
            'event' => 'frontend_password_used',
            'user_id' => null,
            'frontend_password_id' => $frontendPassword->id,
            'user_role' => 'frontend_password',
            'display_name' => $frontendPassword->name,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'context' => [],
        ]);
    }
}
