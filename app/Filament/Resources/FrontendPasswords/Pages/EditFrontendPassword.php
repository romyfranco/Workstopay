<?php

namespace App\Filament\Resources\FrontendPasswords\Pages;

use App\Filament\Resources\FrontendPasswords\FrontendPasswordResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFrontendPassword extends EditRecord
{
    protected static string $resource = FrontendPasswordResource::class;

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
