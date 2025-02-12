<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 mt-12">
    <h1 class="text-4xl font-bold mb-8 text-gray-800">Shop</h1>

    <div class="mb-8 space-y-4 sm:space-y-0 sm:flex sm:items-center sm:space-x-4">
        <!-- Search Bar -->
        <div class="relative flex-grow">
            <input wire:model.live.debounce.500ms="search" type="text" placeholder="Search products..."
                class="w-full pl-10 pr-4 py-3 rounded-full border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-all duration-300 shadow-md hover:shadow-lg">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z">
                    </path>
                </svg>
            </div>
        </div>

        <!-- Category Filter -->
        <div class="relative">
            <select wire:model.live="categoryId"
                class="appearance-none w-full bg-white border border-gray-300 rounded-full pl-4 pr-10 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 shadow-md">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Brand Filter -->
        <div class="relative">
            <select wire:model.live="brandId"
                class="appearance-none w-full bg-white border border-gray-300 rounded-full pl-4 pr-10 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 shadow-md">
                <option value="">All Brands</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Products Display -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-8">
        @forelse($products as $product)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden transform transition-all duration-300 hover:scale-105 hover:shadow-2xl">
                <img src="https://picsum.photos/400/300?random={{ $product->id }}" alt="product"
                    class="w-full h-64 object-cover rounded-t-xl">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-2 text-gray-800">{{ $product->name }}</h2>
                    <p class="mb-2 text-gray-600">{{ $product->description }}</p>
                    <p class="text-green-500 mb-4">{{ $product->status ?? 'Unavailable' }}</p>
                    <h3 class="text-md font-semibold text-gray-700">{{ $product->brand->name ?? 'No Brand' }}</h3>
                    <div class="flex justify-between items-center mt-4">
                        <button wire:click="addToCart({{ $product->id }})"  class="px-5 py-3 bg-indigo-600 text-white font-semibold rounded-lg flex hover:bg-indigo-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 shadow-md transform hover:scale-105">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                class="h-5 w-5 mr-2 text-white" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5 3a1 1 0 011-1h2.268a1 1 0 01.95.684L9.936 3H10a1 1 0 011 1v1h7a1 1 0 011 1v11a1 1 0 01-1 1H4a1 1 0 01-1-1V6a1 1 0 011-1h1V3zm1 2h7l.72 2.16-1.44 4.72a1 1 0 01-1.13.7L6 9.6 4.72 7.36 6 5z"
                                    clip-rule="evenodd" />
                            </svg>
                            Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        @empty
            @for ($i = 0; $i < 3; $i++)
                <div class="bg-gray-200 animate-pulse rounded-xl shadow-lg overflow-hidden">
                    <div class="w-full h-64 bg-gray-300"></div>
                    <div class="p-6">
                        <div class="h-6 bg-gray-400 rounded w-3/4 mb-4"></div>
                        <div class="h-4 bg-gray-400 rounded w-1/2 mb-2"></div>
                        <div class="h-4 bg-gray-400 rounded w-1/3"></div>
                    </div>
                </div>
            @endfor
        @endforelse
    </div>

    <!-- Pagination or Load More -->
    <div class="mt-8 text-center">
        <button wire:click="loadMore"
            class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-full hover:bg-indigo-700 transition-all duration-300">
            Load More
        </button>
    </div>
</div>
