<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use Illuminate\Support\Facades\Session;

class CheckOut extends Component
{
    public $cart = [];

    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        // Load only selected items for checkout
        $this->cart = collect(session()->get('checkout_cart', []))->map(function ($item, $cartKey) {
            $product = Product::find($item->id);
            return $product ? (object) [
                'cart_key' => $cartKey,
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'image' => $item->sku_image ?? $product->product_image,
                'price' => $item->price ?? $product->price,
                'quantity' => $item->quantity ?? 1,
                'selected_color' => $item->selected_color ?? null,
                'selected_size' => $item->selected_size ?? null,
            ] : null;
        })->filter()->values();
    }

    public function removeFromCart($cartKey)
    {
        $cart = session()->get('checkout_cart', []);
        unset($cart[$cartKey]);
        session()->put('checkout_cart', $cart);
        $this->loadCart();
        $this->dispatch('cartUpdated');
    }

    public function increaseQuantity($cartKey)
    {
        $cart = session()->get('checkout_cart', []);
        if (isset($cart[$cartKey])) {
            $cart[$cartKey]->quantity++;
            session()->put('checkout_cart', $cart);
        }
        $this->loadCart();
    }

    public function decreaseQuantity($cartKey)
    {
        $cart = session()->get('checkout_cart', []);
        if (isset($cart[$cartKey]) && $cart[$cartKey]->quantity > 1) {
            $cart[$cartKey]->quantity--;
            session()->put('checkout_cart', $cart);
        }
        $this->loadCart();
    }

    public function render()
    {
        return view('livewire.check-out', [
            'cart' => $this->cart,
            'total' => $this->cart->sum(fn($item) => $item->price * $item->quantity),
        ]);
    }
}
