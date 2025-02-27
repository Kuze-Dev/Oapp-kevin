<?php

namespace App\Models;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductSKU;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_sku_id',
        'user_id',
        'quantity',
        'price',
        'subtotal',
    ];

    // Relationships
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function productSku(): BelongsTo
    {
        return $this->belongsTo(ProductSKU::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


}
