<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\Product;
use Livewire\Component;
use App\Models\ProductSKU;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\PaymentController;

class CheckOut extends Component
{
    public $cart = [];
    public $shipping = [
        'standard' => 150.00,
        'express' => 250.00,
        'same_day' => 350.00
    ];
    public $selectedShipping = 'standard';
    public $billing = [
        'name' => '',
        'email' => '',
        'address' => '',
        'city' => '',
        'state' => '',
        'zip_code' => '',
        'country' => 'Philippines',
        'phone' => '',
        'notes' => ''
    ];
    public $paymentMethod = 'credit_card';
    public $selectedEWallet = 'gcash'; // Default e-wallet option
    public $selectedItems = [];
    public $selectAll = false;

    public function mount()
    {
        $this->loadCart();
        // Initialize selectedItems as empty
        $this->selectedItems = $this->selectedItems ?? []; // Ensure it's an array
    }

    public function loadCart()
{
    $checkoutCart = session()->get('checkout_cart', []);

    if (empty($checkoutCart)) {
        $this->cart = collect([]);
        return;
    }

    $this->cart = collect($checkoutCart)->map(function ($item, $cartKey) {
        // Ensure we preserve the SKU ID from the cart item
        $skuId = $item->sku_id ?? ($item['sku_id'] ?? null);

        // Get product details
        $product = Product::find($item->id ?? $item['id'] ?? null);

        return $product ? (object) [
            'cart_key' => $cartKey,
            'id' => $product->id,
            'sku_id' => $skuId, // Keep the original SKU ID
            'name' => $product->name,
            'description' => $product->description,
            'image' => $item->sku_image ?? $item['sku_image'] ?? $product->product_image,
            'price' => $item->price ?? $item['price'] ?? $product->price,
            'quantity' => $item->quantity ?? $item['quantity'] ?? 1,
            'selected_color' => $item->selected_color ?? $item['selected_color'] ?? null,
            'selected_size' => $item->selected_size ?? $item['selected_size'] ?? null,
        ] : null;
    })->filter()->values();
}

    public function removeFromCheckOut($cartKey)
    {
        if (Auth::check()) {
            // Find the cart item based on cartKey, ensuring it's a valid product in the database
            $cartItem = $this->cart->where('cart_key', $cartKey)->first();

            if ($cartItem) {
                // Remove from the database using the actual product ID
                Cart::where('user_id', Auth::id())
                    ->where('product_id', $cartItem->id) // Use 'product_id' instead of 'sku_id' if applicable
                    ->delete();
            }
        }

        // Remove from session-based cart (for guests)
        $cart = session()->get('checkout_cart', []);

        if (isset($cart[$cartKey])) {
            unset($cart[$cartKey]);
            session()->put('checkout_cart', $cart);
            session()->save(); // Ensure session updates
        }

        // Reload the cart to reflect changes
        $this->loadCart();

        // Dispatch event to update frontend
        $this->dispatch('cartCountUpdated', count($this->cart));

        $this->dispatch('cartUpdated');

        // Provide user feedback
        $this->dispatch('showToast', [
            'message' => 'Item removed from checkout!',
            'type' => 'success',
        ]);
    }

    public function removeSelectedItems()
    {
        if (empty($this->selectedItems)) {
            return;
        }

        if (Auth::check()) {
            // Get product IDs of selected items
            $selectedProductIds = collect($this->cart)
                ->whereIn('cart_key', $this->selectedItems)
                ->pluck('id')
                ->toArray();

            // Remove selected items from the database
            Cart::where('user_id', Auth::id())
                ->whereIn('product_id', $selectedProductIds)
                ->delete();
        }

        // Remove selected items from session
        $cart = session()->get('checkout_cart', []);

        foreach ($this->selectedItems as $cartKey) {
            unset($cart[$cartKey]);
        }

        session()->put('checkout_cart', $cart);
        session()->save(); // Ensure session updates

        // Reset selected items
        $this->selectedItems = [];
        $this->selectAll = false;

        // Reload the cart
        $this->loadCart();

        // Dispatch event to update UI
        $this->dispatch('cartUpdated');
        $this->dispatch('cartCountUpdated', count($this->cart));

        // Provide user feedback
        $this->dispatch('showToast', [
            'message' => 'Selected items removed from checkout!',
            'type' => 'success',
        ]);
    }

