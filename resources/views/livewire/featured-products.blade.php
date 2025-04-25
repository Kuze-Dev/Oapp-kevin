<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-8 text-center">Featured Products</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($featuredProducts as $product)
            <div class="relative bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-lg transition-transform duration-300 hover:scale-105">
              <img src="{{ $product->product_image ? asset('storage/' . $product->product_image) : 'https://picsum.photos/800/600?random=' . $product->id }}"
     alt="{{ $product->name }}"
     class="w-full h-64 object-cover"
     onerror="this.onerror=null;this.src='https://picsum.photos/800/600';">


                <!-- Brand Name - Always Visible -->
                <div class="absolute top-4 left-4 bg-black bg-opacity-60 text-white px-4 py-2 rounded-md">
                    {{ $product->brand->name ?? 'No Brand' }}
                </div>


                <div class="p-6">
                <div class="flex justify-between items-center mb-4">
    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $product->name }}</h3>

    @if($product->featured)
        <span class="bg-yellow-500 text-white text-xs font-bold px-3 py-1 rounded-full uppercase">
            Featured
        </span>
    @endif
</div>

                    <p class="text-gray-600 dark:text-gray-400 mb-4">{!!Str::limit($product->description, 100) !!}</p>
                    <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">PHP {{ number_format($product->price, 2) }}</span>
                    <div class="flex justify-between items-center mt-4">

                        <div class="">
                            <!-- Add to Cart Button -->
                            <a href="/product/{{ $product->slug }}" wire:navigate="product({{ $product->slug }})">
                            <button
                                    class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors duration-200 flex items-center space-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 2a8 8 0 11-8 8 8 8 0 018-8zm0 16a7.93 7.93 0 01-4.8-1.68l-4.72 4.72a1 1 0 001.42 1.42l4.72-4.72A7.93 7.93 0 0110 18zm5-5a5 5 0 10-10 0 5 5 0 0010 0z"/>
        </svg>
        <span>View Details</span>
                            </button>
                            </a>
                        </div>

                        <div class="flex items-center space-x-2">
                            <!-- Like Button -->
                            <button class="p-2 hover:bg-gray-200 rounded-full group">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 group-hover:fill-red-500 group-hover:stroke-none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </button>

                            <!-- More Options Button -->
                            <button class="p-2 hover:bg-gray-200 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-600 dark:text-gray-400 hover:text-blue-600 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <circle cx="5" cy="12" r="2"/>
                                    <circle cx="12" cy="12" r="2"/>
                                    <circle cx="19" cy="12" r="2"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            @for ($i = 0; $i < 6; $i++)
                <div class="bg-gray-300 dark:bg-gray-700 rounded-lg overflow-hidden shadow-lg animate-pulse">
                    <div class="w-full h-64 bg-gray-400 dark:bg-gray-600"></div>
                    <div class="p-6">
                        <div class="h-6 bg-gray-400 dark:bg-gray-600 rounded w-3/4 mb-4"></div>
                        <div class="h-4 bg-gray-400 dark:bg-gray-600 rounded w-full mb-2"></div>
                        <div class="h-4 bg-gray-400 dark:bg-gray-600 rounded w-5/6 mb-4"></div>
                        <div class="flex justify-between">
                            <div class="h-6 bg-gray-400 dark:bg-gray-600 rounded w-1/4"></div>
                            <div class="h-10 bg-gray-400 dark:bg-gray-600 rounded w-1/4"></div>
                        </div>
                    </div>
                </div>
            @endfor
        @endforelse
    </div>
</div>
