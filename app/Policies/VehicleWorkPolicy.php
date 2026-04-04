<?php

namespace App\Policies;

use App\Enums\VehicleWorkStatus;
use App\Models\User;
use App\Models\VehicleWork;

class VehicleWorkPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isTechnician();
    }

    public function view(User $user, VehicleWork $vehicleWork): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return ! $vehicleWork->paid && $vehicleWork->status === VehicleWorkStatus::InProgress;
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, VehicleWork $vehicleWork): bool
    {
        return $this->view($user, $vehicleWork);
    }

    public function delete(User $user, VehicleWork $vehicleWork): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, VehicleWork $vehicleWork): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, VehicleWork $vehicleWork): bool
    {
        return $user->isAdmin();
    }
}
