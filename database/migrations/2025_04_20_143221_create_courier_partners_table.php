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
        Schema::create('courier_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. Bluedart, FShip
            $table->string('code'); // e.g. 'bluedart', 'fship'
            $table->string('url');
            $table->string('api_key');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courier_partners');
    }
};
