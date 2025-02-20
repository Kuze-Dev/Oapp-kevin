<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product as ProductModel;
use App\Models\ProductSKU;

class Product extends Component
{
    public $product;
    public $selectedColor;
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

        // Assuming you want to set default size from product attributes
        $this->setDefaultSize();
    }

    // Set the default size, assuming size is an attribute
    public function setDefaultSize()
    {
        // Fetch the first available size from the product's attributes
        $sizeAttribute = $this->product->productAttributes
            ->where('type', 'size') // Make sure the attribute type is 'size'
            ->first();

        if ($sizeAttribute) {
            $this->selectedSize = $sizeAttribute->productAttributeValues->first()->value;
        }
    }

    public function updatedSelectedColor($colorCode)
    {
        // Find the color value based on the selected color code
        $attributeValue = $this->product->productAttributes
            ->where('type', 'color')
            ->flatMap->productAttributeValues
            ->firstWhere('colorcode', $colorCode);

        if ($attributeValue) {
            $this->selectedColor = $attributeValue->value;
            $this->updateSKU();
        }
    }

    public function updatedSelectedSize($size)
    {
        // Ensure the selected size is valid and update the SKU accordingly
        $this->selectedSize = $size;
        $this->updateSKU();
    }

    private function updateSKU()
    {
        // Query for the SKU based on selected color and size
        $query = ProductSKU::where('product_id', $this->product->id);

        if ($this->selectedColor) {
            $query->whereJsonContains('attributes->attribute1->value', $this->selectedColor);
        }

        if ($this->selectedSize) {
            $query->whereJsonContains('attributes->attribute2->value', $this->selectedSize);
        }

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

    // Ensure SKU exists for the selected color and size
    $sku = ProductSKU::where('product_id', $product->id)
        ->whereJsonContains('attributes->attribute1->value', $this->selectedColor)
        ->whereJsonContains('attributes->attribute2->value', $this->selectedSize)
        ->first();

    if (!$sku) {
        session()->flash('error', 'Selected combination is not available.');
        return;
    }

    $skuId = $sku->id;
    $skuImage = $sku->sku_image;
    $price = $sku->price;

    // Cart Key logic: Use both SKU ID and selected size to uniquely identify the item
    $cartKey = $skuId ? "sku_$skuId" : "product_$productId";

    // Add or update cart item
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
        ];
    } else {
        $cart[$cartKey]['quantity'] += $this->quantity;
    }

    session()->put('cart', $cart);
    session()->save();

    $this->dispatch('cartCountUpdated', count($cart));
    $this->dispatch('cartUpdated');
    $this->dispatch('showToast', [
        'message' => 'Added to Cart!',
        'type' => 'success'
    ]);
}

    public function increaseQuantity()
    {
        if ($this->quantity < $this->product->stock) {
            $this->quantity++;
        }
    }

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
        ]);
    }
}
