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
        // Add the new ENUM value
        DB::statement("ALTER TABLE customer_orders MODIFY COLUMN status ENUM(
            'pending',
            'approved_by_retailer',
            'transfered_retailer_to_wholesaler',
            'approved_by_wholesaler',
            'pickup',
            'in_transit',
            'ofd',
            'delivered',
            'rto',
            'rtn_to_seller',
            'ndr',
            'close',
            'cancel',
            'lost'
        ) DEFAULT 'pending'");

        // Add the ndr_at column
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->dateTime('ndr_at')->nullable()->after('ofd_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
