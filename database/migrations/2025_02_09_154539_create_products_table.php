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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique()->nullable();
            $table->foreignId('wholesaler_id')->constrained('users')->onDelete('cascade'); // Assuming wholesalers are in users table

            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('brand_name')->nullable();
            $table->json('tags')->nullable();

            // Stock & Price Details
            $table->integer('quantity')->default(0);
            $table->decimal('price', 10, 2);
            $table->decimal('discount_price', 10, 2)->nullable();

            // Product Media
            $table->json('images')->nullable(); // Store multiple images as JSON
            $table->json('videos')->nullable(); // Store multiple videos as JSON
            $table->string('url')->nullable();

            // Additional Details
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->json('specifications')->nullable(); // Store product specs as JSON

            // SEO Fields
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
        Schema::dropIfExists('products');
    }
};
