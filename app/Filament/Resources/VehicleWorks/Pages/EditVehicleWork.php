<?php

namespace App\Filament\Resources\VehicleWorks\Pages;

use App\Enums\VehicleWorkStatus;
use App\Filament\Resources\VehicleWorks\VehicleWorkResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVehicleWork extends EditRecord
{
    protected static string $resource = VehicleWorkResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (auth()->user()?->isTechnician() && ! in_array(($data['status'] ?? null), [
            VehicleWorkStatus::InProgress->value,
            VehicleWorkStatus::Waiting->value,
        ], true)) {
            $data['status'] = $this->record->status->value;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        if (! auth()->user()?->isAdmin()) {
            return [];
        }

        return [DeleteAction::make()];
    }
}
