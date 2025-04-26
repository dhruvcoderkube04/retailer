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
        Schema::table('account_transactions', function (Blueprint $table) {
            $table->renameColumn('total_amount', 'product_amount');
            $table->renameColumn('net_amount', 'total_amount');
        });

        // Update user_type enum to include 'admin'
        DB::statement("ALTER TABLE account_transactions MODIFY user_type ENUM('retailer', 'wholesaler', 'admin')");

        // Update order_type enum to include 'other'
        DB::statement("ALTER TABLE account_transactions MODIFY order_type ENUM('completed', 'cancelled', 'returned', 'other')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_transactions', function (Blueprint $table) {
            $table->renameColumn('total_amount', 'net_amount');
            $table->renameColumn('product_amount', 'total_amount');
        });

        // Revert user_type enum back
        DB::statement("ALTER TABLE account_transactions MODIFY user_type ENUM('retailer', 'wholesaler')");

        // Revert order_type enum back
        DB::statement("ALTER TABLE account_transactions MODIFY order_type ENUM('completed', 'cancelled', 'returned')");
    }
};
