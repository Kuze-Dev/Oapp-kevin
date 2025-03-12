<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Order Management';

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
                        // Left column - Order details
                        Forms\Components\Section::make('Order Information')
                            ->schema([
                                Forms\Components\TextInput::make('quantity')
                                    ->required()
                                    ->numeric(),
                                Forms\Components\TextInput::make('amount')
                                    ->required()
                                    ->numeric()
                                    ->prefix('$'),
                                Forms\Components\Toggle::make('is_paid')
                                    ->required(),
                                Forms\Components\Select::make('shipping_method')
                                    ->options([
                                        'standard' => 'Standard',
                                        'express' => 'Express',
                                        'overnight' => 'Overnight',
                                    ])
                                    ->searchable(),
                                Forms\Components\TextInput::make('shipping_fee')
                                    ->numeric()
                                    ->prefix('$'),
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'processing' => 'Processing',
                                        'shipped' => 'Shipped',
                                        'delivered' => 'Delivered',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->required()
                                    ->default('pending'),
                                Forms\Components\Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->required(),
                            ])
                            ->columnSpan(1),

                        // Right column - Shipping Address and Additional Notes
                        Forms\Components\Grid::make(1)
                            ->schema([
                                // Shipping Address Section
                                Forms\Components\Section::make('Shipping Address')
                                    ->schema([
                                        Forms\Components\TextInput::make('address')
                                            ->maxLength(255)
                                            ->required(),
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('city')
                                                    ->maxLength(255)
                                                    ->required(),
                                                Forms\Components\TextInput::make('state')
                                                    ->maxLength(255)
                                                    ->required(),
                                            ]),
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('zip_code')
                                                    ->maxLength(255)
                                                    ->required(),
                                                Forms\Components\TextInput::make('country')
                                                    ->maxLength(255)
                                                    ->required(),
                                            ]),
                                        Forms\Components\TextInput::make('phone')
                                            ->tel()
                                            ->maxLength(255),
                                    ]),

                                // Additional Notes Section - Now placed below Shipping Address in the right column
                                Forms\Components\Section::make('Additional Notes')
                                    ->schema([
                                        Forms\Components\Textarea::make('notes')
                                            ->placeholder('Enter any additional notes or information'),
                                    ])
                                    ->collapsible(),

                                      ])
                            ->columnSpan(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Order ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'danger' => 'cancelled',
                        'warning' => 'pending',
                        'primary' => 'processing',
                        'success' => ['delivered', 'shipped'],
                    ]),
                Tables\Columns\IconColumn::make('is_paid')
                    ->boolean()
                    ->label('Paid'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Order Date'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\Filter::make('is_paid')
                    ->toggle()
                    ->label('Paid Orders Only')
                    ->query(fn (Builder $query): Builder => $query->where('is_paid', true)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading('Order Details')
                    ->modalWidth(MaxWidth::ThreeExtraLarge)
                    ->slideOver(),
                Tables\Actions\EditAction::make()
                    ->modalHeading('Edit Order')
                    ->modalWidth(MaxWidth::ThreeExtraLarge)
                    ->slideOver(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Order Summary')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('id')
                                    ->label('Order ID')
                                    ->weight(FontWeight::Bold),
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Order Date')
                                    ->dateTime(),
                                Infolists\Components\TextEntry::make('user.name')
                                    ->label('Customer'),
                            ]),
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('amount')
                                    ->money('USD')
                                    ->label('Total Amount'),
                                Infolists\Components\TextEntry::make('quantity')
                                    ->label('Items'),
                                Infolists\Components\IconEntry::make('is_paid')
                                    ->boolean()
                                    ->label('Payment Status')
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger'),
                            ]),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'cancelled' => 'danger',
                                'pending' => 'warning',
                                'processing' => 'primary',
                                'shipped', 'delivered' => 'success',
                                default => 'gray',
                            }),
                    ])
                    ->columns(1),

                Infolists\Components\Section::make('Shipping Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('shipping_method')
                            ->label('Shipping Method'),
                        Infolists\Components\TextEntry::make('shipping_fee')
                            ->money('USD')
                            ->label('Shipping Fee'),
                        Infolists\Components\TextEntry::make('address')
                            ->label('Address'),
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('city'),
                                Infolists\Components\TextEntry::make('state'),
                                Infolists\Components\TextEntry::make('zip_code'),
                            ]),
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('country'),
                                Infolists\Components\TextEntry::make('phone'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Additional Notes')
                    ->schema([
                        Infolists\Components\TextEntry::make('notes')
                            ->markdown()
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\OrderItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}