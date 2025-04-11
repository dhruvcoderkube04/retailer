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
            $table->string('offer_text')->default('');
            $table->string('banner_title')->default('');
            $table->string('banner_sub_title')->default('');
            $table->string('banner_button_title')->default('');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('retailer_web_management', function (Blueprint $table) {
            //
        });
    }
};
