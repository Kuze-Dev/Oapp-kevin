<div>
    <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-8 text-center">Featured Products</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        @for ($i = 1; $i <= 6; $i++)
            <div class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-lg transition-transform duration-300 hover:scale-105">
                <img src="https://picsum.photos/800/600?random={{ $i }}" alt="Featured Product {{ $i }}" class="w-full h-64 object-cover" onerror="this.onerror=null;this.src='https://picsum.photos/800/600';">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Featured Product {{ $i }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                    <div class="flex justify-between items-center">
                        <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">$99.99</span>
                        <div class="flex space-x-4">
                        <button class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors duration-200 flex items-center space-x-2">
    <!-- Add to Cart SVG Icon -->
    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="h-5 w-5 text-white" viewBox="0 0 20 20" xmlns:xlink="http://www.w3.org/1999/xlink">
        <path fill-rule="evenodd" d="M5 3a1 1 0 011-1h2.268a1 1 0 01.95.684L9.936 3H10a1 1 0 011 1v1h7a1 1 0 011 1v11a1 1 0 01-1 1H4a1 1 0 01-1-1V6a1 1 0 011-1h1V3zm1 2h7l.72 2.16-1.44 4.72a1 1 0 01-1.13.7L6 9.6 4.72 7.36 6 5z" clip-rule="evenodd" />
    </svg>
    <span class="text-md">Add to Cart</span>
</button>


                            <!-- Filled Heart Icon (TikTok Style Like) -->
                            <button class="p-2 hover:bg-gray-200 rounded-full group">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 group-hover:fill-red-500 group-hover:stroke-none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
    </svg>
</button>




                    <button class="p-2 hover:bg-gray-200 rounded-full">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-600 dark:text-gray-400 hover:text-blue-600 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <circle cx="5" cy="12" r="2" />
        <circle cx="12" cy="12" r="2" />
        <circle cx="19" cy="12" r="2" />
    </svg>
</button>
                        </div>
                    </div>
                </div>
            </div>
        @endfor
    </div>
</div>
