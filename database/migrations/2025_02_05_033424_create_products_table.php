<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('brand_image')->nullable();
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->foreignId('brand_id')->nullable()->constrained()->onDelete('cascade'); // Add this
    $table->timestamps();
});


        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('product_image')->nullable();
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->decimal('price', 10, 2)->default(0.00);
            $table->integer('stock')->default(0); // Added stock column (default 0)
            $table->enum('status', ['Stock In', 'Sold Out', 'Coming Soon']);
            $table->boolean('featured')->default(false); // Updated status column without default
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_s_k_u_s'); // Drop dependent table first
        Schema::dropIfExists('products'); // Now 'products' can be safely dropped
        Schema::dropIfExists('categories'); // Drop 'categories' next
        Schema::dropIfExists('brands'); // Finally, drop 'brands'
    }


};
