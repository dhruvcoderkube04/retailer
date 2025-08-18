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
        Schema::create('account_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_order_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->enum('user_type', ['retailer', 'wholesaler']);
            $table->string('description');
            $table->enum('amount_type', ['add', 'minus']);
            $table->decimal('total_amount', 10, 2);
            $table->json('charges')->nullable();
            $table->decimal('net_amount', 10, 2);
            $table->decimal('current_balance', 10, 2);
            $table->enum('order_type', ['completed', 'cancelled', 'returned']);
            $table->tinyInteger('status')->default(0)->comment('0 = pending, 1 = completed');
            $table->timestamps();

            // foreign keys
            $table->foreign('customer_order_id')->references('id')->on('customer_orders')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_transactions');
    }
};
