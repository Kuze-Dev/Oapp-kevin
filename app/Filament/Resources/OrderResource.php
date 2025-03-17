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
use Filament\Infolists\Components\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Order Management';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Order')
                        ->icon('heroicon-o-shopping-cart')
                        ->description('Basic order details')
                        ->schema([
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\TextInput::make('quantity')
                                        ->required()
                                        ->numeric(),
                                    Forms\Components\TextInput::make('amount')
                                        ->required()
                                        ->numeric()
                                        ->prefix('$'),
                                ]),
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\Toggle::make('is_paid')
                                        ->required()
                                        ->inline(false),
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
                                ]),
                            Forms\Components\Select::make('user_id')
                                ->relationship('user', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                        ]),

                    Step::make('Delivery')
                        ->icon('heroicon-o-truck')
                        ->description('Delivery information')
                        ->schema([
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\Select::make('shipping_method')
                                        ->options([
                                            'standard' => 'Standard',
                                            'express' => 'Express',
                                            'overnight' => 'Overnight',
                                        ])
                                        ->searchable()
                                        ->required(),
                                    Forms\Components\TextInput::make('shipping_fee')
                                        ->numeric()
                                        ->prefix('$')
                                        ->required(),
                                ]),
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
                                ->maxLength(255)
                                ->required(),
                        ]),

                    Step::make('Billing')
                        ->icon('heroicon-o-credit-card')
                        ->description('Payment information')
                        ->schema([
                            Forms\Components\TextInput::make('payment_method')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('transaction_id')
                                ->maxLength(255),
                            Forms\Components\Toggle::make('is_paid')
                                ->required()
                                ->inline(false)
                                ->label('Payment received'),
                            Forms\Components\Textarea::make('notes')
                                ->placeholder('Enter any additional notes or information')
                                ->columnSpanFull()
                                ->rows(5),
                        ]),
                ])
                ->skippable(false)
                ->persistStepInQueryString()
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
                                Infolists\Components\Grid::make([
                                    'default' => 1,
                                    'sm' => 1,
                                    'md' => 2,
                                ])
                                ->schema([
                                    Infolists\Components\IconEntry::make('is_paid')
                                        ->boolean()
                                        ->label('Payment Status')
                                        ->trueIcon('heroicon-o-check-circle')
                                        ->falseIcon('heroicon-o-x-circle')
                                        ->trueColor('success')
                                        ->falseColor('danger'),
                                    Infolists\Components\Actions::make([
                                        Action::make('editPaymentStatus')
                                            ->label('Edit')
                                            ->icon('heroicon-m-pencil-square')
                                            ->size('sm')
                                            ->color('primary')
                                            ->form([
                                                Forms\Components\Toggle::make('is_paid')
                                                    ->label('Mark as Paid')
                                                    ->default(fn ($record) => $record->is_paid),
                                            ])
                                            ->action(function (array $data, $record): void {
                                                $record->update([
                                                    'is_paid' => $data['is_paid'],
                                                ]);

                                                Notification::make()
                                                    ->title('Payment status updated successfully')
                                                    ->success()
                                                    ->send();
                                            })
                                    ])->extraAttributes(['class' => 'flex justify-end']),
                                ]),
                            ]),
                        Infolists\Components\Grid::make([
                            'default' => 1,
                            'sm' => 1,
                            'md' => 2,
                        ])
                        ->schema([
                            Infolists\Components\TextEntry::make('status')
                                ->badge()
                                ->label('Order Status')
                                ->color(fn (string $state): string => match ($state) {
                                    'cancelled' => 'danger',
                                    'pending' => 'warning',
                                    'processing' => 'primary',
                                    'shipped', 'delivered' => 'success',
                                    default => 'gray',
                                }),
                            Infolists\Components\Actions::make([
                                Action::make('editStatus')
                                    ->label('Edit')
                                    ->icon('heroicon-m-pencil-square')
                                    ->size('sm')
                                    ->color('primary')
                                    ->form([
                                        Forms\Components\Select::make('status')
                                            ->label('Order Status')
                                            ->options([
                                                'pending' => 'Pending',
                                                'processing' => 'Processing',
                                                'shipped' => 'Shipped',
                                                'delivered' => 'Delivered',
                                                'cancelled' => 'Cancelled',
                                            ])
                                            ->default(fn ($record) => $record->status),
                                    ])
                                    ->action(function (array $data, $record): void {
                                        $record->update([
                                            'status' => $data['status'],
                                        ]);

                                        Notification::make()
                                            ->title('Order status updated successfully')
                                            ->success()
                                            ->send();
                                    })
                            ])->extraAttributes(['class' => 'flex justify-end']),
                        ]),
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
}