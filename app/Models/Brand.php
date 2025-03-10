<?php

namespace App\Models;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Brand extends Model
{
    //
    protected $fillable = [
        'name',
        'brand_image',
    ];
    public function product(): HasOne
{
    return $this->hasOne(Product::class);
}

public function category(): BelongsTo
{
    return $this->belongsTo(Category::class);
}





}


// Schema::create('brands', function (Blueprint $table) {
//     $table->id();
//     $table->string('name');
//     $table->string('brand_image')->nullable();
//     $table->timestamps();
// });
