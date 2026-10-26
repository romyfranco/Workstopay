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
use Illuminate\Database\Eloquent\Builder;

class VehicleWorksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort(fn (Builder $query): Builder => $query
                ->orderByRaw(
                    'case when status = ? then 0 when status = ? then 1 else 2 end',
                    [VehicleWorkStatus::InProgress->value, VehicleWorkStatus::Waiting->value],
                )
                ->latest())
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
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->wrap(),
                TextColumn::make('comment')
                    ->label('Comentario')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->wrap()
                    ->toggleable()
                    ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->sortable()
                    ->color(fn ($state): string => match ($state) {
                        VehicleWorkStatus::InProgress => 'success',
                        VehicleWorkStatus::Waiting => 'warning',
                        VehicleWorkStatus::Completed => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state): string => $state?->label() ?? '-'),
                ToggleColumn::make('paid')
                    ->label('Pagado')
                    ->sortable()
                    ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
                ToggleColumn::make('payment_sent')
                    ->label('Pago enviado')
                    ->sortable()
                    ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
            ])
            ->filters([
                //
            ])
            ->recordClasses(fn ($record): ?string => auth()->user()?->isTechnician() && $record->status === VehicleWorkStatus::Waiting ? 'opacity-60' : null)
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
