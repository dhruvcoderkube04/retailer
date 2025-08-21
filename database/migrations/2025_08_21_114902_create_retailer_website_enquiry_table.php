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
        Schema::create('retailer_website_enquiry', function (Blueprint $table) {
            $table->id();
        
            $table->unsignedBigInteger('retailer_id'); 
            $table->string('firstname');
            $table->string('lastname')->nullable();
            $table->string('email');
            $table->string('phone_number')->nullable();
            $table->string('subject');
            $table->text('message');
            $table->boolean('subscribe')->default(false);
            $table->timestamps();
        
            $table->foreign('retailer_id')
                  ->references('id')
                  ->on('users') 
                  ->onDelete('cascade'); 
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retailer_website_enquiry');
    }
};
