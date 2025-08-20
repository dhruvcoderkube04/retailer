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
        Schema::table('product_variations', function (Blueprint $table) {
            $table->renameColumn('size', 'product_variation');
            $table->renameColumn('color', 'variation_type');
        });

        Schema::table('product_variations', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->unsignedBigInteger('product_id')->change();

            $table->string('product_variation', 200)->change();
            $table->string('variation_type', 50)->nullable()->change();
            $table->integer('stock')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variations', function (Blueprint $table) {
            $table->renameColumn('product_variation', 'size');
            $table->renameColumn('variation_type', 'color');
        });

        Schema::table('product_variations', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            $table->string('size', 10)->change();
            $table->string('color', 50)->nullable()->change();
            $table->integer('stock')->default(0)->change();
        });
    }
};
