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
    public $price; // Add this for dynamic price

    public function mount($id)
    {
        // Fetch the product with attributes and values
        $this->product = ProductModel::with(['productAttributes.productAttributeValues'])->findOrFail($id);
        $this->price = $this->product->price; // Default price
    }

    public function updatedSelectedColor($colorCode)
{
    // Find the attribute value that has this color code
    $attributeValue = $this->product->productAttributes
        ->where('type', 'color')
        ->flatMap->productAttributeValues
        ->firstWhere('colorcode', $colorCode);

    if ($attributeValue) {
        // Find SKU based on the actual value, not the colorcode
        $sku = ProductSKU::where('product_id', $this->product->id)
            ->whereJsonContains('attributes->attribute1->value', $attributeValue->value)
            ->first();
        // If SKU exists, update the image
        if ($sku) {
            $this->selectedColorImage = $sku->sku_image;
        } else {
            $this->selectedColorImage = $this->product->product_image;
        }
    }
}



    public function updatedSelectedSize($size)
    {
        // Find the product SKU based on selected size
        $sku = ProductSKU::where('product_id', $this->product->id)
            ->whereJsonContains('attributes->attribute2->value', $size) // Assuming 'attribute2' represents the size attribute
            ->first();

        // If SKU is found, update the price
        if ($sku) {
            $this->price = $sku->price; // Assuming 'price' is the field holding the price for the SKU
        }
    }

    public function addToCart($productId)
    {
        $product = ProductModel::findOrFail($productId);
        $cart = session()->get('cart', []);

        if (!isset($cart[$productId])) {
            $cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'product_image' => $product->product_image,
                'status' => $product->status,
                'category_id' => $product->category_id,
                'brand_id' => $product->brand_id,
                'price' => $this->price, // Store the dynamic price
            ];
        }

        session()->put('cart', $cart);
        session()->save();

        // Emit event to update cart count in the header
        $this->dispatch('cartCountUpdated', count($cart));
        $this->dispatch('cartUpdated');

        // Emit event to show toast notification
      $this->dispatch('showToast', ['message' => 'Product added to cart successfully!' ,
                                    'type' => 'success']);
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
            'price' => $this->price, // Pass dynamic price to the view
        ]);
    }
}
