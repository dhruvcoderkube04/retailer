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
        Schema::table('user_details', function (Blueprint $table) {
            $table->renameColumn('aadhar_image', 'aadhar_1_image');
        });

        Schema::table('user_details', function (Blueprint $table) {
            $table->string('aadhar_2_image')->nullable()->after('aadhar_1_image');
        });

        DB::table('user_details')->update([
            'wallet_status' => 'pending',
            'verification_code' => null,
            'wallet_verification_attempt' => 0,
            'wallet_verification_reject_reason' => null,
            'account_number' => null,
            'ifsc_code' => null,
            'account_holder_name' => null,
            'pancard_number' => null,
            'pan_image' => null,
            'aadhar_1_image' => null,
            'aadhar_2_image' => null,
            'cancel_cheque' => null,
            'bank_details_submitted_at' => null,
            'bank_details_verified_at' => null,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->renameColumn('aadhar_1_image', 'aadhar_image');
        });

        Schema::table('user_details', function (Blueprint $table) {
            $table->dropColumn('aadhar_2_image');
        });
    }
};
