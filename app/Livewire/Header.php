<?php

namespace App\Livewire;

use Livewire\Component;

class Header extends Component
{
    public $cartCount = 0;

    protected $listeners = ['cartCountUpdated' => 'updateCartCount'];

    public function mount()
    {
        $this->cartCount = count(session()->get('cart', []));
    }

    public function updateCartCount($count)
    {
        $this->cartCount = $count;
    }

    public function render()
    {
        return view('livewire.header');
    }
}
