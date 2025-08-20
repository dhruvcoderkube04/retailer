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
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('wholesaler_id')->nullable()->change();
            $table->unsignedBigInteger('retailer_id')->nullable()->change();

            $table->unsignedBigInteger('product_id')->nullable()->change();

          
            $table->unsignedBigInteger('retailer_clone_product_id')->nullable()->after('product_id');
            $table->foreign('retailer_clone_product_id')->references('id')->on('retailer_clone_products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('wholesaler_id')->nullable(false)->change();
            $table->unsignedBigInteger('retailer_id')->nullable(false)->change();

            $table->dropForeign(['retailer_clone_product_id']);

            // Drop column
            $table->dropColumn('retailer_clone_product_id');

            // Revert product_id back to NOT NULL (if necessary)
            $table->unsignedBigInteger('product_id')->nullable(false)->change();
        });
    }
};
