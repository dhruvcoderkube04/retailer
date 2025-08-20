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
        DB::statement("ALTER TABLE withdrawal_requests MODIFY COLUMN status ENUM('pending', 'processing', 'completed', 'rejected') NOT NULL DEFAULT 'pending'");

        Schema::table('withdrawal_requests', function (Blueprint $table) {
            $table->bigInteger('transaction_id')->unsigned()->nullable()->after('approver_remark');
            $table->datetime('processing_at')->nullable()->after('account_transaction_id');
            $table->datetime('rejected_at')->nullable()->after('processing_at');
            $table->datetime('completed_at')->nullable()->after('rejected_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE withdrawal_requests MODIFY COLUMN status ENUM('pending', 'on hold', 'completed', 'rejected') NOT NULL DEFAULT 'pending'");

        Schema::table('withdrawal_requests', function (Blueprint $table) {
            $table->dropColumn(['transaction_id', 'processing_at', 'rejected_at', 'completed_at']);
        });
    }
};
