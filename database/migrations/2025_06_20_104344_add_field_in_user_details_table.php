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
            $table->enum('wallet_status', ['pending', 'submitted', 'processing', 'approved', 'rejected', 'attempt_limit_reached'])
                ->default('pending')
                ->after('pending_wallet');
            $table->string('verification_code')->nullable()->after('wallet_status');
            $table->integer('wallet_verification_attempt')->default(0)->after('verification_code');
            $table->datetime('bank_details_submitted_at')->nullable()->after('cancel_cheque');
            $table->datetime('bank_details_verified_at')->nullable()->after('bank_details_submitted_at');
        });

        DB::table('user_details')->update([
            'account_number' => null,
            'ifsc_code' => null,
            'account_holder_name' => null,
            'pancard_number' => null,
            'pan_image' => null,
            'aadhar_image' => null,
            'cancel_cheque' => null,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->dropColumn([
                'wallet_status',
                'verification_code',
                'wallet_verification_attempt',
                'bank_details_submitted_at',
                'bank_details_verified_at'
            ]);
        });
    }
};
