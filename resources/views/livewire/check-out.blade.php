<div class="container mx-auto px-4 py-12 mt-12 min-h-screen flex justify-center items-center bg-gray-50">
    <div class="max-w-6xl w-full bg-white shadow-lg rounded-2xl overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-700 to-purple-600 text-white p-6">
            <h2 class="text-3xl font-bold">Complete Your Purchase</h2>
            <p class="text-indigo-100 mt-1">You're just a few steps away from your order</p>

            <!-- Order Stats -->
            <div class="flex items-center gap-4 mt-4 text-sm">
                <div class="bg-white/20 rounded-lg px-3 py-1 flex items-center">
                    <span>Items in cart: {{ $cartCount }}</span>
                </div>
                <div class="bg-white/20 rounded-lg px-3 py-1 flex items-center">
                    <span>Selected for checkout: {{ $selectedCount }}</span>
                </div>
            </div>
        </div>

        <div class="p-8 flex flex-col lg:flex-row gap-8">
            <!-- Order Summary (Left Side) -->
            <div class="lg:w-2/3">
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="border-b border-gray-200 p-6 flex justify-between items-center">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                            🛒 Order Summary
                        </h3>
                        @if(count($cart) > 0)
    <div class="flex items-center gap-3">
        <label class="flex items-center cursor-pointer">
            <input type="checkbox" wire:model.live="selectAll" class="form-checkbox h-5 w-5 text-indigo-600 rounded">
            <span class="ml-2 text-sm">Select All</span>
        </label>

        @if(!empty($selectedItems))
            <button
                wire:click="removeSelectedItems"
                class="py-2 px-3 bg-red-100 text-red-600 text-sm font-medium rounded-lg hover:bg-red-200 transition-colors flex items-center"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M6 8a1 1 0 011-1h6a1 1 0 011 1v7a1 1 0 01-1 1H7a1 1 0 01-1-1V8zm3-4a1 1 0 00-1 1v1H5v2h10V6h-3V5a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </button>
        @endif
    </div>
