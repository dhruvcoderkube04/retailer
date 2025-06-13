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
        // temporarily disable foreign key checks, truncate, then re-enable
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('retailer_products')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Schema::table('retailer_products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');

            $table->foreignId('sub_category_id')
                ->nullable()
                ->after('wholesaler_id')
                ->constrained('sub_categories')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // temporarily disable foreign key checks, truncate, then re-enable
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('retailer_products')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Schema::table('retailer_products', function (Blueprint $table) {
            $table->dropForeign(['sub_category_id']);
            $table->dropColumn('sub_category_id');

            $table->foreignId('category_id')
                ->nullable()
                ->after('wholesaler_id')
                ->constrained('categories')
                ->onDelete('cascade');
        });
    }
};
