<?php

namespace App\Policies;

use App\Models\FrontendPassword;
use App\Models\User;

class FrontendPasswordPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, FrontendPassword $frontendPassword): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, FrontendPassword $frontendPassword): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, FrontendPassword $frontendPassword): bool
    {
        return $this->viewAny($user);
    }

    public function restore(User $user, FrontendPassword $frontendPassword): bool
    {
        return $this->viewAny($user);
    }

    public function forceDelete(User $user, FrontendPassword $frontendPassword): bool
    {
        return $this->viewAny($user);
    }
}
