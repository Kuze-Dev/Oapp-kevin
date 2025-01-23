<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Stripe\StripeClient;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function payment($id , string $gateway)
    {      
        $order = Order::findOrFail($id);

        abort_if(
            ! in_array($gateway, ['stripe', 'paymongo']) || $order->is_paid,
            400,
            'Payment Gateway Not Supported or Order is paid'
        );

        return $gateway === 'stripe' ? $this->payWithStripe($order, $gateway) : $this->payWithPaymongo($order, $gateway);
    }

    private function payWithStripe($order, $gateway)
    {
        $stripe = new StripeClient(env('STRIPE_SECRET_KEY'));
        $referenceNumber = Str::random(10);

        $lineItems = [[
            'price_data' => [
                'currency' => 'php',
                'product_data' => [
                    'name' => $order->order_name,
                    'description' => $order->order_description,
                ],
                'unit_amount' => $order->amount * 100,
            ],
            'quantity' => $order->quantity,
        ]];

        $checkout_session = $stripe->checkout->sessions->create([
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('payment.success', ['id' => $order->id, 'gateway' => $gateway]),
            'cancel_url' => route('payment.cancel', ['gateway' => $gateway]),
            'customer_email' => 'reynaldomahumot8@gmail.com',
            'metadata' => [ 
                'customer_name' => 'Reynaldo Mahumot',
                'reference_number' => $referenceNumber,
            ],
        ]);

        session(['stripe_checkout_id' => $checkout_session->id]);

        return redirect($checkout_session->url);
    }

    private function payWithPaymongo($order, $gateway)
    {
        $lineItems = [
            [
                "currency" => "PHP",
                "amount" => $order->amount * 100,
                "description" => $order->order_description,
                "name" => $order->order_name,
                "quantity" => $order->quantity
            ],
        ];
        $referenceNumber = Str::random(10);
        $data = [
            "data" => [
                "attributes" => [
                    "billing" => [
                        "name" => 'Reynaldo Mahumot',
                        "email" => 'reynaldomahumot8@gmail.com',
                        "phone" => '9060816596'
                    ],
                    "send_email_receipt" => false,
                    "show_description" => true,
                    "show_line_items" => true,
                    "line_items" => $lineItems,
                    "payment_method_types" => ["card", "gcash", "paymaya", "qrph"],
                    "success_url" => route('payment.success', ['id' => $order->id, 'gateway' => $gateway]),
                    "cancel_url" => route('payment.cancel', ['gateway' => $gateway]),
                    "reference_number" => $referenceNumber,
                    "description" => "testing"
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
            $errorMessage = $response->json()['errors'];

            return redirect()->back()->with('error', $errorMessage);
        }
    }

    public function paymentSuccess(Request $request)
    {
        $orderId = $request->query('id');
        $gateway = $request->query('gateway');
    
        $order = Order::findOrFail($orderId);
        $order->update(['is_paid' => true]);

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

                $billingDetails = $response->json()['data']['attributes']['billing'];
                $referenceNumber = $response->json()['data']['attributes']['reference_number'];
                $lineItems = $response->json()['data']['attributes']['line_items'];

                Payment::create([
                    'order_id' => $orderId,
                    'gateway' => $gateway,
                    'amount' => $lineItems[0]['amount'] * $lineItems[0]['quantity'] / 100,                
                    'name' => $billingDetails['name'],
                    'email' => $billingDetails['email'],
                    'phone' => $billingDetails['phone'],
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
                'gateway' => $gateway,
                'amount' => $responseData['amount_total'] / 100,
                'name' => $responseData['customer_details']['name'],
                'email' => $responseData['customer_email'],
                'phone' => $responseData['customer_details']['phone'] ?? '',
                'reference_number' => $responseData['metadata']['reference_number'],
            ]);
    
        } else {
            return redirect()->route('orders')->withError('Payment Gateway Not Supported');
        }
        return redirect()->route('orders');
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
    
            $response = $stripe->checkout->sessions->expire($sessionId);

            $responseData = $response->toArray(); 
        } else {
            return redirect()->route('home');
        }
        return redirect()->route('home');
    }
}
