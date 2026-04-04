<?php

namespace App\Filament\Resources\VehicleWorks;

use App\Enums\VehicleWorkStatus;
use App\Filament\Resources\VehicleWorks\Pages\CreateVehicleWork;
use App\Filament\Resources\VehicleWorks\Pages\EditVehicleWork;
use App\Filament\Resources\VehicleWorks\Pages\ListVehicleWorks;
use App\Filament\Resources\VehicleWorks\Schemas\VehicleWorkForm;
use App\Filament\Resources\VehicleWorks\Tables\VehicleWorksTable;
use App\Models\User;
use App\Models\VehicleWork;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VehicleWorkResource extends Resource
{
    protected static ?string $model = VehicleWork::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Trabajos';

    protected static ?string $modelLabel = 'Trabajo';

    protected static ?string $pluralModelLabel = 'Trabajos';

    protected static string|\UnitEnum|null $navigationGroup = 'Taller';

    protected static ?string $recordTitleAttribute = 'plate';

    public static function form(Schema $schema): Schema
    {
        return VehicleWorkForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VehicleWorksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user instanceof User && $user->isTechnician()) {
            $query
                ->where('paid', false)
                ->where('status', VehicleWorkStatus::InProgress->value);
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVehicleWorks::route('/'),
            'create' => CreateVehicleWork::route('/create'),
            'edit' => EditVehicleWork::route('/{record}/edit'),
        ];
    }
}
