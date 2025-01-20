<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    
    public function payment(string $gateway)
    {  
        
        abort_if(
            ! in_array($gateway, ['stripe','paymongo']) ,
            400,
            'Payment Gateway Not Supported'
        );
        

    }
}
