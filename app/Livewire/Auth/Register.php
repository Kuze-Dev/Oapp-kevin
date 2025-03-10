<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Cart as CartModel;

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
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        Auth::login($user);

        $this->transferGuestCartToUser();

        session()->flash('message', 'Registration successful!');
        return redirect()->route('home');
    }

    private function transferGuestCartToUser()
    {
        if (session()->has('cart')) {
            $cart = session()->get('cart');

            foreach ($cart as $item) {
                CartModel::updateOrCreate(
                    [
                        'user_id' => Auth::id(),
                        'product_id' => $item['id'],
                        'sku_id' => $item['sku_id'],
                    ],
                    [
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]
                );
            }

            session()->forget('cart');
        }
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