@endif


                    </div>

                    <div class="divide-y divide-gray-200">
                        @if(count($cart) > 0)
                            @foreach($cart as $item)
                            <div class="p-6 flex justify-between items-center hover:bg-gray-50 transition-colors">
                                <div class="flex items-center space-x-4">
                                    <div>
                                        <input
                                            type="checkbox"
                                            wire:model.live="selectedItems" value="{{ $item->cart_key }}"
                                            class="form-checkbox h-5 w-5 text-indigo-600 rounded"
                                        >
                                    </div>
                                    <div class="relative">
                                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="w-20 h-20 object-cover rounded-lg border border-gray-200">
                                        <span class="absolute -top-2 -right-2 bg-indigo-500 text-white text-xs font-bold rounded-full h-6 w-6 flex items-center justify-center">{{ $item->quantity }}</span>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-medium text-gray-800">{{ $item->name }}</h4>
                                        <p class="text-gray-600 text-sm">{{ $item->description }}</p>
                                        <div class="flex gap-3 text-sm text-gray-500 mt-1">
                                            @if($item->selected_color)
                                                <span class="inline-flex items-center">
                                                    <span class="w-3 h-3 rounded-full mr-1" style="background-color: {{ $item->selected_color }};"></span>
                                                    {{ $item->selected_color }}
                                                </span>
                                            @endif
                                            @if($item->selected_size)
                                                <span>Size: {{ $item->selected_size }}</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-2 mt-3">
                                            <button wire:click="decreaseQuantity('{{ $item->cart_key }}')" class="px-3 py-1 bg-gray-200 rounded-full hover:bg-gray-300 transition-colors">-</button>
                                            <span class="text-lg font-medium">{{ $item->quantity }}</span>
                                            <button wire:click="increaseQuantity('{{ $item->cart_key }}')" class="px-3 py-1 bg-gray-200 rounded-full hover:bg-gray-300 transition-colors">+</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-lg font-semibold text-indigo-600">PHP {{ number_format($item->price * $item->quantity, 2) }}</span>
                                    <button wire:click="removeFromCheckOut('{{ $item->cart_key }}')" class="text-red-500 text-sm mt-2 hover:text-red-700 transition-colors">Remove</button>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="p-6 text-center">
                                <p class="text-gray-500">Your cart is empty</p>
                                <a href="{{ route('shop') }}" class="mt-2 inline-block text-indigo-600 hover:underline">
                                    Continue Shopping
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Shipping Options -->
                    <div class="p-6 border-t border-gray-200">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Shipping Method</h4>
                        <div class="space-y-3">
                            @foreach($shipping as $method => $price)
                            <label class="flex items-center justify-between p-4 border rounded-lg cursor-pointer transition-all duration-200 hover:border-indigo-500 {{ $selectedShipping === $method ? 'border-indigo-500 bg-indigo-50' : '' }}">
                                <div class="flex items-center">
                                    <input type="radio" wire:model.live="selectedShipping" value="{{ $method }}" class="form-radio h-5 w-5 text-indigo-600">
                                    <div class="ml-3">
                                        <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $method)) }}</span>
                                        @if($method == 'standard')
                                            <p class="text-sm text-gray-500">Delivery in 3-5 business days</p>
                                        @elseif($method == 'express')
                                            <p class="text-sm text-gray-500">Delivery in 1-2 business days</p>
                                        @else
                                            <p class="text-sm text-gray-500">Delivery today (order before 2PM)</p>
                                        @endif
                                    </div>
                                </div>
                                <span class="text-indigo-600 font-semibold">PHP {{ number_format($price, 2) }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="p-6 bg-gray-50 border-t border-gray-200 rounded-b-xl">
                        <div class="flex justify-between text-gray-600 mb-2">
                            <span>Subtotal</span>
                            <span>PHP {{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600 mb-2">
                            <span>Shipping</span>
                            <span>PHP {{ number_format($shippingFee, 2) }}</span>
                        </div>

                        <div class="flex justify-between font-bold text-xl mt-4 border-t border-gray-200 pt-4">
                            <span>Total</span>
                            <span class="text-indigo-700">PHP {{ number_format($total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Billing & Payment (Right Side) -->
            <div class="lg:w-1/3">
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 sticky top-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Billing Information</h3>
                    <form wire:submit.prevent="confirmPayment" class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium">Full Name</label>
                            <input type="text" id="name" wire:model="billing.name" class="p-3 border rounded-lg w-full focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('billing.name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium">Email Address</label>
                            <input type="email" id="email" wire:model="billing.email" class="p-3 border rounded-lg w-full focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('billing.email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="address" class="block text-sm font-medium">Delivery Address</label>
                            <input type="text" id="address" wire:model="billing.address" class="p-3 border rounded-lg w-full focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('billing.address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="city" class="block text-sm font-medium">City</label>
                                <input type="text" id="city" wire:model="billing.city" class="p-3 border rounded-lg w-full focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                @error('billing.city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="state" class="block text-sm font-medium">State/Province</label>
                                <input type="text" id="state" wire:model="billing.state" class="p-3 border rounded-lg w-full focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                @error('billing.state') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="zip_code" class="block text-sm font-medium">Zip/Postal Code</label>
                                <input type="text" id="zip_code" wire:model="billing.zip_code" class="p-3 border rounded-lg w-full focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                @error('billing.zip_code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="country" class="block text-sm font-medium">Country</label>
                                <select id="country" wire:model="billing.country" class="p-3 border rounded-lg w-full focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="Philippines">Philippines</option>
                                    <option value="Malaysia">Malaysia</option>
                                    <option value="Singapore">Singapore</option>
                                    <option value="Indonesia">Indonesia</option>
                                    <option value="Thailand">Thailand</option>
                                </select>
                                @error('billing.country') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium">Phone Number</label>
                            <input type="text" id="phone" wire:model="billing.phone" class="p-3 border rounded-lg w-full focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('billing.phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium">Special Instructions (Optional)</label>
                            <textarea id="notes" wire:model="billing.notes" rows="2" class="p-3 border rounded-lg w-full focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>

                        <h3 class="text-lg font-semibold text-gray-900 mt-6">Payment Method</h3>
                    <!-- Payment Method Selection -->
<div class="space-y-3">
    <!-- Credit Card Option -->
    <label class="flex items-center p-3 border rounded-lg hover:border-indigo-500 cursor-pointer transition-all duration-200 {{ $paymentMethod === 'credit_card' ? 'border-indigo-500 bg-indigo-50' : '' }}">
        <input type="radio" wire:model.live="paymentMethod" value="credit_card" class="form-radio h-5 w-5 text-indigo-600">
        <div class="ml-3">
            <span class="font-medium">Credit Card</span>
            <p class="text-sm text-gray-500">Visa, Mastercard, Amex</p>
        </div>
    </label>

    <!-- E-wallet Option -->
    <label class="flex items-center p-3 border rounded-lg hover:border-indigo-500 cursor-pointer transition-all duration-200 {{ $paymentMethod === 'e_wallet' ? 'border-indigo-500 bg-indigo-50' : '' }}">
        <input type="radio" wire:model.live="paymentMethod" value="e_wallet" class="form-radio h-5 w-5 text-indigo-600">
        <div class="ml-3">
            <span class="font-medium">E-wallet</span>
            <p class="text-sm text-gray-500">Fast and secure electronic payment</p>
        </div>
    </label>

    <!-- E-wallet options shown when e_wallet is selected -->
    @if($paymentMethod === 'e_wallet')
    <div class="ml-8">
        <!-- E-wallet options in flex side-by-side layout -->
        <div class="flex space-x-3">
            <!-- GCash on the left side -->
            <label class="flex-1 flex items-center p-3 border rounded-lg hover:border-indigo-500 cursor-pointer transition-all duration-200 {{ $selectedEWallet === 'gcash' ? 'border-indigo-500 bg-indigo-50' : '' }}">
                <input type="radio" wire:model.live="selectedEWallet" value="gcash" class="form-radio h-5 w-5 text-indigo-600">
                <div class="ml-3">
                    <span class="font-medium">GCash</span>
                    <p class="text-sm text-gray-500">Philippines e-wallet</p>
                </div>
            </label>

            <!-- PayMaya on the right side -->
            <label class="flex-1 flex items-center p-3 border rounded-lg hover:border-indigo-500 cursor-pointer transition-all duration-200 {{ $selectedEWallet === 'paymaya' ? 'border-indigo-500 bg-indigo-50' : '' }}">
                <input type="radio" wire:model.live="selectedEWallet" value="paymaya" class="form-radio h-5 w-5 text-indigo-600">
                <div class="ml-3">
                    <span class="font-medium">PayMaya</span>
                    <p class="text-sm text-gray-500">Philippines digital payment</p>
                </div>
            </label>
        </div>
    </div>
    @endif

    <!-- Cash on Delivery Option -->
    <label class="flex items-center p-3 border rounded-lg hover:border-indigo-500 cursor-pointer transition-all duration-200 {{ $paymentMethod === 'cash_on_delivery' ? 'border-indigo-500 bg-indigo-50' : '' }}">
        <input type="radio" wire:model.live="paymentMethod" value="cash_on_delivery" class="form-radio h-5 w-5 text-indigo-600">
        <div class="ml-3">
            <span class="font-medium">Cash on Delivery</span>
            <p class="text-sm text-gray-500">Pay when you receive your order</p>
        </div>
    </label>
</div>

                        @error('selectedItems')
                            <div class="bg-red-50 border-l-4 border-red-500 p-4 mt-4">
                                <div class="flex">
                                    <div class="ml-3">
                                        <p class="text-sm text-red-700">{{ $message }}</p>
                                    </div>
                                </div>
                            </div>
                        @enderror

                        <button
                            type="button"
                            wire:click="placeOrder"
                            class="w-full py-3 mt-6 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors relative overflow-hidden disabled:opacity-50 disabled:cursor-not-allowed"
                            @if(empty($selectedItems)) disabled @endif
                        >
                            <span class="relative z-10">
                                Confirm & Pay {{ !empty($selectedItems) ? '(' . count($selectedItems) . ' items)' : '' }}
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
