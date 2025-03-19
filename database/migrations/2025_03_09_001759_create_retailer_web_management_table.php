<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('retailer_web_management', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->unsignedBigInteger('retailer_id'); // Foreign key reference
            $table->string('store_name');
            $table->string('theme')->default('default');
            $table->string('custom_domain')->nullable()->unique();
            $table->string('subdomain')->unique();
            $table->string('product_listing_key')->unique();
            $table->boolean('is_active')->default(1);
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->foreign('retailer_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('retailer_web_management');
    }
};
