<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\VehicleWorkStatus;
use App\Models\FrontendPassword;
use App\Models\User;
use App\Models\VehicleWork;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentReportedNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_payments_from_both_lists_notify_only_admins_once(): void
    {
        $admins = collect(['first', 'second'])->map(fn ($name) => User::factory()->create(['role' => UserRole::Admin, 'email' => $name.'@example.com']));
        $technician = User::factory()->create(['role' => UserRole::Technician]);
        $password = FrontendPassword::query()->create([
            'name' => 'Cliente EV', 'password' => 'prueba-segura', 'is_active' => true, 'can_view_electric_avenue' => true,
        ]);
        $this->post('/acceso', ['password' => 'prueba-segura'])->assertRedirect('/vehiculos');

        foreach (['general', 'electric-avenue'] as $list) {
            $work = VehicleWork::query()->create([
                'plate' => $list, 'vehicle' => 'Tesla EV AVENUE', 'job_description' => 'Trabajo',
                'status' => VehicleWorkStatus::Completed, 'paid' => false, 'payment_sent' => false,
            ]);
            $url = route('frontend.vehicle-works.payment-sent', $work);
            $payload = ['list' => $list, 'comment' => '<script>alert(1)</script> Transferencia enviada'];
            $this->post($url, $payload)->assertRedirect();
            $this->assertFalse($work->fresh()->paid);
            foreach ($admins as $admin) {
                $notification = $admin->notifications()->where('data->title', 'Pago reportado: '.strtoupper($work->plate))->first();
                $this->assertNotNull($notification);
                $this->assertNull($notification->read_at);
                $this->assertSame('filament', $notification->data['format']);
                $this->assertStringContainsString('Cliente EV', $notification->data['body']);
                $this->assertStringContainsString('Transferencia enviada', $notification->data['body']);
                $this->assertStringNotContainsString('<script>', $notification->data['body']);
                $this->assertStringContainsString('/admin/vehicle-works/'.$work->id.'/edit', $notification->data['actions'][0]['url']);
            }
            $this->post($url, $payload)->assertNotFound();
        }

        $this->assertSame(0, $technician->notifications()->count());
        foreach ($admins as $admin) {
            $this->assertSame(2, $admin->notifications()->count());
            $this->actingAs($admin);
            $this->assertTrue(Filament::getPanel('admin')->hasDatabaseNotifications());
        }
        $this->actingAs($technician);
        $this->assertFalse(Filament::getPanel('admin')->hasDatabaseNotifications());
    }
}
