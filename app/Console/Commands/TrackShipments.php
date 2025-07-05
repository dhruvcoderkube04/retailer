<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Services\CourierServiceManager;
use App\Services\OrderStatusService;
use App\Models\CustomerOrder;
use App\Models\CustomerOrders;

class TrackShipments extends Command
{
    protected $signature = 'shipments:track';
    protected $description = 'Get shipment status and update in customer order table';

    public function handle()
    {
        Log::info('📦 TrackShipments command started at ' . now());

        $bucketKeyMap = [
            0 => 'NEW', 1 => 'READY_TO_SHIP', 2 => 'IN_TRANSIT', 3 => 'NDR',
            4 => 'DELIVERED', 5 => 'RTO', 6 => 'CANCELED', 7 => 'LOST_DAMAGED',
            8 => 'DISPOSED', 9 => 'RTO_DELIVERED',
            101 => 'RETURN_CONFIRMED', 102 => 'RETURN_PICKED',
            103 => 'RETURN_CANCELLATION', 104 => 'RETURN_DELIVERED',
            105 => 'RETURN_SHIPMENT_LOST',
        ];

        $statusTextMap = [
            'NEW' => 'pending', 'READY_TO_SHIP' => 'pickup', 'IN_TRANSIT' => 'in_transit',
            'NDR' => 'rto', 'DELIVERED' => 'delivered', 'RTO' => 'rto',
            'RTO_DELIVERED' => 'rtn_to_seller', 'CANCELED' => 'cancel', 'LOST_DAMAGED' => 'lost',
            'DISPOSED' => 'lost', 'RETURN_CONFIRMED' => 'rtn_to_seller',
            'RETURN_ORDER_MANIFESTED' => 'rtn_to_seller', 'RETURN_PICKED' => 'rtn_to_seller',
            'RETURN_CANCELLATION' => 'rtn_to_seller', 'RETURN_DELIVERED' => 'rtn_to_seller',
            'RETURN_OUT_FOR_PICKUP' => 'rtn_to_seller', 'RETURN_IN_TRANSIT' => 'rtn_to_seller',
            'RETURN_CANCELLED_BY_SMARTSHIP' => 'rtn_to_seller',
            'RETURN_CANCELLED_BY_CLIENT' => 'rtn_to_seller',
            'RETURN_SHIPMENT_LOST' => 'rtn_to_seller',
        ];

        $stageDateMap = [
            'pending' => 'created_at', 'approved-by-retailer' => 'approved_by_retailer_at',
            'transferred-to-wholesaler' => 'transfered_retailer_to_wholesaler_at',
            'pickup' => 'pickup_at', 'in_transit' => 'in_transit_at',
            'ofd' => 'ofd_at', 'delivered' => 'delivered_at', 'rto' => 'rto_at',
            'rtn_to_seller' => 'rtn_to_seller_at', 'close' => 'close_at',
            'cancel' => 'cancel_at', 'lost' => 'lost_at',
        ];

        $orders = DB::table('customer_orders')
            ->whereIn('status', ['in_transit', 'pickup', 'ofd', 'rto', 'delivered', 'rtn_to_seller', 'lost', 'cancel'])
            ->whereNotNull('tracking_number')
            ->whereNotNull('courier_partner_code')
            ->get(['order_id', 'tracking_number', 'courier_partner_code']);


        if ($orders->isEmpty()) {
            Log::info('🚫 No orders found for tracking.');
            return;
        }

        $services = CourierServiceManager::getAllServicesForTracking();

        DB::beginTransaction(); // START transaction

        try {
            foreach ($orders as $order) {
                $partnerCode = $order->courier_partner_code;

                if (!isset($services[$partnerCode])) {
                    Log::warning("⚠️ Skipping order #{$order->order_id}: Unknown or unsupported courier partner code '{$partnerCode}'");
                    continue;
                }

                $courierService = $services[$partnerCode];
                Log::info("🔍 Tracking order #{$order->order_id} via courier '{$partnerCode}' with tracking number: {$order->tracking_number}");

                try {
                    $response = $courierService->trackPackage($order->tracking_number);

                    if (isset($response['status']) && $response['status'] && isset($response['summary'])) {
                        $summary = $response['summary'];

                        DB::table('customer_orders')
                            ->where('order_id', $order->order_id)
                            ->update([
                                'shipment_status' => $summary['status'] ?? $order->shipment_status,
                                'fulfilledby' => $summary['fulfilledby'] ?? $order->fulfilledby,
                                'shipment_status_updated_at' => now(),
                            ]);

                            // update status

                        Log::info("✅ Order #{$order->order_id} updated: {$summary['status']}");

                    } elseif (isset($response['valid']) && $response['valid'] && isset($response['order'])) {
                        $bucket_id = $response['order']['bucket'];
                        $key = $bucketKeyMap[$bucket_id] ?? null;
                        $bucket_status = $key ? ($statusTextMap[$key] ?? '') : 'unknown';
                        $dateColumn = $stageDateMap[$bucket_status] ?? null;

                        $latestStage = collect($response['order']['orderStages'] ?? [])->last();
                        $status = $latestStage['action'] ?? $order->shipment_status;

                        $updateData = [
                            'shipment_status' => $status,
                            'fulfilledby' => $response['order']['carrierName'] ?? $order->fulfilledby,
                            // 'status' => $bucket_status,
                        ];

                        if ($dateColumn && Schema::hasColumn('customer_orders', $dateColumn)) {
                            $updateData[$dateColumn] = now();
                        }

                        DB::table('customer_orders')
                            ->where('order_id', $order->order_id)
                            ->update($updateData);

                        Log::info("✅ Order #{$order->order_id} updated (Lorrigo): {$status}");

                        if ($bucket_status === 'delivered') {
                            $orderModel = CustomerOrders::with('retailer')->where('order_id',$order->order_id)->first();

                            if ($orderModel && $orderModel->retailer) {
                                $statusService = new OrderStatusService();
                                [$success, $msg, $finalStatus] = $statusService->handleDeliveredOrder($orderModel->retailer, $orderModel);
                                Log::info("🎯 Delivery processed for order #{$order->order_id}: {$msg}");
                            }
                        }

                    } else {
                        Log::warning("⚠️ Tracking failed for order #{$order->order_id}: No valid summary in response");
                    }
                } catch (\Exception $e) {
                    Log::error("❌ Tracking error for order #{$order->order_id}: " . $e->getMessage());
                }
            }

            DB::commit(); // COMMIT transaction
            Log::info('✅ All orders tracked and updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack(); // ROLLBACK on any failure
            Log::error("❌ Transaction failed. Rolled back. Error: " . $e->getMessage());
        }

        Log::info('✅ TrackShipments command completed at ' . now());
    }
}
