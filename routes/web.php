<?php
use App\Models\Order;

use App\Livewire\Cart;
use App\Livewire\Shop;
use App\Models\Payment;
use App\Livewire\Product;
use App\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;


Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/orders', function () {
    $orders = Order::with('payment')->paginate(5);
    return view('orders.index', compact('orders'));
})->name('orders');

Route::get('/payments', function () {
    $payments = Payment::with('order')->paginate(5);
    return view('payments.index', compact('payments'));
})->name('payments');

Route::controller(PaymentController::class)->group(function () {
    Route::get('payment/{id}/{gateway}', 'payment')->name('payment');
    Route::get('payment-success', 'paymentSuccess')->name('payment.success');
    Route::get('payment-cancel', 'paymentCancel')->name('payment.cancel');
});




Route::get('/home', function () {
    return view('components.layouts.app'); // Correct path to your app.blade.php
});


Route::get('/shop', Shop::class)->name('shop');
Route::get('/cart', Cart::class)->name('cart');
Route::get('/product/{id}', Product::class)->name('product.show');


