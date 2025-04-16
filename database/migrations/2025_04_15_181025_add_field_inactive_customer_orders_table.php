<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add new column: is_active
        Schema::table('customer_orders', function ($table) {
            $table->text('inactive')->nullable()->after('cancelled_by');
        });

        // Modify ENUM to add 'inactive'
        DB::statement("ALTER TABLE customer_orders MODIFY COLUMN status
            ENUM(
                'pending',
                'transfered_retailer_to_wholesaler',
                'confirmed_by_retailer',
                'confirmed_by_wholesaler',
                'shipped_by_retailer',
                'shipped_by_wholesaler',
                'delivered_by_retailer',
                'delivered_by_wholesaler',
                'cancelled_by_customer',
                'cancelled_by_retailer',
                'cancelled_by_wholesaler',
                'received',
                'inactive'
            ) DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Drop the is_active column
        Schema::table('customer_orders', function ($table) {
            $table->dropColumn('is_active');
        });

        // Remove 'inactive' from ENUM
        DB::statement("ALTER TABLE customer_orders MODIFY COLUMN status
            ENUM(
                'pending',
                'transfered_retailer_to_wholesaler',
                'confirmed_by_retailer',
                'confirmed_by_wholesaler',
                'shipped_by_retailer',
                'shipped_by_wholesaler',
                'delivered_by_retailer',
                'delivered_by_wholesaler',
                'cancelled_by_customer',
                'cancelled_by_retailer',
                'cancelled_by_wholesaler',
                'received'
            ) DEFAULT 'pending'");
    }
};
