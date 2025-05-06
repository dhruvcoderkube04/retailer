<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AccountTransaction;
use App\Models\COrders;
use App\Models\CourierPartner;
use App\Models\CustomerDetails;
use App\Models\CustomerOrders;
use App\Models\OrderProductDetails;
use App\Models\PickAddress;
use App\Models\Product;
use App\Models\RetailerCloneProduct;
use App\Models\RetailerProducts;
use App\Models\RTOAddress;
use App\Models\UserDetail;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Storage;

class RetailerOrderController extends Controller
{
    // place-order page view
    public function placeOrderView(Request $request)
    {
        // $retailer = Auth::user();
        // $retailerProducts = RetailerProducts::with([
        //     'product',
        //     'wholesaler.userDetail'
        // ])
        //     ->where('retailer_id', $retailer->id)
        //     ->get();

        $retailer = Auth::user()->id;

        $retailerProducts = RetailerProducts::with(['wholesaler.products', 'wholesaler.userDetail'])
            ->where('retailer_id', $retailer)
            ->get();

        $filteredRetailerProducts = $retailerProducts->map(function ($retailerProduct) {
            $products = Product::where('wholesaler_id', $retailerProduct->wholesaler_id)
                ->where('category_id', $retailerProduct->category_id)
                ->distinct('id')
                ->get();

            $retailerProduct->setRelation('products', $products);
            return $retailerProduct;
        });

        return view('place-order.place-order-view', compact('filteredRetailerProducts'));
    }

