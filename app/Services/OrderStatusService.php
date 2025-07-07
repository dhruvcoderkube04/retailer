<?php

namespace App\Services;

use App\Models\UserDetail;
use App\Models\RetailerProducts;
use App\Models\AccountTransaction;
use App\Models\CourierPartner;
use App\Models\CustomerOrders;
use App\Models\LorrigoCarrier;
use App\Models\MarginManagement;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\RetailerCloneProduct;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class OrderStatusService
{
    // IN-TRANSIT (Pickup to Intransit)
    public function handleInTransitStatus($customerOrder)
    {
        $customerOrder->update([
            'status' => 'in_transit',
            'in_transit_at' => Carbon::now(),
        ]);

        return [true, 'Order has been transferred to In-transit', 'in-transit'];
    }

    // DELIVERED (Intransit to Delivered)
    public function handleDeliveredOrder($retailer, $customerOrder)
    {
        $retailerDetail = UserDetail::where('user_id', $retailer->id)->first();

        $total_charges = ($customerOrder->shipping_charge ?? 0) +
            ($customerOrder->cod_charge ?? 0) +
            ($customerOrder->rto_charge ?? 0) +
            ($customerOrder->shipping_charge_profit ?? 0) +
            ($customerOrder->cod_charge_profit ?? 0) +
            ($customerOrder->rto_charge_profit ?? 0);

        $charges = [
            'Shipping Charge' => ($customerOrder->shipping_charge ?? 0) + ($customerOrder->shipping_charge_profit ?? 0),
            'COD Charge' => ($customerOrder->cod_charge ?? 0) + ($customerOrder->cod_charge_profit ?? 0),
            'RTO Charge' => ($customerOrder->rto_charge ?? 0) + ($customerOrder->rto_charge_profit ?? 0),
        ];

        // wholesaler product
        if ($customerOrder->product_id && !$customerOrder->retailer_clone_product_id) {
            $wholesalerDetail = UserDetail::where('user_id', $customerOrder->order_product_detail->wholesaler_id)->first();

            $marginFetch = RetailerProducts::where('retailer_id', $retailer->id)
                ->where('wholesaler_id', $customerOrder->order_product_detail->wholesaler_id)
                ->where('sub_category_id', $customerOrder->order_product_detail->sub_category_id)
                ->where('product_id', $customerOrder->product_id)
                ->where('product_status', 'active')
                ->first();
            if (!$marginFetch || !$marginFetch->margin) {
                $marginFetch = RetailerProducts::where('retailer_id', $retailer->id)
                    ->where('wholesaler_id', $customerOrder->order_product_detail->wholesaler_id)
                    ->where('sub_category_id', $customerOrder->order_product_detail->sub_category_id)
                    ->whereNull('product_id')
                    ->first();
            }

            if (!$marginFetch || !$marginFetch->margin) {
                Log::error('For Order ID '.$customerOrder->order_id.', Product category margin not added, Please go to Wholesaler section and add margin first');
                return [false, 'Product category margin not added, Please go to Wholesaler section and add margin first', 'delivered'];
            }

            if ($customerOrder->final_amount < $marginFetch->margin) {
                $retailer_transaction_amount = 0;
                $retailer_final_transaction_amount = -abs($total_charges);
                $retailer_current_balance = $retailerDetail->pending_wallet - $total_charges;

                $wholesaler_transaction_amount = $customerOrder->final_amount;
                $wholesaler_final_transaction_amount = $customerOrder->final_amount;
                $wholesaler_current_balance = $wholesalerDetail->pending_wallet + $customerOrder->final_amount;
            } else {
                $retailer_transaction_amount = $marginFetch->margin;
                $retailer_final_transaction_amount = $retailer_transaction_amount - $total_charges;
                $retailer_current_balance = $retailerDetail->pending_wallet + $retailer_final_transaction_amount;

                $wholesaler_transaction_amount = $customerOrder->final_amount - $marginFetch->margin;
                $wholesaler_final_transaction_amount = $customerOrder->final_amount - $marginFetch->margin;
                $wholesaler_current_balance = $wholesalerDetail->pending_wallet + $wholesaler_final_transaction_amount;
            }

            // retailer entry
            AccountTransaction::create([
                'customer_order_id' => $customerOrder->id,
                'tracking_number' => $customerOrder->tracking_number,
                'user_id' => $retailer->id,
                'user_type' => 'retailer',
                'description' => 'Margin amount of order ' . $customerOrder->order_id . ' delivered',
                'transaction_amount' => $retailer_transaction_amount,
                'charges' => $charges,
                'final_transaction_amount' => $retailer_final_transaction_amount,
                'current_balance' => $retailer_current_balance,
                'order_type' => 'completed',
                'status' => 0,
                'type' => 'pending'
            ]);
            $retailerDetail->pending_wallet = $retailer_current_balance;
            $retailerDetail->save();

            // wholesaler entry
            AccountTransaction::create([
                'customer_order_id' => $customerOrder->id,
                'tracking_number' => $customerOrder->tracking_number,
                'user_id' => $customerOrder->order_product_detail->wholesaler_id,
                'user_type' => 'wholesaler',
                'description' => 'Product ' . $customerOrder->order_product_detail->name . ' has been delivered by retailer, Order id is ' . $customerOrder->order_id,
                'transaction_amount' => $wholesaler_transaction_amount,
                'charges' => null,
                'final_transaction_amount' => $wholesaler_final_transaction_amount,
                'current_balance' => $wholesaler_current_balance,
                'order_type' => 'completed',
                'status' => 0,
                'type' => 'pending'
            ]);
            $wholesalerDetail->pending_wallet = $wholesaler_current_balance;
            $wholesalerDetail->save();
        }

        // retailer own product
        if (!$customerOrder->product_id && $customerOrder->retailer_clone_product_id) {
            $retailer_transaction_amount = $customerOrder->final_amount;
            $retailer_final_transaction_amount = $customerOrder->final_amount - $total_charges;
            $retailer_current_balance = $retailerDetail->pending_wallet + $retailer_final_transaction_amount;

            // retailer entry
            AccountTransaction::create([
                'customer_order_id' => $customerOrder->id,
                'tracking_number' => $customerOrder->tracking_number,
                'user_id' => $retailer->id,
                'user_type' => 'retailer',
                'description' => 'Order ' . $customerOrder->order_id . ' has been delivered',
                'transaction_amount' => $retailer_transaction_amount,
                'charges' => $charges,
                'final_transaction_amount' => $retailer_final_transaction_amount,
                'current_balance' => $retailer_current_balance,
                'order_type' => 'completed',
                'status' => 0,
                'type' => 'pending'
            ]);
            $retailerDetail->pending_wallet = $retailer_current_balance;
            $retailerDetail->save();
        }

        $customerOrder->update([
            'status' => 'delivered',
            'delivered_at' => Carbon::now(),
            'delivered_by' => $retailer->id,
        ]);

        return [true, 'Order has been marked as delivered', 'delivered'];
    }

    // CANCELLED (Any-stage to Cancel)
    public function handleCancelledOrder($retailer, $customerOrder, $request)
    {
        // increase quantity in wholesaler-product
        if ($customerOrder->quantity && $customerOrder->product_id) {
            $wholesalerProduct = Product::with('productVariations')
                ->where('id', $customerOrder->product_id)
                ->first();
            if ($wholesalerProduct) {
                if ($wholesalerProduct->productVariations->isNotEmpty()) {
                    $productVariation = ProductVariation::where('product_id', $customerOrder->product_id)
                        ->where('product_variation', $customerOrder->product_variation)
                        ->first();
                    if ($productVariation) {
                        $productVariation->stock += $customerOrder->quantity;
                        $productVariation->save();
                    }
                } else {
                    $wholesalerProduct->quantity += $customerOrder->quantity;
                    $wholesalerProduct->save();
                }
            }
        }

        // increase quantity in retailer-product
        if ($customerOrder->quantity && $customerOrder->retailer_clone_product_id) {
            $retailerProduct = RetailerCloneProduct::with('productVariations')
                ->where('id', $customerOrder->retailer_clone_product_id)
                ->first();
            if ($retailerProduct) {
                if ($retailerProduct->productVariations->isNotEmpty()) {
                    $productVariation = ProductVariation::where('product_id', $customerOrder->retailer_clone_product_id)
                        ->where('product_variation', $customerOrder->product_variation)
                        ->first();
                    if ($productVariation) {
                        $productVariation->stock += $customerOrder->quantity;
                        $productVariation->save();
                    }
                } else {
                    $retailerProduct->quantity += $customerOrder->quantity;
                    $retailerProduct->save();
                }
            }
        }

        $cancelled_reason = ($request->reject_reason_select == 'Other')
            ? $request->reject_reason_input
            : $request->reject_reason_select;

        $customerOrder->update([
            'status' => 'cancel',
            'cancel_at' => Carbon::now(),
            'cancelled_by' => $retailer->id,
            'cancelled_reason' => $cancelled_reason
        ]);

        return [true, 'Order has been cancelled by retailer', 'cancel'];
    }

    // CANCELLED WITH CHARGES (Pickup ke bad & Delivery se pehle Order cancel hota hai)
    public function handleCancelledOrderWithCharges($retailer, $customerOrder)   # $request cancel reson set karan padega
    {
        // increase quantity in wholesaler-product
        if ($customerOrder->quantity && $customerOrder->product_id) {
            $wholesalerProduct = Product::with('productVariations')
                ->where('id', $customerOrder->product_id)
                ->first();
            if ($wholesalerProduct) {
                if ($wholesalerProduct->productVariations->isNotEmpty()) {
                    $productVariation = ProductVariation::where('product_id', $customerOrder->product_id)
                        ->where('product_variation', $customerOrder->product_variation)
                        ->first();
                    if ($productVariation) {
                        $productVariation->stock += $customerOrder->quantity;
                        $productVariation->save();
                    }
                } else {
                    $wholesalerProduct->quantity += $customerOrder->quantity;
                    $wholesalerProduct->save();
                }
            }
        }

        // increase quantity in retailer-product
        if ($customerOrder->quantity && $customerOrder->retailer_clone_product_id) {
            $retailerProduct = RetailerCloneProduct::with('productVariations')
                ->where('id', $customerOrder->retailer_clone_product_id)
                ->first();
            if ($retailerProduct) {
                if ($retailerProduct->productVariations->isNotEmpty()) {
                    $productVariation = ProductVariation::where('product_id', $customerOrder->retailer_clone_product_id)
                        ->where('product_variation', $customerOrder->product_variation)
                        ->first();
                    if ($productVariation) {
                        $productVariation->stock += $customerOrder->quantity;
                        $productVariation->save();
                    }
                } else {
                    $retailerProduct->quantity += $customerOrder->quantity;
                    $retailerProduct->save();
                }
            }
        }

        $retailerDetail = UserDetail::where('user_id', $retailer->id)->first();

        $total_charges = ($customerOrder->shipping_charge ?? 0) +
            ($customerOrder->cod_charge ?? 0) +
            ($customerOrder->rto_charge ?? 0) +
            ($customerOrder->shipping_charge_profit ?? 0) +
            ($customerOrder->cod_charge_profit ?? 0) +
            ($customerOrder->rto_charge_profit ?? 0);

        $charges = [
            'Shipping Charge' => ($customerOrder->shipping_charge ?? 0) + ($customerOrder->shipping_charge_profit ?? 0),
            'COD Charge'      => ($customerOrder->cod_charge ?? 0) + ($customerOrder->cod_charge_profit ?? 0),
            'RTO Charge'      => ($customerOrder->rto_charge ?? 0) + ($customerOrder->rto_charge_profit ?? 0),
        ];

        // retailer entry
        AccountTransaction::create([
            'customer_order_id' => $customerOrder->id,
            'tracking_number' => $customerOrder->tracking_number,
            'user_id' => $retailer->id,
            'user_type' => 'retailer',
            'description' => 'Charges deducted for Order ' . $customerOrder->order_id . ' cancelled from In-transit stage',
            'transaction_amount' => 0,
            'charges' => $charges,
            'final_transaction_amount' => -abs($total_charges),
            'current_balance' => $retailerDetail->pending_wallet - $total_charges,
            'order_type' => 'completed',
            'status' => 0,
            'type' => 'pending'
        ]);
        $retailerDetail->pending_wallet = $retailerDetail->pending_wallet - $total_charges;
        $retailerDetail->save();

        // $cancelled_reason = ($request->reject_reason_select == 'Other')
        //     ? $request->reject_reason_input
        //     : $request->reject_reason_select;

        $customerOrder->update([
            'status' => 'cancel',
            'cancel_at' => Carbon::now(),
            'cancelled_by' => $retailer->id,
            'cancelled_reason' => 'Rejected from the courier service'
        ]);

        return [true, 'Order has been cancelled by retailer', 'cancel'];
    }
}
