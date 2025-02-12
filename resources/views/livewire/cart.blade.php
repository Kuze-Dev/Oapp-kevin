<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 mt-12">
    <!-- Title Section -->
    <h1 class="text-4xl font-extrabold text-gray-900 mb-10 text-center tracking-tight">Shopping Cart</h1>

    <!-- Cart Items Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-8">
    @foreach($cart as $productId => $item)
    <div class="bg-gradient-to-r from-blue-50 via-white to-indigo-50 rounded-xl shadow-xl overflow-hidden flex items-center space-x-6 p-6 transition-all transform hover:scale-105 hover:shadow-2xl hover:bg-indigo-100 duration-300">
        <img src="https://picsum.photos/400/300?random=" alt="" class="w-32 h-32 object-cover rounded-lg shadow-md transition-all duration-300 transform hover:scale-110">

        <div class="flex-1">
            <h2 class="text-xl font-semibold text-gray-800">{{ $item['name'] }}</h2>
            <p class="text-gray-600 mt-1 text-sm">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore.</p>

            <div class="flex justify-between items-center mt-4">
                <span class="text-lg font-semibold text-indigo-600">${{ rand(50, 200) }}</span>

                <!-- Quantity Selector -->
                <div class="flex items-center space-x-2">
                    <button class="px-3 py-1 text-sm bg-gray-200 rounded-full hover:bg-indigo-200 transition duration-200 ease-in-out transform hover:scale-110">-</button>
                    <span class="text-lg font-medium">1</span>
                    <button class="px-3 py-1 text-sm bg-gray-200 rounded-full hover:bg-indigo-200 transition duration-200 ease-in-out transform hover:scale-110">+</button>
                </div>
            </div>
        </div>

        <!-- Remove Button -->
        <button class="text-red-600 hover:text-red-700 text-sm font-semibold flex items-center space-x-2 transition duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <span>Remove</span>
        </button>
    </div>
@endforeach

    </div>

    <!-- Cart Total Section -->
    <div class="mt-12 flex justify-between items-center bg-gradient-to-r from-indigo-100 to-indigo-300 p-6 rounded-xl shadow-xl transition-all duration-500 ease-in-out">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Total</h3>
            <p class="text-2xl font-extrabold text-indigo-700 mt-2">${{ rand(150, 500) }}</p>
        </div>

        <button class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-full hover:bg-indigo-700 transition-all duration-300 ease-in-out transform hover:scale-105">
            Proceed to Checkout
        </button>
    </div>

    <!-- Empty Cart Message -->
    <div class="mt-12 text-center" x-show="cartItems.length === 0">
        <p class="text-gray-600 text-lg">Your cart is empty. Start shopping!</p>
        <button class="mt-4 px-6 py-3 bg-indigo-600 text-white font-semibold rounded-full hover:bg-indigo-700 transition-all duration-300 ease-in-out transform hover:scale-105">
            Continue Shopping
        </button>
    </div>
</div>