    // place-order
    public function placeOrder(Request $request)
    {
        $request->validate([
            'firstname' => 'required|max:30',
            'lastname' => 'required|max:30',
            'phone_number' => 'required|numeric|digits:10',
            'email' => 'nullable|email',
            'address' => 'required|max:250',
            'state' => 'required|max:50',
            'city' => 'required|max:50',
            'pincode' => 'required|numeric|digits:6',

            'product_id' => 'required',
            'retailer_id' => 'required',
            'wholesaler_id' => 'required',
            'quantity' => 'required',
            'payment_method' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $customerDetail = new CustomerDetails();
            $customerDetail->firstname = $request->firstname;
            $customerDetail->lastname = $request->lastname;
            $customerDetail->phone_number = $request->phone_number;
            $customerDetail->email = $request->email ?? null;
            $customerDetail->address = $request->address;
            $customerDetail->state = $request->state;
            $customerDetail->city = $request->city;
            $customerDetail->pincode = $request->pincode;
            $customerDetail->save();

            // START: clone to order_product_details
            $product_detail = Product::where('id', $request->product_id)->where('status', 'active')->first();
            if (!$product_detail) {
                $product_detail = RetailerCloneProduct::where('id', $request->product_id)->where('status', 'active')->first();
                if (!$product_detail) {
                    session()->flash('error', 'Invalid product!');
                    return redirect()->route('retailer.place-order-view');
                }
            }

            $orderProductDetails = new OrderProductDetails();
            $orderProductDetails->product_id = $product_detail->id;
            $orderProductDetails->sku = $product_detail->sku;
            $orderProductDetails->wholesaler_id = $product_detail->wholesaler_id ?? null;
            $orderProductDetails->retailer_id = $product_detail->retailer_id ?? null;
            $orderProductDetails->name = $product_detail->name;
            $orderProductDetails->slug = $product_detail->slug;
            $orderProductDetails->description = $product_detail->description;
            $orderProductDetails->brand_name = $product_detail->brand_name;
            $orderProductDetails->tags = $product_detail->tags;
            $orderProductDetails->quantity = $product_detail->quantity;
            $orderProductDetails->old_price = $product_detail->old_price;
            $orderProductDetails->new_price = $product_detail->new_price;
            $orderProductDetails->discount_price = $product_detail->discount_price;
            $orderProductDetails->images = $product_detail->images;
            $orderProductDetails->videos = $product_detail->videos;
            $orderProductDetails->url = $product_detail->url;
            $orderProductDetails->status = $product_detail->status;
            $orderProductDetails->color = $product_detail->color;
            $orderProductDetails->size = $product_detail->size;
            $orderProductDetails->specifications = $product_detail->specifications;
            $orderProductDetails->category_id = $product_detail->category_id;
            $orderProductDetails->category_name = $product_detail->category->category_name ?? null;
            $orderProductDetails->sub_category_id = $product_detail->sub_category_id;
            $orderProductDetails->sub_category_name = $product_detail->sub_category->sub_category_name ?? null;
            $orderProductDetails->meta_title = $product_detail->meta_title;
            $orderProductDetails->meta_description = $product_detail->meta_description;
            $orderProductDetails->meta_keywords = $product_detail->meta_keywords;
            $orderProductDetails->save();
            // END: clone to order_product_details

            $customerOrder = new CustomerOrders();
            $customerOrder->customer_id = $customerDetail->id;
            $customerOrder->order_product_id = $orderProductDetails->id;
            $customerOrder->product_id = $request->product_id;
            $customerOrder->retailer_id = $request->retailer_id;
            $customerOrder->wholesaler_id = $request->wholesaler_id;
            $customerOrder->quantity = $request->quantity;
            $customerOrder->payment_method = $request->payment_method;
            $customerOrder->order_process_by = 'retailer';
            $customerOrder->save();

            DB::commit();
            session()->flash('success', 'Order has been placed successfully');
            return redirect()->route('retailer.order.list');
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Something went wrong!');
            return redirect()->route('retailer.place-order-view');
        }
    }

    // order list page
    // use courierservicemanager
    public function orderList($type = 'new')
    {
        $retailer = Auth::user();

        // Order status count
        $count = CustomerOrders::where('retailer_id', $retailer->id)
            ->selectRaw("
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as new,
                SUM(CASE WHEN status = 'transfered_retailer_to_wholesaler' THEN 1 ELSE 0 END) as transfered_retailer_to_wholesaler,
                SUM(CASE WHEN status = 'approved_by_retailer' THEN 1 ELSE 0 END) as approved_by_retailer,
                SUM(CASE WHEN status = 'pickup' THEN 1 ELSE 0 END) as ready_to_ship,
                SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN status = 'cancel' THEN 1 ELSE 0 END) as cancel,
                SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive
            ")
            ->where('order_process_by', 'retailer')
            ->where('checkout_type', 'normal')
            ->first()->toArray();

        // Orders query
        $sql = CustomerOrders::with([
            'customer',
            'product',
            'retailerCloneProduct',
            'wholesaler.userDetail',
        ])
        ->where('order_process_by', 'retailer')
        ->where('checkout_type', 'normal')
        ->where('retailer_id', $retailer->id);

        // Filter by type
        $statusMap = [
            'new' => 'pending',
            'approved-by-retailer' => 'approved_by_retailer',
            'pickup' => 'pickup',
            'in-transit' => 'in_transit',
            'ofd' => 'ofd',
            'delivered' => 'delivered',
            'rto' => 'rto',
            'rtn-to-seller' => 'rtn_to_seller',
            'close' => 'close',
            'cancel' => 'cancel',
            'lost' => 'lost'
        ];

        if (!array_key_exists($type, $statusMap)) {
            return redirect()->route('retailer.order.list');
        }

        $sql->where('status', $statusMap[$type]);

        $retailerOrders = $sql->orderBy('id', 'DESC')->get();

        // Pickup address
        $pickupAddress = PickAddress::where('user_id', $retailer->id)->get();

        // Courier list via service manager
        try {
            $courierService = \App\Services\CourierServiceManager::getService();

            $courierServices = $courierService->courierList();
            // dd($courierServices);
        } catch (Exception $e) {
            Log::error('Failed to fetch courier list: ' . $e->getMessage());
            $courierServices = [];
        }

        return view('orders.orders-list', compact('retailerOrders', 'count', 'pickupAddress', 'courierServices'));
    }

    // ACTION : New
    public function newOrderAction(Request $request)
    {
        $request->validate([
            'status' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $retailer = Auth::user();
            $customerOrder = CustomerOrders::find($request->order_id);

            if (!$customerOrder) {
                return response()->json(['status' => false, 'msg' => 'Invalid Order ID']);
            }

            $success = false;
            $message = '';
            $type = '';

            if ($request->status === 'approved_by_retailer') {
                [$success, $message, $type] = $this->handleApproveByRetailer($customerOrder);
            } elseif ($request->status === 'transfered_retailer_to_wholesaler') {
                [$success, $message, $type] = $this->handleTransferToWholesaler($customerOrder);
            } elseif ($request->status === 'cancel') {
                [$success, $message, $type] = $this->handleCancelledOrder($retailer, $customerOrder, $request);
            }

            if ($success) {
                DB::commit();
                return response()->json(['status' => true, 'msg' => $message, 'type' => $type]);
            } else {
                DB::rollBack();
                return response()->json(['status' => false, 'msg' => 'Invalid Order Status']);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'msg' => 'Something went wrong, Plase try later!']);
        }
    }

    // ACTION : Approved
    public function confirmedOrderAction(Request $request)
    {
        set_time_limit(180);
        $request->validate([
            'status' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $retailer = Auth::user();
            $customerOrder = CustomerOrders::with(['customer', 'product', 'retailerCloneProduct'])->find($request->order_id);

            if (!$customerOrder) {
                return response()->json(['status' => false, 'msg' => 'Invalid Order ID']);
            }

            $pickup_address = PickAddress::where('id', $request->pickup_address_id)->first();

            $success = false;
            $message = '';
            $type = '';

            if ($request->status === 'pickup') {
                [$success, $message, $type] = $this->handlePickupStatus($request, $customerOrder, $pickup_address);
            } elseif ($request->status === 'transfered_retailer_to_wholesaler') {
                [$success, $message, $type] = $this->handleTransferToWholesaler($customerOrder);
            } elseif ($request->status === 'cancel') {
                [$success, $message, $type] = $this->handleCancelledOrder($retailer, $customerOrder, $request);
            }

            if ($success) {
                DB::commit();
                return response()->json(['status' => true, 'msg' => $message, 'type' => $type]);
            } else {
                DB::rollBack();
                return response()->json(['status' => false, 'msg' => $message]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order Confirmation Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'msg' => $e->getMessage()]);
        }
    }

    // ACTION : pickup
    public function pickupOrderAction(Request $request)
    {
        $request->validate([
            'status' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $retailer = Auth::user();
            $customerOrder = CustomerOrders::find($request->order_id);

            if (!$customerOrder) {
                return response()->json(['status' => false, 'msg' => 'Invalid Order ID']);
            }

            $success = false;
            $message = '';
            $type = '';

            if ($request->status == 'in_transit') {
                [$success, $message, $type] = $this->handleInTransitStatus($customerOrder);
            } elseif ($request->status == 'cancel') {
                [$success, $message, $type] = $this->handleCancelledOrder($retailer, $customerOrder, $request);
            }

            if ($success) {
                DB::commit();
                return response()->json(['status' => true, 'msg' => $message, 'type' => $type]);
            } else {
                DB::rollBack();
                return response()->json(['status' => false, 'msg' => 'Invalid Order Status']);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'msg' => 'Something went wrong, Plase try later!']);
        }
    }

    public function pickupImageFetch(Request $request)
    {
        try {
            $order_details = CustomerOrders::find($request->order_id);

            if (!$order_details) {
                return response()->json(['status' => false, 'msg' => 'Invalid order details']);
            }

            return response()->json(['status' => true, 'msg' => 'Success', 'data' => $order_details]);
        } catch (Exception $e) {
            Log::error('Pickup Image Fetch: ' . $e->getMessage());
            return response()->json(['status' => false, 'msg' => $e->getMessage()]);
        }
    }

    public function pickupImageUpload(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:customer_orders,id',
            'pickup_image' => 'required|mimes:jpg,jpeg,png,webp,svg|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $order = CustomerOrders::findOrFail($request->order_id);

            if ($request->hasFile('pickup_image')) {
                $file = $request->file('pickup_image');
                $filename = 'orders/pickup-image/order_' . $order->id . '_' . time() . '.' . $file->getClientOriginalExtension();

                if (Storage::disk('spaces')->exists($filename)) {
                    Storage::disk('spaces')->delete($filename);
                }

                Storage::disk('spaces')->put($filename, file_get_contents($file), 'public');
                $imageUrl = Storage::disk('spaces')->url($filename);

                $order->pickup_image = $imageUrl;
                $order->save();
            }

            DB::commit();
            return response()->json(['status' => true, 'msg' => 'Pickup image uploaded successfully']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Pickup Image Upload: ' . $e->getMessage());
            return response()->json(['status' => false, 'msg' => 'Server error while uploading image']);
        }
    }

    // ACTION: In-transit
    public function inTransitOrderAction(Request $request)
    {
        $request->validate([
            'status' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $retailer = Auth::user();

            $customerOrder = CustomerOrders::with('product', 'retailerCloneProduct')
                ->find($request->order_id);

            if (!$customerOrder) {
                return response()->json(['status' => false, 'msg' => 'Invalid Order ID']);
            }

            $success = false;
            $message = '';
            $type = '';

            if ($request->status == 'delivered') {
                [$success, $message, $type] = $this->handleDeliveredOrder($retailer, $customerOrder);
            } else if ($request->status == 'cancel') {
                [$success, $message, $type] = $this->handleCancelledOrderWithCharges($retailer, $customerOrder, $request);
            }

            if ($success) {
                DB::commit();
                return response()->json(['status' => true, 'msg' => $message, 'type' => $type]);
            } else {
                DB::rollBack();
                return response()->json(['status' => false, 'msg' => 'Invalid Order Status']);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'msg' => 'Something went wrong, Plase try later!']);
        }
    }


    //<-------------- START : Default Private Function to Re-use ---------------->
    // APPROVED BY RETAILER
    private function handleApproveByRetailer($customerOrder)
    {
        $customerOrder->update([
            'status' => 'approved_by_retailer',
            'approved_by_retailer_at' => Carbon::now(),
        ]);

        return [true, 'Order has been approved successfully', 'approved-by-retailer'];
    }

    // TRANSFER TO WHOLESALER
    private function handleTransferToWholesaler($customerOrder)
    {
        $customerOrder->update([
            'status' => 'transfered_retailer_to_wholesaler',
            'transfered_retailer_to_wholesaler_at' => Carbon::now(),
            'order_process_by' => 'wholesaler',
            'checkout_type'=>'cod',
        ]);

        return [true, 'Wholesaler will ship this product', 'new'];
    }

    // PICKUP
    private function handlePickupStatus($request, $customerOrder, $pickup_address)
    {
        $productName = $customerOrder->retailerCloneProduct->name ?? $customerOrder->product->name ?? 'N/A';
        $productSku = $customerOrder->retailerCloneProduct->sku ?? $customerOrder->product->sku ?? 'N/A';

        $user = Auth::user();

        $updateData = [
            'status' => $request->status,
            'pickup_at' => now(),
            'pickup_address_id' => $request->pickup_address_id,
            'product_weight' => $request->product_weight,
            'service_mode' => $request->service_mode,
            'shipping_charge' => $request->shipping_charge,
            'cod_charge' => $request->cod_charge,
            'rto_charge' => $request->rto_charge,
        ];
        $message = 'Order has been ready to ship (by supplier)';
        $type = 'pickup';

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
        } elseif (!empty($active_courier_partners) &&  $active_courier_partners->code == 'lorrigo') {
            $payload = [
                "ewaybill" => "",
                "order_reference_id" => $customerOrder->order_id . 1,
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
                    "category" => $customerOrder->retailerCloneProduct->sub_category->sub_category_name ?? $customerOrder->product->sub_category->sub_category_name ?? 'N/A',
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
        } elseif (!empty($active_courier_partners) &&  $active_courier_partners->code == 'lorrigo') {
            if (!empty($response['order']['orderStages'][0]['_id']) && !empty($response['order']['_id'])) {
                $updateData['tracking_number'] = $response['order']['orderStages'][0]['_id']; // Using stage _id as tracking_number
                $updateData['api_order_id'] = $response['order']['_id']; // Main order _id

                $pdf = PDF::loadView('orders.pdf.order-shipping-label', [
                    'courier_service_response' => $response['order'],
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
            }
        } else {
            return [false, $response['response'] ?? 'Courier Partner not Select', ''];
        }
    }

    // IN-TRANSIT
    private function handleInTransitStatus($customerOrder)
    {
        $customerOrder->update([
            'status' => 'in_transit',
            'in_transit_at' => Carbon::now(),
        ]);

        return [true, 'Order has been transferred to In-transit', 'in-transit'];
    }

    // DELIVERED
    private function handleDeliveredOrder($retailer, $customerOrder)
    {
        $retailerDetail = UserDetail::where('user_id', $retailer->id)->first();

        $total_charges = ($customerOrder->shipping_charge ?? 0) +
            ($customerOrder->cod_charge ?? 0) +
            ($customerOrder->rto_charge ?? 0);

        $charges = [
            'Shipping Charge' => $customerOrder->shipping_charge,
            'COD Charge' => $customerOrder->cod_charge,
            'RTO Charge' => $customerOrder->rto_charge,
        ];

        // wholesaler product
        if ($customerOrder->product_id && !$customerOrder->retailer_clone_product_id) {
            $wholesalerDetail = UserDetail::where('user_id', $customerOrder->product->wholesaler_id)->first();

            $marginFetch = RetailerProducts::where('retailer_id', $retailer->id)
                ->where('wholesaler_id', $customerOrder->product->wholesaler_id)
                ->where('category_id', $customerOrder->product->category_id)
                ->first();

            if ($customerOrder->final_amount < $marginFetch->margin) {
                $retailer_transaction_amount = 0;
                $retailer_final_transaction_amount = -abs($total_charges);
                $retailer_current_balance = $retailerDetail->wallet - $total_charges;

                $wholesaler_transaction_amount = $customerOrder->final_amount;
                $wholesaler_final_transaction_amount = $customerOrder->final_amount;
                $wholesaler_current_balance = $wholesalerDetail->wallet + $customerOrder->final_amount;
            } else {
                $retailer_transaction_amount = $marginFetch->margin;
                $retailer_final_transaction_amount = $retailer_transaction_amount - $total_charges;
                $retailer_current_balance = $retailerDetail->wallet + $retailer_final_transaction_amount;

                $wholesaler_transaction_amount = $customerOrder->final_amount - $marginFetch->margin;
                $wholesaler_final_transaction_amount = $customerOrder->final_amount - $marginFetch->margin;
                $wholesaler_current_balance = $wholesalerDetail->wallet + $wholesaler_final_transaction_amount;
            }

            // retailer entry
            AccountTransaction::create([
                'customer_order_id' => $customerOrder->id,
                'user_id' => $retailer->id,
                'user_type' => 'retailer',
                'description' => 'Margin amount of order ' . $customerOrder->order_id . ' delivered',
                'transaction_amount' => $retailer_transaction_amount,
                'charges' => $charges,
                'final_transaction_amount' => $retailer_final_transaction_amount,
                'current_balance' => $retailer_current_balance,
                'order_type' => 'completed',
                'status' => 1
            ]);
            $retailerDetail->wallet = $retailer_current_balance;
            $retailerDetail->save();

            // wholesaler entry
            AccountTransaction::create([
                'customer_order_id' => $customerOrder->id,
                'user_id' => $customerOrder->product->wholesaler_id,
                'user_type' => 'wholesaler',
                'description' => 'Product ' . $customerOrder->product->name . ' has been delivered by retailer, Order id is ' . $customerOrder->order_id,
                'transaction_amount' => $wholesaler_transaction_amount,
                'charges' => null,
                'final_transaction_amount' => $wholesaler_final_transaction_amount,
                'current_balance' => $wholesaler_current_balance,
                'order_type' => 'completed',
                'status' => 1
            ]);
            $wholesalerDetail->wallet = $wholesaler_current_balance;
            $wholesalerDetail->save();
        }

        // retailer own product
        if (!$customerOrder->product_id && $customerOrder->retailer_clone_product_id) {
            $retailer_transaction_amount = $customerOrder->final_amount;
            $retailer_final_transaction_amount = $customerOrder->final_amount - $total_charges;
            $retailer_current_balance = $retailerDetail->wallet + $retailer_final_transaction_amount;

            // retailer entry
            AccountTransaction::create([
                'customer_order_id' => $customerOrder->id,
                'user_id' => $retailer->id,
                'user_type' => 'retailer',
                'description' => 'Order ' . $customerOrder->order_id . ' has been delivered',
                'transaction_amount' => $retailer_transaction_amount,
                'charges' => $charges,
                'final_transaction_amount' => $retailer_final_transaction_amount,
                'current_balance' => $retailer_current_balance,
                'order_type' => 'completed',
                'status' => 1
            ]);
            $retailerDetail->wallet = $retailer_current_balance;
            $retailerDetail->save();
        }

        $customerOrder->update([
            'status' => 'delivered',
            'delivered_at' => Carbon::now(),
            'delivered_by' => $retailer->id,
        ]);

        return [true, 'Order has been marked as delivered', 'delivered'];
    }

    // CANCELLED
    private function handleCancelledOrder($retailer, $customerOrder, $request)
    {
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

    // CANCELLED WITH CHARGES
    private function handleCancelledOrderWithCharges($retailer, $customerOrder, $request)
    {
        $retailerDetail = UserDetail::where('user_id', $retailer->id)->first();

        $total_charges = ($customerOrder->shipping_charge ?? 0) +
            ($customerOrder->cod_charge ?? 0) +
            ($customerOrder->rto_charge ?? 0);

        $charges = [
            'Shipping Charge' => $customerOrder->shipping_charge,
            'COD Charge' => $customerOrder->cod_charge,
            'RTO Charge' => $customerOrder->rto_charge,
        ];


        // retailer entry
        AccountTransaction::create([
            'customer_order_id' => $customerOrder->id,
            'user_id' => $retailer->id,
            'user_type' => 'retailer',
            'description' => 'Charges deducted for Order ' . $customerOrder->order_id . ' cancelled from In-transit stage',
            'transaction_amount' => 0,
            'charges' => $charges,
            'final_transaction_amount' => -abs($total_charges),
            'current_balance' => $retailerDetail->wallet - $total_charges,
            'order_type' => 'completed',
            'status' => 1
        ]);
        $retailerDetail->wallet = $retailerDetail->wallet - $total_charges;
        $retailerDetail->save();

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
    //<-------------- END : Default Private Function to Re-use ---------------->

}
