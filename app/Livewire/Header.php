<?php

namespace App\Livewire;

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
        $this->cartCount = count(session()->get('cart', []));
        $this->checkAuth();
    }

    public function checkAuth()
    {
        $this->isLoggedIn = Auth::check();
        if ($this->isLoggedIn) {
            $this->userName = Auth::user()->name;
        }
    }

    public function updateCartCount($count)
    {
        $this->cartCount = $count;
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        $this->checkAuth();
        return redirect()->route('home');
    }

    public function render()
    {
        return view('livewire.header');
    }
}
