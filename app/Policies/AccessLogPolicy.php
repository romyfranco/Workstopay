<?php

namespace App\Policies;

use App\Models\AccessLog;
use App\Models\User;

class AccessLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, AccessLog $accessLog): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, AccessLog $accessLog): bool
    {
        return false;
    }

    public function delete(User $user, AccessLog $accessLog): bool
    {
        return false;
    }

    public function restore(User $user, AccessLog $accessLog): bool
    {
        return false;
    }

    public function forceDelete(User $user, AccessLog $accessLog): bool
    {
        return false;
    }
}
