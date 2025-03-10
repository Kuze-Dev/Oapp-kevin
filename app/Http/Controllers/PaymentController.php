<?php
namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use Stripe\StripeClient;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class PaymentController extends Controller
{
    public function processCheckout(Request $request)
    {
        // Get checkout data from session
        $checkoutData = session('checkout_data');

        if (!$checkoutData) {
            return redirect()->route('checkout')->with('error', 'No checkout data found');
        }

        // Create order from checkout data
        $order = Order::create([
            'quantity' => $checkoutData['items']->sum('quantity'),
            'amount' => $checkoutData['total'],
            'is_paid' => false,
            'shipping_method' => $checkoutData['shipping_method'],
            'shipping_fee' => $checkoutData['shipping_fee'],
            'status' => 'pending',
            'user_id' => Auth::id() ?? 1, // Guest user ID if not logged in
            'address' => $checkoutData['billing']['address'],
            'city' => $checkoutData['billing']['city'],
            'state' => $checkoutData['billing']['state'],
            'zip_code' => $checkoutData['billing']['zip_code'],
            'country' => $checkoutData['billing']['country'],
            'phone' => $checkoutData['billing']['phone'],
            'notes' => $checkoutData['billing']['notes'] ?? null,
        ]);

        // Create order items
        foreach ($checkoutData['items'] as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_sku_id' => $item->sku_id ?? $item->id, // Use the specific SKU ID if available
                'user_id' => Auth::id() ?? 1, // Guest user ID if not logged in
                'quantity' => $item->quantity,
                'price' => $item->price,
                'subtotal' => $item->price * $item->quantity,
            ]);
        }

        // Determine payment gateway
        $paymentMethod = $checkoutData['payment_method'];
        $gateway = '';

        if ($paymentMethod === 'credit_card') {
            $gateway = 'stripe';
        } elseif (in_array($paymentMethod, ['gcash', 'paymaya'])) {
            $gateway = 'paymongo';
        } elseif ($paymentMethod === 'cash_on_delivery') {
            // For cash on delivery, mark as pending and redirect to success
            return redirect()->route('payment.success', ['id' => $order->id, 'gateway' => 'cash_on_delivery']);
        } else {
            // Fallback for unknown payment methods
            return redirect()->route('checkout')->with('error', 'Invalid payment method selected');
        }
        // Redirect to payment gateway
        return redirect()->route('payment', ['id' => $order->id, 'gateway' => $gateway]);
    }

    public function payment($id, string $gateway)
    {
        $order = Order::with('orderItems')->findOrFail($id);

        abort_if(
            !in_array($gateway, ['stripe', 'paymongo']) || $order->is_paid,
            400,
            'Payment Gateway Not Supported or Order is paid'
        );

        return $gateway === 'stripe' ? $this->payWithStripe($order, $gateway) : $this->payWithPaymongo($order, $gateway);
    }

    private function payWithStripe($order, $gateway)
    {
        $stripe = new StripeClient(env('STRIPE_SECRET_KEY'));
        $referenceNumber = Str::random(10);

        // Format line items for Stripe
        $lineItems = [];

        if ($order->orderItems->count() > 0) {
            foreach ($order->orderItems as $item) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'php',
                        'product_data' => [
                            'name' => "Order #{$order->id} - Item #{$item->id}",
                            'description' => "Product ID: {$item->product_sku_id}",
                        ],
                        'unit_amount' => $item->price * 100, // Convert to cents
                    ],
                    'quantity' => $item->quantity,
                ];
            }
        } else {
            // Fallback if no order items found
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'php',
                    'product_data' => [
                        'name' => "Order #{$order->id}",
                        'description' => "Complete order payment",
                    ],
                    'unit_amount' => $order->amount * 100, // Convert to cents
                ],
                'quantity' => 1,
            ];
        }

        // Add shipping fee as separate line item
        if ($order->shipping_fee > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'php',
                    'product_data' => [
                        'name' => "Shipping Fee ({$order->shipping_method})",
                        'description' => "Shipping cost",
                    ],
                    'unit_amount' => $order->shipping_fee * 100, // Convert to cents
                ],
                'quantity' => 1,
            ];
        }

        $checkout_session = $stripe->checkout->sessions->create([
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('payment.success', ['id' => $order->id, 'gateway' => $gateway]),
            'cancel_url' => route('payment.cancel', ['gateway' => $gateway]),
            'customer_email' => Session::get('checkout_data.billing.email', Auth::user()->email ?? ''),
            'metadata' => [
                'customer_name' => Session::get('checkout_data.billing.name', Auth::user()->name ?? ''),
                'reference_number' => $referenceNumber,
                'order_id' => $order->id
            ],
        ]);

        session(['stripe_checkout_id' => $checkout_session->id]);

        return redirect($checkout_session->url);
    }

    private function payWithPaymongo($order, $gateway)
    {
        // Format line items for PayMongo
        $lineItems = [];

        if ($order->orderItems->count() > 0) {
            foreach ($order->orderItems as $item) {
                $lineItems[] = [
                    "currency" => "PHP",
                    "amount" => $item->price * 100, // Convert to cents
                    "description" => "Product ID: {$item->product_sku_id}",
                    "name" => "Order #{$order->id} - Item #{$item->id}",
                    "quantity" => $item->quantity
                ];
            }
        } else {
            // Fallback if no order items found
            $lineItems[] = [
                "currency" => "PHP",
                "amount" => $order->amount * 100, // Convert to cents
                "description" => "Complete order payment",
                "name" => "Order #{$order->id}",
                "quantity" => 1
            ];
        }

        // Add shipping fee as separate line item
        if ($order->shipping_fee > 0) {
            $lineItems[] = [
                "currency" => "PHP",
                "amount" => $order->shipping_fee * 100, // Convert to cents
                "description" => "Shipping cost",
                "name" => "Shipping Fee ({$order->shipping_method})",
                "quantity" => 1
            ];
        }

        $referenceNumber = Str::random(10);
        $billingDetails = Session::get('checkout_data.billing', []);

        $data = [
            "data" => [
                "attributes" => [
                    "billing" => [
                        "name" => $billingDetails['name'] ?? Auth::user()->name ?? 'Customer',
                        "email" => $billingDetails['email'] ?? Auth::user()->email ?? 'customer@example.com',
                        "phone" => $billingDetails['phone'] ?? '9000000000'
                    ],
                    "send_email_receipt" => true,
                    "show_description" => true,
                    "show_line_items" => true,
                    "line_items" => $lineItems,
                    "payment_method_types" => ["card", "gcash", "paymaya", "qrph"],
                    "success_url" => route('payment.success', ['id' => $order->id, 'gateway' => $gateway]),
                    "cancel_url" => route('payment.cancel', ['gateway' => $gateway]),
                    "reference_number" => $referenceNumber,
                    "description" => "Payment for Order #{$order->id}"
                ]
            ]
        ];

        $apiKey = base64_encode(env('PAYMONGO_SECRET_KEY'));

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Basic ' . $apiKey,
        ])->post('https://api.paymongo.com/v1/checkout_sessions', $data);

        if ($response->successful()) {
            $checkoutUrl = $response->json()['data']['attributes']['checkout_url'];
            $sessionId = $response->json()['data']['id'];

            session(['paymongo_sessionId' => $sessionId]);

            return redirect($checkoutUrl);
        } else {
            $errorMessage = $response->json()['errors'] ?? 'Payment error occurred';

            return redirect()->route('checkout')->with('error', $errorMessage);
        }
    }

    public function paymentSuccess(Request $request)
    {
        $orderId = $request->query('id');
        $gateway = $request->query('gateway');

        $order = Order::findOrFail($orderId);
        $order->update([
            'is_paid' => true,
            'status' => 'processing'
        ]);

        $billingDetails = Session::get('checkout_data.billing', []);
        $userId = Auth::id();

        if ($gateway === 'paymongo') {
            $sessionId = session('paymongo_sessionId');

            $apiKey = base64_encode(env('PAYMONGO_SECRET_KEY'));

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . $apiKey,
            ])->get("https://api.paymongo.com/v1/checkout_sessions/{$sessionId}");

            if ($response->successful()) {
                session()->forget('paymongo_sessionId');

                $responseData = $response->json()['data']['attributes'];
                $billingDetails = $responseData['billing'] ?? $billingDetails;
                $referenceNumber = $responseData['reference_number'];

                // Calculate total amount from line items
                $totalAmount = 0;
                foreach ($responseData['line_items'] as $item) {
                    $totalAmount += ($item['amount'] * $item['quantity']) / 100; // Convert back from cents
                }

                Payment::create([
                    'order_id' => $orderId,
                    'user_id' => $userId,
                    'gateway' => $gateway,
                    'amount' => $totalAmount ?? $order->amount,
                    'name' => $billingDetails['name'] ?? 'Customer',
                    'email' => $billingDetails['email'] ?? 'customer@example.com',
                    'phone' => $billingDetails['phone'] ?? '9000000000',
                    'reference_number' => $referenceNumber,
                ]);
            }

        } elseif ($gateway === 'stripe') {
            $sessionId = session('stripe_checkout_id');
            $stripe = new StripeClient(env('STRIPE_SECRET_KEY'));

            $response = $stripe->checkout->sessions->retrieve($sessionId);
            $responseData = $response->toArray();

            session()->forget('stripe_checkout_id');

            Payment::create([
                'order_id' => $orderId,
                'user_id' => $userId,
                'gateway' => $gateway,
                'amount' => $responseData['amount_total'] / 100, // Convert back from cents
                'name' => $responseData['customer_details']['name'] ?? $billingDetails['name'] ?? 'Customer',
                'email' => $responseData['customer_email'] ?? $billingDetails['email'] ?? 'customer@example.com',
                'phone' => $responseData['customer_details']['phone'] ?? $billingDetails['phone'] ?? '9000000000',
                'reference_number' => $responseData['metadata']['reference_number'],
            ]);
        } else {
            // For cash on delivery or other methods
            Payment::create([
                'order_id' => $orderId,
                'user_id' => $userId,
                'gateway' => 'cash_on_delivery',
                'amount' => $order->amount,
                'name' => $billingDetails['name'] ?? 'Customer',
                'email' => $billingDetails['email'] ?? 'customer@example.com',
                'phone' => $billingDetails['phone'] ?? '9000000000',
                'reference_number' => Str::random(10),
            ]);
        }

      // Clear checkout session
    session()->forget('checkout_data');
    session()->forget('checkout_cart');

    // Clear cart data for authenticated users
    if (Auth::check()) {
        // Get the product IDs that were in the order
        $orderItemSkuIds = $order->orderItems->pluck('product_sku_id')->toArray();

        // Delete cart items with those product SKU IDs
        Cart::where('user_id', Auth::id())
            ->whereIn('sku_id', $orderItemSkuIds)
            ->delete();
    } else {
        // For guests, clear the entire cart session
        session()->forget('cart');
    }

        return redirect()->route('home')->with('success', 'Order placed successfully!');
    }

    public function paymentCancel(Request $request)
    {
        $gateway = $request->query('gateway');

        if ($gateway === "paymongo") {
            $sessionId = session('paymongo_sessionId');

            $apiKey = base64_encode(env('PAYMONGO_SECRET_KEY'));

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . $apiKey,
            ])->post("https://api.paymongo.com/v1/checkout_sessions/{$sessionId}/expire");

            if ($response->successful()) {
                session()->forget('paymongo_sessionId');
            }

        } elseif ($gateway === 'stripe') {
            $sessionId = session('stripe_checkout_id');
            $stripe = new StripeClient(env('STRIPE_SECRET_KEY'));

            $stripe->checkout->sessions->expire($sessionId);
            session()->forget('stripe_checkout_id');
        }

        return redirect()->route('checkout')->with('error', 'Payment was cancelled');
    }
}
