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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_id')->unique(); // Unique ticket identifier
            $table->unsignedBigInteger('user_id'); // Foreign key for user raising the ticket
            $table->string('subject'); // Ticket subject
            $table->string('ref_image');
            $table->text('description'); // Detailed issue description
            $table->string('category'); // Category like Billing, Technical, etc.
            $table->enum('priority', ['Low', 'Medium', 'High'])->default('Medium'); // Ticket priority
            $table->enum('status', ['Pending', 'In Progress', 'Resolved', 'Closed'])->default('Pending'); // Ticket status
            $table->timestamp('resolved_at')->nullable(); // Date when the ticket was resolved
            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
