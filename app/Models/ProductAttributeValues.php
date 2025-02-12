<?php

namespace App\Models;

use App\Models\ProductAttribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAttributeValues extends Model
{
    //
    protected $fillable = [
        'product_attribute_id',
        'value',
    ];



    public function productAttribute(): BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class);
    }

}
