<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <!-- Main Container -->
    <div class="grid md:grid-cols-2 w-full max-w-5xl bg-white rounded-2xl shadow-lg overflow-hidden">

        <!-- Left Section - Login Form -->
        <div class="p-8 space-y-6 w-full max-w-md mx-auto">
            <h2 class="text-3xl font-bold text-center text-gray-900">Welcome Back</h2>
            <p class="text-sm text-gray-500 text-center">Login to access your account</p>

            @if ($errorMessage)
                <div class="text-red-500 text-sm text-center bg-red-100 p-2 rounded-lg">
                    {{ $errorMessage }}
                </div>
            @endif

            <form wire:submit.prevent="login" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email" wire:model="email" id="email"
                        class="w-full p-3 mt-2 text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="you@example.com" required>
                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" wire:model="password" id="password"
                        class="w-full p-3 mt-2 text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="********" required>
                    @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 rounded">
                        <span class="ml-2 text-sm text-gray-600">Remember Me</span>
                    </label>
                    <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Forgot password?</a>
                </div>

                <button type="submit"
                    class="w-full p-3 text-white bg-indigo-600 rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-lg font-semibold">
                    Log in
                </button>
            </form>

            <div class="flex items-center justify-center space-x-2">
                <span class="text-sm text-gray-600">Don't have an account?</span>
                <a href="{{ route('register') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Sign up</a>
            </div>
        </div>

        <!-- Right Section - Background Image -->
        <div class="hidden md:block relative">
            <img src="https://picsum.photos/800/900?random=1"
                class="w-full h-full object-cover rounded-r-2xl"
                alt="Login Background">
            <div class="absolute inset-0 bg-black bg-opacity-30 rounded-r-2xl"></div>
        </div>

    </div>
</div>
