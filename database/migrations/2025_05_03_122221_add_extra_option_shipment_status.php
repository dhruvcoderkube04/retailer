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
            $table->string('shipment_status')->nullable()->after('status'); // adjust position as needed
            $table->string('fulfilledby')->nullable()->after('shipment_status');
            $table->timestamp('shipment_status_updated_at')->nullable()->after('fulfilledby');
            $table->unsignedBigInteger('courier_partner_id')->nullable()->after('fulfilledby'); // Add foreign key reference
            $table->foreign('courier_partner_id')->references('id')->on('courier_partners')->onDelete('set null'); // Create foreign key relationship
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
