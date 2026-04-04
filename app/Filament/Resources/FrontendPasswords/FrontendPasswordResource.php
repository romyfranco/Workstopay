<?php

namespace App\Filament\Resources\FrontendPasswords;

use App\Filament\Resources\FrontendPasswords\Pages\CreateFrontendPassword;
use App\Filament\Resources\FrontendPasswords\Pages\EditFrontendPassword;
use App\Filament\Resources\FrontendPasswords\Pages\ListFrontendPasswords;
use App\Filament\Resources\FrontendPasswords\Schemas\FrontendPasswordForm;
use App\Filament\Resources\FrontendPasswords\Tables\FrontendPasswordsTable;
use App\Models\FrontendPassword;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FrontendPasswordResource extends Resource
{
    protected static ?string $model = FrontendPassword::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Contrasenas frontend';

    protected static ?string $modelLabel = 'Contrasena frontend';

    protected static ?string $pluralModelLabel = 'Contrasenas frontend';

    protected static string|\UnitEnum|null $navigationGroup = 'Configuracion';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return FrontendPasswordForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FrontendPasswordsTable::configure($table);
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
            'index' => ListFrontendPasswords::route('/'),
            'create' => CreateFrontendPassword::route('/create'),
            'edit' => EditFrontendPassword::route('/{record}/edit'),
        ];
    }
}
