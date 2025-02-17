<div class="container mx-auto px-4 py-12 mt-12 min-h-screen">
    <!-- Title Section -->
    <div class="text-center mb-12">
        <h1 class="text-5xl font-extrabold text-gray-900 tracking-tight leading-tight">Your Shopping Cart</h1>
    </div>

    <!-- Cart Items Section -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10 mb-12">
        @foreach($cart as $productId => $item)
        <div class="bg-gradient-to-t from-indigo-50 via-white to-indigo-200 rounded-xl shadow-2xl overflow-hidden flex flex-col hover:scale-105 hover:shadow-xl hover:bg-indigo-100 transition-all duration-300 ease-in-out relative">
            <!-- Brand Label -->
            <div class="absolute top-4 left-4 bg-black bg-opacity-60 text-white px-4 py-2 rounded-md text-sm font-semibold" style="z-index: 20;">
                {{ $item['brand']['name'] ?? 'No Brand' }}
            </div>

            <!-- Product Image -->
            <img src="https://picsum.photos/400/300?random=" alt="" class="w-full h-60 object-cover rounded-t-xl">

            <!-- Product Details -->
            <div class="p-6 flex-1">
                <h2 class="text-2xl font-semibold text-gray-800 mb-2">{{ $item['name'] }}</h2>
                <p class="text-gray-600 text-sm mb-4">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore.</p>

                <!-- Price and Quantity -->
                <div class="flex justify-between items-center mt-4">
                    <span class="text-xl font-bold text-indigo-700">${{ rand(50, 200) }}</span>

                    <!-- Quantity Selector -->
                    <div class="flex items-center space-x-2">
                        <button class="px-3 py-2 text-xl bg-gray-100 rounded-full hover:bg-indigo-200 transition duration-200 ease-in-out transform hover:scale-110">-</button>
                        <span class="text-xl font-medium">1</span>
                        <button class="px-3 py-2 text-xl bg-gray-100 rounded-full hover:bg-indigo-200 transition duration-200 ease-in-out transform hover:scale-110">+</button>
                    </div>
                </div>
            </div>

            <!-- Remove Button -->
            <div class="p-4 border-t border-gray-200">
                <button class="w-full text-red-600 hover:text-red-700 text-sm font-semibold flex items-center justify-center space-x-2 transition duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span>Remove</span>
                </button>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Cart Summary Section -->
   <div class="flex bg-gradient-to-r from-indigo-100 to-indigo-300 p-8 rounded-xl shadow-xl transition-all duration-500 ease-in-out mb-12">
    <!-- Cart Total on the Left Side -->
    <div class="flex flex-col items-start mb-6 mr-auto">
        <h3 class="text-xl font-semibold text-gray-800">Cart Total</h3>
        <p class="text-3xl font-extrabold text-indigo-700">${{ rand(150, 500) }}</p>
    </div>

    <!-- Checkout Button on the Right Side -->
    <div class="flex justify-end">
        <button class="px-8 py-4 bg-indigo-600 text-white font-semibold rounded-full hover:bg-indigo-700 transition-all duration-300 ease-in-out transform hover:scale-105">
            Proceed to Checkout
        </button>
    </div>
</div>


    <!-- Empty Cart Message -->
    <div class="mt-12 text-center" x-show="cartItems.length === 0">
        <p class="text-gray-600 text-lg mb-4">Your cart is empty. Start shopping!</p>
        <button class="px-8 py-4 bg-indigo-600 text-white font-semibold rounded-full hover:bg-indigo-700 transition-all duration-300 ease-in-out transform hover:scale-105">
            Continue Shopping
        </button>
    </div>
</div>
