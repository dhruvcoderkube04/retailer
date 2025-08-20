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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('retailer_products')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        Schema::table('retailer_products', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable()->after('category_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('retailer_products')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        Schema::table('retailer_products', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
        });
    }
};
