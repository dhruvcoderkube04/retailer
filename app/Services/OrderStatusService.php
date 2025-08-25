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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\RetailerNdrNotification;
use App\Models\GstConfiguration;
use App\Models\PickAddress;
use App\Services\Courier\FShipService;
use App\Services\Courier\LorrigoService;
use App\Services\Courier\LorrigoServiceLive;

class OrderStatusService
{
    // public $partners;
    public $services = [];

    public function __construct()
    {
        $partners = CourierPartner::where('is_active', true)->get();

        foreach ($partners as $partner) {
            $service = match ($partner->code) {
                'lorrigotest'  => new LorrigoService($partner->toArray()),
                'fship'        => new FShipService($partner->toArray()),
                'lorrigolive'  => new LorrigoServiceLive($partner->toArray()),
                default        => null,
            };
            $this->services[$partner->code] = $service;
        }
    }
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

        $gst_config = GstConfiguration::where('status', true)->first();
        if ($gst_config) {
            if ($gst_config->gst_mode == 'same') {
                // Use only GST field, default to 0 if null
                $gstRate = floatval($gst_config->gst ?? 0);

                $shippingChargeGstAmount = round(($finalShipping * $gstRate) / 100, 2);
                $codChargeGstAmount      = round(($finalCod * $gstRate) / 100, 2);
                $rtoChargeGstAmount      = round(($finalRto * $gstRate) / 100, 2);
            } else {
                // Sum IGST + CGST + SGST, default to 0 if null
                $igstRate = floatval($gst_config->igst ?? 0);
                $cgstRate = floatval($gst_config->cgst ?? 0);
                $sgstRate = floatval($gst_config->sgst ?? 0);

                $totalGstRate = $igstRate + $cgstRate + $sgstRate;

                $shippingChargeGstAmount = round(($finalShipping * $totalGstRate) / 100, 2);
                $codChargeGstAmount      = round(($finalCod * $totalGstRate) / 100, 2);
                $rtoChargeGstAmount      = round(($finalRto * $totalGstRate) / 100, 2);
            }
        } else {
            $shippingChargeGstAmount = 0;
            $codChargeGstAmount      = 0;
            $rtoChargeGstAmount      = 0;
        }

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

        $get_pickup = PickAddress::where('id', $request->pickup_address_id)
            ->first();


