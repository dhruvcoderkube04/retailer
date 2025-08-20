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
        // First, drop the existing column
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->dropColumn('order_process_by');
        });

        // Then, add the new enum column
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->enum('order_process_by', ['retailer', 'wholesaler'])->default('retailer')->after('final_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, drop the enum column
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->dropColumn('order_process_by');
        });

        // Then, restore the original unsignedBigInteger column
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('order_process_by')
                ->nullable()
                ->after('final_amount')
                ->comment('user_id');
        });
    }
};
