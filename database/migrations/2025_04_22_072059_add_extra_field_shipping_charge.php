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
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->string('service_mode')->after('courier_service')->nullable();
            $table->decimal('shipping_charge', 10, 2)->after('service_mode')->nullable();
            $table->decimal('cod_charge', 10, 2)->after('shipping_charge')->nullable();
            $table->decimal('rto_charge', 10, 2)->after('cod_charge')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_orders', function (Blueprint $table) {
            //
        });
    }
};
