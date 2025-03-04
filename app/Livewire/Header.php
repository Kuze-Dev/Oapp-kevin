<?php

namespace App\Livewire;

use App\Models\Cart;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Header extends Component
{
    public $cartCount = 0;
    public $isLoggedIn = false;
    public $userName = '';

    protected $listeners = [
        'cartCountUpdated' => 'updateCartCount',
        'checkAuth' => 'checkAuth'
    ];

    public function mount()
    {
        $this->checkAuth(); // Check authentication first

        if ($this->isLoggedIn) {
            // Get cart count from the database for authenticated users
            $this->cartCount = Cart::where('user_id', Auth::id())->count();
        } else {
            // Get cart count from session for guest users
            $this->cartCount = count(session()->get('cart', []));
        }
    }

    public function checkAuth()
    {
        $this->isLoggedIn = Auth::check();
        if ($this->isLoggedIn) {
            $this->userName = Auth::user()->name;
        }
    }

    public function updateCartCount()
    {
        if ($this->isLoggedIn) {
            $this->cartCount = \App\Models\Cart::where('user_id', Auth::id())->count();
        } else {
            $this->cartCount = count(session()->get('cart', []));
        }
    }

    public function logout()
    {
        Auth::logout();

        $this->checkAuth();
        return redirect()->route('home');
    }

    public function render()
    {
        return view('livewire.header');
    }
}
