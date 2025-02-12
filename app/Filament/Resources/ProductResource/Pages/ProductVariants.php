<?php

namespace App\Filament\Resources\ProductResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use App\Models\ProductSKU;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ProductResource;

class ProductVariants extends EditRecord
{
    protected static string $resource = ProductResource::class;

    public function form(Form $form): Form
    {
        return $form->schema([
            Repeater::make('variations')
                ->label('Product Variations')
                ->collapsible()
                ->defaultItems(1) // Ensures at least one item is always there
                ->columnSpan(2)
                ->schema(function () {
                    return [
                        Section::make('Variation Details')
                            ->columns(2)
                            ->schema($this->getVariationFields()), // Call method dynamically

                        Group::make([
                            FileUpload::make('sku_image_dir')->label('Variant Image')->columnSpanFull(),
                            TextInput::make('sku')->label('SKU')->disabled(),
                            TextInput::make('stock')->label('Stock')->numeric()->required(),
                            TextInput::make('price')->label('Price')->numeric()->required(),
                        ])->columns(3)
                    ];
                })
        ]);
    }


    private function getVariationFields(): array
{
    $product = $this->getRecord();

    if (!$product) {
        \Log::error('Product record is null.');
        return [];
    }

    $product->loadMissing('productAttributes.productAttributeValues');

    if (!$product->productAttributes || $product->productAttributes->isEmpty()) {
        \Log::warning('No product attributes found.');
        return [];
    }


    return collect($product->productAttributes)->flatMap(function ($productAttribute) {
        return [
            TextInput::make("attribute_{$productAttribute->id}_id")
                ->hidden()
                ->default($productAttribute->id),

            TextInput::make("attribute_{$productAttribute->id}_name")
                ->label('Attribute Type')
                ->disabled()
                ->default($productAttribute->name),

            TextInput::make("attribute_{$productAttribute->id}_value")
                ->label('Attribute Value')
                ->required()
                ->default(fn () => $productAttribute->productAttributeValues->pluck('value')->join(', ')), // Get value(s)
        ];
    })->toArray();
}



    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        $record->sku()->delete(); // Remove old SKUs to prevent duplicates

        foreach ($data['variations'] as $variation) {
            ProductSKU::create([
                'sku' => $this->generateSKU($variation),
                'product_id' => $record->id,
                'product_attribute_id' => $variation["variation_type_{$variation['variation_type_id']}_id"] ?? null,
                'product_attribute_value_id' => $variation["variation_type_{$variation['variation_type_id']}_value"] ?? null,
                'stock' => $variation['stock'],
                'price' => $variation['price'],
            ]);
        }

        return $record;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $existingVariations = $this->record->sku->toArray();
        \Log::info('Existing Variations:', $existingVariations);

        $data['variations'] = $this->generateVariationCombination(
            $this->record->productAttributes, $existingVariations
        );

        \Log::info('Generated Variations:', $data['variations']);

        if (empty($data['variations'])) {
            $data['variations'] = [
                ['stock' => 0, 'price' => 0, 'sku' => '']
            ];
        }

        return $data;
    }

    private function generateVariationCombination($productAttributes, array $existingVariations): array
    {
        \Log::info('Product Attributes:', $productAttributes->toArray());
        \Log::info('Existing Variations Before Processing:', $existingVariations);

        $defaultStock = $this->record->stock ?? 0;
        $defaultPrice = $this->record->price ?? 0;

        $variations = collect($this->productVariantCollection($productAttributes))
            ->map(function ($variant) use ($existingVariations, $defaultStock, $defaultPrice) {
                $optionIds = collect($variant)
                    ->filter(fn($value, $key) => str_starts_with($key, 'variation_type_'))
                    ->map(fn($option) => $option['id'])
                    ->values()
                    ->toArray();

                \Log::info('Processing Variant:', $variant);

                $existing = collect($existingVariations)
                    ->firstWhere(fn($existing) => $existing['attributes'] === $optionIds);

                return array_merge($variant, [
                    'stock' => $existing['stock'] ?? $defaultStock,
                    'price' => $existing['price'] ?? $defaultPrice,
                    'sku' => $existing['sku'] ?? $this->generateSKU($optionIds),
                ]);
            })->toArray();

        \Log::info('Final Variations:', $variations);

        return $variations;
    }

    private function productVariantCollection($productAttributes): array
    {
        return collect($productAttributes)->reduce(
            fn($result, $productAttribute) => $this->generateCombinations($result, $productAttribute), [[]]
        );
    }

    private function generateCombinations(array $existingCombinations, $productAttribute): array
    {
        return collect($productAttribute['values'])->flatMap(fn($option) =>
            collect($existingCombinations)->map(fn($combination) => array_merge($combination, [
                "variation_type_{$productAttribute['id']}" => [
                    'id' => $option['id'],
                    'name' => $option['value'],
                    'label' => $productAttribute['name'],
                ]
            ]))->toArray()
        )->toArray();
    }

    private function generateSKU($combination): string
    {
        return 'SKU-' . substr(md5(json_encode($combination)), 0, 8);
    }

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
