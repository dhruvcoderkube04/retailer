<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('store_customers_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('firstname'); // Full name
            $table->string('lastname'); // Full name
            $table->string('phone_number');
            $table->string('email')->unique();
            $table->string('password');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('user_token')->unique(); // Random hash token
            $table->boolean('is_active')->default(false); // Email verification flag
            $table->string('email_verification_token')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')
                ->references('id')
                ->on('customer_details')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_customers_details');
    }
};
