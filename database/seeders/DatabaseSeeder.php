<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\VehicleWorkStatus;
use App\Models\FrontendPassword;
use App\Models\User;
use App\Models\VehicleWork;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@taller.test'],
            [
                'name' => 'Administrador',
                'role' => UserRole::Admin,
                'password' => 'admin12345',
                'email_verified_at' => now(),
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'tecnico@taller.test'],
            [
                'name' => 'Tecnico',
                'role' => UserRole::Technician,
                'password' => 'tecnico12345',
                'email_verified_at' => now(),
            ],
        );

        FrontendPassword::query()->updateOrCreate(
            ['name' => 'Acceso principal'],
            [
                'password' => 'consulta12345',
                'is_active' => true,
            ],
        );

        VehicleWork::query()->updateOrCreate(
            ['plate' => 'ABC123', 'vehicle' => 'Mazda 3'],
            [
                'job_description' => 'Cambio de aceite, revision general y lavado del motor.',
                'job_photos' => [],
                'status' => VehicleWorkStatus::Completed,
                'paid' => false,
            ],
        );

        VehicleWork::query()->updateOrCreate(
            ['plate' => 'XYZ987', 'vehicle' => 'Toyota Hilux'],
            [
                'job_description' => 'Ajuste de frenos delanteros y cambio de pastillas.',
                'job_photos' => [],
                'status' => VehicleWorkStatus::InProgress,
                'paid' => true,
            ],
        );
    }
}
