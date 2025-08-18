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
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->decimal('shipping_charge_profit', 10, 2)->after('rto_charge')->nullable();
            $table->decimal('cod_charge_profit', 10, 2)->after('shipping_charge_profit')->nullable();
            $table->decimal('rto_charge_profit', 10, 2)->after('cod_charge_profit')->nullable();
            $table->renameColumn('charges', 'final_charges');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename column back to 'charges'
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->renameColumn('final_charges', 'charges');
        });

        // Drop the added profit columns
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_charge_profit',
                'cod_charge_profit',
                'rto_charge_profit',
            ]);
        });
    }
};
