<?php

namespace App\Filament\Resources\VehicleWorks\Pages;

use App\Filament\Resources\VehicleWorks\VehicleWorkResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVehicleWork extends CreateRecord
{
    protected static string $resource = VehicleWorkResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
