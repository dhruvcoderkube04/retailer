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
       // Add new columns
       Schema::table('customer_orders', function (Blueprint $table) {
        $table->timestamp('retailer_transit_at')->nullable()->after('inactive');
        $table->timestamp('wholesaler_transit_at')->nullable()->after('retailer_transit_at');
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
            'transit_by_retailer',
            'transit_by_wholesaler',
            'delivered_by_retailer',
            'delivered_by_wholesaler',
            'cancelled_by_customer',
            'cancelled_by_retailer',
            'cancelled_by_wholesaler',
            'received',
            'inactive'
        ) DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new columns
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->dropColumn('retailer_transit_at');
            $table->dropColumn('wholesaler_transit_at');
        });


    }
};
