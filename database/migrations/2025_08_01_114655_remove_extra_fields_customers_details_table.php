<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customer_details', function (Blueprint $table) {
            $table->dropColumn([
                'password',
                'user_token',
                'is_active',
                'email_verification_token',
                'email_verified_at',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_details', function (Blueprint $table) {
            $table->string('password')->nullable()->after('email');
            $table->string('user_token')->nullable()->after('password');
            $table->boolean('is_active')->default(false)->after('user_token');
            $table->string('email_verification_token')->nullable()->after('pincode');
            $table->timestamp('email_verified_at')->nullable()->after('email_verification_token');
        });
    }
};
