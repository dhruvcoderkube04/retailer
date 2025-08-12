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
        // fship status code
        $fshipBucketKeyMap =[
            1	=> 'New',
            2	=> 'Booked',
            3	=> 'Order Cancelled',
            4	=> 'Pickup Initiated',
            5	=> 'Pickup Cancelled',
            7	=> 'Pickup Pending',
            8	=> 'Pickup Completed',
            9	=> 'In Transit',
            10	=> 'Undelivered',
            11	=> 'Out For Delivery',
            12	=> 'Delivered',
            13	=> 'RTO',
            14	=> 'RTO In Transit',
            15	=> 'RTO Delivered',
            18	=> 'Shipment Lost',
            19	=> 'Shipment Damaged',
            22	=> 'Out for Pickup'
        ];

        $fshipStatusTextMap = [
            'New' => 'pending',
            'Pickup Initiated' => 'pickup',
            'Pickup Pending' => 'pickup',
            'Out for Pickup' => 'in_transit',
            'In Transit' => 'in_transit',
            'Pickup Completed' => 'in_transit',
            'Out For Delivery' => 'in_transit',
            'Undelivered' => 'ndr',
            'DELIVERED' => 'delivered',
            'RTO' => 'rto',
            'RTO In Transit' => 'rto',
            'RTO Delivered' => 'rtn_to_seller',
            'Order Cancelled' => 'cancel',
            'Pickup Cancelled' => 'cancel',
            'Shipment Lost' => 'lost',
            'Shipment Damaged' => 'lost'
        ];

        $fshipStageDateMap = [
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

        // lorrigo status code
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
            ->whereIn('status', ['in_transit', 'pickup', 'ofd', 'rto', 'delivered','ndr','lost', 'cancel'])
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
                // FShip
                if (isset($response['status']) && $response['status'] && isset($response['summary'])) {
                    $bucket_id = $response['summary']['statusid'];
                    $key = $fshipBucketKeyMap[$bucket_id] ?? null;
                    $bucket_status = $key ? ($fshipStatusTextMap[$key] ?? '') : 'unknown';
                    $dateColumn = $fshipStageDateMap[$bucket_status] ?? null;

                    $status = $response['summary']['status'] ?? $order->shipment_status;
                    $stage_reason = $response['summary']['status'] ?? '';
                    $fulfilledBy = $response['summary']['fulfilledby'] ?? $order->fulfilledby;

                    $this->processOrderStatus($order, $bucket_status, $dateColumn, $status, $stage_reason, $fulfilledBy);

                } elseif (isset($response['valid']) && $response['valid'] && isset($response['order'])) {
                    // Lorrigo
                    $bucket_id = $response['order']['bucket'];
                    $key = $bucketKeyMap[$bucket_id] ?? null;
                    $bucket_status = $key ? ($statusTextMap[$key] ?? '') : 'unknown';
                    $dateColumn = $stageDateMap[$bucket_status] ?? null;

                    $latestStage = collect($response['order']['orderStages'] ?? [])->last();
                    $status = $latestStage['action'] ?? $order->shipment_status;
                    $stage_reason = $latestStage['activity'] ?? '';
                    $fulfilledBy = $response['order']['carrierName'] ?? $order->fulfilledby;

                    $this->processOrderStatus($order, $bucket_status, $dateColumn, $status, $stage_reason, $fulfilledBy);

                } else {
                    Log::warning("⚠️ Order #{$order->order_id} tracking failed: No valid data.");
                    DB::rollBack();
                }
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error("❌ Error processing order #{$order->order_id}: " . $e->getMessage());
                continue;
            }
        }
        Log::info('✅ TrackShipments command completed at ' . now());
    }


    private function processOrderStatus($order, $bucket_status, $dateColumn, $status, $stage_reason, $fulfilledBy)
    {
        $updateData = [
            'shipment_status' => $status,
            'fulfilledby' => $fulfilledBy ?? $order->fulfilledby,
            'shipment_activity' => $stage_reason
        ];

        if ($dateColumn && Schema::hasColumn('customer_orders', $dateColumn)) {
            $updateData[$dateColumn] = now();
        }

        DB::table('customer_orders')->where('order_id', $order->order_id)->update($updateData);

        $orderModel = CustomerOrders::with('retailer', 'customer')->where('order_id', $order->order_id)->first();
        if (!$orderModel || !$orderModel->retailer) {
            return;
        }

        $statusService = new OrderStatusService();
        $alreadySet = $orderModel->status === $bucket_status && !empty($orderModel->{$dateColumn});

        if ($alreadySet) {
            Log::info("🚫 Order #{$order->order_id} already {$bucket_status}. Skipping update.");
            DB::rollBack();
            return;
        }

        switch ($bucket_status) {
            case 'in_transit':
                [$success, $msg] = $statusService->handleInTransitStatus($orderModel);
                break;
            case 'delivered':
                [$success, $msg] = $statusService->handleDeliveredOrder($orderModel->retailer, $orderModel);
                break;
            case 'ndr':
                [$success, $msg] = $statusService->handleNdrOrder($orderModel);
                break;
            case 'rto':
                [$success, $msg] = $statusService->handleRtoOrder($orderModel);
                break;
            case 'rtn_to_seller':
                [$success, $msg] = $statusService->NdrtoRto($orderModel->retailer, $orderModel);
                break;
            case 'cancel':
                $reject_reason_select = 'Other';
                $reject_reason_input = 'Rejected from the courier service';
                [$success, $msg] = $statusService->handleCancelledOrderWithCharges(
                    $orderModel->retailer,
                    $orderModel,
                    $reject_reason_select,
                    $reject_reason_input
                );
                if ($success) {
                    $cancelled_reason = ($reject_reason_select == 'Other') ? $reject_reason_input : $reject_reason_select;
                    $customer = [
                        'name' => $orderModel->customer->firstname,
                        'email' => $orderModel->customer->email,
                    ];
                    Mail::to($orderModel->customer->email)
                        ->send(new CancelOrderMailToCustomer($orderModel, $customer, $cancelled_reason));
                }
                break;
            default:
                $success = false;
                $msg = 'Unknown status';
        }

        if ($success) {
            DB::commit();
            Log::info("🎯 Success : {$bucket_status} processed for order #{$order->order_id}: {$msg}");
        } else {
            DB::rollBack();
            Log::error("🚫 Failed : {$bucket_status} processed for order #{$order->order_id}: {$msg}");
        }
    }
}



