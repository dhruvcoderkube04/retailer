<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
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
        // lorrigo shipment status
        // Step 1: bucket_id to constant key (status name)
        $bucketKeyMap = [
            0   => 'NEW',
            1   => 'READY_TO_SHIP',
            2   => 'IN_TRANSIT',
            3   => 'NDR',
            4   => 'DELIVERED',
            5   => 'RTO',
            6   => 'CANCELED',
            7   => 'LOST_DAMAGED',
            8   => 'DISPOSED',
            9   => 'RTO_DELIVERED',

            101 => 'RETURN_CONFIRMED',
            102 => 'RETURN_PICKED',
            103 => 'RETURN_CANCELLATION',
            104 => 'RETURN_DELIVERED',
            105 => 'RETURN_SHIPMENT_LOST',
        ];

        // Step 2: key (status name) to text label
        $statusTextMap = [
            'NEW'                        => 'pending',
            'READY_TO_SHIP'              => 'pickup',
            'IN_TRANSIT'                 => 'in_transit',
            'NDR'                        => 'rto',    //added by me
            'DELIVERED'                  => 'delivered',
            'RTO'                        => 'rto',
            'RTO_DELIVERED'              => 'rtn_to_seller', // added by me
            'CANCELED'                   => 'cancel',
            'LOST_DAMAGED'               => 'lost',
            'DISPOSED'                   => 'lost',   // added by me

            'RETURN_CONFIRMED'           => 'rtn_to_seller',
            'RETURN_ORDER_MANIFESTED'    => 'rtn_to_seller',
            'RETURN_PICKED'              => 'rtn_to_seller',
            'RETURN_CANCELLATION'        => 'rtn_to_seller',
            'RETURN_DELIVERED'           => 'rtn_to_seller',
            'RETURN_OUT_FOR_PICKUP'      => 'rtn_to_seller',
            'RETURN_IN_TRANSIT'          => 'rtn_to_seller',
            'RETURN_CANCELLED_BY_SMARTSHIP' => 'rtn_to_seller',
            'RETURN_CANCELLED_BY_CLIENT'    => 'rtn_to_seller',
            'RETURN_SHIPMENT_LOST'       => 'rtn_to_seller',
        ];

        $stageDateMap = [
            'pending' => 'created_at',
            'approved-by-retailer' => 'approved_by_retailer_at',
            'transferred-to-wholesaler' => 'transfered_retailer_to_wholesaler_at',
            'pickup' => 'pickup_at',
            'in_transit' => 'in_transit_at',
            'ofd' => 'ofd_at',
            'delivered' => 'delivered_at',
            'rto' => 'rto_at',
            'rtn_to_seller' => 'rtn_to_seller_at',
            'close' => 'close_at',
            'cancel' => 'cancel_at',
            'lost' => 'lost_at',
        ];

        // Step 1: Get all orders that need tracking
        $orders = DB::table('customer_orders')
            ->whereIn('status', ['in_transit', 'pickup', 'ofd','rto','delivered','rtn_to_seller','lost','cancel'])
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
                            // status pending for in fship
                        ]);

                    Log::info("✅ Order #{$order->order_id} updated: {$summary['status']}");
                }
                // Lorrigo-style response
                elseif (isset($response['valid']) && $response['valid'] && isset($response['order'])) {

                    // Sample: you get bucket_id from API
                    $bucket_id = $response['order']['bucket'];

                    // Step 3: Resolve to status text
                    $key = $bucketKeyMap[$bucket_id] ?? null;
                    $bucket_status = $key ? ($statusTextMap[$key] ?? '') : 'unknown';
                    $dateColumn = $stageDateMap[$bucket_status] ?? null;


                    $latestStage = collect($response['order']['orderStages'] ?? [])->last();
                    $status = $latestStage['action'] ?? $order->shipment_status;

                    $updateData = [
                        'shipment_status' => $status,
                        'fulfilledby' => $response['order']['carrierName'] ?? $order->fulfilledby,
                        'status' => $bucket_status,
                    ];

                    if (is_null($order->shipment_status_updated_at)) {
                        $updateData['shipment_status_updated_at'] = now();
                    }

                    if ($dateColumn && Schema::hasColumn('customer_orders', $dateColumn)) {
                        if (is_null($order->{$dateColumn})) {
                            $updateData[$dateColumn] = now();
                        }
                    }

                    DB::table('customer_orders')
                        ->where('order_id', $order->order_id)
                        ->update($updateData);

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
