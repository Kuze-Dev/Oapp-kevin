<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 mt-12">
    <div class="swiper-container rounded-lg overflow-hidden shadow-xl relative" style="height: 500px;">
        <div class="swiper-wrapper">
            @for ($i = 1; $i <= 3; $i++)
                <div class="swiper-slide" style="height: 500px;">
                    <img src="https://picsum.photos/1600/900?random={{ $i }}" alt="Product {{ $i }}" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='https://picsum.photos/1600/900';">
                </div>
            @endfor
        </div>

        <div class="swiper-pagination"></div>
        <div class="swiper-button-next absolute right-4 top-1/2 transform -translate-y-1/2 z-10 bg-opacity-50 rounded-full p-2 hover:bg-opacity-75 transition-all duration-200">
            <svg class="h-6 w-6 text-gray-800" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                <path fill-rule="evenodd" d="M16.28 11.47a.75.75 0 010 1.06l-7.5 7.5a.75.75 0 01-1.06-1.06L14.69 12 7.72 5.03a.75.75 0 011.06-1.06l7.5 7.5z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="swiper-button-prev absolute left-4 top-1/2 transform -translate-y-1/2 z-10 bg-opacity-50 rounded-full p-2 hover:bg-opacity-75 transition-all duration-200">
            <svg class="h-6 w-6 text-gray-800" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 010-1.06l7.5-7.5a.75.75 0 111.06 1.06L9.31 12l6.97 6.97a.75.75 0 11-1.06 1.06l-7.5-7.5z" clip-rule="evenodd" />
            </svg>
        </div>
    </div>

    <div class="mt-16 text-center">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4">Welcome to Our Modern Store</h1>
        <p class="text-xl text-gray-600 dark:text-gray-400 mb-8">Discover our latest collection of premium products.</p>
        <div class="flex justify-center space-x-4">
        <a href="/shop" wire:navigate="shop" class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors duration-200 flex items-center space-x-2">
    <!-- Shop Icon (SVG) -->
    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="h-5 w-5 text-white" viewBox="0 0 24 24" xmlns:xlink="http://www.w3.org/1999/xlink">
        <path fill-rule="evenodd" d="M7 4V3a1 1 0 112 0v1h8V3a1 1 0 112 0v1h2a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V6a1 1 0 011-1h2zm2 2H5v12h14V6h-4v1a1 1 0 01-1 1H9a1 1 0 01-1-1V6zm6 3a3 3 0 11-6 0 3 3 0 016 0z" clip-rule="evenodd" />
    </svg>
    <span>Shop Now</span>
</a>


<a href="/cart" wire:navigate="cart" class="px-6 py-3 bg-gray-200 text-gray-800 font-semibold rounded-lg hover:bg-gray-300 transition-colors duration-200 flex items-center space-x-2">
    <!-- Cart Icon (SVG) for View Cart -->
    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="h-5 w-5 text-gray-800" viewBox="0 0 24 24" xmlns:xlink="http://www.w3.org/1999/xlink">
        <path fill-rule="evenodd" d="M10 2a2 2 0 114 0 2 2 0 01-4 0zM4 7h16v11H4V7zm0 12a1 1 0 011-1h14a1 1 0 011 1v1H4v-1z" clip-rule="evenodd" />
    </svg>
    <span>View Cart</span>
</a>


        </div>
    </div>
</div>
