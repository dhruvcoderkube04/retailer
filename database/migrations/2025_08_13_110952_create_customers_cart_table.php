<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customers_cart', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable(); // Now nullable
            $table->unsignedBigInteger('retailer_product_id')->nullable(); // New FK

            $table->integer('quantity')->nullable(); // For cart only
            $table->enum('type', ['wishlist', 'cart']);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            // Foreign keys
            $table->foreign('customer_id')->references('id')->on('customer_details')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('retailer_product_id')->references('id')->on('retailer_clone_products')->onDelete('set null');

            // Unique constraint
            $table->unique(['customer_id', 'product_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers_cart');
    }
};
