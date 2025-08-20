<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Disable FK checks & truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('customer_orders')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Schema::table('customer_orders', function (Blueprint $table) {
            $table->dropForeign('customer_orders_product_id_foreign');
            $table->dropForeign('customer_orders_retailer_clone_product_id_foreign');

            $table->foreignId('order_product_id')
                ->after('customer_id')
                ->comment('order_product_details')
                ->constrained('order_product_details')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Disable FK checks & truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('customer_orders')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Schema::table('customer_orders', function (Blueprint $table) {
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');

            $table->foreign('retailer_clone_product_id')
                ->references('id')
                ->on('retailer_clone_products')
                ->onDelete('cascade');

            $table->dropForeign('customer_orders_order_product_id_foreign');
            $table->dropColumn('order_product_id');
        });
    }
};
