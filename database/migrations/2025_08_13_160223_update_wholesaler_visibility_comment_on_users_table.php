<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_all_wholesaler_visible')
                ->comment('0 = not-visible, 1 = visible, 2 = request')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_all_wholesaler_visible')
                ->comment('0 = not-visible, 1 = visible')
                ->change();
        });
    }
};

