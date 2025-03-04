<?php

namespace App\Models;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    //
    protected $fillable = [
        'name',

    ];

    public function product(): HasOne
{
    return $this->hasOne(Product::class);
}

public function brand(): BelongsTo
{
    return $this->belongsTo(Brand::class);
}




}



// Schema::create('categories', function (Blueprint $table) {
//     $table->id();
//     $table->string('name');
//     $table->timestamps();
// });
