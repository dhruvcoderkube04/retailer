<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AccountTransaction;
use App\Models\COrders;
use App\Models\CourierPartner;
use App\Models\CustomerDetails;
use App\Models\CustomerOrders;
use App\Models\OrderProductDetails;
use App\Models\LorrigoCarrier;
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
                SUM(CASE WHEN status = 'approved_by_retailer' THEN 1 ELSE 0 END) as approved_by_retailer,
                SUM(CASE WHEN status = 'pickup' THEN 1 ELSE 0 END) as pickup,
                SUM(CASE WHEN status = 'in_transit' THEN 1 ELSE 0 END) as in_transit,
                SUM(CASE WHEN status = 'ofd' THEN 1 ELSE 0 END) as ofd,
                SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN status = 'rto' THEN 1 ELSE 0 END) as rto,
                SUM(CASE WHEN status = 'rtn_to_seller' THEN 1 ELSE 0 END) as rtn_to_seller,
                SUM(CASE WHEN status = 'close' THEN 1 ELSE 0 END) as close,
                SUM(CASE WHEN status = 'cancel' THEN 1 ELSE 0 END) as cancel,
                SUM(CASE WHEN status = 'lost' THEN 1 ELSE 0 END) as lost
            ")
            ->where('order_process_by', 'retailer')
            ->where('checkout_type', 'normal')
            ->first()->toArray();

        // Pickup address
        $pickupAddress = PickAddress::where('user_id', $retailer->id)->get();

        // Courier list via service manager
        try {
            $courierService = \App\Services\CourierServiceManager::getService();
            $courierServices = $courierService->courierList();
        } catch (Exception $e) {
            Log::error('Failed to fetch courier list: ' . $e->getMessage());
            $courierServices = [];
        }

        // Payment status
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
        $payment_method_list = CustomerOrders::select('payment_method')
            ->where('retailer_id', $retailer->id)
            ->where('status', $statusMap[$type])
            ->distinct()
            ->get();

        return view('orders.orders-list', compact(
            'count',
            'pickupAddress',
            'courierServices',
            'payment_method_list',
            'type'
        ));
    }

    // AJAX : server-side data-table for order list
    public function fetchRecordOrderList(Request $request)
    {
        $limit = ($request->has('length') ? $request->input('length') : 10);
        $page = ($request->has('start') ? $request->input('start') : 0);
        $search = ($request->has('search') ? $request->input('search')['value'] : '');
        $payment_method_filter = $request->input('payment_method_filter');
        $date_filter = explode(' - ', $request->input('date_filter'));
        $type = $request->type;

        $from = Carbon::createFromFormat('d/m/Y', $date_filter[0])->format('Y-m-d');
        $to = Carbon::createFromFormat('d/m/Y', $date_filter[1])->format('Y-m-d');

        $retailer = Auth::user();

        // Filter by type to stage
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

        // Filter by type to date_at
        $stageDateMap = [
            'new' => 'created_at',
            'approved-by-retailer' => 'approved_by_retailer_at',
            'pickup' => 'pickup_at',
            'in-transit' => 'in_transit_at',
            'ofd' => 'ofd_at',
            'delivered' => 'delivered_at',
            'rto' => 'rto_at',
            'rtn-to-seller' => 'rtn_to_seller_at',
            'close' => 'close_at',
            'cancel' => 'cancel_at',
            'lost' => 'lost_at',
        ];

        // Filter by type to name
        $typeNameMap = [
            'new' => 'New',
            'approved-by-retailer' => 'Approved',
            'pickup' => 'Pickup',
            'in-transit' => 'In Transit',
            'ofd' => 'OFD',
            'delivered' => 'Delivered',
            'rto' => 'RTO',
            'rtn-to-seller' => 'RTN to Seller',
            'close' => 'Close',
            'cancel' => 'Cancel',
            'lost' => 'Lost',
        ];

        // Filter by type to color
        $typeColorMap = [
            'new' => 'primary',
            'approved-by-retailer' => 'info',
            'pickup' => 'success',
            'in-transit' => 'warning',
            'ofd' => 'warning',
            'delivered' => 'success',
            'rto' => 'danger',
            'rtn-to-seller' => 'success',
            'close' => 'danger',
            'cancel' => 'danger',
            'lost' => 'muted',
        ];

        $query = CustomerOrders::with([
            'customer',
            'order_product_detail',
            'wholesaler.userDetail',
        ])
            ->where('order_process_by', 'retailer')
            ->where('checkout_type', 'normal')
            ->where('retailer_id', $retailer->id)
            ->where('status', $statusMap[$type])
            ->whereDate($stageDateMap[$type], '>=', $from) // filter : date
            ->whereDate($stageDateMap[$type], '<=', $to); // filter : date

        // search
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', '%' . $search . '%')
                    ->orWhere('quantity', 'like', '%' . $search . '%')
                    ->orWhere('final_amount', 'like', '%' . $search . '%')
                    ->orWhere('shipment_status', 'like', '%' . $search . '%')
                    ->orWhere('courier_partner_code', 'like', '%' . $search . '%')
                    ->orWhere('cancelled_reason', 'like', '%' . $search . '%')
                    ->orWhere('product_weight', 'like', '%' . $search . '%')
                    ->orWhere('tracking_number', 'like', '%' . $search . '%')
                    ->orWhere('courier_service', 'like', '%' . $search . '%')
                    ->orWhere('service_mode', 'like', '%' . $search . '%')
                    ->orWhere('shipping_charge', 'like', '%' . $search . '%')
                    ->orWhere('cod_charge', 'like', '%' . $search . '%')
                    ->orWhere('rto_charge', 'like', '%' . $search . '%')
                    // ->orWhere('charges', 'like', '%' . $search . '%')
                    ->orWhere('payment_method', 'like', '%' . $search . '%')
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where(function ($q) use ($search) {
                            $q->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ["%$search%"])
                                ->orWhere('firstname', 'like', '%' . $search . '%')
                                ->orWhere('lastname', 'like', '%' . $search . '%')
                                ->orWhere('phone_number', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%')
                                ->orWhere('address', 'like', '%' . $search . '%')
                                ->orWhere('state', 'like', '%' . $search . '%')
                                ->orWhere('city', 'like', '%' . $search . '%')
                                ->orWhere('pincode', 'like', '%' . $search . '%');
                        });
                    })
                    ->orWhereHas('order_product_detail', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%')
                            ->orWhere('slug', 'like', '%' . $search . '%');
                    });
            });
        }

        // filter : payment_method
        if (!empty($payment_method_filter) && $payment_method_filter != 'all') {
            $query->where('payment_method', $payment_method_filter);
        }

        if ($request->has('order') && isset($request->order[0])) {
            $columnIndex = $request->order[0]['column'];  // get column index
            $columnName = $request->columns[$columnIndex]['data'];  // get column name
            $direction = $request->order[0]['dir'];  // get sort direction (asc or desc)

            $query->orderBy($columnName, $direction);
        } else {
            $query->orderBy($stageDateMap[$type], 'desc');
        }

        $cntFilter = clone $query;
        $query->offset($page)->limit($limit);
        $orders = $query->get();

        $queryTotal = CustomerOrders::with([
            'customer',
            'order_product_detail',
            'wholesaler.userDetail',
        ])
            ->where('order_process_by', 'retailer')
            ->where('checkout_type', 'normal')
            ->where('retailer_id', $retailer->id)
            ->where('status', $statusMap[$type])
            ->whereDate($stageDateMap[$type], '>=', $from)
            ->whereDate($stageDateMap[$type], '<=', $to)
            ->count('id');

        $data = [];
        $i = $page;
        foreach ($orders as $item) {
            $i++;

            $stageDateField = $stageDateMap[$type];
            $order_date = '<div>';
            if ($type !== 'new') {
                $order_date .= '<div class="mb-7">
                    <div class="fw-bold">' . $typeNameMap[$type] . ' At:</div>
                    <div>' . date('F d, Y, h:i a', strtotime($item->$stageDateField)) . '</div>
                </div>';
            }
            $order_date .= '<div>
                    <div class="fw-bold">Order Date:</div>
                    <div>' . date('F d, Y, h:i a', strtotime($item->created_at)) . '</div>
                </div>
            </div>';

            $order_detail = '<div>
            <div class="my-2">
                <strong>Order Id:</strong> ' . $item->order_id . '
            </div>
            <div class="my-2">
                <strong>Name:</strong> ' . ($item?->order_product_detail?->name ?? '') . '
            </div>
            <div class="my-2">
                <strong>Quantity:</strong> Qty: ' . $item->quantity . ' ' . ($item->size ? '| Size: ' . $item->size : '') . '
            </div>
            <div class="my-2">
                <strong>Amount:</strong> ₹' . $item?->final_amount . '
            </div>
            <div class="my-2">
                <strong>Payment:</strong> ' . strtoupper($item->payment_method) . '
            </div>
            <div class="my-2">
                <strong>Order Status:</strong>
                <span class="badge badge-' . $typeColorMap[$type] . '">
                    ' . order_status($item->status) . '
                </span>
            </div>
            <div class="my-2">
                <strong>Tracking Id:</strong> ' . ($item->tracking_number ?? '') . '
            </div>
            <div class="my-2">
                <strong>API Order Id:</strong> ' . ($item->api_order_id ?? '') . '
            </div>';
            if ($item->status == 'pickup' && $item->shipping_label_url) {
                $order_detail .= '
                    <div class="my-2">
                        <a href="' . $item->shipping_label_url . '" target="_blank">
                            <i class="fa-solid fa-download"></i> Shipping Label
                        </a>
                    </div>
                    <div class="my-2">
                        <a href="javascript:void(0)" id="uploadPickupImage" data-order-id="' . $item->id . '">
                            <i class="fa-solid fa-upload"></i> Upload Pickup Image
                        </a>
                    </div>';
            }
            $order_detail .= '</div>';

            $imagePath = explode(',', $item->order_product_detail->images)[0];
            $media = '<div class="mt-2">';
            if ($imagePath) {
                $media .= '<img src="' . $imagePath . '" alt="Product Image" style="width: 100px; height: auto; border-radius: 5px;">';
            }
            $media .= '</div>';

            $customer_detail = '<div class="my-2">
                <strong>Name:</strong> ' . $item->customer->firstname . ' ' . $item->customer->lastname . '
            </div>
            <div class="my-2">
                <strong>Email Id:</strong> ' . $item->customer->email . '
            </div>
            <div class="my-2">
                <strong>Address:</strong> ' . $item->customer->address . '
            </div>
            <div class="my-2">
                <strong>Pin Code:</strong> ' . $item->customer->pincode . '
            </div>
            <div class="my-2">
                <strong>City:</strong> ' . $item->customer->city . '
            </div>
            <div class="my-2">
                <strong>Mobile no:</strong> ' . $item->customer->phone_number . '
            </div>';

            $action = '';

            $common_attrs = '
                data-order-product-id="' . $item->order_product_id . '"
                data-product-id="' . $item->product_id . '"
                data-retailer-clone-product-id="' . $item->retailer_clone_product_id . '"
                data-order-id="' . $item->id . '"
                data-product-amount="' . ($item?->final_amount) . '"
                data-product-pincode="' . $item->customer->pincode . '"
                data-c-order-id="' . $item->order_id . '"';

            if ($item->status == 'pending') {
                $action .= '<button type="button" class="btn btn-primary btn-sm newOrderAction"' . $common_attrs . '>Action</button>';
            } elseif ($item->status == 'approved_by_retailer') {
                $action .= '<button type="button" class="btn btn-primary btn-sm confirmedOrderAction"' . $common_attrs . '>Action</button>';
            } elseif ($item->status == 'pickup') {
                $action .= '<button type="button" class="btn btn-primary btn-sm pickupOrderAction"' . $common_attrs . '>Action</button>';
            } elseif ($item->status == 'in_transit') {
                $action .= '<button type="button" class="btn btn-primary btn-sm inTransitOrderAction"' . $common_attrs . '>Action</button>';
            } else {
                $action .= '<button type="button" class="btn btn-primary btn-sm" style="white-space: nowrap; opacity: 0.4" ' . $common_attrs . ' disabled>Action</button>';
            }

            $data[] = array(
                'sr_no' => $i,
                'order_date' => $order_date,
                'order_detail' => $order_detail,
                'media' => $media,
                'customer_detail' => $customer_detail,
                'action' => $action,
            );
        }
        return response()->json(array("draw" => $_POST['draw'], "recordsTotal" => $queryTotal, "recordsFiltered" => $cntFilter->count(), 'data' => $data));
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
            $customerOrder = CustomerOrders::with(['customer', 'order_product_detail'])->find($request->order_id);

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

            $customerOrder = CustomerOrders::with('order_product_detail')
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
                return response()->json(['status' => false, 'msg' => $message ?? 'Invalid Order Status']);
            }
        } catch (Exception $e) {
            DB::rollBack();
            // return response()->json(['status' => false, 'msg' => $e->getmessage()]);
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
            'checkout_type' => 'cod',
        ]);

        return [true, 'Wholesaler will ship this product', 'new'];
    }

    // PICKUP
    private function handlePickupStatus($request, $customerOrder, $pickup_address)
    {
        $productName = $customerOrder->order_product_detail->name ?? 'N/A';
        $productSku = $customerOrder->order_product_detail->sku ?? 'N/A';

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
        } elseif (!empty($active_courier_partners) &&  $active_courier_partners->code == 'lorrigo') {

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
                        $updateData['courier_partner_code'] = 'lorrigo';

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
            $wholesalerDetail = UserDetail::where('user_id', $customerOrder->order_product_detail->wholesaler_id)->first();

            $marginFetch = RetailerProducts::where('retailer_id', $retailer->id)
                ->where('wholesaler_id', $customerOrder->order_product_detail->wholesaler_id)
                ->where('category_id', $customerOrder->order_product_detail->category_id)
                ->first();

            if (!$marginFetch) {
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


    //<-------------- START : My Orders (Retailer's own orders) ------------------>
    public function myOrderList(Request $request)
    {
        try {
            $retailer = Auth::user();

            // Orders query
            $sql = CustomerOrders::with([
                'order_product_detail',
                'wholesaler.userDetail',
            ])
                ->where('order_process_by', 'wholesaler')
                ->where('checkout_type', 'punch')
                ->where('retailer_id', $retailer->id);

            $myOrders = $sql->orderBy('id', 'DESC')->get();

            return view('my-orders.my-orders-list', compact('myOrders'));
        } catch (Exception $e) {
            Log::error('Failed to fetch my order list: ' . $e->getMessage());
            session()->flash('error', 'Something went wrong!');
            return redirect()->route('retailer.dashboard');
        }
    }

    public function fetchmyOrderList(Request $request)
    {
        try {
            $retailer = Auth::user();
            $query = CustomerOrders::with(['order_product_detail', 'wholesaler.userDetail'])
                ->where('order_process_by', 'wholesaler')
                ->where('checkout_type', 'punch')
                ->where('retailer_id', $retailer->id);

            // Search filter
            if (!empty($request->search['value'])) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('order_id', 'like', "%{$search}%")
                        ->orWhereHas('order_product_detail', function($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                });
            }

            $totalRecords = $query->count();

            $records = $query->orderBy('id', 'desc')
                ->skip($request->start)
                ->take($request->length)
                ->get();

            $data = [];

            foreach ($records as $index => $order) {
                $product = $order->order_product_detail;
                $imagePath = explode(',', $product->images ?? '')[0] ?? '';
                $wholesaler = $order->wholesaler->userDetail ?? null;

                $orderStatus = [
                    'pending' => 'Pending',
                    'approved_by_retailer' => 'Approved By Retailer',
                    'transfered_retailer_to_wholesaler' => 'Transferred To Wholesaler',
                    'approved_by_wholesaler' => 'Confirmed By Wholesaler',
                    'pickup' => 'Pickup',
                    'in_transit' => 'In Transit',
                    'ofd' => 'OFD',
                    'delivered' => 'Delivered',
                    'rto' => 'RTO',
                    'rtn_to_seller' => 'RTN To Seller',
                    'close' => 'Close',
                    'cancel' => 'Cancelled',
                    'lost' => 'Lost',
                    'received' => 'Received',
                ];

                $statusText = $orderStatus[$order->status] ?? 'Unknown';
                $statusBadge = $order->status == 'approved' ? 'badge-success' : 'badge-danger';

                $orderDetailHTML = "
                    <div><strong>Order Id:</strong> {$order->order_id}</div>
                    <div><strong>Name:</strong> " . ($product->name ?? 'N/A') . "</div>
                    <div><strong>Quantity:</strong> Qty: {$order->quantity}" . ($order->size ? ' | Size: ' . $order->size : '') . "</div>
                    <div><strong>Amount:</strong> ₹{$order->final_amount}</div>
                    <div><strong>Order Status:</strong> <span class='badge {$statusBadge}'>{$statusText}</span></div>";

                if ($order->status == 'cancel') {
                    $orderDetailHTML .= "<div><strong>Reject Reason:</strong> <span class='text-danger'>" . ($order->cancelled_reason ?? 'N/A') . "</span></div>";
                }

                $orderDetailHTML .= "
                    <div><strong>Tracking Id:</strong> " . ($order->tracking_number ?? 'N/A') . "</div>
                    <div><strong>API Order Id:</strong> " . ($order->api_order_id ?? 'N/A') . "</div>";

                if ($order->status == 'pickup' && $order->shipping_label_url) {
                    $orderDetailHTML .= "
                        <div><a href='{$order->shipping_label_url}' target='_blank'><i class='fa-solid fa-download'></i> Shipping Label</a></div>
                        <div><a href='javascript:void(0)' id='uploadPickupImage' data-order-id='{$order->id}'><i class='fa-solid fa-upload'></i> Upload Pickup Image</a></div>";
                }

                $data[] = [
                    'no' => $request->start + $index + 1,
                    'order_date' => date('F d, Y, h:i a', strtotime($order->created_at)),
                    'order_detail' => $orderDetailHTML,
                    'media' => $imagePath ? "<img src='{$imagePath}' width='100' style='border-radius: 5px;'>" : '',
                    'wholesaler_detail' => $wholesaler ? "
                        <strong>Name:</strong> {$wholesaler->company_name}<br>
                        <strong>Email:</strong> {$order->wholesaler->email}<br>
                        <strong>Mobile:</strong> {$order->wholesaler->phone_number}
                    " : 'N/A',
                ];
            }

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
    }
    //<-------------- END : My Orders (Retailer's own orders) ------------------>
}
