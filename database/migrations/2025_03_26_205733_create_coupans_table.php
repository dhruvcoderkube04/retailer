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
        Schema::create('coupans', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Unique coupon code
            $table->string('coupan_code_name')->unique();
            $table->decimal('discount', 8, 2); // Discount amount or percentage
            $table->enum('discount_type', ['fixed', 'percentage'])->default('fixed'); // Type of discount
            $table->integer('usage_limit')->nullable(); // Maximum usage count
            $table->integer('used_count')->default(0); // Number of times used
            $table->timestamp('valid_from')->nullable(); // Start date
            $table->timestamp('valid_until')->nullable(); // Expiry date
            $table->boolean('status')->default(true); // Active/inactive status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupans');
    }
};
