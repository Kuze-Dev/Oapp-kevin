<?php

use App\Models\Order;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {

    $orders = Order::all();
    return view('welcome',compact('orders'));
});