    public function updatedSelectedItems()
    {
        $this->selectAll = !empty($this->cart) && count($this->selectedItems) === count($this->cart);
        $this->selectedItems = !empty($this->selectedItems) ? collect($this->cart)->whereIn('cart_key', $this->selectedItems)->pluck('cart_key')->toArray() : [];
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

    public function updatedPaymentMethod()
    {
        // If e-wallet is selected, ensure a default e-wallet option is set
        if ($this->paymentMethod === 'e_wallet' && empty($this->selectedEWallet)) {
            $this->selectedEWallet = 'gcash';
        }
    }

    public function increaseQuantity($cartKey)
    {
        $cart = session()->get('checkout_cart', []);
        if (isset($cart[$cartKey])) {
            $cart[$cartKey]->quantity++;
            session()->put('checkout_cart', $cart);
        }
        $this->loadCart();
    }

    public function decreaseQuantity($cartKey)
    {
        $cart = session()->get('checkout_cart', []);
        if (isset($cart[$cartKey]) && $cart[$cartKey]->quantity > 1) {
            $cart[$cartKey]->quantity--;
            session()->put('checkout_cart', $cart);
        }
        $this->loadCart();
    }

    public function updateShippingMethod()
    {
        // Validate shipping method
        if (!array_key_exists($this->selectedShipping, $this->shipping)) {
            $this->selectedShipping = 'standard';
        }
    }

    public function getCartCountProperty()
    {
        return $this->cart->count();
    }

    public function getSelectedItemsCountProperty()
    {
        return count($this->selectedItems);
    }

    public function getSubtotalProperty()
    {
        if (empty($this->selectedItems)) {
            return 0;
        }

        return $this->cart
            ->filter(fn($item) => in_array($item->cart_key, $this->selectedItems))
            ->sum(fn($item) => $item->price * $item->quantity);
    }

    public function getShippingFeeProperty()
    {
        return $this->shipping[$this->selectedShipping] ?? $this->shipping['standard'];
    }

    public function getTotalProperty()
    {
        return max(0, $this->getSubtotalProperty() + $this->getShippingFeeProperty());
    }

    public function getActualPaymentMethodProperty()
    {
        if ($this->paymentMethod === 'e_wallet') {
            return $this->selectedEWallet;
        }
        return $this->paymentMethod;
    }

    public function placeOrder()
    {
        $this->validate([
            'billing.name' => 'required|min:3',
            'billing.email' => 'required|email',
            'billing.address' => 'required|min:5',
            'billing.city' => 'required',
            'billing.state' => 'required',
            'billing.zip_code' => 'required',
            'billing.country' => 'required',
            'billing.phone' => 'required|min:10',
            'paymentMethod' => 'required|in:credit_card,e_wallet,cash_on_delivery',
            'selectedItems' => 'required|array|min:1',
        ]);

        // Additional validation for e-wallet if selected
        if ($this->paymentMethod === 'e_wallet') {
            $this->validate([
                'selectedEWallet' => 'required|in:gcash,paymaya',
            ]);
        }

        // Determine the actual payment method
        $actualPaymentMethod = $this->paymentMethod === 'e_wallet' ? $this->selectedEWallet : $this->paymentMethod;

        // Save checkout data for order processing
        session()->put('checkout_data', [
            'items' => $this->cart->filter(fn($item) => in_array($item->cart_key, $this->selectedItems))->values(),
            'billing' => $this->billing,
            'shipping_method' => $this->selectedShipping,
            'shipping_fee' => $this->getShippingFeeProperty(),
            'subtotal' => $this->getSubtotalProperty(),
            'total' => $this->getTotalProperty(),
            'payment_method' => $actualPaymentMethod // Use the actual payment method here
        ]);

        // Instead of redirecting, call the processCheckout method directly
        return app(PaymentController::class)->processCheckout(request());
    }
    public function render()
    {
        return view('livewire.check-out', [
            'cart' => $this->cart,
            'subtotal' => $this->getSubtotalProperty(),
            'shippingFee' => $this->getShippingFeeProperty(),
            'total' => $this->getTotalProperty(),
            'cartCount' => $this->getCartCountProperty(),
            'selectedCount' => $this->getSelectedItemsCountProperty(),
        ]);
    }
}
