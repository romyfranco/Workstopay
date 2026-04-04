<?php

namespace App\Filament\Resources\FrontendPasswords\Pages;

use App\Filament\Resources\FrontendPasswords\FrontendPasswordResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFrontendPassword extends CreateRecord
{
    protected static string $resource = FrontendPasswordResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
