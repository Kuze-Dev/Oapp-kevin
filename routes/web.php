<?php
use App\Models\Order;
use App\Livewire\Cart;
use App\Livewire\Shop;
use App\Models\Payment;
use App\Livewire\Product;
use App\Livewire\CheckOut;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Shop, Cart, and Product pages
Route::get('/shop', Shop::class)->name('shop');
Route::get('/cart', Cart::class)->name('cart');
Route::get('/product/{slug}', Product::class)->name('product.show');

// Authentication pages
Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');

Route::middleware(['auth'])->group(function () {
    // Checkout process
    Route::get('/checkout', CheckOut::class)->name('checkout');
    Route::post('/process-checkout', [PaymentController::class, 'processCheckout'])->name('checkout.process');
    // Route::get('/checkout/success', function() {
    //     return view('checkout.success');
    // })->name('checkout.success');

    // Redirect /orders to /home
    Route::get('/home', function () {
        return view('home');
    })->name('home');

    // Payment processing
    Route::controller(PaymentController::class)->group(function () {
        Route::get('payment/{id}/{gateway}', 'payment')->name('payment');
        Route::get('payment-success', 'paymentSuccess')->name('payment.success');
        Route::get('payment-cancel', 'paymentCancel')->name('payment.cancel');
    });
});

Route::get('/home', function () {
    return view('components.layouts.app');
});
