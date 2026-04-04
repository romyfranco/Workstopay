<?php

namespace App\Filament\Resources\AccessLogs\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AccessLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('channel')
                    ->label('Canal')
                    ->badge()
                    ->sortable(),
                TextColumn::make('event')
                    ->label('Evento')
                    ->badge()
                    ->sortable(),
                TextColumn::make('display_name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('user_role')
                    ->label('Rol')
                    ->badge()
                    ->sortable(),
                TextColumn::make('ip_address')
                    ->label('IP')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([])
            ->toolbarActions([])
            ->defaultSort('created_at', 'desc');
    }
}
