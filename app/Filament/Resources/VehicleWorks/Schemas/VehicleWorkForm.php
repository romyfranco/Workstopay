<?php

namespace App\Filament\Resources\VehicleWorks\Schemas;

use App\Enums\VehicleWorkStatus;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VehicleWorkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos del trabajo')
                    ->schema([
                        TextInput::make('plate')
                            ->label('Placa')
                            ->required()
                            ->maxLength(20),
                        TextInput::make('vehicle')
                            ->label('Vehiculo')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('job_description')
                            ->label('Descripcion del trabajo')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull(),
                        Textarea::make('comment')
                            ->label('Comentario')
                            ->rows(4)
                            ->maxLength(2000)
                            ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false)
                            ->dehydrated(fn (): bool => auth()->user()?->isAdmin() ?? false)
                            ->columnSpanFull(),
                        FileUpload::make('job_photos')
                            ->label('Fotos del trabajo')
                            ->image()
                            ->disk('public')
                            ->directory('vehicle-works')
                            ->visibility('public')
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            ->maxFiles(10)
                            ->maxParallelUploads(1)
                            ->automaticallyResizeImagesMode('contain')
                            ->automaticallyResizeImagesToWidth('1000')
                            ->automaticallyResizeImagesToHeight('1000')
                            ->automaticallyUpscaleImagesWhenResizing(false)
                            ->imagePreviewHeight('220')
                            ->openable()
                            ->downloadable()
                            ->extraAlpineAttributes([
                                'x-init' => <<<'JS'
                                    let compressionAttempts = 0

                                    const applyCompressionOptions = () => {
                                        if (pond?.setOptions) {
                                            pond.setOptions({
                                                imageTransformOutputMimeType: 'image/jpeg',
                                                imageTransformOutputQuality: 70,
                                                imageTransformOutputQualityMode: 'always',
                                            })

                                            return
                                        }

                                        if (compressionAttempts < 20) {
                                            compressionAttempts++
                                            setTimeout(applyCompressionOptions, 150)
                                        }
                                    }

                                    applyCompressionOptions()
                                JS,
                            ])
                            ->columnSpanFull(),
                        Select::make('status')
                            ->label('Estado')
                            ->options(VehicleWorkStatus::options())
                            ->default(VehicleWorkStatus::InProgress->value)
                            ->native(false)
                            ->required(fn (): bool => auth()->user()?->isAdmin() ?? false)
                            ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false)
                            ->dehydrated(fn (): bool => auth()->user()?->isAdmin() ?? false),
                        Toggle::make('paid')
                            ->label('Pagado')
                            ->default(false)
                            ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
                        Toggle::make('payment_sent')
                            ->label('Pago enviado')
                            ->default(false)
                            ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
                    ])
                    ->columns(2),
            ]);
    }
}
