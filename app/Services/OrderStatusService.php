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
    // APPROVED BY RETAILER (New to Approved)
    public function handleApproveByRetailer($customerOrder)
    {
        $customerOrder->update([
            'status' => 'approved_by_retailer',
            'approved_by_retailer_at' => Carbon::now(),
        ]);

        return [true, 'Order has been approved successfully', 'approved-by-retailer'];
    }

    // TRANSFER TO WHOLESALER (Approved to Transfer-to-wholesaler)
    public function handleTransferToWholesaler($customerOrder)
    {
        $customerOrder->update([
            'status' => 'transfered_retailer_to_wholesaler',
            'transfered_retailer_to_wholesaler_at' => Carbon::now(),
            'order_process_by' => 'wholesaler',
            'checkout_type' => 'cod',
        ]);
        // $userId =  $wholesaler->wholesaler_id;
        // $type = 'wholesaler-notification';

        // OrderNotification::insert([
        //     'user_id' => $userId,
        //     'order_id' => $orderID,
        //     'type' => $type,
        //     'message' => 'Punch Order Placed',
        //     'is_read' => 0,
        //     'created_at'=>now(),
        //     'updated_at' => now()
        // ]);

        return [true, 'Wholesaler will ship this product', 'new'];
    }

    // PICKUP (Approved to Pickup)
    public function handlePickupStatus($request, $customerOrder, $pickup_address)
    {
        $productName = $customerOrder->order_product_detail->name ?? 'N/A';
        $productSku = $customerOrder->order_product_detail->sku ?? 'N/A';

        $user = Auth::user();

        // calculate base and profit
        $finalShipping = (float) $request->shipping_charge;
        $finalCod = (float) $request->cod_charge;
        $finalRto = (float) $request->rto_charge;

        $shippingBase = $codBase = $rtoBase = 0;
        $shippingProfit = $codProfit = $rtoProfit = 0;

        $marginPercentage = (float) ($user->userDetail?->margin_percentage_tag ?? 0);
        $marginTagName = $user->userDetail?->margin_tag_name;
        $getMargin = MarginManagement::where('margin_name', $marginTagName)->first();
        if ($getMargin) {
            $marginType = $getMargin->type; // 'percentage' or 'flat'
            $flatAmount = (float) ($getMargin->flat_percentage ?? 0);

            if ($marginType === 'percentage' && $marginPercentage > 0) {
                $divider = 1 + ($marginPercentage / 100);

                $shippingBase = round($finalShipping / $divider, 2);
                $shippingProfit = round($finalShipping - $shippingBase, 2);

                $codBase = round($finalCod / $divider, 2);
                $codProfit = round($finalCod - $codBase, 2);

                $rtoBase = round($finalRto / $divider, 2);
                $rtoProfit = round($finalRto - $rtoBase, 2);
            } elseif ($marginType === 'flat') {
                $shippingBase = $finalShipping - $flatAmount;
                $codBase = $finalCod - $flatAmount;
                $rtoBase = $finalRto - $flatAmount;

                $shippingProfit = round($flatAmount, 2);
                $codProfit = round($flatAmount, 2);
                $rtoProfit = round($flatAmount, 2);
            }
        } else {
            // No margin and percentage calculation applied, use request values as is
            $shippingBase = $finalShipping;
            $codBase = $finalCod;
            $rtoBase = $finalRto;

            $shippingProfit = 0.0;
            $codProfit = 0.0;
            $rtoProfit = 0.0;
        }

        $updateData = [
            'status' => $request->status,
            'pickup_at' => now(),
            'pickup_address_id' => $request->pickup_address_id,
            'product_weight' => $request->product_weight,
            'service_mode' => $request->service_mode,
            'shipping_charge' => $shippingBase,
            'cod_charge' => $codBase,
            'rto_charge' => $rtoBase,
            'shipping_charge_profit' => $shippingProfit,
            'cod_charge_profit' => $codProfit,
            'rto_charge_profit' => $rtoProfit,
        ];

        $active_courier_partners = CourierPartner::where('is_active', 1)->first();

        if (!empty($active_courier_partners) && $active_courier_partners->code == 'fship') {
            $payload = [
                "customer_Name" => trim(($customerOrder->customer->firstname ?? '') . ' ' . ($customerOrder->customer->lastname ?? '')),
                "customer_Mobile" => $customerOrder->customer->phone_number,
                "customer_Emailid" => $customerOrder->customer->email,
                "customer_Address" => $customerOrder->customer->address,
                "landMark" => "",
                "customer_Address_Type" => "Home",
                "customer_PinCode" => $customerOrder->customer->pincode,
                "customer_City" => $customerOrder->customer->city,
                "orderId" => $customerOrder->order_id,
                "invoice_Number" => '',
                "payment_Mode" => 1,
                "express_Type" => "surface",
                "is_Ndd" => 0,
                "order_Amount" => $customerOrder->final_amount,
                "tax_Amount" => 0,
                "extra_Charges" => 0,
                "total_Amount" => $customerOrder->final_amount,
                "cod_Amount" => $customerOrder->final_amount ?? 0,
                "shipment_Weight" => $request->product_weight,
                "shipment_Length" => 12,
                "shipment_Width" => 12,
                "shipment_Height" => 12,
                "volumetric_Weight" => 1,
                "latitude" => 0,
                "longitude" => 0,
                "pick_Address_ID" => $pickup_address->warehouse_id,
                "return_Address_ID" => $pickup_address->warehouse_id,
                "products" => [[
                    "productId" => $customerOrder->id,
                    "productName" => $productName,
                    "unitPrice" => $customerOrder->final_amount,
                    "quantity" => $customerOrder->quantity,
                    "productCategory" => "",
                    "hsnCode" => "",
                    "sku" => $productSku,
                    "taxRate" => 0,
                    "productDiscount" => 0
                ]],
                "courierId" => $request->courier_service_id,
            ];
        } elseif (!empty($active_courier_partners) &&  ($active_courier_partners->code == 'lorrigotest' || $active_courier_partners->code == 'lorrigolive')) {
            $payload = [
                "ewaybill" => "",
                "order_reference_id" => $customerOrder->order_id . rand(1, 9999999),
                "payment_mode" => 1,
                "orderWeight" => 0.5,
                "orderWeightUnit" => "kg",
                "order_invoice_date" => "",
                "order_invoice_number" => "",
                "numberOfBoxes" => 1,
                "orderSizeUnit" => "cm",
                "orderBoxHeight" => 0.5,
                "orderBoxWidth" => 0.5,
                "orderBoxLength" => 0.5,
                "amount2Collect" => $customerOrder->final_amount,
                "customerDetails" => [
                    "name" => trim(($customerOrder->customer->firstname ?? '') . ' ' . ($customerOrder->customer->lastname ?? '')),
                    "phone" => $customerOrder->customer->phone_number,
                    "address" => $customerOrder->customer->address . ' ' . $customerOrder->customer->city,
                    "pincode" => $customerOrder->customer->pincode,
                ],
                "productDetails" => [
                    "name" => $productName,
                    "category" => $customerOrder->order_product_detail->sub_category_name ?? 'N/A',
                    "hsn_code" => "",
                    "quantity" => 1,
                    "taxRate" => "1",
                    "taxableValue" => $customerOrder->final_amount,
                ],
                "pickupAddress" => $pickup_address->warehouse_id,
                "sellerDetails" => [
                    "sellerName" => $user->firstname . ' ' . @$user->lastname,
                    "sellerGSTIN" => "",
                    "isSellerAddressAdded" => true,
                    "sellerPhone" => @$user->phone_number,
                    "sellerAddress" => @$user->userDetail->address . ' ' . @$user->userDetail->city . ' ' . @$user->userDetail->state . ' ' . @$user->userDetail->country,
                    "sellerPincode" => @$user->userDetail->postal_code
                ],
                "isReverseOrder" => false
            ];
        }

        $courierService = \App\Services\CourierServiceManager::getService();

        $response = $courierService->createOrder($payload);

        if (!empty($active_courier_partners) && $active_courier_partners->code == 'fship') {
            if (!empty($response['waybill']) && !empty($response['apiorderid'])) {
                $updateData['tracking_number'] = $response['waybill'];
                $updateData['api_order_id'] = $response['apiorderid'];
                $updateData['courier_service'] = $request->courier_service;
                $updateData['courier_partner_id'] = $active_courier_partners->id;
                $updateData['courier_partner_code'] = 'fship';

                $pdf = PDF::loadView('orders.pdf.order-shipping-label', [
                    'courier_service_response' => $response,
                    'courier_logo' => $request->courier_service_logo,
                    'pickupAddress' => $pickup_address,
                    'productName' => $productName,
                    'productSku' => $productSku,
                    'customerOrder' => $customerOrder,
                    'date' => Carbon::now(),
                ]);
                $pdf->setOptions([
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true
                ]);
                $filename = 'orders/shipping-labels/order_' . $customerOrder->id . '.pdf';

                if (Storage::disk('spaces')->exists($filename)) {
                    Storage::disk('spaces')->delete($filename);
                }
                Storage::disk('spaces')->put($filename, $pdf->output(), 'public');
                $pdfUrl = Storage::disk('spaces')->url($filename);

                CustomerOrders::where('id', $customerOrder->id)->update([
                    'shipping_label_url' => $pdfUrl
                ]);

                $customerOrder->update($updateData);

                return [true, 'Order has been marked ready to ship', 'pickup'];
            } else {
                return [false, $response['response'] ?? 'Failed to create shipping order', ''];
            }
        } elseif (!empty($active_courier_partners) &&  ($active_courier_partners->code == 'lorrigotest' || $active_courier_partners->code == 'lorrigolive')) {
            if (!empty($response['order']['_id'])) {

                // dd($request->courier_service);
                // $get_carrier = LorrigoCarrier::where('name',$request->courier_service)->first();
                $get_carrier = LorrigoCarrier::whereRaw("REPLACE(LOWER(name), ' ', '') = ?", [
                    str_replace(' ', '', strtolower($request->courier_service))
                ])->first();

                if ($get_carrier) {
                    $create_shipment_payload = [
                        "carrierId" => $get_carrier->id,
                        "orderId" => $response['order']['_id'],
                        "carrierNickName" => $get_carrier->nickname,
                        "charge" => $request->shipping_charge,
                        "orderType" => 0,
                        "type" => $get_carrier->type
                    ];
                    $createshipment = $courierService->createShipment($create_shipment_payload);

                    if ($createshipment['valid']  && $createshipment['order']) {
                        $updateData['tracking_number'] = $createshipment['order']['awb'];
                        $updateData['api_order_id'] = $createshipment['order']['_id']; // Main order _id
                        $updateData['courier_service'] = $request->courier_service;
                        $updateData['courier_partner_id'] = $active_courier_partners->id;
                        $updateData['courier_partner_code'] = $active_courier_partners->code;

                        $pdf = PDF::loadView('pdf.lorrigo-order-shipping-label', [
                            'courier_service_response' => $createshipment['order']['awb'],
                            'courier_logo' => @$request->courier_service_logo,
                            'pickupAddress' => $pickup_address,
                            'productName' => $productName,
                            'productSku' => $productSku,
                            'customerOrder' => $customerOrder,
                            'date' => Carbon::now(),
                        ]);

                        $pdf->setOptions([
                            'isRemoteEnabled' => true,
                            'isHtml5ParserEnabled' => true
                        ]);

                        $filename = 'orders/shipping-labels/order_' . $customerOrder->id . '.pdf';

                        if (Storage::disk('spaces')->exists($filename)) {
                            Storage::disk('spaces')->delete($filename);
                        }

                        Storage::disk('spaces')->put($filename, $pdf->output(), 'public');
                        $pdfUrl = Storage::disk('spaces')->url($filename);

                        CustomerOrders::where('id', $customerOrder->id)->update([
                            'shipping_label_url' => $pdfUrl
                        ]);

                        $customerOrder->update($updateData);

                        return [true, 'Order has been marked ready to ship', 'pickup'];
                    } else {
                        return [false,  $response['response'] ?? 'tracking number created failed ', ''];
                    }
                } else {
                    return [false, $response['response'] ?? 'Carrier id  not Select', ''];
                }
            }
        } else {
            return [false, $response['response'] ?? 'Courier Partner not Select', ''];
        }
    }

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

        // // wholesaler product
        // if ($customerOrder->product_id && !$customerOrder->retailer_clone_product_id) {
        //     $wholesalerDetail = UserDetail::where('user_id', $customerOrder->order_product_detail->wholesaler_id)->first();

        //     $marginFetch = RetailerProducts::where('retailer_id', $retailer->id)
        //         ->where('wholesaler_id', $customerOrder->order_product_detail->wholesaler_id)
        //         ->where('sub_category_id', $customerOrder->order_product_detail->sub_category_id)
        //         ->where('product_id', $customerOrder->product_id)
        //         ->where('product_status', 'active')
        //         ->first();
        //     if (!$marginFetch || !$marginFetch->margin) {
        //         $marginFetch = RetailerProducts::where('retailer_id', $retailer->id)
        //             ->where('wholesaler_id', $customerOrder->order_product_detail->wholesaler_id)
        //             ->where('sub_category_id', $customerOrder->order_product_detail->sub_category_id)
        //             ->whereNull('product_id')
        //             ->first();
        //     }

        //     if (!$marginFetch || !$marginFetch->margin) {
        //         Log::error('For Order ID '.$customerOrder->order_id.', Product category margin not added, Please go to Wholesaler section and add margin first');
        //         return [false, 'Product category margin not added, Please go to Wholesaler section and add margin first', 'delivered'];
        //     }

        //     if ($customerOrder->final_amount < $marginFetch->margin) {
        //         $retailer_transaction_amount = 0;
        //         $retailer_final_transaction_amount = -abs($total_charges);
        //         $retailer_current_balance = $retailerDetail->pending_wallet - $total_charges;

        //         $wholesaler_transaction_amount = $customerOrder->final_amount;
        //         $wholesaler_final_transaction_amount = $customerOrder->final_amount;
        //         $wholesaler_current_balance = $wholesalerDetail->pending_wallet + $customerOrder->final_amount;
        //     } else {
        //         $retailer_transaction_amount = $marginFetch->margin;
        //         $retailer_final_transaction_amount = $retailer_transaction_amount - $total_charges;
        //         $retailer_current_balance = $retailerDetail->pending_wallet + $retailer_final_transaction_amount;

        //         $wholesaler_transaction_amount = $customerOrder->final_amount - $marginFetch->margin;
        //         $wholesaler_final_transaction_amount = $customerOrder->final_amount - $marginFetch->margin;
        //         $wholesaler_current_balance = $wholesalerDetail->pending_wallet + $wholesaler_final_transaction_amount;
        //     }

        //     // retailer entry
        //     AccountTransaction::create([
        //         'customer_order_id' => $customerOrder->id,
        //         'tracking_number' => $customerOrder->tracking_number,
        //         'user_id' => $retailer->id,
        //         'user_type' => 'retailer',
        //         'description' => 'Margin amount of order ' . $customerOrder->order_id . ' delivered',
        //         'transaction_amount' => $retailer_transaction_amount,
        //         'charges' => $charges,
        //         'final_transaction_amount' => $retailer_final_transaction_amount,
        //         'current_balance' => $retailer_current_balance,
        //         'order_type' => 'completed',
        //         'status' => 0,
        //         'type' => 'pending'
        //     ]);
        //     $retailerDetail->pending_wallet = $retailer_current_balance;
        //     $retailerDetail->save();

        //     // wholesaler entry
        //     AccountTransaction::create([
        //         'customer_order_id' => $customerOrder->id,
        //         'tracking_number' => $customerOrder->tracking_number,
        //         'user_id' => $customerOrder->order_product_detail->wholesaler_id,
        //         'user_type' => 'wholesaler',
        //         'description' => 'Product ' . $customerOrder->order_product_detail->name . ' has been delivered by retailer, Order id is ' . $customerOrder->order_id,
        //         'transaction_amount' => $wholesaler_transaction_amount,
        //         'charges' => null,
        //         'final_transaction_amount' => $wholesaler_final_transaction_amount,
        //         'current_balance' => $wholesaler_current_balance,
        //         'order_type' => 'completed',
        //         'status' => 0,
        //         'type' => 'pending'
        //     ]);
        //     $wholesalerDetail->pending_wallet = $wholesaler_current_balance;
        //     $wholesalerDetail->save();
        // }

        // // retailer own product
        // if (!$customerOrder->product_id && $customerOrder->retailer_clone_product_id) {
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
        // }

        $customerOrder->update([
            'status' => 'delivered',
            'delivered_at' => Carbon::now(),
            'delivered_by' => $retailer->id,
        ]);

        return [true, 'Order has been marked as delivered', 'delivered'];
    }

    // CANCELLED (Any-stage to Cancel)
    public function handleCancelledOrder($retailer, $customerOrder, $reject_reason_select, $reject_reason_input)
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

        $cancelled_reason = ($reject_reason_select == 'Other')
            ? $reject_reason_input
            : $reject_reason_select;

        $customerOrder->update([
            'status' => 'cancel',
            'cancel_at' => Carbon::now(),
            'cancelled_by' => $retailer->id,
            'cancelled_reason' => $cancelled_reason
        ]);

        return [true, 'Order has been cancelled by retailer', 'cancel'];
    }

    // CANCELLED WITH CHARGES (Pickup ke bad & Delivery se pehle Order cancel hota hai)
    public function handleCancelledOrderWithCharges($retailer, $customerOrder, $reject_reason_select, $reject_reason_input)
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
            'order_type' => 'cancelled',
            'status' => 0,
            'type' => 'pending'
        ]);
        $retailerDetail->pending_wallet = $retailerDetail->pending_wallet - $total_charges;
        $retailerDetail->save();

        $cancelled_reason = ($reject_reason_select == 'Other')
            ? $reject_reason_input
            : $reject_reason_select;

        $customerOrder->update([
            'status' => 'cancel',
            'cancel_at' => Carbon::now(),
            'cancelled_by' => $retailer->id,
            'cancelled_reason' => $cancelled_reason
        ]);

        return [true, 'Order has been cancelled by retailer', 'cancel'];
    }
}
