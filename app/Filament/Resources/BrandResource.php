<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Brand;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Infolists\Components\Grid;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use App\Filament\Resources\BrandResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;
    protected static ?string $navigationGroup = 'Shop Management';
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->columnspan('full') // Ensure full width
                    ->label('Brand Name')
                    ->placeholder('Enter brand name'),
                Forms\Components\FileUpload::make('brand_image')
                    ->image()
                    ->directory('brands')
                    ->columnspan('full'), // Ensure full width
            ])
            ->columns(1); // Force single column for better centering
    }

    public static function table(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 2,
                'lg' => 2,
            ])
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\ImageColumn::make('brand_image')
                        ->height(220)
                        ->extraImgAttributes([
                            'class' => 'w-full h-full object-cover rounded-xl transition-all duration-300 hover:scale-105',
                            'style' => 'aspect-ratio: 16/7;',
                        ]),
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('name')
                            ->weight('bold')
                            ->size('lg')
                            ->searchable()
                            ->alignment('center') // Add this for center alignment
                            ->extraAttributes([
                                'class' => 'mt-3 font-sans tracking-tight text-center w-full', // Ensure full width
                            ]),
                    ])->space(1),
                ])->extraAttributes([
                    'class' => 'group relative overflow-hidden rounded-xl transition-all duration-300 hover:shadow-md text-center', // Add text-center
                ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View')
                    ->modalHeading('View Brand Details')
                    ->infolist(
                        fn (Brand $record) => [
                            Section::make()
                                ->schema([
                                    TextEntry::make('name')
                                        ->label('Brand Name')
                                        ->alignment('center'), // Center in modal
                                    ImageEntry::make('brand_image')
                                        ->label('Brand Image')
                                        ->alignment('center'), // Center in modal
                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('created_at')
                                                ->label('Created At')
                                                ->dateTime()
                                                ->alignment('center'),
                                            TextEntry::make('updated_at')
                                                ->label('Updated At')
                                                ->dateTime()
                                                ->alignment('center'),
                                        ]),
                                ])
                        ]
                    )
                    ->button()
                    ->extraAttributes([
                        'class' => 'flex justify-center items-center w-full', // Ensure full width and center
                    ]),

                Tables\Actions\EditAction::make()
                    ->modalHeading('Edit Brand')
                    ->form([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnspan('full')
                                    ->label('Brand Name')
                                    ->placeholder('Enter brand name'),
                                Forms\Components\FileUpload::make('brand_image')
                                    ->image()
                                    ->directory('brands')
                                    ->columnspan('full'),
                            ])
                            ->columns(1) // Force single column for better centering
                    ])
                    ->button()
                    ->extraAttributes([
                        'class' => 'flex justify-center items-center w-full', // Ensure full width and center
                    ]),

                Tables\Actions\DeleteAction::make()
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->modalHeading('Delete Brand')
                    ->modalDescription('Are you sure you want to delete this brand? This action cannot be undone.')
                    ->modalSubmitActionLabel('Yes, delete brand')
                    ->modalIcon('heroicon-o-trash')
                    ->button()
                    ->extraAttributes([
                        'class' => 'flex justify-center items-center w-full', // Ensure full width and center
                    ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListBrands::route('/'),
        ];
    }
}
