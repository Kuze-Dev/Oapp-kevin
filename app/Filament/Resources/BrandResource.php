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
                    ->maxLength(255),
                Forms\Components\FileUpload::make('brand_image')
                    ->image()
                    ->directory('brands'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('brand_image'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading('View Brand Details')
                    ->infolist(
                        fn (Brand $record) => [
                            Section::make()
                                ->schema([
                                    TextEntry::make('name')
                                        ->label('Brand Name'),
                                    ImageEntry::make('brand_image')
                                        ->label('Brand Image'),
                                    Grid::make(2)
                                        ->schema([
                                          TextEntry::make('created_at')
                                                ->label('Created At')
                                                ->dateTime(),
                                           TextEntry::make('updated_at')
                                                ->label('Updated At')
                                                ->dateTime(),
                                        ]),
                                ])
                        ]
                    ),
                Tables\Actions\EditAction::make()
                    ->modalHeading('Edit Brand')
                    ->form([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\FileUpload::make('brand_image')
                                    ->image()
                                    // ->imagePreviewHeight('250')
                                    ->directory('brands'),
                            ])
                    ]),
                Tables\Actions\DeleteAction::make()
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->modalHeading('Delete Brand')
                    ->modalDescription('Are you sure you want to delete this brand? This action cannot be undone.')
                    ->modalSubmitActionLabel('Yes, delete brand')
                    ->modalIcon('heroicon-o-trash'),
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

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBrands::route('/'),
        ];
    }
}
