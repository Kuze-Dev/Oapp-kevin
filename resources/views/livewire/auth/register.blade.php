<div class="flex items-center justify-center min-h-screen bg-gray-50">
    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold text-center text-gray-800">Create Your Account</h2>

        {{-- Success Message --}}
        @if (session()->has('message'))
            <div class="text-green-500 text-sm text-center">
                {{ session('message') }}
            </div>
        @endif

        {{-- Error Message --}}
        @if ($errorMessage)
            <div class="text-red-500 text-sm text-center">
                {{ $errorMessage }}
            </div>
        @endif

        <form wire:submit.prevent="register" class="space-y-6">
            <!-- Name Field -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-600">Full Name</label>
                <input type="text" wire:model="name" id="name" class="w-full p-3 mt-2 text-gray-800 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="John Doe" required>
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Email Field -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-600">Email Address</label>
                <input type="email" wire:model="email" id="email" class="w-full p-3 mt-2 text-gray-800 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="you@example.com" required>
                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Password Field -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-600">Password</label>
                <input type="password" wire:model="password" id="password" class="w-full p-3 mt-2 text-gray-800 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="********" required>
                @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Confirm Password Field -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-600">Confirm Password</label>
                <input type="password" wire:model="password_confirmation" id="password_confirmation" class="w-full p-3 mt-2 text-gray-800 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="********" required>
                @error('password_confirmation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit" class="w-full p-3 text-white bg-indigo-600 rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">Sign up</button>
            </div>
        </form>

        <div class="flex items-center justify-center space-x-2">
            <span class="text-sm text-gray-600">Already have an account?</span>
            <a href="{{ route('login') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Log in</a>
        </div>
    </div>
</div>
