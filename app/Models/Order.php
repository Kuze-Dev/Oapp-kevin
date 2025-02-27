<?php

namespace App\Models;

use App\Models\User;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'product_id',
        'order_number',
        'quantity',
        'amount',
        'is_paid',
        'shipping_method',
        'shipping_fee',
        'status',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'phone',
        'notes',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }


}
