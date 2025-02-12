<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

}



// Schema::create('categories', function (Blueprint $table) {
//     $table->id();
//     $table->string('name');
//     $table->timestamps();
// });
