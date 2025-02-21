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

    // Fetch products with brands for display
    $products = Product::whereIn('id', $productIds)->with('brand')->get();

    // Update the cart with product details
    $this->cart = collect($cartData)->map(function ($item, $cartKey) use ($products) {
        $product = $products->where('id', $item['id'])->first();

        if (!$product) return null; // Handle missing products

        return (object) [
            'cart_key' => $cartKey,
            'id' => $product->id,
            'sku_id' => $item['sku_id'] ?? 'N/A',
            'name' => $product->name,
            'description' => $product->description,
            'sku_image' => $item['sku_image'] ?? $product->product_image,
            'status' => $product->status,
            'category_id' => $product->category_id,
            'brand' => $product->brand,
            'price' => $item['price'] ?? $product->price,
            'quantity' => $item['quantity'] ?? 1,
            'selected_color' => $item['selected_color'] ?? null,
            'selected_size' => $item['selected_size'] ?? null,
            'timestamp' => $item['timestamp'] ?? now()->timestamp, // Ensure each item has a timestamp
        ];
    })->filter();

    // Sort items by timestamp in descending order (newest first)
    $this->cart = $this->cart->sortByDesc('timestamp')->values();
}


    // Remove item from cart
    public function removeFromCart($cartKey)
{
    $cart = session()->get('cart', []);

    // Check if item exists before removing
    $itemExists = isset($cart[$cartKey]);
    // dd($itemExists);

    if ($itemExists) {
        unset($cart[$cartKey]);
        session()->put('cart', $cart);
        session()->save();
    }

    // Reload the cart after removal
    $this->loadCart();
    $this->dispatch('cartCountUpdated', count($cart));
    $this->dispatch('cartUpdated');

    // Show success message if item was removed, otherwise show error
    $this->dispatch('showToast', [
        'message' => $itemExists ? 'Removed from Cart!' : 'Failed to Remove from Cart!',
        'type' => $itemExists ? 'success' : 'error',
    ]);
}


    // Increase item quantity in the cart
    public function increaseQuantity($cartKey)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity']++;
            session()->put('cart', $cart);
            session()->save();
        }

        // Reload the cart after quantity change
        $this->loadCart();
        $this->dispatch('cartUpdated');
    }

    // Decrease item quantity in the cart
    public function decreaseQuantity($cartKey)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey]) && $cart[$cartKey]['quantity'] > 1) {
            $cart[$cartKey]['quantity']--;
            session()->put('cart', $cart);
            session()->save();
        }

        // Reload the cart after quantity change
        $this->loadCart();
        $this->dispatch('cartUpdated');
    }

    public function render()
    {
        return view('livewire.cart', [
            'cart' => $this->cart,
        ]);
    }
}
