<?php

namespace App\Filament\Resources\ProductSKUResource\Pages;

use App\Filament\Resources\ProductSKUResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductSKUS extends ListRecords
{
    protected static string $resource = ProductSKUResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
