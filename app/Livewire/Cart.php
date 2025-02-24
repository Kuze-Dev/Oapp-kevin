<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;

class Cart extends Component
{
    public $cart = [];
    public $selectedItems = [];
    public $selectAll = false;

    public function mount()
    {
        $this->selectedItems = $this->selectedItems ?? []; // Ensure it's an array
        $this->loadCart();
    }


    public function loadCart()
    {
        $cartData = session()->get('cart', []);
        $productIds = collect($cartData)->pluck('id')->unique()->toArray();

        $products = Product::whereIn('id', $productIds)->with('brand')->get();

        $this->cart = collect($cartData)->map(function ($item, $cartKey) use ($products) {
            $product = $products->where('id', $item['id'])->first();

            if (!$product) return null;

            return (object) [
                'cart_key' => $cartKey,
                'id' => $product->id,
                'sku_id' => $item['sku_id'] ?? 'N/A',
                'name' => $product->name,
                'description' => $item['description'] ?? $product->description,
                'sku_image' => $item['sku_image'] ?? $product->product_image,
                'status' => $product->status,
                'category_id' => $product->category_id,
                'brand' => $product->brand,
                'price' => $item['price'] ?? $product->price,
                'quantity' => $item['quantity'] ?? 1,
                'selected_color' => $item['selected_color'] ?? null,
                'selected_size' => $item['selected_size'] ?? null,
                'timestamp' => $item['timestamp'] ?? now()->timestamp,
            ];
        })->filter();

        $this->cart = $this->cart->sortByDesc('timestamp')->values();

        $this->updateSelectionState();
    }

    public function removeFromCart($cartKey)
    {
        $cart = session()->get('cart', []);
        $itemExists = isset($cart[$cartKey]);

        if ($itemExists) {
            unset($cart[$cartKey]);
            session()->put('cart', $cart);
            session()->save();
        }

        $this->loadCart();
        $this->dispatch('cartCountUpdated', count($cart));
        $this->dispatch('cartUpdated');

        $this->dispatch('showToast', [
            'message' => $itemExists ? 'Removed from Cart!' : 'Failed to Remove from Cart!',
            'type' => $itemExists ? 'success' : 'error',
        ]);
    }

    public function removeSelectedFromCart()
{
    $cart = session()->get('cart', []);
    foreach ($this->selectedItems as $cartKey) {
        unset($cart[$cartKey]);
    }

    session()->put('cart', $cart);
    session()->save();

    $this->loadCart();
    $this->selectedItems = [];
    $this->selectAll = false;

    $this->dispatch('cartCountUpdated', count($cart));
    $this->dispatch('cartUpdated');
    $this->dispatch('showToast', ['message' => 'Selected items removed from Cart!', 'type' => 'success']);
}


public function toggleSelectAll()
{
    if ($this->selectAll) {
        // If "Select All" is checked, select all cart items
        $this->selectedItems = $this->cart->pluck('cart_key')->toArray();
    } else {
        // If "Select All" is unchecked, deselect all items
        $this->selectedItems = [];
    }
}


    public function toggleSelection($cartKey)
    {
        if (in_array($cartKey, $this->selectedItems)) {
            $this->selectedItems = array_values(array_diff($this->selectedItems, [$cartKey]));
        } else {
            $this->selectedItems[] = $cartKey;
        }

        $this->updateSelectionState();
    }

    private function updateSelectionState()
    {
        if (empty($this->cart)) {
            $this->selectAll = false;
            $this->selectedItems = [];
            return;
        }

        $allCartKeys = $this->cart->pluck('cart_key')->toArray();
        $this->selectAll = !empty($allCartKeys) && count($this->selectedItems) === count($allCartKeys) &&
            empty(array_diff($allCartKeys, $this->selectedItems));
    }






    public function proceedToCheckout()
    {
        if (empty($this->selectedItems)) {
            $this->dispatch('showToast', [
                'message' => 'Please select at least one item to proceed.',
                'type' => 'error',
            ]);
            return;
        }

        $selectedCartItems = collect($this->cart)->filter(fn($item) => in_array($item->cart_key, $this->selectedItems));

        session()->put('checkout_cart', $selectedCartItems);
        session()->save();

        return redirect()->route('checkout');
    }

    public function increaseQuantity($cartKey)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity']++;
            session()->put('cart', $cart);
            session()->save();
        }

        $this->loadCart();
        $this->dispatch('cartUpdated');
    }

    public function decreaseQuantity($cartKey)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey]) && $cart[$cartKey]['quantity'] > 1) {
            $cart[$cartKey]['quantity']--;
            session()->put('cart', $cart);
            session()->save();
        }

        $this->loadCart();
        $this->dispatch('cartUpdated');
    }

    public function render()
    {
        return view('livewire.cart', [
            'cart' => $this->cart,
        ]);
    }
}
