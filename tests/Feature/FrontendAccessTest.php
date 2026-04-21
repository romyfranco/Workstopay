<?php

namespace Tests\Feature;

use App\Enums\VehicleWorkStatus;
use App\Models\AccessLog;
use App\Models\FrontendPassword;
use App\Models\VehicleWork;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_frontend_vehicle_list_requires_a_valid_password(): void
    {
        FrontendPassword::query()->create([
            'name' => 'Cliente',
            'password' => 'consulta-segura',
            'is_active' => true,
        ]);

        VehicleWork::query()->create([
            'plate' => 'AAA111',
            'vehicle' => 'Renault Logan',
            'job_description' => 'Cambio de bateria.',
            'job_photos' => ['vehicle-works/renault-logan.jpg'],
            'status' => VehicleWorkStatus::Completed,
            'paid' => false,
            'payment_sent' => false,
        ]);

        VehicleWork::query()->create([
            'plate' => 'BBB222',
            'vehicle' => 'Mazda CX5',
            'job_description' => 'Trabajo ya cancelado.',
            'job_photos' => ['vehicle-works/test.jpg'],
            'status' => VehicleWorkStatus::Completed,
            'paid' => true,
            'payment_sent' => false,
        ]);

        VehicleWork::query()->create([
            'plate' => 'CCC333',
            'vehicle' => 'Kia Sportage',
            'job_description' => 'Trabajo aun en proceso.',
            'job_photos' => [],
            'status' => VehicleWorkStatus::InProgress,
            'paid' => false,
            'payment_sent' => false,
        ]);

        VehicleWork::query()->create([
            'plate' => 'DDD444',
            'vehicle' => 'Hyundai Tucson',
            'job_description' => 'Pago ya reportado por el cliente.',
            'job_photos' => [],
            'status' => VehicleWorkStatus::Completed,
            'paid' => false,
            'payment_sent' => true,
        ]);

        $this->get('/vehiculos')
            ->assertRedirect('/');

        $this->post('/acceso', [
            'password' => 'incorrecta',
        ])
            ->assertSessionHasErrors('password');

        $this->post('/acceso', [
            'password' => 'consulta-segura',
        ])
            ->assertRedirect('/vehiculos');

        $this->get('/vehiculos')
            ->assertOk()
            ->assertSee('Renault Logan')
            ->assertSee('Cambio de bateria.')
            ->assertSee('Vehiculos terminados')
            ->assertSee('Descripcion del trabajo')
            ->assertSee('Fotos')
            ->assertSee('vehicle-works/renault-logan.jpg')
            ->assertSee('Enviar pago')
            ->assertDontSee('Mazda CX5')
            ->assertDontSee('Trabajo ya cancelado.')
            ->assertDontSee('Kia Sportage')
            ->assertDontSee('Trabajo aun en proceso.')
            ->assertDontSee('Hyundai Tucson')
            ->assertDontSee('Pago ya reportado por el cliente.')
            ->assertDontSee('Creado')
            ->assertDontSee('Pagado');

        $this->assertDatabaseHas('access_logs', [
            'channel' => 'frontend',
            'event' => 'frontend_password_used',
            'display_name' => 'Cliente',
        ]);
    }

    public function test_frontend_can_mark_payment_as_sent(): void
    {
        FrontendPassword::query()->create([
            'name' => 'Cliente',
            'password' => 'consulta-segura',
            'is_active' => true,
        ]);

        $vehicleWork = VehicleWork::query()->create([
            'plate' => 'EEE555',
            'vehicle' => 'Nissan Versa',
            'job_description' => 'Listo para confirmar envio de pago.',
            'job_photos' => [],
            'status' => VehicleWorkStatus::Completed,
            'paid' => false,
            'payment_sent' => false,
        ]);

        $this->post('/acceso', [
            'password' => 'consulta-segura',
        ])->assertRedirect('/vehiculos');

        $this->post(route('frontend.vehicle-works.payment-sent', $vehicleWork), [
            'comment' => 'Transferencia programada para hoy.',
        ])
            ->assertRedirect('/vehiculos');

        $this->assertDatabaseHas('vehicle_works', [
            'id' => $vehicleWork->id,
            'comment' => 'Transferencia programada para hoy.',
            'payment_sent' => true,
        ]);

        $this->get('/vehiculos')
            ->assertOk()
            ->assertDontSee('Nissan Versa')
            ->assertDontSee('Listo para confirmar envio de pago.');
    }

    public function test_panel_user_login_is_recorded(): void
    {
        $user = \App\Models\User::query()->create([
            'name' => 'Admin de prueba',
            'email' => 'admin-prueba@example.com',
            'role' => \App\Enums\UserRole::Admin,
            'password' => 'secret12345',
        ]);

        event(new \Illuminate\Auth\Events\Login('web', $user, false));

        $this->assertDatabaseHas('access_logs', [
            'channel' => 'panel',
            'event' => 'user_login',
            'user_id' => $user->id,
            'user_role' => 'admin',
            'display_name' => 'Admin de prueba',
        ]);
    }
}
