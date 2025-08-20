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
        Schema::table('account_transactions', function (Blueprint $table) {
            $table->renameColumn('product_amount', 'transaction_amount');
            $table->renameColumn('total_amount', 'final_transaction_amount');
        });
    
        Schema::table('account_transactions', function (Blueprint $table) {
            $table->dropColumn('amount_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_transactions', function (Blueprint $table) {
            $table->enum('amount_type', ['add', 'minus'])->nullable();
        });

        Schema::table('account_transactions', function (Blueprint $table) {
            $table->renameColumn('transaction_amount', 'product_amount');
            $table->renameColumn('final_transaction_amount', 'total_amount');
        });
    }
};
