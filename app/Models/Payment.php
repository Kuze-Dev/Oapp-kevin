<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'order_id',
        'gateway',
        'amount',
        'name',
        'email',
        'reference_number',
        'phone'
    ];

    public function order() :BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
