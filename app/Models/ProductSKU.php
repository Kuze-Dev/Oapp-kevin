<?php

namespace App\Models;

use App\Models\Product;
use App\Models\OrderItem;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValues;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSKU extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'stock',
        'sku_image',
        'attributes',
    ];

    protected $cast = [
        'attributes' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function Cart(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function OrderItem(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

   

    // public function productAttribute(): BelongsTo
    // {
    //     return $this->belongsTo(ProductAttribute::class);
    // }

    // public function productAttributeValue(): BelongsTo
    // {
    //     return $this->belongsTo(ProductAttributeValues::class);
    // }
}
