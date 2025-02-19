@if($type = 'success')



<div x-data="{ open: @entangle('showToast') }"
     x-show="open"
     x-transition.opacity.duration.500ms
     class="fixed top-5 right-5 bg-green-500 text-white p-4 rounded-lg shadow-lg z-50">
    <div class="flex justify-between items-center">
        <span x-text="$wire.message"></span>
        <button @click="open = false" class="text-white ml-2">&times;</button>
    </div>
</div>

@elseif($type = 'error')

<div x-data="{ open: @entangle('showToast') }"
     x-show="open"
     x-transition.opacity.duration.500ms
     class="fixed top-5 right-5 bg-red-500 text-white p-4 rounded-lg shadow-lg z-50">
    <div class="flex justify-between items-center">
        <span x-text="$wire.message"></span>
        <button @click="open = false" class="text-white ml-2">&times;</button>
    </div>
</div>

@endif