        if ($get_pickup) {
            $warehouseName = $get_pickup->warehouse_name;

            $get_pickup_address = PickAddress::where('id', $get_pickup->id)
                ->first();

            $active_courier_partners = CourierPartner::where('is_active', 1)->where('code', !empty($get_pickup_address->courier_code) ? $get_pickup_address->courier_code : "lorrigolive")->first();

            $updateData = [
                'status' => $request->status,
                'pickup_at' => now(),
                'pickup_address_id' => $get_pickup_address->id,
                'product_weight' => $request->product_weight,
                'service_mode' => $request->service_mode,
                'shipping_charge' => $shippingBase,
                'cod_charge' => $codBase,
                'rto_charge' => $rtoBase,
                'shipping_charge_profit' => $shippingProfit,
                'cod_charge_profit' => $codProfit,
                'rto_charge_profit' => $rtoProfit,
                'shipping_charge_gst_amount' => $shippingChargeGstAmount,
                'cod_charge_gst_amount' => $codChargeGstAmount,
                'rto_charge_gst_amount' => $rtoChargeGstAmount,
                'gst_config_id' => $gst_config->id ?? null,
            ];

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
                    // "shipment_Weight" => (float) preg_replace('/[^0-9.]/', '', $request->product_weight),
                    "shipment_Weight" => 0.5,
                    "shipment_Length" => 12,
                    "shipment_Width" => 12,
                    "shipment_Height" => 12,
                    "volumetric_Weight" => 1,
                    "latitude" => 0,
                    "longitude" => 0,
                    "pick_Address_ID" => $get_pickup_address->warehouse_id,
                    "return_Address_ID" => $get_pickup_address->warehouse_id,
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
                    "orderWeight" => (float) preg_replace('/[^0-9.]/', '', $request->product_weight),
                    // "orderWeight" => $request->product_weight,
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
                    "pickupAddress" => $get_pickup_address->warehouse_id,
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

            // $courierService = \App\Services\CourierServiceManager::getService();
            $courierService = \App\Services\CourierServiceManager::getServiceByCode($active_courier_partners->code);
            $apiOrderId = null;
            if (empty($customerOrder->api_order_id) || !empty($active_courier_partners) && $active_courier_partners->code == 'fship') {
                $response = $courierService->createOrder($payload);
                if (!empty($response['order'])) {
                    $apiOrderId = $response['order']['_id'];
                }
            } else {
                $apiOrderId = $customerOrder->api_order_id;
            }
            if (!empty($active_courier_partners) && $active_courier_partners->code == 'fship') {
                if (!empty($response['waybill']) && !empty($response['apiorderid'])) {
                    $updateData['tracking_number'] = $response['waybill'];
                    $updateData['api_order_id'] = !empty($apiOrderId) ? $apiOrderId : $response['apiorderid'];
                    $updateData['courier_service'] = $request->courier_service;
                    $updateData['courier_partner_id'] = $active_courier_partners->id;
                    $updateData['courier_partner_code'] = $active_courier_partners->code;

                    $pdf = PDF::loadView('orders.pdf.order-shipping-label', [
                        'courier_service_response' => $response,
                        'courier_logo' => $request->courier_service_logo,
                        'pickupAddress' => $pickup_address,
                        'productName' => $productName,
                        'productSku' => $productSku,
                        'customerOrder' => $customerOrder,
                        'courier_service' => @$request->courier_service,
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

                if (!empty($apiOrderId)) {
                    if (!empty($response['order']['_id'])) {
                        $updateData['api_order_id'] = $response['order']['_id'];
                        $customerOrder->update($updateData);
                    }

                    $create_shipment_payload = [
                        "carrierId" => $request->carrier_id,
                        "orderId" => $apiOrderId,
                        "carrierNickName" => $request->nickName,
                        "charge" => $request->shipping_charge,
                        "orderType" => 0,
                        "type" => $request->service_mode
                    ];
                    Log::info("create shipment payload");
                    Log::info(json_encode($create_shipment_payload));
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
                            'courier_service' => @$request->courier_service,
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
                        Log::info("create shipment error");
                        Log::info(json_encode($createshipment));
                        return [false,  $response['response'] ?? 'tracking number created failed ', ''];
                    }
                }
            } else {
                return [false, $response['response'] ?? 'Courier Partner not Select', ''];
            }
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

    public function handleOutForDeliveryStatus($customerOrder)
    {
        $customerOrder->update([
            'status' => 'ofd',
            'ofd_at' => Carbon::now(),
        ]);

        return [true, 'Order has been transferred to In-transit', 'in-transit'];
    }

    public function handleNdrOrder($customerOrder)
    {
        $customerOrder->update([
            'status' => 'ndr',
            'ndr_at' => Carbon::now(),
        ]);
        // Send email to retailer
        if ($customerOrder->retailer && $customerOrder->retailer->email) {
            Mail::to($customerOrder->retailer->email)->send(
                new RetailerNdrNotification($customerOrder)
            );
        }
        return [true, 'Order has been transferred to Non Delivered Report', 'ndr'];
    }

    // DELIVERED (Intransit to Delivered)
    public function handleDeliveredOrder($retailer, $customerOrder)
    {
        $retailerDetail = UserDetail::where('user_id', $retailer->id)->first();

        $total_charges = ($customerOrder->shipping_charge ?? 0) +
            ($customerOrder->cod_charge ?? 0) +
            // ($customerOrder->rto_charge ?? 0) +
            ($customerOrder->shipping_charge_profit ?? 0) +
            ($customerOrder->cod_charge_profit ?? 0);
        // ($customerOrder->rto_charge_profit ?? 0);

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

        if ($customerOrder->status == 'pickup') {
            if ($customerOrder->courier_partner_code == 'fship') {
                $apiUrl = config('services.fship.base_url');
                $signature = config('services.fship.signature');

                $response = Http::withHeaders([
                    'signature' => $signature,
                    'Content-Type' => 'application/json',
                ])->post($apiUrl . '/cancelorder', [
                    'reason' => $cancelled_reason,
                    'waybill' => $customerOrder->tracking_number,
                ]);

                if ($response->successful()) {
                    Log::info('FSHIP Cancel Order Successfull', [
                        'status' => $response->status(),
                        'response' => $response->json(),
                    ]);
                } else {
                    Log::error('FSHIP Cancel Order Failed', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                    return [false, 'FSHIP Cancel Order Failed', 'cancel'];
                }
            } else if ($customerOrder->courier_partner_code == 'lorrigolive'  || $customerOrder->courier_partner_code == 'lorrigotest') {
                [$status, $message, $type] = $this->services[$customerOrder->courier_partner_code]->cancelShipment(['orderId' => $customerOrder->api_order_id]);
                if (!$status) {
                    return [$status, $message];
                }
            }
        }

        $customerOrder->update([
            'status' => 'cancel',
            'cancel_at' => Carbon::now(),
            'cancelled_by' => $retailer->id,
            'cancelled_reason' => $cancelled_reason
        ]);

        return [true, 'Order has been cancelled by retailer', 'cancel'];
    }

    // Rto
    public function handleRtoOrder($customerOrder)
    {
        $customerOrder->update([
            'status' => 'rto',
            'rto_at' => Carbon::now(),
        ]);

        return [true, 'Order has been transferred to RTO', 'rto'];
    }

    // Return to seller



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

        if ($customerOrder->courier_partner_code == 'fship') {
            $apiUrl = config('services.fship.base_url');
            $signature = config('services.fship.signature');

            $response = Http::withHeaders([
                'signature' => $signature,
                'Content-Type' => 'application/json',
            ])->post($apiUrl . '/cancelorder', [
                'reason' => $cancelled_reason,
                'waybill' => $customerOrder->tracking_number,
            ]);

            if ($response->successful()) {
                Log::info('FSHIP Cancel Order Successfull', [
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);
            } else {
                Log::error('FSHIP Cancel Order Failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return [false, 'FSHIP Cancel Order Failed', 'cancel'];
            }
        } else if ($customerOrder->courier_partner_code == 'lorrigolive') {
            // call lorrigo cancel order API
        }

        $customerOrder->update([
            'status' => 'cancel',
            'cancel_at' => Carbon::now(),
            'cancelled_by' => $retailer->id,
            'cancelled_reason' => $cancelled_reason
        ]);

        return [true, 'Order has been cancelled by retailer', 'cancel'];
    }

    // CANCELLED WITH CHARGES (Pickup ke bad & Delivery se pehle Order cancel hota hai) (Return to seller)
    public function NdrtoRto($retailer, $customerOrder)
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

        $customerOrder->update([
            'status' => 'rtn_to_seller',
            'shipment_activity' => 'Marked as RTO by system',
            'rtn_to_seller_at' => Carbon::now(),
        ]);

        return [true, 'Order has been RTO By Courier Partner', 'cancel'];
    }
}
