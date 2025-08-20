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
        Schema::table('category_suggestions', function (Blueprint $table) {
            $table->unsignedBigInteger('wholesaler_id')->nullable()->change();

            $table->unsignedBigInteger('retailer_id')->nullable()->after('wholesaler_id');
            $table->foreign('retailer_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('category_suggestions', function (Blueprint $table) {
            $table->dropForeign(['retailer_id']);
            $table->dropColumn('retailer_id');
        });
    }
};
