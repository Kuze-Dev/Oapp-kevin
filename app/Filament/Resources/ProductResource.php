<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use App\Filament\Resources\ProductResource\Pages;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationGroup = 'Shop Management';
    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Section::make('Product Information')
                            ->schema([
                                TextInput::make('name')->required()->maxLength(255),
                                Forms\Components\Textarea::make('description')->required()->columnSpanFull(),
                                Forms\Components\FileUpload::make('product_image')->image()
                                    ->panelLayout('integrated')->extraAttributes(['style' => 'height: 100%; min-height: 200px;']),
                            ])->columnSpan(1),
                        Forms\Components\Section::make('Product Details')
                            ->schema([
                                TextInput::make('price')->numeric()->required()->default(0.00)->prefix('PHP'),
                                TextInput::make('stock')->numeric()->required()->default(0),
                                Select::make('brand_id')->relationship('brand', 'name')->required(),
                                Select::make('category_id')->relationship('category', 'name')->required(),
                                Select::make('status')->label('Product Status')
                                    ->options([
                                        'Stock In' => 'Stock In',
                                        'Sold Out' => 'Sold Out',
                                        'Coming Soon' => 'Coming Soon',
                                    ])->required(),
                            ])->columnSpan(1),
                    ]),
                Forms\Components\Section::make('Product Attributes')
                    ->schema([
                        Repeater::make('Product_Attributes')->relationship('productAttributes')
                            ->schema([
                                Select::make('type')->label('Product Attributes')->options([
                                    'color' => 'Color',
                                    'sizes' => 'Sizes',
                                    'metadata' => 'Metadata',
                                ])->reactive()
                                    ->disableOptionWhen(fn (string $value,callable $get):bool=>
                                    in_array($value,array_column($get('../../Product_Attributes') ?? [],'type'))),

                                Repeater::make('Product_Values')->relationship('productAttributeValues')
                                    ->schema([
                                        TextInput::make('value')->label('Product Attribute Values')->required(),
                                    ]),
                            ])->collapsible()->defaultItems(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                ImageColumn::make('product_image'),
                TextColumn::make('brand.name')->sortable(),
                TextColumn::make('category.name')->sortable(),
                TextColumn::make('status')->label('Product Status')
                    ->color(fn (string $state): string => match ($state) {
                        'Stock In' => 'success',
                        'Sold Out' => 'danger',
                        'Coming Soon' => 'warning',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Stock In' => 'heroicon-o-check-circle',
                        'Sold Out' => 'heroicon-o-x-circle',
                        'Coming Soon' => 'heroicon-o-clock',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
            'variance'=> Pages\ProductVariants::route('/{record}/variance'),

        ];
    }

    public static function getRecordSubNavigation(Page $page): array
{
    return $page->generateNavigationItems([
        Pages\EditProduct::class,
        Pages\ProductVariants::class,
    ]);
}


}
