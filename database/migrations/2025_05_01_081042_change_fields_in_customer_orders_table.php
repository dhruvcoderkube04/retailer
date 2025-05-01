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
        // Disable foreign key checks and truncate the table
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('customer_orders')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Modify ENUM values
        DB::statement("ALTER TABLE customer_orders MODIFY COLUMN status ENUM(
            'pending',
            'approved_by_retailer',
            'transfered_retailer_to_wholesaler',
            'approved_by_wholesaler',
            'pickup',
            'in_transit',
            'ofd',
            'delivered',
            'rto',
            'rtn_to_seller',
            'close',
            'cancel',
            'lost'
        ) DEFAULT 'pending'");

        // 2. Add and drop columns
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->foreignId('order_process_by')
                ->nullable()
                ->after('final_amount')
                ->comment('user_id');

            $table->timestamp('in_transit_at')->nullable()->after('shipped_by_retailer_at');
            $table->timestamp('ofd_at')->nullable()->after('in_transit_at');
            $table->timestamp('rto_at')->nullable()->after('delivered_by_retailer_at');
            $table->timestamp('rtn_to_seller_at')->nullable()->after('rto_at');
            $table->timestamp('close_at')->nullable()->after('rtn_to_seller_at');
            $table->timestamp('lost_at')->nullable()->after('cancelled_by_customer_at');

            $table->dropColumn([
                'shipped_by_wholesaler_at',
                'delivered_by_wholesaler_at',
                'cancelled_by_retailer_at',
                'cancelled_by_wholesaler_at',
                'retailer_transit_at',
                'wholesaler_transit_at'
            ]);
        });

        // 3. Rename columns
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->renameColumn('confirmed_by_retailer_at', 'approved_by_retailer_at');
            $table->renameColumn('confirmed_by_wholesaler_at', 'approved_by_wholesaler_at');
            $table->renameColumn('shipped_by_retailer_at', 'pickup_at');
            $table->renameColumn('delivered_by_retailer_at', 'delivered_at');
            $table->renameColumn('cancelled_by_customer_at', 'cancel_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Disable foreign key checks and truncate the table
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('customer_orders')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Revert ENUM values
        DB::statement("ALTER TABLE customer_orders MODIFY COLUMN status ENUM(
            'pending',
            'transfered_retailer_to_wholesaler',
            'confirmed_by_retailer',
            'confirmed_by_wholesaler',
            'shipped_by_retailer',
            'shipped_by_wholesaler',
            'delivered_by_retailer',
            'delivered_by_wholesaler',
            'cancelled_by_customer',
            'cancelled_by_retailer',
            'cancelled_by_wholesaler',
            'received'
        ) DEFAULT 'pending'");

        Schema::table('customer_orders', function (Blueprint $table) {
            // Drop added columns
            $table->dropForeign(['order_process_by']);
            $table->dropColumn('order_process_by');
            $table->dropColumn([
                'in_transit_at',
                'ofd_at',
                'rto_at',
                'rtn_to_seller_at',
                'close_at',
                'lost_at'
            ]);

            // Recreate dropped columns
            $table->timestamp('shipped_by_wholesaler_at')->nullable()->after('shipped_by_retailer_at');
            $table->timestamp('delivered_by_wholesaler_at')->nullable()->after('delivered_by_retailer_at');
            $table->timestamp('cancelled_by_retailer_at')->nullable()->after('cancelled_by_customer_at');
            $table->timestamp('cancelled_by_wholesaler_at')->nullable()->after('cancelled_by_retailer_at');
            $table->timestamp('retailer_transit_at')->nullable()->after('confirmed_by_retailer_at');
            $table->timestamp('wholesaler_transit_at')->nullable()->after('confirmed_by_wholesaler_at');
        });

        // Revert renamed columns
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->renameColumn('approved_by_retailer_at', 'confirmed_by_retailer_at');
            $table->renameColumn('approved_by_wholesaler_at', 'confirmed_by_wholesaler_at');
            $table->renameColumn('pickup_at', 'shipped_by_retailer_at');
            $table->renameColumn('delivered_at', 'delivered_by_retailer_at');
            $table->renameColumn('cancel_at', 'cancelled_by_customer_at');
        });
    }
};
