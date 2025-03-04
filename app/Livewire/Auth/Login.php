<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Login extends Component
{
    public $email, $password;
    public $errorMessage = '';

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:6',
    ];

    public function login()
{
    $this->validate();

    if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
        session()->regenerate();
        $this->dispatch('checkAuth'); // Add this line to notify the header
        return redirect()->route('home');
    }

    $this->errorMessage = 'Invalid credentials. Please try again.';
}





    public function render()
    {
        return view('livewire.auth.login');
    }
}

