<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'order_description',
        'amount',
        'is_paid',
    ];

    public function payment() :HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
