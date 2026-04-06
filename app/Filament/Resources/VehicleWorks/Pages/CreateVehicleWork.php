<?php

namespace App\Filament\Resources\VehicleWorks\Pages;

use App\Enums\VehicleWorkStatus;
use App\Filament\Resources\VehicleWorks\VehicleWorkResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVehicleWork extends CreateRecord
{
    protected static string $resource = VehicleWorkResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (auth()->user()?->isTechnician() && ! in_array(($data['status'] ?? null), [
            VehicleWorkStatus::InProgress->value,
            VehicleWorkStatus::Waiting->value,
        ], true)) {
            $data['status'] = VehicleWorkStatus::InProgress->value;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
