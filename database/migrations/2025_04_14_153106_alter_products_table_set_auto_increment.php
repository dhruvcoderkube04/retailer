<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE products AUTO_INCREMENT = 1000001');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE products AUTO_INCREMENT = 1');
    }
};


// products
// product_variations
// retailer_clone_products
// customer_orders
// wholesaler_orders

// retailer_products (if product_id not null)