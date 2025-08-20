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
        Schema::table('retailer_products', function (Blueprint $table) {
            $table->string('product_name')->nullable()->after('product_id');
            $table->string('product_slug')->nullable()->after('product_name');
            $table->text('product_description')->nullable()->after('product_slug');
            $table->text('product_images')->nullable()->after('product_description');
            $table->text('product_videos')->nullable()->after('product_images');
            $table->enum('product_status', ['active', 'inactive'])->nullable()->after('product_videos');
            $table->boolean('is_deleted_product')->default(0)->after('product_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('retailer_products', function (Blueprint $table) {
            $table->dropColumn(['product_name', 'product_slug', 'product_description', 'product_images', 'product_videos', 'product_status', 'is_deleted_product']);
        });
    }
};
