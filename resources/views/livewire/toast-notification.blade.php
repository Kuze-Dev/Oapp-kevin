<div x-data="{ open: @entangle('showToast'), progress: 100 }"
     x-show="open"
     x-cloak
     x-transition:enter="transition ease-out duration-500"
     x-transition:enter-start="opacity-0 -translate-y-5 scale-90"
     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
     x-transition:leave-end="opacity-0 -translate-y-5 scale-90"
     class="fixed top-20 right-5 w-80 p-4 rounded-2xl shadow-lg z-50 transform flex flex-col space-y-2"
     x-init="
         $watch('open', value => {
             if (value) {
                 progress = 100;
                 let interval = setInterval(() => {
                     progress -= 1.67; // 100% / 60 steps (3 seconds)
                     if (progress <= 0) {
                         clearInterval(interval);
                         open = false;
                     }
                 }, 50);
             }
         });
     "
     :class="{
         'bg-green-500': $wire.type == 'success',
         'bg-red-500': $wire.type == 'error'
     }">

    <!-- Notification Content -->
    <div class="flex justify-between items-center">
        <div class="flex items-center space-x-2">
            <!-- Dynamic Icon -->
            <svg  x-show="$wire.type == 'success'" xmlns="http://www.w3.org/2000/svg" class="w-[18px] shrink-0 fill-white inline mr-3" viewBox="0 0 512 512">
              <ellipse cx="256" cy="256" fill="#fff" data-original="#fff" rx="256" ry="255.832" />
              <path class="fill-green-500"
                  d="m235.472 392.08-121.04-94.296 34.416-44.168 74.328 57.904 122.672-177.016 46.032 31.888z"
                  data-original="#ffffff" />
          </svg>

          <svg x-show="$wire.type == 'error'" xmlns="http://www.w3.org/2000/svg" class="w-5 shrink-0 fill-white inline" viewBox="0 0 32 32">
                      <path
                          d="M16 1a15 15 0 1 0 15 15A15 15 0 0 0 16 1zm6.36 20L21 22.36l-5-4.95-4.95 4.95L9.64 21l4.95-5-4.95-4.95 1.41-1.41L16 14.59l5-4.95 1.41 1.41-5 4.95z"
                          data-original="#ea2d3f" />
                  </svg>

            <!-- Message -->
            <span class="text-white font-medium text-base" x-text="$wire.message"></span>
        </div>

        <!-- Close Button -->
        <button @click="open = false" class="text-white text-2xl ml-2 hover:text-gray-200">&times;</button>
    </div>

    <!-- Modern Progress Bar -->
    <div class="w-full h-1 bg-white bg-opacity-20 rounded-full overflow-hidden relative">
        <div class="absolute top-0 left-0 h-full bg-white rounded-full transition-all duration-100"
             :style="'width:' + progress + '%'"></div>
    </div>
</div>
