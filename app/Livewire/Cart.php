<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;

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
        $this->cart = Product::whereIn('id', array_keys($cartData))->with('brand')->get();

        // Emit an event with the updated cart count
        $this->dispatch('cartCountUpdated', count($cartData));
    }

    public function removeFromCart($productId)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
            session()->save();
        }

        // Reload the cart after removing an item
        $this->loadCart();
    }

    public function render()
    {
        return view('livewire.cart');
    }
}
