<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\ProductSKU;

class Cart extends Component
{
    public $cart = [];

    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
{
    $cartData = session()->get('cart', []);
    $productIds = collect($cartData)->pluck('id')->unique()->toArray();

    $products = Product::whereIn('id', $productIds)->with('brand')->get();

    $this->cart = collect($cartData)->map(function ($item, $cartKey) use ($products) {
        $product = $products->where('id', $item['id'])->first();

        if (!$product) return null; // Handle missing products

        return (object) [
            'cart_key' => $cartKey,
            'id' => $product->id,
            'sku_id' => $item['sku_id'] ?? 'N/A', // Handle missing SKU ID
            'name' => $product->name,
            'description' => $product->description,
            'sku_image' => $item['sku_image'] ?? $product->product_image,
            'status' => $product->status,
            'category_id' => $product->category_id,
            'brand' => $product->brand,
            'price' => $item['price'] ?? $product->price,
            'quantity' => $item['quantity'] ?? 1,
            // Ensure these keys exist before assigning them
            'selected_color' => $item['selected_color'] ?? null,
            'selected_size' => $item['selected_size'] ?? null,
        ];
    })->filter();

    $this->dispatch('cartCountUpdated', $this->cart->sum('quantity'));
}


    public function removeFromCart($cartKey)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            unset($cart[$cartKey]);
            session()->put('cart', $cart);
            session()->save();
        }

        $this->loadCart();
    }

    public function increaseQuantity($cartKey)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity']++;
            session()->put('cart', $cart);
            session()->save();
        }

        $this->loadCart();
    }

    public function decreaseQuantity($cartKey)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey]) && $cart[$cartKey]['quantity'] > 1) {
            $cart[$cartKey]['quantity']--;
            session()->put('cart', $cart);
            session()->save();
        }

        $this->loadCart();
    }

    public function render()
    {
        return view('livewire.cart', [
            'cart' => $this->cart,
        ]);
    }
}
