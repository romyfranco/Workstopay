<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\VehicleWorkStatus;
use App\Models\FrontendPassword;
use App\Models\User;
use App\Models\VehicleWork;
use Illuminate\Database\Seeder;

class TestEnvironmentSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedUsers();
        $this->seedFrontendPasswords();
        $this->seedVehicleWorks();
    }

    private function seedUsers(): void
    {
        $users = [
            [
                'email' => 'admin@taller.test',
                'name' => 'Administrador Principal',
                'role' => UserRole::Admin,
                'password' => 'admin12345',
            ],
            [
                'email' => 'admin2@taller.test',
                'name' => 'Administrador Secundario',
                'role' => UserRole::Admin,
                'password' => 'admin22345',
            ],
            [
                'email' => 'tecnico@taller.test',
                'name' => 'Tecnico General',
                'role' => UserRole::Technician,
                'password' => 'tecnico12345',
            ],
            [
                'email' => 'tecnico2@taller.test',
                'name' => 'Tecnico Patio',
                'role' => UserRole::Technician,
                'password' => 'tecnico22345',
            ],
        ];

        foreach ($users as $user) {
            User::query()->updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'role' => $user['role'],
                    'password' => $user['password'],
                    'email_verified_at' => now(),
                ],
            );
        }
    }

    private function seedFrontendPasswords(): void
    {
        $passwords = [
            [
                'name' => 'Acceso principal',
                'password' => 'consulta12345',
                'is_active' => true,
            ],
            [
                'name' => 'Cliente VIP',
                'password' => 'clientevip123',
                'is_active' => true,
            ],
            [
                'name' => 'Acceso vencido',
                'password' => 'inactivo12345',
                'is_active' => false,
            ],
        ];

        foreach ($passwords as $password) {
            FrontendPassword::query()->updateOrCreate(
                ['name' => $password['name']],
                [
                    'password' => $password['password'],
                    'is_active' => $password['is_active'],
                ],
            );
        }
    }

    private function seedVehicleWorks(): void
    {
        $vehicleWorks = [
            [
                'plate' => 'ABC123',
                'vehicle' => 'Mazda 3',
                'job_description' => 'Cambio de aceite, revision general y lavado del motor.',
                'comment' => null,
                'job_photos' => [],
                'status' => VehicleWorkStatus::Completed,
                'paid' => false,
                'payment_sent' => false,
            ],
            [
                'plate' => 'XYZ987',
                'vehicle' => 'Toyota Hilux',
                'job_description' => 'Ajuste de frenos delanteros y cambio de pastillas.',
                'comment' => null,
                'job_photos' => [],
                'status' => VehicleWorkStatus::InProgress,
                'paid' => false,
                'payment_sent' => false,
            ],
            [
                'plate' => 'LMN456',
                'vehicle' => 'Chevrolet Onix',
                'job_description' => 'Cambio de bateria y prueba de carga del sistema.',
                'comment' => 'Cliente indica que realizara la transferencia en la tarde.',
                'job_photos' => [],
                'status' => VehicleWorkStatus::Completed,
                'paid' => false,
                'payment_sent' => true,
            ],
            [
                'plate' => 'QRS852',
                'vehicle' => 'Renault Duster',
                'job_description' => 'Latoneria menor y pulida de farola delantera.',
                'comment' => null,
                'job_photos' => [],
                'status' => VehicleWorkStatus::Completed,
                'paid' => true,
                'payment_sent' => false,
            ],
            [
                'plate' => 'TUV741',
                'vehicle' => 'Kia Picanto',
                'job_description' => 'Mantenimiento preventivo y cambio de filtro de aire.',
                'comment' => null,
                'job_photos' => [],
                'status' => VehicleWorkStatus::InProgress,
                'paid' => false,
                'payment_sent' => false,
            ],
        ];

        foreach ($vehicleWorks as $vehicleWork) {
            VehicleWork::query()->updateOrCreate(
                [
                    'plate' => $vehicleWork['plate'],
                    'vehicle' => $vehicleWork['vehicle'],
                ],
                [
                    'job_description' => $vehicleWork['job_description'],
                    'comment' => $vehicleWork['comment'],
                    'job_photos' => $vehicleWork['job_photos'],
                    'status' => $vehicleWork['status'],
                    'paid' => $vehicleWork['paid'],
                    'payment_sent' => $vehicleWork['payment_sent'],
                ],
            );
        }
    }
}
