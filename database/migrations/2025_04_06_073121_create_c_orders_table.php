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
        Schema::create('c_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->nullable();
            $table->foreignId('customer_id')->constrained('customer_details')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('cascade');
            $table->unsignedBigInteger('retailer_clone_product_id')->nullable();
            $table->foreign('retailer_clone_product_id')->references('id')->on('retailer_clone_products')->onDelete('cascade');
            $table->foreignId('retailer_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('wholesaler_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('final_amount', 10, 2)->default(0);
            // order status tracking
            $table->enum('status', [
                'new',
                'processing',
                'pickups',
                'ready_to_ship',
                'transit',
                'ofd',
                'delivered',
                'rto',
                'received',
                'cancel',
                'close',
            ])->default('new');

            // delivered or cancelled user
            $table->foreignId('delivered_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');

            $table->text('cancelled_reason')->nullable();

            $table->foreignId('pickup_address_id')->nullable()->constrained('pickup_addresses')->onDelete('cascade');
            $table->string('product_weight')->nullable();
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
        Schema::dropIfExists('c_orders');
    }
};
