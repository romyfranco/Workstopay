<?php

namespace App\Filament\Resources\FrontendPasswords\Pages;

use App\Filament\Resources\FrontendPasswords\FrontendPasswordResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFrontendPasswords extends ListRecords
{
    protected static string $resource = FrontendPasswordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
