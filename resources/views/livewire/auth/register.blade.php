<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="grid md:grid-cols-2 w-full max-w-5xl bg-white rounded-2xl shadow-lg overflow-hidden">

        <!-- Left Section - Background Image -->
        <div class="hidden md:block relative">
            <img src="https://picsum.photos/800/900?random=2"
                class="w-full h-full object-cover rounded-l-2xl"
                alt="Register Background">
            <div class="absolute inset-0 bg-black bg-opacity-30 rounded-l-2xl"></div>
        </div>

        <!-- Right Section - Registration Form -->
        <div class="p-8 space-y-6 w-full max-w-md mx-auto">
            <h2 class="text-3xl font-bold text-center text-gray-900">Create Your Account</h2>

            {{-- Success Message --}}
            @if (session()->has('message'))
                <div class="text-green-500 text-sm text-center bg-green-100 p-2 rounded-lg">
                    {{ session('message') }}
                </div>
            @endif

            {{-- Error Message --}}
            @if ($errorMessage)
                <div class="text-red-500 text-sm text-center bg-red-100 p-2 rounded-lg">
                    {{ $errorMessage }}
                </div>
            @endif

            <form wire:submit.prevent="register" class="space-y-6">
                <!-- Name Field -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input type="text" wire:model="name" id="name"
                        class="w-full p-3 mt-2 text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="John Doe" required>
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email" wire:model="email" id="email"
                        class="w-full p-3 mt-2 text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="you@example.com" required>
                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" wire:model="password" id="password"
                        class="w-full p-3 mt-2 text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="********" required>
                    @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Confirm Password Field -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input type="password" wire:model="password_confirmation" id="password_confirmation"
                        class="w-full p-3 mt-2 text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="********" required>
                    @error('password_confirmation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit"
                        class="w-full p-3 text-white bg-indigo-600 rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-lg font-semibold">
                        Sign up
                    </button>
                </div>
            </form>

            <div class="flex items-center justify-center space-x-2">
                <span class="text-sm text-gray-600">Already have an account?</span>
                <a href="{{ route('login') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Log in</a>
            </div>
        </div>

    </div>
</div>
