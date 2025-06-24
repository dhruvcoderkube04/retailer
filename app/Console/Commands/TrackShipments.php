<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\CourierServiceManager;

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

        // Step 1: Get all orders that need tracking
        $orders = DB::table('customer_orders')
            ->whereIn('status', ['in_transit', 'pickup', 'ofd'])
            ->whereNotNull('tracking_number')
            ->whereNotNull('courier_partner_code')
            ->get(['order_id', 'tracking_number', 'courier_partner_code']);
        if ($orders->isEmpty()) {
            Log::info('🚫 No orders found for tracking.');
            return;
        }

        // Step 2: Load all courier services (active + inactive)
        $services = CourierServiceManager::getAllServicesForTracking(); // returns array keyed by courier code

        foreach ($orders as $order) {
            $partnerCode = $order->courier_partner_code;

            // Step 3: Match order's courier code with available services
            if (!isset($services[$partnerCode])) {
                Log::warning("⚠️ Skipping order #{$order->order_id}: Unknown or unsupported courier partner code '{$partnerCode}'");
                continue;
            }

            $courierService = $services[$partnerCode];
            Log::info("🔍 Tracking order #{$order->order_id} via courier '{$partnerCode}' with tracking number: {$order->tracking_number}");

            try {
                $response = $courierService->trackPackage($order->tracking_number);

                // FShip-style response
                if (isset($response['status']) && $response['status'] && isset($response['summary'])) {
                    $summary = $response['summary'];

                    DB::table('customer_orders')
                        ->where('order_id', $order->order_id)
                        ->update([
                            'shipment_status' => $summary['status'] ?? $order->shipment_status,
                            'fulfilledby' => $summary['fulfilledby'] ?? $order->fulfilledby,
                            'shipment_status_updated_at' => now(),
                        ]);

                    Log::info("✅ Order #{$order->order_id} updated: {$summary['status']}");
                }
                // Lorrigo-style response
                elseif (isset($response['valid']) && $response['valid'] && isset($response['order'])) {
                    $latestStage = collect($response['order']['orderStages'] ?? [])->last();
                    $status = $latestStage['action'] ?? $order->shipment_status;

                    DB::table('customer_orders')
                        ->where('order_id', $order->order_id)
                        ->update([
                            'shipment_status' => $status,
                            'fulfilledby' => $response['order']['carrierName'] ?? $order->fulfilledby,
                            'shipment_status_updated_at' => now(),
                        ]);

                    Log::info("✅ Order #{$order->order_id} updated (Lorrigo): {$status}");
                }
                // Unrecognized format
                else {
                    Log::warning("⚠️ Tracking failed for order #{$order->order_id}: No valid summary in response");
                }
            } catch (\Exception $e) {
                Log::error("❌ Tracking error for order #{$order->order_id}: " . $e->getMessage());
            }

        }

        Log::info('✅ TrackShipments command completed at ' . now());
    }
}
