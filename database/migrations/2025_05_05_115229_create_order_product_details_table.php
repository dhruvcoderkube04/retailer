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
        Schema::create('order_product_details', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_id');
            $table->string('sku')->nullable();
            $table->unsignedBigInteger('wholesaler_id')->nullable();
            $table->unsignedBigInteger('retailer_id')->nullable();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->string('brand_name')->nullable();
            $table->json('tags')->nullable();
            $table->integer('quantity')->default(0);
            $table->decimal('old_price', 10, 2)->nullable();
            $table->decimal('new_price', 10, 2)->nullable();
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->json('images')->nullable();
            $table->json('videos')->nullable();
            $table->string('url')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->json('specifications')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('category_name')->nullable();
            $table->unsignedBigInteger('sub_category_id')->nullable();
            $table->string('sub_category_name')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_product_details');
    }
};
