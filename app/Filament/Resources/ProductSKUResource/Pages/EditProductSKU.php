<?php

namespace App\Filament\Resources\ProductSKUResource\Pages;

use App\Filament\Resources\ProductSKUResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductSKU extends EditRecord
{
    protected static string $resource = ProductSKUResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
