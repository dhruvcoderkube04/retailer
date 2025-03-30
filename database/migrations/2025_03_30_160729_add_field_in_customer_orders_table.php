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
            $table->foreignId('pickup_address_id')->nullable()->after('cancelled_by')->constrained('pickup_addresses')->onDelete('cascade');
            $table->string('product_weight')->nullable()->after('pickup_address_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->dropForeign(['pickup_address_id']);
            $table->dropColumn(['pickup_address_id', 'product_weight']);
        });
    }
};
