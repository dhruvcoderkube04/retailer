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
        Schema::table('pickup_addresses', function (Blueprint $table) {
            $table->unsignedBigInteger('courier_partner_id')->nullable()->after('user_id');
            $table->foreign('courier_partner_id')
                ->references('id')
                ->on('courier_partners')
                ->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pickup_addresses', function (Blueprint $table) {
            //
        });
    }
};
