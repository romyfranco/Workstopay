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
            ->assertSee('/fotos/0')
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

    public function test_electric_avenue_has_independent_filters_and_requires_access(): void
    {
        $this->get('/electric-avenue')->assertRedirect('/');
        $password = FrontendPassword::query()->create([
            'name' => 'EV Cliente',
            'password' => 'ev-segura',
            'is_active' => true,
            'can_view_electric_avenue' => true,
        ]);

        $cases = [
            ['EV001', 'Tesla EV AVENUE Model 3', VehicleWorkStatus::Completed, false, false],
            ['EV002', 'EV AVENUE Model Y', VehicleWorkStatus::Completed, false, true],
            ['EV003', 'Tesla EV AVENUE', VehicleWorkStatus::Completed, true, false],
            ['EV004', 'Tesla EV AVENUE', VehicleWorkStatus::InProgress, false, false],
            ['EV005', 'Tesla EV AVENUE', VehicleWorkStatus::Waiting, false, false],
            ['OTHER01', 'Tesla Model S', VehicleWorkStatus::Completed, false, false],
        ];

        foreach ($cases as [$plate, $vehicle, $status, $paid, $paymentSent]) {
            VehicleWork::query()->create([
                'plate' => $plate,
                'vehicle' => $vehicle,
                'job_description' => 'Trabajo de prueba',
                'job_photos' => ['vehicle-works/test.jpg'],
                'status' => $status,
                'paid' => $paid,
                'payment_sent' => $paymentSent,
            ]);
        }

        $this->withSession(['frontend_access_granted' => true, 'frontend_access_name' => 'Cliente', 'frontend_password_id' => $password->id])
            ->get('/electric-avenue')
            ->assertOk()
            ->assertSee('EV001')->assertSee('EV002')
            ->assertDontSee('EV003')->assertDontSee('EV004')->assertDontSee('EV005')
            ->assertDontSee('OTHER01')
            ->assertSee('/fotos/0')
            ->assertSee('Pago enviado');

        $this->get('/vehiculos')->assertOk()
            ->assertSee('EV001')->assertSee('OTHER01')->assertDontSee('EV002');

        $work = VehicleWork::query()->where('plate', 'EV001')->firstOrFail();
        $this->post(route('frontend.vehicle-works.payment-sent', $work), [
            'list' => 'electric-avenue',
            'comment' => 'Transferencia enviada',
        ])->assertRedirect('/electric-avenue');

        $this->get('/electric-avenue')->assertOk()->assertSee('EV001')->assertSee('Transferencia enviada');
        $this->get('/vehiculos')->assertOk()->assertDontSee('EV001')->assertSee('OTHER01');
        $this->assertDatabaseHas('vehicle_works', ['id' => $work->id, 'paid' => false, 'payment_sent' => true]);

        $work->update(['paid' => true]);
        $this->get('/electric-avenue')->assertOk()->assertDontSee('EV001');
    }

    public function test_electric_avenue_permission_is_checked_on_each_request(): void
    {
        $password = FrontendPassword::query()->create([
            'name' => 'Acceso configurable',
            'password' => 'clave-prueba',
            'is_active' => true,
        ]);

        $this->post('/acceso', ['password' => 'clave-prueba'])
            ->assertRedirect('/vehiculos')
            ->assertSessionHas('frontend_password_id', $password->id);
        $this->get('/vehiculos')->assertOk()->assertDontSee('ELECTRIC AVENUE');
        $this->get('/electric-avenue')->assertForbidden();

        $password->update(['can_view_electric_avenue' => true]);
        $this->get('/vehiculos')->assertOk()->assertSee('ELECTRIC AVENUE');
        $this->get('/electric-avenue')->assertOk();

        $password->update(['can_view_electric_avenue' => false]);
        $this->get('/electric-avenue')->assertForbidden();
        $this->get('/vehiculos')->assertOk()->assertDontSee('ELECTRIC AVENUE');

        $password->update(['can_view_electric_avenue' => true, 'is_active' => false]);
        $this->get('/electric-avenue')->assertForbidden();

        $this->post('/salir')->assertSessionMissing('frontend_password_id');
        $this->get('/electric-avenue')->assertRedirect('/');
    }

    public function test_both_lists_paginate_without_losing_or_duplicating_works(): void
    {
        $password = FrontendPassword::query()->create([
            'name' => 'EV', 'password' => 'prueba', 'is_active' => true, 'can_view_electric_avenue' => true,
        ]);
        for ($i = 1; $i <= 16; $i++) {
            VehicleWork::query()->create([
                'plate' => 'P'.$i, 'vehicle' => 'EV AVENUE', 'job_description' => 'Trabajo',
                'status' => VehicleWorkStatus::Completed, 'paid' => false, 'payment_sent' => false,
            ]);
        }
        $this->withSession(['frontend_access_granted' => true, 'frontend_password_id' => $password->id]);
        foreach (['/vehiculos', '/electric-avenue'] as $url) {
            $first = $this->get($url)->assertOk()->assertSee('Siguiente')->viewData('vehicleWorks');
            $second = $this->get($url.'?page=2')->assertOk()->assertSee('Anterior')->viewData('vehicleWorks');
            $this->assertCount(15, $first);
            $this->assertCount(1, $second);
            $this->assertSame(16, $first->total());
            $this->assertEmpty(array_intersect($first->pluck('id')->all(), $second->pluck('id')->all()));
        }
    }
}
