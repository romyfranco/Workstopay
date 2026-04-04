<?php

namespace App\Filament\Resources\AccessLogs;

use App\Filament\Resources\AccessLogs\Pages\ListAccessLogs;
use App\Filament\Resources\AccessLogs\Tables\AccessLogsTable;
use App\Models\AccessLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AccessLogResource extends Resource
{
    protected static ?string $model = AccessLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Registro de accesos';

    protected static ?string $modelLabel = 'Registro de acceso';

    protected static ?string $pluralModelLabel = 'Registros de acceso';

    protected static string|\UnitEnum|null $navigationGroup = 'Configuracion';

    protected static ?string $recordTitleAttribute = 'channel';

    public static function table(Table $table): Table
    {
        return AccessLogsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAccessLogs::route('/'),
        ];
    }
}
