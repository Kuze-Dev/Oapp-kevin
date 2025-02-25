<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product as ProductModel;
use App\Models\ProductSKU;

class Product extends Component
{
    public $product;
    public $selectedColor;
    public $selectedColorCode; // Add this property to store the color code
    public $selectedSize;
    public $quantity = 1;
    public $selectedColorImage = null;
    public $price;

    public function mount($id)
    {
        // Load product along with its attributes
        $this->product = ProductModel::with(['productAttributes.productAttributeValues'])->findOrFail($id);
        $this->price = $this->product->price;
        $this->selectedColorImage = $this->product->product_image;

        // Set default size from product attributes
        $this->setDefaultAttributes();
    }

    // Set default color and size
    public function setDefaultAttributes()
    {
        // Set default size
        $sizeAttribute = $this->product->productAttributes->where('type', 'sizes')->first();
        if ($sizeAttribute) {
            $this->selectedSize = $sizeAttribute->productAttributeValues->first()->value;
            $this->sizeAttributeId = $sizeAttribute->id; // Store size attribute ID
        }

        // Set default color
        $colorAttribute = $this->product->productAttributes->where('type', 'color')->first();
        if ($colorAttribute) {
            $attributeValue = $colorAttribute->productAttributeValues->first();
            $this->selectedColor = $attributeValue->value;
            $this->selectedColorCode = $attributeValue->colorcode; // Store the color code
            $this->colorAttributeId = $colorAttribute->id; // Store color attribute ID
            $this->selectedSize = null;
            $this->selectedColor = null;
            $this->selectedColorCode = null;

        }
    }

    public function updatedSelectedColor($colorCode)
    {
        // Store the color code
        $this->selectedColorCode = $colorCode;

        // Find color attribute value based on selected color code
        $attributeValue = $this->product->productAttributes->where('type', 'color')->flatMap->productAttributeValues->firstWhere('colorcode', $colorCode);

        if ($attributeValue) {
            $this->selectedColor = $attributeValue->value;
            $this->updateSKU();
        }
    }

    public function updatedSelectedSize($size)
    {
        // Ensure the selected size is valid and update the SKU
        $this->selectedSize = $size;
        $this->updateSKU();
    }

    private function updateSKU()
    {
        // Build SKU query based on selected attributes dynamically
        $query = ProductSKU::where('product_id', $this->product->id);

        // Loop through the product's attributes to apply dynamic filters
        foreach ($this->product->productAttributes as $attribute) {
            $attributeType = $attribute->type;
            $attributeId = $attribute->id;
            $selectedValue = null;

            // Check if this attribute is selected by the user (e.g., color, size, etc.)
            if ($attributeType == 'color') {
                $selectedValue = $this->selectedColor;
            } elseif ($attributeType == 'sizes') {
                $selectedValue = $this->selectedSize;
            } else {
                // Handle other attributes dynamically (e.g., material, pattern, etc.)
                $selectedValue = $this->{"selected{$attributeType}"} ?? null; // Dynamically fetch selected attribute value
            }

            if ($selectedValue) {
                // Dynamically apply the JSON filtering based on attribute ID
                $query->whereJsonContains("attributes->attribute{$attributeId}->value", $selectedValue);
            }
        }

        // Fetch the matching SKU
        $sku = $query->first();

        if ($sku) {
            $this->selectedColorImage = $sku->sku_image ?? $this->product->product_image;
            $this->price = $sku->price;
        } else {
            $this->selectedColorImage = $this->product->product_image;
            $this->price = $this->product->price;
        }
    }

    // Add product to the cart
    public function addToCart($productId)
    {
        $product = ProductModel::findOrFail($productId);
        $cart = session()->get('cart', []);

        // Ensure valid color and size selection
        if (!$this->selectedColor || !$this->selectedSize) {
            session()->flash('error', 'Please select a valid color and size.');
            return;
        }

        // Find SKU based on selected attributes dynamically
        $skuQuery = ProductSKU::where('product_id', $product->id);

        // Loop through the product's attributes to dynamically build the SKU query
        foreach ($this->product->productAttributes as $attribute) {
            $attributeType = $attribute->type;
            $attributeId = $attribute->id;
            $selectedValue = null;

            // Dynamically check for selected attributes
            if ($attributeType == 'color') {
                $selectedValue = $this->selectedColor;
            } elseif ($attributeType == 'sizes') {
                $selectedValue = $this->selectedSize;
            } else {
                // For other attributes (like material, pattern), dynamically use the selected value
                $selectedValue = $this->{"selected{$attributeType}"} ?? null;
            }

            if ($selectedValue) {
                // Dynamically apply the JSON filtering based on attribute ID
                $skuQuery->whereJsonContains("attributes->attribute{$attributeId}->value", $selectedValue);
            }
        }

        // Fetch the matching SKU
        $sku = $skuQuery->first();

        if (!$sku) {
            session()->flash('error', 'Selected combination is not available.');
            return;
        }

        $skuId = $sku->id;
        $skuImage = $sku->sku_image;
        $price = $sku->price;

        // Ensure unique cart key based on product ID and SKU ID
        $cartKey = "product_{$product->id}_sku_{$skuId}";
        // dd($cartKey);

        // Add or update the cart item
        if (!isset($cart[$cartKey])) {
            $cart[$cartKey] = [
                'id' => $product->id,
                'sku_id' => $skuId,
                'selected_color' => $this->selectedColor,
                'selected_size' => $this->selectedSize,
                'name' => $product->name,
                'description' => $product->description,
                'sku_image' => $skuImage,
                'status' => $product->status,
                'category_id' => $product->category_id,
                'brand_id' => $product->brand_id,
                'price' => $price,
                'quantity' => $this->quantity,
                'timestamp' => now()->timestamp,
            ];
        } else {
            // If item already exists, update the quantity
            $cart[$cartKey]['quantity'] += $this->quantity;
        }

        // Save updated cart in session
        session()->put('cart', $cart);
        session()->save();

        // Dispatch events to update UI
        $this->dispatch('cartCountUpdated', count($cart));
        $this->dispatch('cartUpdated');
        $this->dispatch('showToast', [
            'message' => 'Added to Cart!',
            'type' => 'success'
        ]);
    }

    // Increase product quantity
    public function increaseQuantity()
    {
        if ($this->quantity < $this->product->stock) {
            $this->quantity++;
        }
    }

    // Decrease product quantity
    public function decreaseQuantity()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function render()
    {
        return view('livewire.product', [
            'product' => $this->product,
            'price' => $this->price,
            'selectedColorImage' => $this->selectedColorImage,
            'selectedColorCode' => $this->selectedColorCode, // Pass the selected color code to the view
        ]);
    }
}
