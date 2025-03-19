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
        Schema::create('customer_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customer_details')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('retailer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('wholesaler_id')->constrained('users')->onDelete('cascade');
            $table->integer('quantity');

            // order status tracking
            $table->enum('status', [
                'pending',
                'transfered_retailer_to_wholesaler',
                'confirmed_by_retailer',
                'confirmed_by_wholesaler',
                'shipped_by_retailer',
                'shipped_by_wholesaler',
                'delivered_by_retailer',
                'delivered_by_wholesaler',
                'cancelled_by_customer',
                'cancelled_by_retailer',
                'cancelled_by_wholesaler',
                'received'
            ])->default('pending');

            // timestamps for order flow
            $table->timestamp('confirmed_by_retailer_at')->nullable();
            $table->timestamp('transfered_retailer_to_wholesaler_at')->nullable();
            $table->timestamp('confirmed_by_wholesaler_at')->nullable();
            $table->timestamp('shipped_by_retailer_at')->nullable();
            $table->timestamp('shipped_by_wholesaler_at')->nullable();
            $table->timestamp('delivered_by_retailer_at')->nullable();
            $table->timestamp('delivered_by_wholesaler_at')->nullable();
            $table->timestamp('cancelled_by_customer_at')->nullable();
            $table->timestamp('cancelled_by_retailer_at')->nullable();
            $table->timestamp('cancelled_by_wholesaler_at')->nullable();
            $table->timestamp('received_at')->nullable();

            // delivered or cancelled user
            $table->foreignId('delivered_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');

            // delivery
            $table->string('tracking_number')->nullable();
            $table->string('courier_service')->nullable();
            $table->dateTime('expected_delivery')->nullable();

            // payment
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->string('payment_method')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_orders');
    }
};
