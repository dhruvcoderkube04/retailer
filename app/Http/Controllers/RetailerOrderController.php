<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\CancelOrderMailToCustomer;
use App\Models\COrders;
use App\Models\CourierPartner;
use App\Models\CustomerDetails;
use App\Models\CustomerOrders;
use App\Models\OrderProductDetails;
use App\Models\LorrigoCarrier;
use App\Models\MarginManagement;
use App\Models\OrderNotification;
use App\Models\PickAddress;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\RetailerCloneProduct;
use App\Models\RetailerProducts;
use App\Models\RTOAddress;
use App\Models\UserDetail;
use App\Services\OrderStatusService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

use App\Services\Courier\FShipService;
use App\Services\Courier\LorrigoService;  //for test
use App\Services\Courier\LorrigoServiceLive; // for live

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
                ->where('sub_category_id', $retailerProduct->sub_category_id)
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
                SUM(CASE WHEN status = 'transfered_retailer_to_wholesaler' THEN 1 ELSE 0 END) as transfered_retailer_to_wholesaler,
                SUM(CASE WHEN status = 'pickup' THEN 1 ELSE 0 END) as pickup,
                SUM(CASE WHEN status = 'in_transit' THEN 1 ELSE 0 END) as in_transit,
                SUM(CASE WHEN status = 'ofd' THEN 1 ELSE 0 END) as ofd,
                SUM(CASE WHEN status = 'ndr' THEN 1 ELSE 0 END) as ndr,
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
        $active_courier_partner = CourierPartner::where('is_active', 1)->first();
        $pickupAddress = PickAddress::where('user_id', $retailer->id)->where('courier_partner_id', $active_courier_partner->id)->get();

        // Courier list via service manager
        try {
            $courierService = \App\Services\CourierServiceManager::getServiceByCode('fship'); #only get couier list api call
            $courierServices = $courierService->courierList();
            // $courierServices = [];
        } catch (Exception $e) {
            Log::error('Failed to fetch courier list: ' . $e->getMessage());
            $courierServices = [];
        }

        // Payment status
        $statusMap = [
            'new' => 'pending',
            'approved-by-retailer' => 'approved_by_retailer',
            'transferred-to-wholesaler' => 'transfered_retailer_to_wholesaler',
            'pickup' => 'pickup',
            'in-transit' => 'in_transit',
            'ofd' => 'ofd',
            'ndr' => 'ndr',
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
            'transferred-to-wholesaler' => 'transfered_retailer_to_wholesaler',
            'pickup' => 'pickup',
            'in-transit' => 'in_transit',
            'ofd' => 'ofd',
            'ndr' => 'ndr',
            'delivered' => 'delivered',
            'rto' => 'rto',
            'rtn-to-seller' => 'rtn_to_seller',
            'close' => 'close',
            'cancel' => 'cancel',
            'lost' => 'lost',
        ];

        // Filter by type to date_at
        $stageDateMap = [
            'new' => 'created_at',
            'approved-by-retailer' => 'approved_by_retailer_at',
            'transferred-to-wholesaler' => 'transfered_retailer_to_wholesaler_at',
            'pickup' => 'pickup_at',
            'in-transit' => 'in_transit_at',
            'ofd' => 'ofd_at',
            'ndr' => 'ndr_at',
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
            'transferred-to-wholesaler' => 'Transferred to Wholesaler',
            'pickup' => 'Pickup',
            'in-transit' => 'In Transit',
            'ofd' => 'OFD',
            'ndr' => 'NDR',
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
            'transferred-to-wholesaler' => 'primary',
            'pickup' => 'success',
            'in-transit' => 'warning',
            'ofd' => 'warning',
            'ndr' => 'danger',
            'delivered' => 'success',
            'rto' => 'danger',
            'rtn-to-seller' => 'success',
            'close' => 'danger',
            'cancel' => 'danger',
            'lost' => 'muted',
        ];

        // Filter by status to color
        $statusColorMap = [
            'pending' => 'primary',
            'approved_by_retailer' => 'info',
            'transfered_retailer_to_wholesaler' => 'primary',
            'approved_by_wholesaler' => 'info',
            'pickup' => 'success',
            'in_transit' => 'warning',
            'ofd' => 'warning',
            'ndr' => 'danger',
            'delivered' => 'success',
            'rto' => 'danger',
            'rtn_to_seller' => 'success',
            'close' => 'danger',
            'cancel' => 'danger',
            'lost' => 'muted',
            'received' => 'success',
        ];

        $query = CustomerOrders::with([
            'customer',
            'order_product_detail',
            'wholesaler.userDetail',
            'appliedCoupon'
        ])
            ->where('retailer_id', $retailer->id)
            ->whereDate($stageDateMap[$type], '>=', $from) // filter : date
            ->whereDate($stageDateMap[$type], '<=', $to); // filter : date

        if ($type == 'transferred-to-wholesaler') {
            $query->whereNotNull('transfered_retailer_to_wholesaler_at')
                ->where('order_process_by', 'wholesaler');
        } else {
            $query->where('status', $statusMap[$type])
                ->where('order_process_by', 'retailer')
                ->where('checkout_type', 'normal');
        }

        // search
        if (!empty($search)) {
            $search = trim($search);
            $search = htmlspecialchars($search, ENT_QUOTES, 'UTF-8');

            if (isMaliciousSearch($search) || !preg_match('/^[a-zA-Z0-9\s\-\.]+$/', $search)) {
                abort(400, 'Invalid search input detected.');
            }

            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', '%' . $search . '%')
                    ->orWhere('product_variation', 'like', '%' . $search . '%')
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
        $queryTotalSql = CustomerOrders::with([
            'customer',
            'order_product_detail',
            'wholesaler.userDetail',
        ])
            ->where('retailer_id', $retailer->id)
            ->whereDate($stageDateMap[$type], '>=', $from) // filter : date
            ->whereDate($stageDateMap[$type], '<=', $to); // filter : date
        if ($type == 'transferred-to-wholesaler') {
            $queryTotalSql->whereNotNull('transfered_retailer_to_wholesaler_at')
                ->where('order_process_by', 'wholesaler');
        } else {
            $queryTotalSql->where('status', $statusMap[$type])
                ->where('order_process_by', 'retailer')
                ->where('checkout_type', 'normal');
        }
        $queryTotal = $queryTotalSql->count('id');

        $data = [];
        $i = $page;
        foreach ($orders as $item) {
            $i++;

            $stageDateField = $stageDateMap[$type];
            $order_date = '<div class="row">
                    <div class="col-12 mb-2 fs-6">
                        <strong>Order Date:</strong><br>
                        <span>' . date('F d, Y, h:i a', strtotime($item->created_at)) . '</span>
                    </div>';

            if ($type !== 'new') {
                $order_date .= '
                    <div class="col-12">
                        <strong>' . $typeNameMap[$type] . ' At:</strong><br>
                        <span>' . date('F d, Y, h:i a', strtotime($item->$stageDateField)) . '</span>
                    </div>';
            }
            $order_date .= '</div>';

            $order_detail = '<div class="p-3">
                    <table class="table table-sm mb-0 fs-6">
                        <tr>
                            <td style="width: 30%; padding: 0 !important;"><strong>Order Id:</strong></td>
                            <td style="padding: 0 !important;" class="text-start">' . $item->order_id . '</td>
                        </tr>
                        <tr>
                            <td style="width: 30%; padding: 0 !important;"><strong>Name:</strong></td>
                            <td style="padding: 0 !important;" class="text-start">' . ($item?->order_product_detail?->name ?? '') . '</td>
                        </tr>';
            if ($item->product_variation) {
                $order_detail .= '
                        <tr>
                            <td style="padding: 0 !important;"><strong>Variation:</strong></td>
                            <td style="padding: 0 !important;" class="text-start">
                                <span class="badge badge-light-success">' . $item->product_variation . '</span>
                            </td>
                        </tr>';
            }

            $order_detail .= '
                        <tr>
                            <td style="padding: 0 !important;"><strong>Quantity:</strong></td>
                            <td style="padding: 0 !important;" class="text-start">
                                <span class="badge badge-light-secondary">' . $item->quantity . '</span>';
            if ($item->size) {
                $order_detail .= ' | Size: ' . $item->size;
            }
            $order_detail .= '</td></tr>';
            if (!empty($item->appliedCoupon)) {
                $order_detail .= '
                        <tr>
                            <td style="width: 30%; padding: 0 !important;"><strong>Coupon Code:</strong></td>
                            <td style="padding: 0 !important;" class="text-start">
                                <span class="badge badge-light-danger">' . $item->appliedCoupon->coupon_code . '</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%; padding: 0 !important;"><strong>Discount Amount:</strong></td>
                            <td style="padding: 0 !important;" class="text-start">
                                <span class="badge badge-light-success">₹' . $item->appliedCoupon->discount . '</span>
                            </td>
                        </tr>';
            }

            $order_detail .= '
                        <tr>
                            <td style="width: 30%; padding: 0 !important;"><strong>Amount:</strong></td>
                            <td style="padding: 0 !important;" class="text-start">
                                <span class="badge badge-light-primary">₹' . $item->final_amount . '</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%; padding: 0 !important;"><strong>Payment:</strong></td>
                            <td style="padding: 0 !important;" class="text-start">' . strtoupper($item->payment_method) . '</td>
                        </tr>';

            if ($type == 'transferred-to-wholesaler') {
                $order_detail .= '
                        <tr>
                            <td style="padding: 0 !important;"><strong>Order Status:</strong></td>
                            <td style="padding: 0 !important;">
                                <span class="badge badge-' . $typeColorMap[$type] . '">Transferred to Wholesaler</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 0 !important;"><strong>Current Status:</strong></td>
                            <td style="padding: 0 !important;">
                                <span class="badge badge-' . $statusColorMap[$item->status] . '">' . order_status($item->status) . '</span>
                            </td>
                        </tr>';
                if ($item->status == 'cancel') {
                    $order_detail .= '
                        <tr>
                            <td style="padding: 0 !important;"><strong>Cancel Reason:</strong></td>
                            <td style="padding: 0 !important;" class="text-danger">' . ($item->cancelled_reason ?? 'N/A') . '</td>
                        </tr>';
                }
            } else {
                $order_detail .= '
                        <tr>
                            <td style="padding: 0 !important;"><strong>Order Status:</strong></td>
                            <td style="padding: 0 !important;">
                                <span class="badge badge-' . $typeColorMap[$type] . '">' . order_status($item->status) . '</span>
                            </td>
                        </tr>';
                if ($item->status == 'cancel') {
                    $order_detail .= '
                        <tr>
                            <td style="padding: 0 !important;"><strong>Cancel Reason:</strong></td>
                            <td style="padding: 0 !important;" class="text-danger">' . ($item->cancelled_reason ?? 'N/A') . '</td>
                        </tr>';
                }
            }

            $order_detail .= '
                        <tr>
                            <td style="padding: 0 !important;"><strong>Tracking Id:</strong></td>
                            <td style="padding: 0 !important;">' . ($item->tracking_number ?? 'N/A') . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 0 !important;"><strong>API Order Id:</strong></td>
                            <td style="padding: 0 !important;">' . ($item->api_order_id ?? 'N/A') . '</td>
                        </tr>
                    </table>';

            if (($item->status == 'pickup' || $item->status == 'in_transit' || $item->status == 'ofd'||  $item->status == 'ndr') && $item->shipping_label_url && $type !== 'transferred-to-wholesaler') {
                $order_detail .= '
                    <div class="mt-2">
                        <a href="' . $item->shipping_label_url . '" target="_blank">
                            <i class="fa-solid fa-download me-1"></i> Shipping Label
                        </a>
                    </div>';
            }
            if ($item->status == 'pickup' && $item->shipping_label_url && $type !== 'transferred-to-wholesaler') {
                $order_detail .= '<div class="mt-1">
                        <a href="javascript:void(0)" id="uploadPickupImage" data-order-id="' . $item->id . '">
                            <i class="fa-solid fa-upload me-1"></i> Upload Pickup Image
                        </a>
                    </div>';
            }
            if ($item->status == 'ndr' && $type !== 'transferred-to-wholesaler') {
                $order_detail .= '<div class="mt-1">
                        <tr>
                            <td style="padding: 0 !important;"><strong>Reason:</strong></td>
                            <td style="padding: 0 !important;">' . ($item->shipment_activity ?? '') . '</td>
                        </tr>
                    </div>';
            }

            $order_detail .= '</div>';

            $imagePath = explode(',', $item->order_product_detail->images)[0] ?? null;
            $media = '<div class="text-center">
                    <img src="' . ($imagePath ? Storage::disk('spaces')->url($imagePath) : asset('assets/media/images/no_image.jpg')) . '"
                        onerror="this.onerror=null;this.src=\'' . asset('assets/media/images/no_image.jpg') . '\';"
                        alt="Product Image"
                        class="img-fluid rounded" style="max-width: 100px;">
                </div>';

            $customer_detail = '<div class="p-3"><table class="table table-sm mb-0 fs-6">';
            $customer_detail .= '
                    <tr><td style="padding: 0 !important;"><strong>Name:</strong></td><td style="padding: 0 !important;">' . $item->customer->firstname . ' ' . $item->customer->lastname . '</td></tr>
                    <tr><td style="padding: 0 !important;"><strong>Email Id:</strong></td><td style="padding: 0 !important;">' . $item->customer->email . '</td></tr>
                    <tr><td style="padding: 0 !important;"><strong>Address:</strong></td><td style="padding: 0 !important;">' . $item->customer->address . '</td></tr>
                    <tr><td style="padding: 0 !important;"><strong>Pin Code:</strong></td><td style="padding: 0 !important;">' . $item->customer->pincode . '</td></tr>
                    <tr><td style="padding: 0 !important;"><strong>City:</strong></td><td style="padding: 0 !important;">' . $item->customer->city . '</td></tr>
                    <tr><td style="padding: 0 !important;"><strong>Mobile no:</strong></td><td style="padding: 0 !important;">' . $item->customer->phone_number . '</td></tr>';
            $customer_detail .= '</table></div>';

            $wholesaler_detail = '';
            if ($item->wholesaler) {
                $wholesaler = $item->wholesaler;
                $wholesaler_detail = '<div class="p-3"><table class="table table-sm mb-0 fs-6">';
                $wholesaler_detail .= '
                        <tr><td style="padding: 0 !important;"><strong>Wholesaler Name:</strong></td><td style="padding: 0 !important;">' . ($wholesaler->firstname ?? '') . ' ' . ($wholesaler->lastname ?? '') . '</td></tr>
                        <tr><td style="padding: 0 !important;"><strong>Email Id:</strong></td><td style="padding: 0 !important;">' . ($wholesaler->email ?? '') . '</td></tr>
                        <tr><td style="padding: 0 !important;"><strong>Address:</strong></td><td style="padding: 0 !important;">' . ($wholesaler->userDetail->address ?? '') . '</td></tr>
                        <tr><td style="padding: 0 !important;"><strong>Pin Code:</strong></td><td style="padding: 0 !important;">' . ($wholesaler->userDetail->postal_code ?? '') . '</td></tr>
                        <tr><td style="padding: 0 !important;"><strong>City:</strong></td><td style="padding: 0 !important;">' . ($wholesaler->userDetail->city ?? '') . '</td></tr>
                        <tr><td style="padding: 0 !important;"><strong>Mobile no:</strong></td><td style="padding: 0 !important;">' . ($wholesaler->phone_number ?? '') . '</td></tr>';
                $wholesaler_detail .= '</table></div>';
            }

            $action = '<div class="d-flex justify-content-center gap-2">';
            $common_attrs = '
                    data-order-product-id="' . $item->order_product_id . '"
                    data-product-id="' . $item->product_id . '"
                    data-retailer-clone-product-id="' . $item->retailer_clone_product_id . '"
                    data-order-id="' . $item->id . '"
                    data-product-amount="' . ($item?->final_amount) . '"
                data-product-pincode="' . $item->customer->pincode . '"
                    data-c-order-id="' . $item->order_id . '"
                    data-order-status="' . $item->status . '"
                    data-api-order_id="' . $item->api_order_id . '"';

            if ($item->status == 'pending' && $type !== 'transferred-to-wholesaler') {
                $action .= '<button type="button" class="btn btn-primary btn-sm newOrderAction"' . $common_attrs . '>Action</button>';
            } elseif ($item->status == 'approved_by_retailer' && $type !== 'transferred-to-wholesaler') {
                $action .= '<button type="button" class="btn btn-primary btn-sm confirmedOrderAction"' . $common_attrs . '>Action</button>';
            } elseif ($item->status == 'pickup' && $type !== 'transferred-to-wholesaler') {
                // $action .= '<button type="button" style="white-space: nowrap; opacity: 0.4" class="btn btn-primary btn-sm"' . $common_attrs . ' disabled>Action</button>';
                $action .= '<button type="button" class="btn btn-primary btn-sm pickupOrderAction"' . $common_attrs . ' >Action</button>';
                // $action .= '<button type="button" class="btn btn-danger btn-sm cancelOrder"' . $common_attrs . ' >Cancel</button>';
            } elseif ($item->status == 'in_transit' && $type !== 'transferred-to-wholesaler') {
                // $action .= '<button type="button" style="white-space: nowrap; opacity: 0.4" class="btn btn-primary btn-sm"' . $common_attrs . ' disabled>Action</button>';
                $action .= '<button type="button" class="btn btn-primary btn-sm inTransitOrderAction"' . $common_attrs . ' >Action</button>';
                // $action .= '<button type="button" class="btn btn-danger btn-sm cancelOrder"' . $common_attrs . ' >Cancel</button>';
            } elseif ($item->status == 'ofd' && $type !== 'transferred-to-wholesaler') {
                $action .= '<button type="button" style="white-space: nowrap; opacity: 0.4" class="btn btn-primary btn-sm"' . $common_attrs . ' >Action</button>';
                // $action .= '<button type="button" class="btn btn-primary btn-sm" style="white-space: nowrap; opacity: 0.4" ' . $common_attrs . ' disabled>Action</button>';
                // $action .= '<button type="button" class="btn btn-danger btn-sm cancelOrder"' . $common_attrs . ' >Cancel</button>';
             }elseif ($item->status == 'ndr' && $type !== 'transferred-to-wholesaler') {
                $action .= '<button type="button" style="white-space: nowrap;" class="btn btn-primary btn-sm ndr-reattempt"' . $common_attrs . ' >Re-Attempet Order</button>';
                // $action .= '<button type="button" class="btn btn-primary btn-sm" style="white-space: nowrap; opacity: 0.4" ' . $common_attrs . ' disabled>Action</button>';
                // $action .= '<button type="button" class="btn btn-danger btn-sm cancelOrder"' . $common_attrs . ' >Cancel</button>';

            } elseif ($item->status == 'delivered' && $type !== 'transferred-to-wholesaler') {
                $action .= '<button type="button" style="white-space: nowrap; opacity: 0.4" class="btn btn-primary btn-sm"' . $common_attrs . ' >Action</button>';
                // $action .= '<button type="button" class="btn btn-primary btn-sm" style="white-space: nowrap; opacity: 0.4" ' . $common_attrs . ' disabled>Action</button>';
                // $action .= '<button type="button" class="btn btn-danger btn-sm cancelOrder"' . $common_attrs . ' >Cancel</button>';

            } else {
                $action .= '<button type="button" class="btn btn-primary btn-sm" style="white-space: nowrap; opacity: 0.4" ' . $common_attrs . ' >Action</button>';
            }

            if ($type !== 'transferred-to-wholesaler') {
                $action .= '<button type="button"
                        class="btn btn-icon btn-dark btn-active-light-dark w-30px h-30px raise-issue"
                        data-id="' . $item->order_id . '"
                        data-bs-toggle="tooltip" title="Raise an issue">
                        <i class="ki-duotone ki-cheque fs-3">
                        <span class="path1"></span><span class="path2"></span>
                        <span class="path3"></span><span class="path4"></span>
                        <span class="path5"></span><span class="path6"></span><span class="path7"></span>
                        </i>
                    </button>';
            }

            $whatsappNumber = preg_replace('/[^0-9]/', '', $item->customer->phone_number); // clean up number

            $action .= '<a href="https://web.whatsapp.com/send?phone=' . $whatsappNumber . '" target="_blank"
                class="btn btn-icon btn-success btn-active-light-success w-30px h-30px"
                data-bs-toggle="tooltip" title="Contact via WhatsApp">
                <i class="fab fa-whatsapp fs-3"></i>
            </a></div>';

            $data[] = array(
                'sr_no' => $i,
                'order_date' => $order_date,
                'order_detail' => $order_detail,
                'media' => $media,
                'customer_detail' => $customer_detail,
                'wholesaler_detail' => $wholesaler_detail,
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
            $statusService = new OrderStatusService();

            if ($request->status === 'approved_by_retailer') {
                [$success, $message, $type] = $statusService->handleApproveByRetailer($customerOrder);
            } elseif ($request->status === 'transfered_retailer_to_wholesaler') {
                [$success, $message, $type] = $statusService->handleTransferToWholesaler($customerOrder);
            } elseif ($request->status === 'cancel') {
                $reject_reason_select = $request->reject_reason_select;
                $reject_reason_input = $request->reject_reason_input;

                [$success, $message, $type] = $statusService->handleCancelledOrder($retailer, $customerOrder, $reject_reason_select, $reject_reason_input);
            }

            if ($success) {
                DB::commit();

                if ($request->status == 'cancel') {
                    $cancelled_reason = ($request->reject_reason_select == 'Other')
                        ? $request->reject_reason_input
                        : $request->reject_reason_select;

                    $customer = [
                        'name' => $customerOrder->customer->firstname,
                        'email' => $customerOrder->customer->email,
                    ];
                    Mail::to($customerOrder->customer->email)->send(new CancelOrderMailToCustomer($customerOrder, $customer, $cancelled_reason));
                }

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
            $statusService = new OrderStatusService();

            if ($request->status === 'pickup') {
                [$success, $message, $type] = $statusService->handlePickupStatus($request, $customerOrder, $pickup_address);
            } elseif ($request->status === 'transfered_retailer_to_wholesaler') {
                [$success, $message, $type] = $statusService->handleTransferToWholesaler($customerOrder);
            } elseif ($request->status === 'cancel') {
                $reject_reason_select = $request->reject_reason_select;
                $reject_reason_input = $request->reject_reason_input;

                [$success, $message, $type] = $statusService->handleCancelledOrder($retailer, $customerOrder, $reject_reason_select, $reject_reason_input);
            }

            if ($success) {
                DB::commit();

                if ($request->status == 'cancel') {
                    $cancelled_reason = ($request->reject_reason_select == 'Other')
                        ? $request->reject_reason_input
                        : $request->reject_reason_select;

                    $customer = [
                        'name' => $customerOrder->customer->firstname,
                        'email' => $customerOrder->customer->email,
                    ];
                    Mail::to($customerOrder->customer->email)->send(new CancelOrderMailToCustomer($customerOrder, $customer, $cancelled_reason));
                }

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
            $statusService = new OrderStatusService();

            if ($request->status == 'in_transit') {
                [$success, $message, $type] = $statusService->handleInTransitStatus($customerOrder);
            } elseif ($request->status == 'cancel') {
                $reject_reason_select = $request->reject_reason_select;
                $reject_reason_input = $request->reject_reason_input;

                [$success, $message, $type] = $statusService->handleCancelledOrder($retailer, $customerOrder, $reject_reason_select, $reject_reason_input);
            }

            if ($success) {
                DB::commit();

                if ($request->status == 'cancel') {
                    $cancelled_reason = ($request->reject_reason_select == 'Other')
                        ? $request->reject_reason_input
                        : $request->reject_reason_select;

                    $customer = [
                        'name' => $customerOrder->customer->firstname,
                        'email' => $customerOrder->customer->email,
                    ];
                    Mail::to($customerOrder->customer->email)->send(new CancelOrderMailToCustomer($customerOrder, $customer, $cancelled_reason));
                }

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

            $pickup_image = $order_details->pickup_image
                ? Storage::disk('spaces')->url($order_details->pickup_image)
                : asset('assets/media/images/no_image.jpg');

            return response()->json(['status' => true, 'msg' => 'Success', 'data' => $order_details, 'pickup_image' => $pickup_image]);
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
                $imageUrl = uploadOrUpdateImageToSpaces($file, 'orders/pickup-image', $order->pickup_image);

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
            $statusService = new OrderStatusService();

            if ($request->status == 'delivered') {
                [$success, $message, $type] = $statusService->handleDeliveredOrder($retailer, $customerOrder);
            } else if ($request->status == 'cancel') {
                $reject_reason_select = $request->reject_reason_select;
                $reject_reason_input = $request->reject_reason_input;

                [$success, $message, $type] = $statusService->handleCancelledOrderWithCharges($retailer, $customerOrder, $reject_reason_select, $reject_reason_input);
            }

            if ($success) {
                DB::commit();

                if ($request->status == 'cancel') {
                    $cancelled_reason = ($request->reject_reason_select == 'Other')
                        ? $request->reject_reason_input
                        : $request->reject_reason_select;

                    $customer = [
                        'name' => $customerOrder->customer->firstname,
                        'email' => $customerOrder->customer->email,
                    ];
                    Mail::to($customerOrder->customer->email)->send(new CancelOrderMailToCustomer($customerOrder, $customer, $cancelled_reason));
                }

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

    // ACTION: Cancel Order (NOT IN USED NOW - REMOVED)
    public function cancelOrderAction(Request $request)
    {
        $request->validate([
            'reject_reason_select' => 'required',
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
            $statusService = new OrderStatusService();

            $reject_reason_select = $request->reject_reason_select;
            $reject_reason_input = $request->reject_reason_input;

            if ($customerOrder->status == 'pickup') {
                [$success, $message, $type] = $statusService->handleCancelledOrder($retailer, $customerOrder, $reject_reason_select, $reject_reason_input);
            } else if ($customerOrder->status == 'in_transit' || $customerOrder->status == 'ofd') {
                [$success, $message, $type] = $statusService->handleCancelledOrderWithCharges($retailer, $customerOrder, $reject_reason_select, $reject_reason_input);
            }

            if ($success) {
                DB::commit();

                $cancelled_reason = ($request->reject_reason_select == 'Other')
                    ? $request->reject_reason_input
                    : $request->reject_reason_select;

                $customer = [
                    'name' => $customerOrder->customer->firstname,
                    'email' => $customerOrder->customer->email,
                ];
                Mail::to($customerOrder->customer->email)->send(new CancelOrderMailToCustomer($customerOrder, $customer, $cancelled_reason));

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
            if ($request->has('search') && !empty($request->search) && $request->search !== '') {
                $search = $request->search;
                $search = trim($search);
                $search = htmlspecialchars($search, ENT_QUOTES, 'UTF-8');
    
                if (isMaliciousSearch($search) || !preg_match('/^[a-zA-Z0-9\s\-\.]+$/', $search)) {
                    abort(400, 'Invalid search input detected.');
                }
                $query->where(function ($q) use ($search) {
                    $q->where('order_id', 'like', "%{$search}%")
                        ->orWhere('product_variation', 'like', '%' . $search . '%')
                        ->orWhere('quantity', 'like', '%' . $search . '%')
                        ->orWhere('final_amount', 'like', '%' . $search . '%')
                        ->orWhere('cancelled_reason', 'like', '%' . $search . '%')
                        ->orWhere('tracking_number', 'like', '%' . $search . '%')
                        ->orWhere('courier_service', 'like', '%' . $search . '%')
                        ->orWhere('payment_method', 'like', '%' . $search . '%')
                        ->orWhereHas('order_product_detail', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                });
            }

            $cntFilter = clone $query;
            $records = $query->orderBy('id', 'desc')
                ->skip($request->start)
                ->take($request->length)
                ->get();

            $totalRecords = CustomerOrders::where('order_process_by', 'wholesaler')
                ->where('checkout_type', 'punch')
                ->where('retailer_id', $retailer->id)
                ->count();

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
                    'ndr' => 'NDR',
                    'delivered' => 'Delivered',
                    'rto' => 'RTO',
                    'rtn_to_seller' => 'RTN To Seller',
                    'close' => 'Close',
                    'cancel' => 'Cancelled',
                    'lost' => 'Lost',
                    'received' => 'Received',
                ];

                // Filter by type to color
                $typeColorMap = [
                    'pending' => 'primary',
                    'approved_by_retailer' => 'info',
                    'transfered_retailer_to_wholesaler' => 'primary',
                    'approved_by_wholesaler' => 'info',
                    'pickup' => 'success',
                    'in_transit' => 'warning',
                    'ofd' => 'warning',
                    'ndr' => 'danger',
                    'delivered' => 'success',
                    'rto' => 'danger',
                    'rtn_to_seller' => 'success',
                    'close' => 'danger',
                    'cancel' => 'danger',
                    'lost' => 'muted',
                    'received' => 'success',
                ];

                // $statusText = $orderStatus[$order->status] ?? 'Unknown';
                // $statusBadge = $order->status == 'approved' ? 'badge-success' : 'badge-danger';

                $orderDetailHTML = "
                    <div class='my-2'><strong>Order Id:</strong> {$order->order_id}</div>
                    <div class='my-2'><strong>Name:</strong> " . ($product->name ?? 'N/A') . "</div>";

                if ($order->product_variation) {
                    $orderDetailHTML .= '<div class="col-12 my-2"><strong>Variation:</strong> <div class="badge badge-light-success text-wrap">' . ($order->product_variation ?? 'N/A') . '</div></div>';
                }

                $orderDetailHTML .= "<div class='my-2'><strong>Quantity:</strong> <div class='badge badge-light-secondary text-wrap'> {$order->quantity} </div>" . ($order->size ? ' | Size: ' . $order->size : '') . "</div>
                    <div class='my-2'><strong>Amount:</strong><div class='badge badge-light-primary text-wrap'> ₹ {$order->final_amount} </div></div>
                    <div class='my-2'>
                        <strong>Order Status:</strong>
                        <span class='badge badge-" . $typeColorMap[$order->status] . "'>
                            " . ($orderStatus[$order->status] ?? 'Unknown') . "
                        </span>
                    </div>
                    <div class='my-2'>
                        <strong>Reason:</strong>
                        <span>
                            " . ($orderStatus[$order->status] ?? 'Unknown') . "
                        </span>
                    </div>
                    ";

                if ($order->status == 'cancel') {
                    $orderDetailHTML .= "<div class='my-2'><strong>Reject Reason:</strong> <span class='text-danger'>" . ($order->cancelled_reason ?? 'N/A') . "</span></div>";
                }

                $orderDetailHTML .= "
                    <div class='my-2'><strong>Tracking Id:</strong> " . ($order->tracking_number ?? 'N/A') . "</div>
                    <div class='my-2'><strong>API Order Id:</strong> " . ($order->api_order_id ?? 'N/A') . "</div>";

                if ($order->status == 'pickup' && $order->shipping_label_url) {
                    $orderDetailHTML .= "
                        <div class='my-2'><a href='{$order->shipping_label_url}' target='_blank'><i class='fa-solid fa-download'></i> Shipping Label</a></div>
                        <div class='my-2'><a href='javascript:void(0)' id='uploadPickupImage' data-order-id='{$order->id}'><i class='fa-solid fa-upload'></i> Upload Pickup Image</a></div>";
                }

                $data[] = [
                    'no' => $request->start + $index + 1,
                    'order_date' => date('F d, Y, h:i a', strtotime($order->created_at)),
                    'order_detail' => $orderDetailHTML,
                    'media' => "<img src='" . ($imagePath
                        ? Storage::disk('spaces')->url($imagePath)
                        : asset('assets/media/images/no_image.jpg')) . "'
                        onerror='this.onerror=null;this.src=\"" . asset('assets/media/images/no_image.jpg') . "\";'
                        width='100' style='border-radius: 5px;'>",
                    'wholesaler_detail' => $wholesaler ? "
                        <div class='my-2'><strong>Name:</strong> {$wholesaler->company_name}</div>
                        <div class='my-2'><strong>Email:</strong> {$order->wholesaler->email}</div>
                        <div class='my-2'><strong>Mobile:</strong> {$order->wholesaler->phone_number}</div>
                    " : 'N/A',
                ];
            }

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $cntFilter->count(),
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

    //<-------------- Start : NDR ------------------>
    public function reattemptNdr(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
            'type' => 'required|in:1,2',
            'reschedule_date' => 'nullable|date',
        ]);

        $reschedule_date_time = Carbon::parse($request->reschedule_date)->setTimezone('Asia/Kolkata');
        $customerOrder = CustomerOrders::with('customer')->where('api_order_id', $request->order_id)->first();

        try {
            DB::beginTransaction();

            // ✅ Choose courier service based on partner code
            $courierCode = $customerOrder->courier_partner_code;

            $courierService = match ($courierCode) {
                'fship' => new \App\Services\Courier\FShipService(),
                'lorrigolive' => new \App\Services\Courier\LorrigoServiceLive(),
                'lorrigotest' => new \App\Services\Courier\LorrigoService(),
                default => throw new \Exception('Unsupported courier partner: ' . $courierCode)
            };

            if ($request->type == 1) {
                //  Reattempt logic
                $payload = [
                    'orderId' => $customerOrder->api_order_id,
                    'ndrInfo' => [
                        'name' => trim(($customerOrder->customer->firstname ?? '') . ' ' . ($customerOrder->customer->lastname ?? '')),
                        'rescheduleDate' => $reschedule_date_time,
                        'contact' => $customerOrder->customer->phone_number,
                        'address' => $customerOrder->customer->address ?? '',
                        'comment' => ''
                    ],
                    'type' => 1,
                ];

                $response = $courierService->reattemptShipment($payload);

                if (isset($response['valid'])) {
                    throw new \Exception($response['message']);
                }

                $customerOrder->update([
                    'status' => 'in_transit',
                    'ndr_at' => null,
                    'reschedule_date' => Carbon::parse($request->reschedule_date),
                    'shipment_activity' => 'Reattempt scheduled for ' . $request->reschedule_date,
                ]);

                DB::commit();

                return response()->json([
                    // 'message' => 'Order reattempted successfully.',
                    'api_response' => $response,
                    'type'=>'in-transit'
                ]);

            } elseif ($request->type == 2) {
                // RTO logic
                $retailer = Auth::user();

                $payload = [
                    'orderId' => $customerOrder->api_order_id,
                    // 'ndrInfo' => [
                    //     'name' => trim(($customerOrder->customer->firstname ?? '') . ' ' . ($customerOrder->customer->lastname ?? '')),
                    //     'rescheduleDate' => $reschedule_date_time,
                    //     'contact' => $customerOrder->customer->phone_number,
                    //     'address' => $customerOrder->customer->address ?? '',
                    //     'comment' => ''
                    // ],
                    'type' => 2,
                ];

                $response = $courierService->reattemptShipment($payload);  # rto

                if (isset($response['valid'])) {
                    throw new \Exception($response['message']);
                }

                $statusService = new OrderStatusService();
                [$success, $message, $type] = $statusService->handleRtoOrder($customerOrder);

                $customerOrder->update([
                    'status' => 'rto',
                    'shipment_activity' => 'Marked as RTO by system',
                ]);

                DB::commit();

                return response()->json([
                    'message' => 'Order marked as RTO.',
                    'type' => 'rto'
                ]);
            }

            DB::rollBack();
            return response()->json(['message' => 'Invalid action'], 400);

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('ReattemptNdr error: ' . $e->getMessage());
            return response()->json(['message' =>  $e->getMessage()], 500);
        }
    }
    //<-------------- END : NDR ------------------>
}
