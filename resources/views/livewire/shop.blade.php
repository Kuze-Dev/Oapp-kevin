<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 mt-12">
    <h1 class="text-4xl font-bold mb-8 text-gray-800">Shop</h1>

    <div class="mb-8 space-y-4 sm:space-y-0 sm:flex sm:items-center sm:space-x-4">
        <!-- Search Bar -->
        <div class="relative flex-grow">
            <input wire:model.live.debounce.500ms="search" id="search" type="text" placeholder="Search products..."
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
            <select wire:model.live="categoryId" id="category"
                class="appearance-none w-full bg-white border border-gray-300 rounded-full pl-4 pr-10 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 shadow-md">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            <!-- Dropdown Icon -->
            <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
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
            <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Products Display -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-8">
        @forelse($products as $product)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden transform transition-all duration-300 hover:scale-105 hover:shadow-2xl">
                <div class="relative">
                <img src="{{ $product->product_image ? asset('storage/' . $product->product_image) : 'https://picsum.photos/800/600?random=' . $product->id }}"
     alt="{{ $product->name }}"
     class="w-full h-64 object-cover"
     onerror="this.onerror=null;this.src='https://picsum.photos/800/600';">
                    <div class="absolute top-4 left-4 bg-black bg-opacity-60 text-white px-4 py-2 rounded-md">
                        {{ $product->brand->name ?? 'No Brand' }}
                    </div>
                </div>
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-2 text-gray-800">{{ $product->name }}</h2>
                    <p class="mb-2 text-gray-600 font-sans">{!! $product->description !!}</p>

                    <p class="mb-4  {{ $product->status == 'Stock In' ? 'text-green-500' : ($product->status == 'Sold Out' ? 'text-red-500' : ($product->status == 'Coming Soon' ? 'text-yellow-500' : 'text-gray-500')) }}">
                        @if ($product->status == 'Stock In')
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1.707-10.707a1 1 0 00-1.414 0L9 9.586 8.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4a1 1 0 000-1.414z" clip-rule="evenodd" />
                            </svg>
                        @elseif ($product->status == 'Sold Out')
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1.707-10.707a1 1 0 00-1.414 0L9 9.586 8.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4a1 1 0 000-1.414z" clip-rule="evenodd" />
                            </svg>
                        @elseif ($product->status == 'Coming Soon')
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1.707-10.707a1 1 0 00-1.414 0L9 9.586 8.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4a1 1 0 000-1.414z" clip-rule="evenodd" />
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1.707-10.707a1 1 0 00-1.414 0L9 9.586 8.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4a1 1 0 000-1.414z" clip-rule="evenodd" />
                            </svg>
                        @endif
                        {{ $product->status ?? 'Unavailable' }}
                    </p>
                    <p class="text-lg font-semibold text-gray-800">{{ $product->price }} PHP</p>
                    <div class="flex justify-between items-center mt-4">
                    <a href="/product/{{ $product->id }}" wire:navigate="product({{ $product->id }})">
    <button class="px-5 py-3 bg-indigo-600 text-white font-semibold rounded-lg flex items-center space-x-1 hover:bg-indigo-700 transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 shadow-lg transform hover:scale-105">
        <!-- Magnifying Glass Icon for View Details -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 2a8 8 0 11-8 8 8 8 0 018-8zm0 16a7.93 7.93 0 01-4.8-1.68l-4.72 4.72a1 1 0 001.42 1.42l4.72-4.72A7.93 7.93 0 0110 18zm5-5a5 5 0 10-10 0 5 5 0 0010 0z"/>
        </svg>
        <span>View Details</span>
    </button>
</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-1 sm:col-span-2 lg:col-span-3 flex flex-col items-center justify-center p-12 bg-white rounded-xl shadow-lg">
                <!-- Hero Icon: Package-X (Outline) -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-32 w-32 text-gray-400 mb-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0-6.75h-3.75m3.75 0h3.75M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                </svg>

                <h2 class="text-2xl font-bold text-gray-800 mb-4">No Products Found</h2>
                <p class="text-gray-600 text-center mb-6 max-w-md">We couldn't find any products matching your criteria. Try adjusting your filters or search terms.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination or Load More -->
    @if(count($products) > 0)
    <div class="mt-8 text-center">
        <button wire:click="loadMore"
            class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-full hover:bg-indigo-700 transition-all duration-300">
            Load More
        </button>
    </div>
    @endif
</div>