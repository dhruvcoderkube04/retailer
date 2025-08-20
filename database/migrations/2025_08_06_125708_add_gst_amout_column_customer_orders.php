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
        // Add the gst_amount column
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->decimal('shipping_charge_gst_amount', 10, 2)->default(0.00)->after('rto_charge');
            $table->decimal('cod_charge_gst_amount', 10, 2)->default(0.00)->after('shipping_charge_gst_amount');
            $table->decimal('rto_charge_gst_amount', 10, 2)->default(0.00)->after('cod_charge_gst_amount');
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
