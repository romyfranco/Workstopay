<?php

namespace App\Filament\Resources\VehicleWorks\Pages;

use App\Filament\Resources\VehicleWorks\VehicleWorkResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVehicleWork extends EditRecord
{
    protected static string $resource = VehicleWorkResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
