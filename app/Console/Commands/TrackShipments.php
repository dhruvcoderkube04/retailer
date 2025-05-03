<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TrackShipments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'track:shipment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get shipment status and update in customer order table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('📦 TrackShipments command started at ' . now());

        // Get orders that need tracking
        $orders = DB::table('customer_orders')
            ->whereIn('status', ['in_transit', 'pickup', 'ofd'])
            ->whereNotNull('tracking_number')
            ->get(['order_id', 'tracking_number']);

        if ($orders->isEmpty()) {
            Log::info('🚫 No orders found for tracking.');
            return;
        }

        $courierService = \App\Services\CourierServiceManager::getService();
        foreach ($orders as $order) {
            Log::info("🔍 Tracking order #{$order->order_id} with tracking number: {$order->tracking_number}");

            try {
                $response = $courierService->trackPackage($order->tracking_number);

                // $response is already an array, no need to decode or call
                if ($response['status'] && isset($response['summary'])) {
                    $summary = $response['summary'];

                    DB::table('customer_orders')
                        ->where('order_id', $order->order_id)
                        ->update([
                            'shipment_status' => $summary['status'] ?? $order->shipment_status,
                            'fulfilledby' => $summary['fulfilledby'] ?? $order->fulfilledby,
                            'shipment_status_updated_at' => now(),
                        ]);

                    Log::info("✅ Order #{$order->order_id} updated: {$summary['status']}");
                } else {
                    Log::warning("⚠️ Tracking failed for order #{$order->order_id}: No summary found");
                }
            } catch (\Exception $e) {
                Log::error("❌ Tracking error for order #{$order->order_id}: " . $e->getMessage());
            }
        }

        Log::info('✅ TrackShipments command completed at ' . now());
    }

}
