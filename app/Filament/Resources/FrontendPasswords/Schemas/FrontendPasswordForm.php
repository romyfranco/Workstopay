<?php

namespace App\Filament\Resources\FrontendPasswords\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FrontendPasswordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Acceso del frontend')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('password')
                            ->label('Contrasena')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->same('password_confirmation')
                            ->helperText('La contrasena se guarda en hash y no se puede volver a ver luego.')
                            ->maxLength(255),
                        TextInput::make('password_confirmation')
                            ->label('Confirmar contrasena')
                            ->password()
                            ->revealable()
                            ->dehydrated(false)
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->maxLength(255),
                        Toggle::make('is_active')
                            ->label('Activa')
                            ->default(true),
                        Toggle::make('can_view_electric_avenue')
                            ->label('Ver ELECTRIC AVENUE')
                            ->helperText('Permite acceder a la lista ELECTRIC AVENUE. La lista general sigue disponible.')
                            ->default(false),
                    ])
                    ->columns(2),
            ]);
    }
}
