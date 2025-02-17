<?php

namespace App\Livewire;

use App\Models\Product; // Make sure to import the Product model
use Livewire\Component;

class Cart extends Component
{
    public $cart = [];

    protected $listeners = ['cartUpdated' => 'loadCart'];

    public function mount()
    {
        $this->loadCart();
        // dd( $this->cart );
    }

    public function loadCart()
    {
        // Assuming the cart contains product IDs as keys, and quantities as values
        $cartData = session()->get('cart', []);

        // Retrieve the actual products along with their brands
        $this->cart = Product::whereIn('id', array_keys($cartData))
                             ->with('brand')  // Load the brand relation
                             ->get();
    }

    public function removeFromCart($productId)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
        }

        session()->put('cart', $cart);
        session()->save();

        $this->loadCart();
    }

    public function render()
    {
        return view('livewire.cart', ['cart' => $this->cart]);
    }
}
