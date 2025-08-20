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
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            Schema::table('withdrawal_requests', function (Blueprint $table) {
                $table->enum('request_type', ['to_account', 'to_wholesaler'])->nullable()->after('user_type');

                $table->unsignedBigInteger('wholesaler_id')->after('request_type')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            $table->dropColumn(['request_type', 'wholesaler_id']);
        });
    }
};
