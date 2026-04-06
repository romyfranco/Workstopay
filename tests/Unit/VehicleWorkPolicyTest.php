<?php

namespace Tests\Unit;

use App\Enums\UserRole;
use App\Enums\VehicleWorkStatus;
use App\Models\User;
use App\Models\VehicleWork;
use App\Policies\VehicleWorkPolicy;
use Tests\TestCase;

class VehicleWorkPolicyTest extends TestCase
{
    public function test_technician_can_view_in_progress_and_waiting_unpaid_works(): void
    {
        $policy = new VehicleWorkPolicy();

        $technician = new User([
            'role' => UserRole::Technician,
        ]);

        $inProgressWork = new VehicleWork([
            'status' => VehicleWorkStatus::InProgress,
            'paid' => false,
        ]);

        $waitingWork = new VehicleWork([
            'status' => VehicleWorkStatus::Waiting,
            'paid' => false,
        ]);

        $completedWork = new VehicleWork([
            'status' => VehicleWorkStatus::Completed,
            'paid' => false,
        ]);

        $this->assertTrue($policy->view($technician, $inProgressWork));
        $this->assertTrue($policy->view($technician, $waitingWork));
        $this->assertFalse($policy->view($technician, $completedWork));
    }
}
