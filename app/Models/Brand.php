<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

}


// Schema::create('brands', function (Blueprint $table) {
//     $table->id();
//     $table->string('name');
//     $table->string('brand_image')->nullable();
//     $table->timestamps();
// });
