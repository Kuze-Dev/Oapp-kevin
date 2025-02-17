<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product as ProductModel;

class Product extends Component
{
    public $product;

    public function mount($id)
    {
        // Corrected to use ProductModel for fetching the product
        $this->product = ProductModel::findOrFail($id);
    }

    public function addToCart($productId)
    {
        // Corrected to use ProductModel for fetching the product
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
            ];
        }

        session()->put('cart', $cart);
        session()->save();

        $this->cart = session()->get('cart');
        $this->dispatch('cartUpdated');

    }

    public function render()
    {
        return view('livewire.product', [
            'product' => $this->product
        ]);
    }
}
