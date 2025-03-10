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
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\ColorPicker;
use Filament\Tables\Columns\BooleanColumn;
use App\Filament\Resources\ProductResource\Pages;
use Filament\Pages\SubNavigationPosition;


class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationGroup = 'Shop Management';
    protected static ?string $navigationIcon = 'heroicon-o-cube';

     protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

   public static function form(Form $form): Form
{
    return $form
        ->schema([
            // Creating a 2-column layout
            Forms\Components\Grid::make(2)
                ->schema([
                    // Product Information in the left column
                    Forms\Components\Section::make('Product Information')
                        ->schema([
                            TextInput::make('name')->required()->maxLength(255),
                            RichEditor::make('description')->required()->columnSpanFull(),
                            Forms\Components\FileUpload::make('product_image')->image()
                                ->panelLayout('integrated')->extraAttributes(['style' => 'height: 100%; min-height: 200px;']),
                        ])->columnSpan(1),  // Left column (Product Information)

                    // Pricing & Stock + Category & Brand in the right column
                    Forms\Components\Section::make('Pricing & Product Details')
                        ->schema([
                            Forms\Components\Grid::make(2) // Nested grid for right side content
                                ->schema([
                                    // Pricing & Stock in the first column of the right section
                                    Forms\Components\Section::make('Pricing & Stock')
                                        ->schema([
                                            TextInput::make('price')->numeric()->required()->default(0.00)->prefix('PHP'),
                                            TextInput::make('stock')->numeric()->required()->default(0),
                                            Forms\Components\Toggle::make('featured')->label('Featured')->default(false), // Add featured toggle
                                        ]),

                                    // Category & Brand in the second column of the right section
                                    Forms\Components\Section::make('Category & Brand')
                                        ->schema([
                                            Select::make('category_id')->relationship('category', 'name')->required(),
                                            Select::make('brand_id')->relationship('brand', 'name')->required(),
                                        ]),
                                ]),
                        ])->columnSpan(1),  // Right column (Pricing & Stock + Category & Brand)
                ]),

            // Product Status section in a separate section below
            Forms\Components\Section::make('Product Status')
                ->schema([
                    Select::make('status')->label('Product Status')
                        ->options([
                            'Stock In' => 'Stock In',
                            'Sold Out' => 'Sold Out',
                            'Coming Soon' => 'Coming Soon',
                        ])->required(),
                ]),

            // Product Attributes section with Repeater for attributes and values
            Forms\Components\Section::make('Product Attributes')
                ->schema([
                    Repeater::make('Product_Attributes')->relationship('productAttributes')
                        ->schema([
                            Select::make('type')->label('Product Attributes')->options([
                                'color' => 'Color',
                                'sizes' => 'Sizes',
                                'metadata' => 'Metadata',
                            ])->reactive()
                                ->disableOptionWhen(fn (string $value, callable $get): bool =>
                                    in_array($value, array_column($get('../../Product_Attributes') ?? [], 'type'))),

                            Repeater::make('Product_Values')->relationship('productAttributeValues')
                                ->schema([
                                    TextInput::make('value')
                                    ->label('Product Attribute Values')
                                    ->required(),
                                    ColorPicker::make('colorcode')
                                    ->label('pick a color')
                                    ->required()
                                    ->hidden(fn (callable $get) => $get('../../type') !== 'color'),
                                    // FileUpload::make('image')
                                    // ->label('Image')
                                    // ->hidden(fn (callable $get) => $get('../../type') !== 'color')
                                    // ->image(),

                                ]),
                        ])->collapsible()->defaultItems(1),

                ]),


        ]);
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
                // Product Image
                Tables\Columns\ImageColumn::make('product_image')
                    ->height(220)
                    ->extraImgAttributes([
                        'class' => 'w-full h-full object-cover rounded-xl transition-all duration-300 hover:scale-105',
                        'style' => 'aspect-ratio: 16/7;',
                    ]),

                // Product Details Stack
                Tables\Columns\Layout\Stack::make([
                    // Product Name
                    Tables\Columns\TextColumn::make('name')
                        ->weight('bold')
                        ->size('lg')
                        ->searchable()
                        ->alignment('center')
                        ->extraAttributes([
                            'class' => 'mt-3 font-sans tracking-tight text-center w-full',
                        ]),

                    // Brand and Category
                    Tables\Columns\Layout\Grid::make(2)
                        ->schema([
                            Tables\Columns\TextColumn::make('brand.name')
                                ->label('Brand')
                                ->alignment('center')
                                ->color('secondary')
                                ->icon('heroicon-o-building-storefront'),

                            Tables\Columns\TextColumn::make('category.name')
                                ->label('Category')
                                ->alignment('center')
                                ->color('primary')
                                ->icon('heroicon-o-tag'),
                        ]),

                    // Price and Stock
                    Tables\Columns\Layout\Grid::make(2)
                        ->schema([
                            Tables\Columns\TextColumn::make('price')
                                ->money('PHP')
                                ->alignment('center')
                                ->color('success')
                                ->icon('heroicon-o-currency-dollar'),

                            Tables\Columns\TextColumn::make('stock')
                                ->label('Available Stock')
                                ->alignment('center')
                                ->color(fn ($state) => $state <= 10 ? 'danger' : 'primary')
                                ->icon('heroicon-o-archive-box'),
                        ]),

                    // Product Status
                    Tables\Columns\TextColumn::make('status')
                        ->label('Product Status')
                        ->badge()
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
                        ->alignment('center'),

                    // Featured Product Indicator
                    Tables\Columns\TextColumn::make('featured')
                        ->label('Featured')
                        ->badge()
                        ->color(fn ($state) => $state ? 'primary' : 'secondary')
                        ->icon(fn ($state) => $state ? 'heroicon-o-star' : 'heroicon-o-star')
                        ->alignment('center'),
                ])->space(2),
            ])->extraAttributes([
                'class' => 'group relative overflow-hidden rounded-xl transition-all duration-300 hover:shadow-md text-center',
            ]),
        ])
        ->actions([
            Tables\Actions\ViewAction::make()
                ->label('View')
                ->modalHeading('Product Details')
                ->button()
                ->extraAttributes([
                    'class' => 'flex justify-center items-center w-full',
                ]),

            Tables\Actions\EditAction::make()
                ->label('Edit')
                ->modalHeading('Edit Product')
                ->button()
                ->extraAttributes([
                    'class' => 'flex justify-center items-center w-full',
                ]),

            Tables\Actions\DeleteAction::make()
                ->label('Delete')
                ->modalHeading('Delete Product')
                ->modalDescription('Are you sure you want to delete this product? This action cannot be undone.')
                ->button()
                ->extraAttributes([
                    'class' => 'flex justify-center items-center w-full',
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
