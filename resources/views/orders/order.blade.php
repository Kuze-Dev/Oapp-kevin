<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Orders - Laravel</title>
        @vite('resources/css/app.css')

    </head>
    <body class="font-sans antialiased bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300">
        <div class="min-h-screen flex flex-col items-center justify-center">
            <header class="py-6 text-center">
                <h1 class="text-3xl font-bold text-gray-700 dark:text-gray-200">Orders Management</h1>
            </header>

            <main class="mt-10 space-y-12">
            <nav class="w-full py-4 px-6 bg-gray-200 dark:bg-gray-800 text-gray-800 dark:text-gray-300">
                <a href="{{ url('/') }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                    &larr; Back to Welcome
                </a>
            </nav>
                <section class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">Orders Table</h2>
                    <div class="overflow-x-auto">
                        <table class="table-auto w-full border-collapse border border-gray-300 dark:border-gray-700">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Order ID</th>
                                    <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Order Name</th>
                                    <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Description</th>
                                    <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Quantity</th>
                                    <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Amount</th>
                                    <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Status</th>
                                    <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Payment Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr class="text-center">
                                        <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{ $order->id }}</td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{ $order->order_name }}</td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{ $order->order_description }}</td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{ $order->quantity }}</td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{ $order->amount }}</td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">
                                            <span class="{{ $order->is_paid ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $order->is_paid ? 'Paid' : 'Unpaid' }}
                                            </span>
                                        </td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">
    @if (!$order->is_paid)
        <a href="{{ route('payment', ['id' => $order->id, 'gateway' => 'stripe']) }}" class="text-blue-500 hover:underline">Pay via Stripe</a> |
        <a href="{{ route('payment', ['id' => $order->id, 'gateway' => 'paymongo']) }}" class="text-blue-500 hover:underline">Pay via Paymongo</a>
    @else
        <span class="text-gray-500">Payment Complete</span>
    @endif
</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $orders->links() }}
                    </div>
                </section>
            </main>

            <footer class="py-8 text-center text-gray-600 dark:text-gray-400 text-sm">
                Built with Laravel & Tailwind CSS
            </footer>
        </div>
    </body>
</html>
