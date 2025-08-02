<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers_cart', function (Blueprint $table) {
            $table->unsignedBigInteger('retailer_product_id')->nullable()->after('product_id');
            $table->unsignedBigInteger('product_id')->nullable()->change();

            $table->foreign('retailer_product_id')
                ->references('id')
                ->on('retailer_clone_products')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers_cart', function (Blueprint $table) {
            $table->dropForeign(['retailer_product_id']);
            $table->dropColumn('retailer_product_id');

            $table->unsignedBigInteger('product_id')->nullable(false)->change();

        });
    }
};
