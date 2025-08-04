<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('store_customers_details', function (Blueprint $table) {
            $table->dropUnique('store_customers_details_user_token_unique');
        });
    }

    public function down(): void
    {
        Schema::table('store_customers_details', function (Blueprint $table) {
            $table->unique('user_token');
        });
    }
};
