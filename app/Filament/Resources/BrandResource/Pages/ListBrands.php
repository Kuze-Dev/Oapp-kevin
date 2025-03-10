<?php

namespace App\Filament\Resources\BrandResource\Pages;

use App\Filament\Resources\BrandResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBrands extends ListRecords
{
    protected static string $resource = BrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New brand')
                ->modalHeading('Create Brand')
                ->form([
                    \Filament\Forms\Components\Section::make()
                        ->schema([
                            \Filament\Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                            \Filament\Forms\Components\FileUpload::make('brand_image')
                                ->image()
                                ->imagePreviewHeight('250')
                                ->directory('brands'),
                        ])
                ]),
        ];
    }
}
