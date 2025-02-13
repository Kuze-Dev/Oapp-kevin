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
                ->defaultItems(1)
                ->columnSpan(2)
                ->schema([
                    Section::make('Variation Details')
                        ->columns(2)
                        ->schema($this->getVariationFields()),

                    Group::make([
                        FileUpload::make('sku_image_dir')->label('Variant Image')->columnSpanFull(),
                        TextInput::make('sku')->label('SKU')->disabled(),
                        TextInput::make('stock')->label('Stock')->numeric()->required(),
                        TextInput::make('price')->label('Price')->numeric()->required(),
                    ])->columns(3),
                ])
        ]);
    }

    private function getVariationFields(): array
    {
        $product = $this->getRecord();

        if (!$product || !$product->productAttributes || $product->productAttributes->isEmpty()) {
            \Log::warning('No product attributes found.');
            return [];
        }

        return $product->productAttributes->flatMap(function ($productAttribute) {
            return [
                TextInput::make("attributes.attribute{$productAttribute->id}.id")
                    ->hidden()
                    ->default($productAttribute->id),

                TextInput::make("attributes.attribute{$productAttribute->id}.label")
                    ->label('Label')
                    ->disabled(),

                TextInput::make("attributes.attribute{$productAttribute->id}.value")
                    ->label('Value')
                    ->required(),
            ];
        })->toArray();
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $product = $this->getRecord();

        if (!$product->productAttributes || $product->productAttributes->isEmpty()) {
            \Log::warning('No product attributes found for variation generation.');
            return $data;
        }

        // Ensure SKUs exist
        $existingVariations = $product->skus->map(function ($sku) use ($product) {
            $product_attributes = $product->productAttributes;
            $attributes = [];

            // Ensure attributes are decoded properly
            $skuAttributes = is_array($sku->attributes) ? $sku->attributes : json_decode($sku->attributes, true);

            if (!empty($skuAttributes)) {
                foreach ($skuAttributes as $attr) {
                    $productAttribute = $product_attributes->firstWhere('id', $attr['id']);

                    $attributes["attribute{$attr['id']}"] = [
                        'id' => $attr['id'],
                        'label' => $productAttribute ? $productAttribute->type : 'Unknown',
                        'value' => $attr['value'],
                    ];
                }
            }

            return [
                'sku' => $sku->sku,
                'stock' => $sku->stock,
                'price' => $sku->price,
                'sku_image_dir' => $sku->sku_image_dir,
                'attributes' => $attributes,
            ];
        })->toArray();

        // If we have existing variations, use them
        if (!empty($existingVariations)) {
            $data['variations'] = $existingVariations;
        } else {
            // Generate new variations if none exist
            $data['variations'] = $this->generateVariationCombination(
                $product->productAttributes,
                []
            );
        }

        return $data;
    }


    private function generateVariationCombination($productAttributes, array $existingVariations): array
    {
        $defaultStock = $this->getRecord()->stock ?? 0;
        $defaultPrice = $this->getRecord()->price ?? 0;

        $combinations = $this->productVariantCollection($productAttributes);

        return collect($combinations)->map(function ($variant) use ($defaultStock, $defaultPrice) {
            $attributes = [];
            foreach ($variant as $key => $option) {
                $attrId = str_replace('attribute_', '', $key);
                $attributes["attribute{$attrId}"] = [
                    'id' => $attrId,
                    'label' => $option['label'],
                    'value' => $option['name'],
                ];
            }

            return [
                'sku' => $this->generateSKU($variant),
                'stock' => $defaultStock,
                'price' => $defaultPrice,
                'attributes' => $attributes,
            ];
        })->toArray();

    }

    private function productVariantCollection($productAttributes): array
    {
        return $productAttributes->reduce(
            function ($result, $attribute) {
                return $this->generateCombinations($result, $attribute);
            },
            [[]]
        );
    }

    private function generateCombinations(array $existingCombinations, $productAttribute): array
    {
        $combinations = [];

        foreach ($existingCombinations as $existing) {
            foreach ($productAttribute->productAttributeValues as $value) {
                $combinations[] = array_merge($existing, [
                    "attribute_{$productAttribute->id}" => [
                        'id' => $value->id,
                        'name' => $value->value,
                        'label' => $productAttribute->type,
                    ]
                ]);
            }
        }

        return $combinations;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        $record->skus()->delete(); // Delete old SKUs

        foreach ($data['variations'] as $variation) {
            $sku = ProductSKU::create([
                'sku' => $variation['sku'] ?? $this->generateSKU($variation),
                'product_id' => $record->id,
                'attributes' => json_encode($variation['attributes']),
                'stock' => $variation['stock'],
                'price' => $variation['price'],
                'sku_image_dir' => $variation['sku_image_dir'] ?? null,
            ]);

        }

        return $record;
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
