<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class Register extends Component
{
    public $name, $email, $password, $password_confirmation;
    public $errorMessage = '';

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|confirmed|min:6',
    ];

    public function register()
    {
        $this->validate();

        // Create the user
        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        session()->flash('message', 'Registration successful! Please login.');
        return redirect()->route('login');  // Redirect to login after successful registration
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
