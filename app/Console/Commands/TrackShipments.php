<?php

namespace App\Console\Commands;

use App\Mail\CancelOrderMailToCustomer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Services\CourierServiceManager;
use App\Services\OrderStatusService;
use App\Models\CustomerOrder;
use App\Models\CustomerOrders;
use Illuminate\Support\Facades\Mail;

class TrackShipments extends Command
{
    protected $signature = 'track:shipment';
    protected $description = 'Get shipment status and update the retailer orders in customer orders table';

    public function handle()
    {
        Log::info('📦 TrackShipments command started at ' . now());

        $bucketKeyMap = [
            0 => 'NEW',
            1 => 'READY_TO_SHIP',
            2 => 'IN_TRANSIT',
            3 => 'NDR',
            4 => 'DELIVERED',
            5 => 'RTO',
            6 => 'CANCELED',
            7 => 'LOST_DAMAGED',
            8 => 'DISPOSED',
            9 => 'RTO_DELIVERED',
            101 => 'RETURN_CONFIRMED',
            102 => 'RETURN_PICKED',
            103 => 'RETURN_CANCELLATION',
            104 => 'RETURN_DELIVERED',
            105 => 'RETURN_SHIPMENT_LOST',
        ];

        $statusTextMap = [
            'NEW' => 'pending',
            'READY_TO_SHIP' => 'pickup',
            'IN_TRANSIT' => 'in_transit',
            'NDR' => 'ndr',
            'DELIVERED' => 'delivered',
            'RTO' => 'rto',
            'RTO_DELIVERED' => 'rtn_to_seller',
            'CANCELED' => 'cancel',
            'LOST_DAMAGED' => 'lost',
            'DISPOSED' => 'lost',
            'RETURN_CONFIRMED' => 'rtn_to_seller',
            'RETURN_ORDER_MANIFESTED' => 'rtn_to_seller',
            'RETURN_PICKED' => 'rtn_to_seller',
            'RETURN_CANCELLATION' => 'rtn_to_seller',
            'RETURN_DELIVERED' => 'rtn_to_seller',
            'RETURN_OUT_FOR_PICKUP' => 'rtn_to_seller',
            'RETURN_IN_TRANSIT' => 'rtn_to_seller',
            'RETURN_CANCELLED_BY_SMARTSHIP' => 'rtn_to_seller',
            'RETURN_CANCELLED_BY_CLIENT' => 'rtn_to_seller',
            'RETURN_SHIPMENT_LOST' => 'rtn_to_seller',
        ];

        $stageDateMap = [
            'pending' => 'created_at',
            'approved-by-retailer' => 'approved_by_retailer_at',
            'transferred-to-wholesaler' => 'transfered_retailer_to_wholesaler_at',
            'pickup' => 'pickup_at',
            'in_transit' => 'in_transit_at',
            'ofd' => 'ofd_at',
            'ndr' => 'ndr_at',
            'delivered' => 'delivered_at',
            'rto' => 'rto_at',
            'rtn_to_seller' => 'rtn_to_seller_at',
            'close' => 'close_at',
            'cancel' => 'cancel_at',
            'lost' => 'lost_at'
        ];

        $orders = DB::table('customer_orders')
            ->whereIn('status', ['in_transit', 'pickup', 'ofd', 'rto', 'delivered', 'rtn_to_seller','ndr','lost', 'cancel'])
            ->where('order_process_by', 'retailer')
            ->where('checkout_type', 'normal')
            ->whereNotNull('tracking_number')
            ->whereNotNull('courier_partner_code')
            ->get(['order_id', 'tracking_number', 'courier_partner_code']);



        if ($orders->isEmpty()) {
            Log::info('🚫 No orders found for tracking.');
            return;
        }

        $services = CourierServiceManager::getAllServicesForTracking();

        foreach ($orders as $order) {
            DB::beginTransaction();
            try {
                $partnerCode = $order->courier_partner_code;

                if (!isset($services[$partnerCode])) {
                    Log::warning("⚠️ Skipping order #{$order->order_id}: Unknown courier partner '{$partnerCode}'");
                    DB::rollBack();
                    continue;
                }

                $courierService = $services[$partnerCode];
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

                    DB::commit();
                    Log::info("✅ Order #{$order->order_id} updated: {$summary['status']}");
                } elseif (isset($response['valid']) && $response['valid'] && isset($response['order'])) {
                    $bucket_id = $response['order']['bucket'];
                    $key = $bucketKeyMap[$bucket_id] ?? null;
                    $bucket_status = $key ? ($statusTextMap[$key] ?? '') : 'unknown';
                    $dateColumn = $stageDateMap[$bucket_status] ?? null;

                    $latestStage = collect($response['order']['orderStages'] ?? [])->last();
                    $status = $latestStage['action'] ?? $order->shipment_status;
                    $stage_reason  = $latestStage['activity'] ?? '';

                    $updateData = [
                        'shipment_status' => $status,
                        'fulfilledby' => $response['order']['carrierName'] ?? $order->fulfilledby,
                        'shipment_activity' => $stage_reason
                    ];

                    if ($dateColumn && Schema::hasColumn('customer_orders', $dateColumn)) {
                        $updateData[$dateColumn] = now();
                    }

                    DB::table('customer_orders')->where('order_id', $order->order_id)->update($updateData);

                    $orderModel = CustomerOrders::with('retailer')->where('order_id', $order->order_id)->first();

                    if ($orderModel && $orderModel->retailer) {
                        $statusService = new OrderStatusService();

                        // IN-TRANSIT
                        if ($bucket_status === 'in_transit') {
                            if ($orderModel->status === 'in_transit' && $orderModel->in_transit_at) {
                                Log::info("🚫 Order #{$order->order_id} already in_transit. Skipping update.");
                                DB::rollBack();
                                continue;
                            }

                            [$success, $msg, $finalStatus] = $statusService->handleInTransitStatus($orderModel);

                            if ($success) {
                                DB::commit();
                                Log::info("🎯 Success : In Transit processed for order #{$order->order_id}: {$msg}");
                            } else {
                                DB::rollBack();
                                Log::error("🚫 Failed : In Transit processed for order #{$order->order_id}: {$msg}");
                            }
                        }
                        // DELIVERED
                        elseif ($bucket_status === 'delivered') {
                            if ($orderModel->status === 'delivered' && $orderModel->delivered_at) {
                                Log::info("🚫 Order #{$order->order_id} already delivered. Skipping update.");
                                DB::rollBack();
                                continue;
                            }

                            [$success, $msg, $finalStatus] = $statusService->handleDeliveredOrder($orderModel->retailer, $orderModel);

                            if ($success) {
                                DB::commit();
                                Log::info("🎯 Success : Delivered processed for order #{$order->order_id}: {$msg}");
                            } else {
                                DB::rollBack();
                                Log::error("🚫 Failed : Delivered processed for order #{$order->order_id}: {$msg}");
                            }
                        }
                        // ndr  customer not accept

                        elseif ($bucket_status === 'ndr')
                        {
                            if ($orderModel->status === 'ndr' && $orderModel->ndr_at) {
                                Log::info("🚫 Order #{$order->order_id} already Non Delivered Report. Skipping update.");
                                DB::rollBack();
                                continue;
                            }

                            [$success, $msg, $finalStatus] = $statusService->handleNdrOrder($orderModel);

                            if ($success) {
                                DB::commit();
                                Log::info("🎯 Success : NDR processed for order #{$order->order_id}: {$msg}");
                            } else {
                                DB::rollBack();
                                Log::error("🚫 Failed : NDR processed for order #{$order->order_id}: {$msg}");
                            }
                        }
                        // CANCEL
                        elseif ($bucket_status === 'cancel') {
                            if ($orderModel->status === 'cancel' && $orderModel->cancel_at) {
                                Log::info("🚫 Order #{$order->order_id} already cancelled. Skipping update.");
                                DB::rollBack();
                                continue;
                            }

                            $reject_reason_select = 'Other';
                            $reject_reason_input = 'Rejected from the courier service';

                            [$success, $msg, $finalStatus] = $statusService->handleCancelledOrderWithCharges($orderModel->retailer, $orderModel, $reject_reason_select, $reject_reason_input);

                            if ($success) {
                                DB::commit();

                                $cancelled_reason = ($reject_reason_select == 'Other')
                                    ? $reject_reason_input
                                    : $reject_reason_select;

                                $customer = [
                                    'name' => $orderModel->customer->firstname,
                                    'email' => $orderModel->customer->email,
                                ];
                                Mail::to($orderModel->customer->email)->send(new CancelOrderMailToCustomer($orderModel, $customer, $cancelled_reason));

                                Log::info("🎯 Success : Cancel processed for order #{$order->order_id}: {$msg}");
                            } else {
                                DB::rollBack();
                                Log::error("🚫 Failed : Cancel processed for order #{$order->order_id}: {$msg}");
                            }
                        }
                    }
                } else {
                    Log::warning("⚠️ Order #{$order->order_id} tracking failed: No valid data.");
                    DB::rollBack();
                    continue;
                }
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error("❌ Error processing order #{$order->order_id}: " . $e->getMessage());
                continue;
            }
        }
        Log::info('✅ TrackShipments command completed at ' . now());
    }
}
