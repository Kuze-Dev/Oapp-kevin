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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete(); // If cart belongs to logged-in user
           $table->string('session_id')->nullable(); // If cart is for guest users
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('sku_id')->constrained('product_s_k_u_s')->cascadeOnDelete();
            $table->integer('quantity')->default(1);  // SKU reference
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
