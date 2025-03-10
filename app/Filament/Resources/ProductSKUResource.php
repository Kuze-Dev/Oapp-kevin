<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Form;
use App\Models\ProductSKU;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\ProductAttribute;
use Filament\Resources\Resource;
use App\Models\ProductAttributeValues;
use Filament\Tables\Columns\ImageColumn;
use App\Filament\Resources\ProductSKUResource\Pages;

class ProductSKUResource extends Resource
{
    protected static ?string $model = ProductSKU::class;
    protected static ?string $navigationGroup = 'Shop Management';
    protected static ?string $navigationLabel = 'Product SKU';
    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label('Product')
                            ->options(Product::pluck('name', 'id'))
                            ->reactive()
                            ->required()
                            ->afterStateUpdated(fn ($state, callable $set) => self::generateVariations($set, $state)),
                    ]),

                Forms\Components\Repeater::make('variations')
                    ->label('Product Variations')
                    ->schema([
                        Forms\Components\TextInput::make('attribute_combination')
                            ->label('Variation (Color - Size)')
                            ->readOnly()
                            ->required(),

                        Forms\Components\TextInput::make('sku')
                            ->label('SKU')
                            ->required()
                            ->readOnly(),

                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->default(fn (callable $get) => Product::find($get('product_id'))?->price ?? 0.00),

                        Forms\Components\TextInput::make('stock')
                            ->required()
                            ->numeric()
                            ->default(fn (callable $get) => Product::find($get('product_id'))?->stock ?? 0),
                    ])
                    ->defaultItems(0)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('attributes')
                    ->label('Attributes')
                    ->formatStateUsing(fn ($state) => self::extractAttributeValues($state))
                    ->sortable()
                    ->searchable(),

                ImageColumn::make('sku_image'),

                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('product_id')
                    ->label('Product')
                    ->options(Product::pluck('name', 'id')),

            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductSKUS::route('/'),
            'create' => Pages\CreateProductSKU::route('/create'),
            'view' => Pages\ViewProductSKU::route('/{record}'),
            'edit' => Pages\EditProductSKU::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    private static function extractAttributeValues($state): string
    {
        if (!$state) {
            return '-';
        }

        $attributes = is_array($state) ? $state : json_decode($state, true);

        if (!is_array($attributes)) {
            return '-';
        }

        $values = array_map(fn($attr) => $attr['value'] ?? '', $attributes);

        return implode(' | ', $values);
    }
}
