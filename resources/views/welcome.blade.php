<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300">
    <div class="min-h-screen flex flex-col items-center justify-center">
        <div class="relative w-full max-w-7xl px-6">
            <header class="py-6 text-center">
                <h1 class="text-3xl font-bold text-gray-700 dark:text-gray-200">Welcome to Order Management</h1>
                <div class="mt-6 space-x-4">
                    <a href="{{ route('orders') }}" class="px-6 py-3 bg-blue-600 text-white font-semibold rounded hover:bg-blue-700">View Orders</a>
                    <a href="{{ route('payments') }}" class="px-6 py-3 bg-green-600 text-white font-semibold rounded hover:bg-green-700">View Payments</a>
                </div>
            </header>
        </div>
    </div>
</body>
</html>
