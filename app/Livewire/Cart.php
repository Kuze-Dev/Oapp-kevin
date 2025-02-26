<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use App\Models\ProductSKU;
use App\Models\ProductAttribute;
use App\Models\Cart as CartModel;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductAttributeValues;

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
    if (Auth::check()) {
        // Initialize an empty array for cart data
        $cartData = [];

        // Load cart from database for authenticated users
        $cartItems = CartModel::where('user_id', Auth::id())->get();

        foreach ($cartItems as $cartItem) {
            $sku = ProductSKU::find($cartItem->sku_id);

            if (!$sku) continue;

            // Parse the SKU attributes to get color and size
            $skuAttributes = json_decode($sku->attributes, true);
            $color = null;
            $size = null;

            // Extract color and size from SKU attributes
            if (isset($skuAttributes['attribute1']) && isset($skuAttributes['attribute1']['value'])) {
                $color = $skuAttributes['attribute1']['value'];
            }

            if (isset($skuAttributes['attribute2']) && isset($skuAttributes['attribute2']['value'])) {
                $size = $skuAttributes['attribute2']['value'];
            }

            $cartData[] = [
                'id' => $cartItem->product_id,
                'sku_id' => $cartItem->sku_id,
                'quantity' => $cartItem->quantity,
                'price' => $sku->price,
                'sku_image' => $sku->sku_image,
                'selected_color' => $color,
                'selected_size' => $size,
                'timestamp' => $cartItem->created_at->timestamp,
            ];
        }
    } else {
        // Load cart from session for guest users
        $cartData = session()->get('cart', []);

        // For session-based cart, we also need to parse SKU attributes for each item
        foreach ($cartData as $key => $item) {
            if (isset($item['sku_id'])) {
                $sku = ProductSKU::find($item['sku_id']);
                if ($sku) {
                    $skuAttributes = json_decode($sku->attributes, true);

                    // Extract color and size from SKU attributes
                    if (isset($skuAttributes['attribute1']) && isset($skuAttributes['attribute1']['value'])) {
                        $cartData[$key]['selected_color'] = $skuAttributes['attribute1']['value'];
                    }

                    if (isset($skuAttributes['attribute2']) && isset($skuAttributes['attribute2']['value'])) {
                        $cartData[$key]['selected_size'] = $skuAttributes['attribute2']['value'];
                    }
                }
            }
        }
    }

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
    $this->updatedSelectedItems();
}
    public function removeFromCart($cartKey)
    {
        if (Auth::check()) {
            // For authenticated users, remove from database
            $cartItem = $this->cart->where('cart_key', $cartKey)->first();
            if ($cartItem) {
                CartModel::where('user_id', Auth::id())
                    ->where('sku_id', $cartItem->sku_id)
                    ->delete();
            }
        } else {
            // For guests, remove from session
            $cart = session()->get('cart', []);
            $itemExists = isset($cart[$cartKey]);

            if ($itemExists) {
                unset($cart[$cartKey]);
                session()->put('cart', $cart);
                session()->save();
            }
        }

        $this->loadCart();
        $this->dispatch('cartCountUpdated', count($this->cart));
        $this->dispatch('cartUpdated');

        $this->dispatch('showToast', [
            'message' => 'Removed from Cart!',
            'type' => 'success',
        ]);
    }

    public function removeSelectedFromCart()
    {
        if (Auth::check()) {
            // For authenticated users, remove from database
            foreach ($this->selectedItems as $cartKey) {
                $cartItem = $this->cart->where('cart_key', $cartKey)->first();
                if ($cartItem) {
                    CartModel::where('user_id', Auth::id())
                        ->where('sku_id', $cartItem->sku_id)
                        ->delete();
                }
            }
        } else {
            // For guests, remove from session
            $cart = session()->get('cart', []);
            foreach ($this->selectedItems as $cartKey) {
                unset($cart[$cartKey]);
            }

            session()->put('cart', $cart);
            session()->save();
        }

        $this->loadCart();
        $this->selectedItems = [];
        $this->selectAll = false;

        $this->dispatch('cartCountUpdated', count($this->cart));
        $this->dispatch('cartUpdated');
        $this->dispatch('showToast', ['message' => 'Selected items removed from Cart!', 'type' => 'success']);
    }

    public function updatedSelectAll($value)
    {
        if ($value)
            $this->selectedItems = collect($this->cart)->pluck('cart_key')->toArray();
        else {
            $this->selectedItems = [];
        }
        $this->updatedSelectedItems();
    }

    public function updatedSelectedItems()
    {
        $this->selectAll = !empty($this->cart) && count($this->selectedItems) === count($this->cart);
        $this->selectedItems = !empty($this->selectedItems) ? collect($this->cart)->whereIn('cart_key', $this->selectedItems)->pluck('cart_key')->toArray() : [];
    }

    public function proceedToCheckout()
{
    if (!Auth::check()) {
        return redirect()->route('register')->with('message', 'Please register or login to proceed to checkout.');
    }

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
        if (Auth::check()) {
            // For authenticated users, update in database
            $cartItem = $this->cart->where('cart_key', $cartKey)->first();
            if ($cartItem) {
                CartModel::where('user_id', Auth::id())
                    ->where('sku_id', $cartItem->sku_id)
                    ->increment('quantity');
            }
        } else {
            // For guests, update in session
            $cart = session()->get('cart', []);

            if (isset($cart[$cartKey])) {
                $cart[$cartKey]['quantity']++;
                session()->put('cart', $cart);
                session()->save();
            }
        }

        $this->loadCart();
        $this->dispatch('cartUpdated');
    }

    public function decreaseQuantity($cartKey)
    {
        if (Auth::check()) {
            // For authenticated users, update in database
            $cartItem = $this->cart->where('cart_key', $cartKey)->first();
            if ($cartItem && $cartItem->quantity > 1) {
                CartModel::where('user_id', Auth::id())
                    ->where('sku_id', $cartItem->sku_id)
                    ->decrement('quantity');
            }
        } else {
            // For guests, update in session
            $cart = session()->get('cart', []);

            if (isset($cart[$cartKey]) && $cart[$cartKey]['quantity'] > 1) {
                $cart[$cartKey]['quantity']--;
                session()->put('cart', $cart);
                session()->save();
            }
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
