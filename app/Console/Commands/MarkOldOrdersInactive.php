<?php

namespace App\Console\Commands;

use App\Models\CustomerOrders;
use Illuminate\Console\Command;
use Carbon\Carbon;

class MarkOldOrdersInactive extends Command
{
    protected $signature = 'orders:mark-inactive';
    protected $description = 'Mark orders as inactive if they are pending for more than 48 hours';

    public function handle(): void
    {
        $cutoff = Carbon::now()->subHours(48);

        $orders = CustomerOrders::where('status','pending')
            ->where('created_at', '<=', $cutoff)
            ->where('order_process_by', 'retailer')
            ->get();

        $orders->each(function ($order) {
            $order->update(['status' => 'inactive']);
        });

        $this->info(" {$orders->count()} order(s) marked as inactive.");
    }
}

