<?php

namespace App\Models;

use App\Models\Brand;
use App\Models\Category;
use App\Models\ProductSKU;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValues;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'product_image',
        'brand_id',
        'category_id',
        'price',
        'stock',
        'status',
        'featured',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function productAttributes(): HasMany
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function productAttributeValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValues::class, 'product_id');
    }


    public function skus(): HasMany
    {
        return $this->hasMany(ProductSKU::class);
    }

// protected $casts = [
//     'attributes' => 'array', // Ensure attributes are treated as an array
// ];


}








// Schema::create('products', function (Blueprint $table) {
//     $table->id();
//     $table->string('name');
//     $table->text('description');
//     $table->string('product_image')->nullable();
//     $table->foreignId('brand_id')->constrained()->onDelete('cascade');
//     $table->foreignId('category_id')->constrained()->onDelete('cascade');
//     $table->enum('status', ['Stock In', 'Sold Out', 'Coming Soon']); // Updated status column without default
//     $table->timestamps();
// });
// }
