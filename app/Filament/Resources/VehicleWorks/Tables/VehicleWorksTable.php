<?php

namespace App\Filament\Resources\VehicleWorks\Tables;

use App\Enums\VehicleWorkStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class VehicleWorksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('plate')
                    ->label('Placa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('vehicle')
                    ->label('Vehiculo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('job_description')
                    ->label('Descripcion')
                    ->limit(50)
                    ->wrap(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->sortable()
                    ->color(fn ($state): string => match ($state) {
                        VehicleWorkStatus::InProgress => 'success',
                        VehicleWorkStatus::Completed => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state): string => $state?->label() ?? '-')
                    ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
                ToggleColumn::make('paid')
                    ->label('Pagado')
                    ->sortable()
                    ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
                ]),
            ]);
    }
}
