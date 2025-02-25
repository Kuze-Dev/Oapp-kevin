<?php

namespace App\Models;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductAttributeValues;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAttribute extends Model
{
    //
    protected $fillable = [
        'type',
        'product_id',
    ];


    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productAttributeValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValues::class);
    }

    public function Cart(): HasMany
    {
        return $this->hasMany(Cart::class);
    }
}

