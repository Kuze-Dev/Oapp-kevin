<?php

namespace App\Livewire;

use session;
use Livewire\Component;

class Cart extends Component
{
    public $cart = [];

    protected $listeners = ['cartUpdated' => 'loadCart'];

    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        $this->cart = session()->get('cart', []);
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
