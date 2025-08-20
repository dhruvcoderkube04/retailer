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
        Schema::table('retailer_web_management', function (Blueprint $table) {
            if (Schema::hasColumn('retailer_web_management', 'wallet')) {
                $table->dropColumn('wallet');
            }
        });

        Schema::table('wholesaler_web_management', function (Blueprint $table) {
            if (Schema::hasColumn('wholesaler_web_management', 'wallet')) {
                $table->dropColumn('wallet');
            }
        });
        
        Schema::table('user_details', function (Blueprint $table) {
            if (!Schema::hasColumn('user_details', 'wallet')) {
                $table->decimal('wallet', 10, 2)->default(0)->after('postal_code');
            }
        });

        Schema::table('withdrawal_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('withdrawal_requests', 'approver_remark')) {
                $table->text('approver_remark')->nullable()->after('remarks');
            }
            if (!Schema::hasColumn('withdrawal_requests', 'account_transaction_id')) {
                $table->foreignId('account_transaction_id')
                    ->nullable()
                    ->after('approver_remark')
                    ->constrained('account_transactions')
                    ->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('retailer_web_management', function (Blueprint $table) {
            if (!Schema::hasColumn('retailer_web_management', 'wallet')) {
                $table->decimal('wallet', 10, 2)->default(0)->after('retailer_id');
            }
        });

        Schema::table('wholesaler_web_management', function (Blueprint $table) {
            if (!Schema::hasColumn('wholesaler_web_management', 'wallet')) {
                $table->decimal('wallet', 10, 2)->default(0)->after('wholesaler_id');
            }
        });

        Schema::table('user_details', function (Blueprint $table) {
            if (Schema::hasColumn('user_details', 'wallet')) {
                $table->dropColumn('wallet');
            }
        });

        Schema::table('withdrawal_requests', function (Blueprint $table) {
            if (Schema::hasColumn('withdrawal_requests', 'approver_remark')) {
                $table->dropColumn('approver_remark');
            }
            if (Schema::hasColumn('withdrawal_requests', 'account_transaction_id')) {
                $table->dropConstrainedForeignId('account_transaction_id');
            }
        });
    }
};
