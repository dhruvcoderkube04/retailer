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
            $table->decimal('wallet', 10,2)->default(0)->after('retailer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('retailer_web_management', function (Blueprint $table) {
            $table->dropColumn('wallet');
        });
    }
};
