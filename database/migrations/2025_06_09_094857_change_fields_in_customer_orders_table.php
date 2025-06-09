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
            $table->dropForeign(['variation_id']);
            $table->dropColumn('variation_id');

            $table->string('product_variation')->nullable()->after('wholesaler_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->dropColumn('product_variation');
            
            $table->unsignedBigInteger('variation_id')->nullable()->after('wholesaler_id');
        });
    }
};
