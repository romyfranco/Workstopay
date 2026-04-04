<?php

namespace App\Filament\Resources\VehicleWorks\Pages;

use App\Filament\Resources\VehicleWorks\VehicleWorkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVehicleWorks extends ListRecords
{
    protected static string $resource = VehicleWorkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
