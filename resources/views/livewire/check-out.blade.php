<div class="container mx-auto px-4 py-12 mt-12 min-h-screen flex justify-center items-center bg-gray-100">
    <div class="max-w-4xl w-full bg-white shadow-2xl rounded-2xl p-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Checkout</h2>

        <!-- Order Summary -->
        <div class="bg-gray-100 p-6 rounded-2xl mb-8">
            <h3 class="text-xl font-semibold mb-4">Order Summary</h3>
            <div class="space-y-6 divide-y divide-gray-300">
                @foreach($cart as $item)
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center space-x-4">
                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="w-20 h-20 object-cover rounded-lg shadow-md">
                        <div>
                            <h4 class="text-lg font-medium text-gray-800">{{ $item->name }}</h4>
                            <p class="text-sm text-gray-500">Color: {{ $item->selected_color ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">Size: {{ $item->selected_size ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">Qty: {{ $item->quantity }}</p>
                        </div>
                    </div>
                    <span class="text-lg font-semibold text-indigo-600">PHP {{ number_format($item->price * $item->quantity, 2) }}</span>
                </div>
                @endforeach
            </div>
            <div class="flex justify-between items-center font-bold text-xl mt-6 border-t pt-4">
                <span>Total:</span>
                <span class="text-indigo-700">PHP {{ number_format($total, 2) }}</span>
            </div>
        </div>

        <!-- Billing Details -->
        <div class="bg-white p-6 rounded-2xl shadow-md">
            <h3 class="text-xl font-semibold mb-4">Billing Information</h3>
            <form wire:submit.prevent="placeOrder" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="text" wire:model="billing.name" placeholder="Full Name" class="p-3 border rounded-lg w-full focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <input type="email" wire:model="billing.email" placeholder="Email Address" class="p-3 border rounded-lg w-full focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="text" wire:model="billing.address" placeholder="Address" class="p-3 border rounded-lg w-full focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <input type="text" wire:model="billing.phone" placeholder="Phone Number" class="p-3 border rounded-lg w-full focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <textarea wire:model="billing.notes" placeholder="Additional Notes" class="p-3 border rounded-lg w-full focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition ease-in-out duration-300 shadow-lg">Place Order</button>
            </form>
        </div>
    </div>
</div>




