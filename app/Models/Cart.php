<?php

namespace App\Models;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductSKU;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValues;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    //
    protected $fillable = [
        'user_id',
        'product_id',
        'sku_id',
        'quantity',
        'price',
    ];

public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}

public function product(): BelongsTo
{
    return $this->belongsTo(Product::class);

}


public function productSKU(): BelongsTo
{
    return $this->belongsTo(ProductSKU::class);
}
}
