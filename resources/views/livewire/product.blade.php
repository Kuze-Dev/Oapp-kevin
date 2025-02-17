<div class="inset-0 overflow-y-auto mt-12 py-12 w-full flex justify-center items-center">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-6xl m-4 overflow-hidden">
        <div class="flex flex-col lg:flex-row p-8 space-y-6 lg:space-y-0">
            <!-- Product Image and Brand -->
            <div class="w-full lg:w-1/2 mb-6 lg:mb-0 relative">
                <!-- Brand Above Image -->
                <div class="bg-black bg-opacity-60 text-white px-4 py-2 rounded-md text-sm font-semibold absolute top-4 left-4 z-10">
                    {{ $product->brand->name ?? 'No Brand' }}
                </div>
                <img src="https://picsum.photos/400/300?random={{ $product->id }}" alt="product"
                    class="w-full h-full object-cover rounded-t-xl rounded-b-xl transition-transform hover:scale-105 ease-in-out duration-300">
            </div>

            <!-- Product Details -->
            <div class="w-full lg:w-1/2 p-6 space-y-6 flex flex-col justify-between">
                <div class="space-y-4">
                    <h2 class="text-3xl font-semibold text-gray-800 leading-tight">{{ $product->name }}</h2>
                    <p class="text-2xl font-semibold text-indigo-600">{{ $product->price }} PHP</p>
                    <p class="text-gray-700 mt-2 text-lg">{{ $product->description }}</p>
                </div>

                <!-- Color Variants -->
                <div class="space-y-2">
                    <h3 class="font-semibold text-gray-800 text-lg">Color:</h3>
                    <div class="flex flex-wrap space-x-4">
                        @foreach(['#FF5733', '#33FF57', '#3357FF', '#F333FF', '#FF3333'] as $color)
                            <button class="w-10 h-10 rounded-full focus:outline-none transform transition-all hover:scale-125 ring-2 ring-indigo-600" style="background-color: {{ $color }}"></button>
                        @endforeach
                    </div>
                </div>

                <!-- Size Variant -->
                <div class="space-y-2">
                    <h3 class="font-semibold text-gray-800 text-lg">Size:</h3>
                    <div class="flex flex-wrap space-x-3">
                        @foreach(['S', 'M', 'L', 'XL'] as $size)
                            <button class="w-12 h-12 rounded-full border-2 border-gray-300 hover:bg-indigo-500 hover:text-white focus:outline-none transition-all">
                                {{ $size }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Quantity Selector and Stock Information -->
                <div class="flex flex-col lg:flex-row justify-between items-center space-y-4 lg:space-y-0">
                    @if($product->status > 0)
                        <div class="w-full lg:w-1/2">
                            <h3 class="font-semibold text-gray-800">Quantity:</h3>
                            <div class="flex items-center space-x-4 mt-4 ">
                                <button class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300 focus:outline-none transition-transform transform active:scale-90">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                    </svg>
                                </button>
                                <input type="number" class="form-input w-16 text-center border-gray-300 rounded-md shadow-sm focus:border-indigo-500 transition-all" value="1" min="1" max="{{ $product->stock }}">
                                <button class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300 focus:outline-none transition-transform transform active:scale-90">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Status -->
                    <div class="text-center lg:text-right w-full lg:w-1/3">
                        <h3 class="font-semibold text-gray-800">Status:</h3>
                        <p class="font-semibold {{ $product->status > 0 ? 'text-green-600' : ($product->status === 0 ? 'text-red-600' : 'text-yellow-600') }}">
                            {{ $product->status > 0 ? 'Stock In' : ($product->status === 0 ? 'Sold Out' : 'Coming Soon') }}
                        </p>
                        @if($product->stock > 0)
                            <p class="text-sm text-gray-500">{{ $product->stock }} units available</p>
                        @endif
                    </div>
                </div>

                <!-- Add to Cart Button -->
                <div class="mt-6">
                    @if($product->status > 0)
                        <button wire:click="addToCart({{ $product->id }})" class="w-full px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-all ease-in-out duration-300 flex items-center justify-center {{ $product->stock == 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $product->stock == 0 ? 'disabled' : '' }}>
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Add to Cart
                        </button>
                    @else
                        <button class="w-full px-6 py-3 bg-yellow-500 text-white font-semibold rounded-lg transition-colors duration-200 flex items-center justify-center cursor-not-allowed">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Coming Soon
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
