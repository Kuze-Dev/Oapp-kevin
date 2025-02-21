<div class="flex items-center justify-center min-h-screen bg-gray-50">
    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold text-center text-gray-800">Login to Your Account</h2>

        {{-- Display error message if login fails --}}
        @if ($errorMessage)
            <div class="text-red-500 text-sm text-center">
                {{ $errorMessage }}
            </div>
        @endif

        <form wire:submit.prevent="login" class="space-y-6">
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

            <!-- Remember Me Checkbox -->
            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 rounded">
                    <span class="ml-2 text-sm text-gray-600">Remember Me</span>
                </label>
                <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800">Forgot password?</a>
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit" class="w-full p-3 text-white bg-indigo-600 rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">Log in</button>
            </div>
        </form>

        <div class="flex items-center justify-center space-x-2">
            <span class="text-sm text-gray-600">Don't have an account?</span>
            <a href="{{ route('register') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Sign up</a>
        </div>
    </div>
</div>
