<?php

namespace App\Http\Controllers;

use App\Models\CourierPartner;
use App\Models\CustomerDetails;
use App\Models\CustomerOrders;
use App\Models\OrderProductDetails;
use App\Models\PickAddress;
use App\Models\Product;
use App\Models\RetailerCategory;
use App\Models\RetailerCloneProduct;
use App\Models\RTOAddress;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Services\CourierServiceManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ShippingController extends Controller
{
    public function index(){
        return view('shipping.shipping-list');
    }

    public function directShipping()
    {
        $user = Auth::user();

        $sub_category_ids = RetailerCategory::where('retailer_id', $user->id)
            ->pluck('sub_category_id');

        $sub_category_list = SubCategory::select('category_id', 'sub_category_name', 'id')
            ->where('status', 1)
            ->whereIn('id', $sub_category_ids)
            ->get();

        return view('shipping.direct-shipping', compact('sub_category_list'));
    }


    public function getCustomerRecrodAccrodingOrder(Request $request)
    {
        $userId = Auth::id();
        $customerRecords = CustomerDetails::where('user_id', $userId)->get();

        return response()->json($customerRecords);
    }

    public function storeCustomer(Request $request)
    {
        $validated = $request->validate([
            'firstname' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'email' =>'required|email',
            'phone_number' => 'required|string|max:20',
            'pincode' => 'required|string|max:10',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
        ]);

        $validated['user_id'] = Auth::user()->id; // Add logged-in user

        $customer = CustomerDetails::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Customer added successfully!',
            'customer' => $customer
        ]);
    }
    public function directShippingPlaceOrder(Request $request)
    {
        DB::beginTransaction();

        try {
            // Step 1: Validate product and customer data
            $validatedData = $this->validateDirectShipping($request);

            // Step 2: Create customer if not exists or passed
            $customer = $this->getCustomer($request);

            // Step 3: Create product
            $product = $this->createRetailerProduct($request);

            // Step 4: Place order
            $order = $this->createCustomerOrder($request, $customer, $product);

            // Step 5: Set product to inactive
            $product->update(['status' => 'inactive']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully and product marked as inactive.',
                'order_id' => $order->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Direct Shipping Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function validateDirectShipping(Request $request)
    {
        return $request->validate([
            'product_name' => 'required|min:3|max:100',
            'sub_category_id' => 'required|numeric|exists:sub_categories,id',
            'qty' => 'required|integer|min:1',
            'price' => 'required|numeric|min:1|max:99999999.99',
            'customer_id' => 'required|exists:customer_details,id',
        ]);
    }

    private function createCustomerOrder(Request $request, CustomerDetails $customer, RetailerCloneProduct $product)
    {
        $orderID = 'ORD' . now()->timestamp . rand(10000, 99999);

        $orderProductDetails = OrderProductDetails::create([
            'product_id' => $product->id,
            'sku' => $product->sku,
            'retailer_id' => $product->retailer_id,
            'name' => $product->name,
            'slug' => $product->slug,
            'old_price' => $product->old_price,
            'new_price' => $product->new_price,
            'quantity' => $product->quantity,
            'images' => $product->images,
            'category_id' => $product->category_id,
            'sub_category_id' => $product->sub_category_id,
            'status' => $product->status,
        ]);

        $order = CustomerOrders::create([
            'order_id' => $orderID,
            'customer_id' => $customer->id,
            'order_product_id' => $orderProductDetails->id,
            'retailer_clone_product_id' => $product->id,
            'retailer_id' => $product->retailer_id,
            'quantity' => $request->qty,
            'final_amount' => $request->price * $request->qty,
            'order_process_by' => 'retailer',
            'payment_method' => $request->payment_method,
        ]);

        return $order;
    }

    private function getCustomer(Request $request)
    {
        if (!$request->filled('customer_id')) {
            throw new \Exception('Customer ID is required.');
        }

        return CustomerDetails::findOrFail($request->customer_id);
    }

    private function createRetailerProduct(Request $request)
    {
        $retailerId = Auth::id();
        $product = new RetailerCloneProduct();

        if ($request->hasFile('product_image')) {
            $file = $request->file('product_image');
            $product->images = uploadOrUpdateImageToSpaces($file, 'products/images');
        }

        $subCategory = SubCategory::findOrFail($request->sub_category_id);

        $sku = $request->sku ?: $this->generateUniqueSKU();

        $slug = Str::slug($request->product_name) . '-' . now()->timestamp . '-' . uniqid();

        $product->fill([
            'retailer_id' => $retailerId,
            'name' => $request->product_name,
            'slug' => $slug,
            'category_id' => $subCategory->category_id,
            'sub_category_id' => $request->sub_category_id,
            'status' => 'active',
            'old_price' => 0,
            'new_price' => $request->price,
            'sku' => $sku,
            'quantity' => $request->qty,
        ]);

        $product->save();

        return $product;
    }

    private function generateUniqueSKU()
    {
        do {
            $sku = str_pad(mt_rand(111, 99999999999999), 14, '0', STR_PAD_LEFT);
        } while (RetailerCloneProduct::where('sku', $sku)->exists());

        return $sku;
    }

    public function CreateOwnOrder(){
        return view('shipping.create-own-order');
    }
    public function NDR(){
        return view('shipping.ndr');
    }
    public function labelSetting(){
        return view('shipping.label-setting');
    }
    public function pickAddressList(){

        $user = Auth::user();
        $addresses = PickAddress::where('user_id',$user->id)->get();

        return view('shipping.pick-up-address-list',['addresses' => $addresses]);
    }
    public function rtoAddress()
    {
        $user = Auth::user();
        $RTOaddresses = RTOAddress::where('retailer_id',$user->id)->get();
        return view('shipping.rto-address',['RTOaddresses' => $RTOaddresses]);
    }
    public function reportPage(){
        return view('shipping.report-page');
    }
    public function shippingCharges(){
        return view('shipping.shipping-charges');
    }

    // for muitple courier partner
    public function pickAddressStore(Request $request)
    {
        // Validate the request
        $request->validate([
            'warehouse_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile' => 'required|digits:10',
            'pincode' => 'required|digits:6',
            'address' => 'required|string',
            'state' => 'required|string',
            'city' => 'required|string',
        ]);
        $user = Auth::user();

        // Check if warehouse already exists for this retailer with same name
        $courier_id = CourierPartner::select('id')->where('is_active',1)->first();

        $existingWarehouse = PickAddress::where('user_id', $user->id)
            ->where('warehouse_name', $request->warehouse_name)->where('courier_partner_id',@$courier_id->id)
            ->first();

        $isUpdate = false;
        $warehouseId = null;

        $warehouseData = [
            "warehouseName" => $request->warehouse_name,
            "contactName" => $request->first_name,
            "addressLine1" => $request->address,
            "addressLine2" => "",
            "pincode" => $request->pincode,
            "city" => $request->city,
            "phoneNumber" => $request->mobile,
            "email" => $user->email,
        ];
        try {
            // Log warehouse data before the API call
            Log::info('Warehouse Data:', $warehouseData);

            // Get the active courier service based on the selected courier partner
            $courierService = CourierServiceManager::getService();
            if (!$courierService) {
                // Log if no courier service is found
                Log::error('Courier service not found.');
                return back()->with('error', 'Courier service not found.');
            }

            if ($existingWarehouse && $existingWarehouse->warehouse_id) {
                // If name matches and warehouse ID exists, update warehouse
                $warehouseData['warehouseId'] = $existingWarehouse->warehouse_id;
                $response = $courierService->updateWarehouse($warehouseData);
                $isUpdate = true;
            } else {
                // Create new warehouse
                $response = $courierService->addWarehouse($warehouseData);
            }
            // Log the response received
            Log::info('Courier service response:', ['response' => $response]);

            // Check if the response is an array or an HTTP response
            if (is_array($response)) {
                $res = $response;
            } else {
                $res = $response->json();
            }

            // Log the final response data
            Log::info('Parsed Response:', ['response' => $res]);

            // If the status is false, display the error from the API
            if (isset($res['response']['status']) && $res['response']['status'] === false) {
                // Return error if status is false, show message from response
                return back()->with('error', $res['response']['response']); // Show the specific API error message
            }

            // If the status is true, continue with saving the warehouse data
            if (isset($res['status']) && $res['status'] === true) {
                $warehouseId = $res['warehouseId'] ?? null;

                // Save/update in local DB
                $address = $existingWarehouse ?? new PickAddress();
                $address->warehouse_id = $warehouseId;
                $address->warehouse_name = $request->warehouse_name;
                $address->first_name = $request->first_name;
                $address->last_name = $request->last_name;
                $address->mobile_number = $request->mobile;
                $address->pincode = $request->pincode;
                $address->address = $request->address;
                $address->state = $request->state;
                $address->city = $request->city;
                $address->user_id = $user->id;
                $address->courier_partner_id = @$courier_id->id;
                $address->save();

                $message = $isUpdate ? 'Warehouse updated successfully!' : 'Warehouse added successfully!';
                return back()->with('success', $message);
            }


            $errorMessage = is_array($res['response'] ?? null)
            ? ($res['response']['message'] ?? 'Courier service error.')
            : ($res['response'] ?? 'Courier service error.');

            return back()->with('error', $errorMessage);
        } catch (\Exception $e) {
            // Log the exception for better debugging
            Log::error('Error in pickAddressStore:', ['error' => $e->getMessage()]);

            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function pickAddressedit($id)
    {
        $address = PickAddress::findOrFail($id);

        return response()->json($address);
    }

    // update on updateWarehouse
    public function pickAddressupdate(Request $request, $id)
    {
        $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'mobile_number' => 'required|digits:10',
            'pincode'       => 'required|digits:6',
            'address'       => 'required|string',
            'state'         => 'required|string',
            'city'          => 'required|string',
        ]);

        $user = Auth::user();
        if (!$user) {
            return back()->with('error', 'User not authenticated.');
        }

        $pickAddress = PickAddress::where('warehouse_id', $id)->first();
        if (!$pickAddress) {
            return back()->with('error', 'Warehouse not found.');
        }

        // Prepare data for API call
        $warehouseData = [
            "warehouseId"   => $pickAddress->warehouse_id,
            "warehouseName" => $pickAddress->warehouse_name,
            "contactName"   => $request->first_name . ' ' . $request->last_name,
            "addressLine1"  => $request->address,
            "addressLine2"  => "",
            "pincode"       => $request->pincode,
            "city"          => $request->city,
            "phoneNumber"   => $request->mobile_number,
            "email"         => $user->email,
        ];

        try {
            // Log the warehouse data before making the API call
            Log::info('Warehouse Update Data:', $warehouseData);

            // Get the active courier service based on the selected courier partner
            $courierService = CourierServiceManager::getService();

            if (!$courierService) {
                Log::error('Courier service not found.');
                return back()->with('error', 'Courier service not found.');
            }

            // Make the API call to update the warehouse
            $res = $courierService->updateWarehouse($warehouseData);

            // Log the raw response received from the API
            Log::info('Courier service response:', ['response' => $res]);

            // Check if the response is in array format or if it's an HTTP response object
            if (is_array($res)) {
                $response = $res;
            } else {
                $response = $res->json();
            }

            // Log the parsed response data
            Log::info('Parsed Response:', ['response' => $response]);
            // Check if the status is true and continue with updating the address in the local DB
            if (isset($response['status']) && $response['status'] === true) {
                // Update the warehouse details in the local database
                $pickAddress->update([
                    'first_name'    => $request->first_name,
                    'last_name'     => $request->last_name,
                    'mobile_number' => $request->mobile_number,
                    'pincode'       => $request->pincode,
                    'address'       => $request->address,
                    'state'         => $request->state,
                    'city'          => $request->city,
                    'user_id'       => $user->id,
                ]);

                // Return success message
                return back()->with('success', 'Warehouse updated successfully!');
            }

            // If status is not true, show the error message
            $errorMessage = isset($response['response']) ? ($response['response']['message'] ?? 'Courier service error.') : 'Courier service error.';
            return back()->with('error', $errorMessage);
        } catch (\Exception $e) {
            // Log the exception and provide a generic error message
            Log::error('Warehouse update failed:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function pickAddressdestroy($id)
    {
        $address = PickAddress::find($id);

        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Address not found.'], 404);
        }

        $address->delete();

        return response()->json(['success' => true, 'message' => 'Address deleted successfully.']);
    }

    public function RTOAddressStore(Request $request)
    {

        // dd($request->all());
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile' => 'required|digits:10',
            'pincode' => 'required|digits:6',
            'address' => 'required|string',
            'state' => 'required|string',
            'city' => 'required|string',
        ]);

        // Get authenticated retailer
        $retailer = Auth::user();


        // dd($retailer);
        // Store data with retailer_id
        RTOAddress::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'mobile_number' => $request->mobile,
            'pincode' => $request->pincode,
            'address' => $request->address,
            'state' => $request->state,
            'city' => $request->city,
            'retailer_id' => $retailer->id, // Store retailer_id
        ]);

        return back()->with('success', 'Address added successfully!');
    }

    public function RTOAddressedit($id)
    {
        $address = RTOAddress::findOrFail($id);

        // dd($address);
        return response()->json($address);
    }

    public function RTOAddressupdate(Request $request, $id)
    {

        // dd($request->all());
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'mobile_number'     => 'required|digits:10',
            'pincode'    => 'required|digits:6',
            'address'    => 'required|string',
            'state'      => 'required|string',
            'city'       => 'required|string',
        ]);

        $pickAddress = RTOAddress::findOrFail($id);
        $pickAddress->update($request->all());

        return redirect()->back()->with('success', 'Address updated successfully!');
    }

    public function RTOAddressdestroy($id)
    {
        $address = RTOAddress::find($id);

        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Address not found.'], 404);
        }

        $address->delete();

        return response()->json(['success' => true, 'message' => 'Address deleted successfully.']);
    }

    public function pincodeServiceable()
    {
        $partner = CourierPartner::where('is_active', true)->firstOrFail();
        return view('shipping.pincode-serviceable',compact('partner'));
    }

    public function pincodeCheckAvailability(Request $request)
    {
        try {
            $courierService = CourierServiceManager::getService();

            // Let the service itself handle validation
            $response = $courierService->checkPincodeAvailability($request->all());
            return response()->json([
                'success' => true,
                'data' => $response,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Courier API Error (Pincode Check): ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
            ], 500);
        }
    }

    public function trackOrder()
    {
        return view('shipping.track-order-status');
    }

    // user interface courier service manager
    public function trackOrderStatus(Request $request)
    {
        $request->validate([
            'track_no' => 'required|string',
        ]);

        try {
            $courierService = \App\Services\CourierServiceManager::getService();

            $response = $courierService->trackPackage($request->track_no);

            return response()->json([
                'success' => true,
                'data' => $response,
            ]);
        } catch (\Exception $e) {
            \Log::error('Courier API Error (Tracking): ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
