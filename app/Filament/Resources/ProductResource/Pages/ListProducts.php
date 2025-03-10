<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Products')
                ->icon('heroicon-o-squares-2x2')
                ->badge(fn () => $this->getModel()::count())
                ->badgeColor('gray'),

            'featured' => Tab::make('Featured')
                ->icon('heroicon-o-star')
                ->badge(fn () => $this->getModel()::where('featured', true)->count())
                ->badgeColor('warning') // Yellow color for featured products
                ->modifyQueryUsing(fn (Builder $query) => $query->where('featured', true)),

            'stock_in' => Tab::make('In Stock')
                ->icon('heroicon-o-check-circle')
                ->badge(fn () => $this->getStockCount('Stock In'))
                ->badgeColor('success') // Green color for available stock
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Stock In')),

            'sold_out' => Tab::make('Sold Out')
                ->icon('heroicon-o-x-circle')
                ->badge(fn () => $this->getStockCount('Sold Out'))
                ->badgeColor('danger') // Red color for sold out products
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Sold Out')),

            'coming_soon' => Tab::make('Coming Soon')
                ->icon('heroicon-o-clock')
                ->badge(fn () => $this->getStockCount('Coming Soon'))
                ->badgeColor('info') // Blue color for upcoming products
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Coming Soon')),
        ];
    }

    protected function getStockCount(string $status): int
    {
        return $this->getModel()::where('status', $status)->count();
    }
}
