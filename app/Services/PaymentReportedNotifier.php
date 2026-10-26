<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Filament\Resources\VehicleWorks\VehicleWorkResource;
use App\Models\User;
use App\Models\VehicleWork;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class PaymentReportedNotifier
{
    public function send(VehicleWork $work, string $accessName): void
    {
        $notification = Notification::make()
            ->title('Pago reportado: '.e(strtoupper($work->plate)))
            ->success()
            ->body('Vehiculo: '.e($work->vehicle).'<br>Reportado por: '.e($accessName)
                .'<br>Comentario: '.nl2br(e($work->comment ?? 'Sin comentario')))
            ->actions([
                Action::make('viewWork')
                    ->label('Ver trabajo')
                    ->url(VehicleWorkResource::getUrl('edit', ['record' => $work], panel: 'admin'))
                    ->button()
                    ->markAsRead(),
            ])
            ->toDatabase();

        foreach (User::query()->where('role', UserRole::Admin->value)->get() as $admin) {
            // Store immediately: shared hosting does not require a queue worker.
            $admin->notifyNow($notification, ['database']);
        }
    }
}
