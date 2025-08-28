<?php

namespace App\Http\Controllers;

use App\Mail\CancelOrderMailToCustomer;
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
use App\Services\OrderStatusService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ShippingController extends Controller
{
    public function index()
    {
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
        $search = cleanInput($request->input('query'));

        $customerRecords = CustomerDetails::where('user_id', $userId)
            ->when($search, function ($queryBuilder, $search) {
                $queryBuilder->where(function ($q) use ($search) {
                    $q->where('firstname', 'like', "%$search%")
                        ->orWhere('lastname', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%")
                        ->orWhere('phone_number', 'like', "%$search%");
                });
            })
            ->get();

        return response()->json($customerRecords);
    }

    public function storeCustomer(Request $request)
    {
        // Step 1: Clean all inputs using cleanInput and detect modifications
        $cleanedData = [];
        $cleaningErrors = [];

        foreach ($request->all() as $key => $value) {
            $cleanedValue = cleanInput($value);
            $cleanedData[$key] = $cleanedValue;

            // Check if the input was modified (indicating potential XSS or malicious input)
            if ($cleanedValue !== $value) {
                $cleaningErrors[$key] = "The $key field contained unsafe content and has been sanitized.";
            }
        }

        // Step 2: If cleaning errors exist, return them as validation errors
        if (!empty($cleaningErrors)) {
            return response()->json([
                'success' => false,
                'errors' => $cleaningErrors
            ], 422);
        }

        // Step 3: Validate the cleaned input
        $validator = Validator::make($cleanedData, [
            'firstname'     => 'required|string|max:100',
            'lastname'      => 'required|string|max:100',
            'email'         => 'required|email:rfc,dns|unique:customer_details,email',
            'phone_number'  => 'required|string|max:10|unique:customer_details,phone_number',
            'pincode'       => 'required|string|max:6',
            'address'       => 'required|string|max:255',
            'city'          => 'required|string|max:100',
            'state'         => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Step 4: Create customer with validated data
        $validated = $validator->validated();
        $validated['user_id'] = auth()->id();


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
            'sub_category_id' => 'nullable|numeric|exists:sub_categories,id',
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

        $categoryId = null;
        if ($request->filled('sub_category_id')) {
            $subCategory = SubCategory::findOrFail($request->sub_category_id);
            $categoryId = $subCategory->category_id;
        }

        $sku = $request->sku ?: $this->generateUniqueSKU();

        $slug = Str::slug($request->product_name) . '-' . now()->timestamp . '-' . uniqid();

        $product->fill([
            'retailer_id' => $retailerId,
            'name' => $request->product_name,
            'slug' => $slug,
            'category_id' => $categoryId,
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

    public function CreateOwnOrder()
    {
        return view('shipping.create-own-order');
    }
    public function NDR()
    {
        return view('shipping.ndr');
    }
    public function labelSetting()
    {
        return view('shipping.label-setting');
    }
    // public function pickAddressList()
    // {

    //     $user = Auth::user();
    //     $addresses = PickAddress::where('user_id',$user->id)->get();

    //     return view('shipping.pick-up-address-list',['addresses' => $addresses]);
    // }

    public function pickAddressList()
    {
        $active_courier_partner = CourierPartner::where('is_active', 1)->first();
        $user = Auth::user();
        $addresses = PickAddress::select('id', 'warehouse_name', 'first_name', 'last_name', 'mobile_number', 'pincode', 'address', 'state', 'city')
            ->where('user_id', $user->id)
            ->where('courier_partner_id', $active_courier_partner->id)
            ->groupBy('id', 'warehouse_name', 'first_name', 'last_name', 'mobile_number', 'pincode', 'address', 'state', 'city')
            ->get();

        return view('shipping.pick-up-address-list', ['addresses' => $addresses]);
    }

    public function rtoAddress()
    {
        $user = Auth::user();
        $RTOaddresses = RTOAddress::where('retailer_id', $user->id)->get();
        return view('shipping.rto-address', ['RTOaddresses' => $RTOaddresses]);
    }
    public function reportPage()
    {
        return view('shipping.report-page');
    }
    public function shippingCharges()
    {
        return view('shipping.shipping-charges');
    }

    // for muitple courier partner
    // public function pickAddressStore(Request $request)
    // {
    //     // Validate the request
    //     $request->validate([
    //         'warehouse_name' => 'required|string|max:255',
    //         'first_name' => 'required|string|max:255',
    //         'last_name' => 'required|string|max:255',
    //         'mobile' => 'required|digits:10',
    //         'pincode' => 'required|digits:6',
    //         'address' => 'required|string',
    //         'state' => 'required|string',
    //         'city' => 'required|string',
    //     ]);
    //     $user = Auth::user();

    //     // Check if warehouse already exists for this retailer with same name
    //     $courier_id = CourierPartner::select('id')->where('is_active',1)->first();

    //     $existingWarehouse = PickAddress::where('user_id', $user->id)
    //         ->where('warehouse_name', $request->warehouse_name)->where('courier_partner_id',@$courier_id->id)
    //         ->first();

    //     $isUpdate = false;
    //     $warehouseId = null;

    //     $warehouseData = [
    //         "warehouseName" => $request->warehouse_name,
    //         "contactName" => $request->first_name,
    //         "addressLine1" => $request->address,
    //         "addressLine2" => "",
    //         "pincode" => $request->pincode,
    //         "city" => $request->city,
    //         "phoneNumber" => $request->mobile,
    //         "email" => $user->email,
    //     ];
    //     try {
    //         // Log warehouse data before the API call
    //         Log::info('Warehouse Data:', $warehouseData);

    //         // Get the active courier service based on the selected courier partner
    //         $courierService = CourierServiceManager::getService();

    //         if (!$courierService) {
    //             // Log if no courier service is found
    //             Log::error('Courier service not found.');
    //             return back()->with('error', 'Courier service not found.');
    //         }

    //         if ($existingWarehouse && $existingWarehouse->warehouse_id) {
    //             // If name matches and warehouse ID exists, update warehouse
    //             $warehouseData['warehouseId'] = $existingWarehouse->warehouse_id;
    //             $response = $courierService->updateWarehouse($warehouseData);
    //             $isUpdate = true;
    //         } else {
    //             // Create new warehouse
    //             $response = $courierService->addWarehouse($warehouseData);
    //         }
    //         // Log the response received
    //         Log::info('Courier service response:', ['response' => $response]);

    //         // Check if the response is an array or an HTTP response
    //         if (is_array($response)) {
    //             $res = $response;
    //         } else {
    //             $res = $response->json();
    //         }

    //         // Log the final response data
    //         Log::info('Parsed Response:', ['response' => $res]);

    //         // If the status is false, display the error from the API
    //         if (isset($res['response']['status']) && $res['response']['status'] === false) {
    //             // Return error if status is false, show message from response
    //             return back()->with('error', $res['response']['response']); // Show the specific API error message
    //         }

    //         // If the status is true, continue with saving the warehouse data
    //         if (isset($res['status']) && $res['status'] === true) {
    //             $warehouseId = $res['warehouseId'] ?? null;

    //             // Save/update in local DB
    //             $address = $existingWarehouse ?? new PickAddress();
    //             $address->warehouse_id = $warehouseId;
    //             $address->warehouse_name = $request->warehouse_name;
    //             $address->first_name = $request->first_name;
    //             $address->last_name = $request->last_name;
    //             $address->mobile_number = $request->mobile;
    //             $address->pincode = $request->pincode;
    //             $address->address = $request->address;
    //             $address->state = $request->state;
    //             $address->city = $request->city;
    //             $address->user_id = $user->id;
    //             $address->courier_partner_id = @$courier_id->id;
    //             $address->save();

    //             $message = $isUpdate ? 'Warehouse updated successfully!' : 'Warehouse added successfully!';
    //             return back()->with('success', $message);
    //         }


    //         $errorMessage = is_array($res['response'] ?? null)
    //         ? ($res['response']['message'] ?? 'Courier service error.')
    //         : ($res['response'] ?? 'Courier service error.');

    //         return back()->with('error', $errorMessage);
    //     } catch (\Exception $e) {
    //         // Log the exception for better debugging
    //         Log::error('Error in pickAddressStore:', ['error' => $e->getMessage()]);

    //         return back()->with('error', 'Something went wrong: ' . $e->getMessage());
    //     }
    // }

    public function pickAddressStore(Request $request)
    {
        $request->validate([
            'warehouse_name' => 'required|string|max:255|unique:pickup_addresses,warehouse_name',
            'first_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-Za-z\s]+$/'
            ],
            'last_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-Za-z\s]+$/'
            ],

            'mobile' => [
                'required',
                'digits:10',
                'regex:/^[6-9][0-9]{9}$/', // must start with 6-9 and 10 digits
                function ($attribute, $value, $fail) {
                    // Block ascending sequence
                    if ($value === '1234567890') {
                        $fail('Invalid mobile number.');
                    }

                    // Block descending sequence
                    if ($value === '9876543210') {
                        $fail('Invalid mobile number.');
                    }

                    // Block same digit repeated 10 times (1111111111, 9999999999)
                    if (preg_match('/^(\d)\1{9}$/', $value)) {
                        $fail('Invalid mobile number.');
                    }
                },
            ],


            'pincode' => 'required|digits:6',
            'address' => 'required|string|min:10|max:255',
            'state' => 'required|string',
            'city' => 'required|string',
        ], [
            'first_name.regex' => 'Only letters allow as first name',
            'last_name.regex' => 'Only letters allow as last name',
        ]);

        try {
            $user = Auth::user();

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

            $services = CourierServiceManager::getAllServicesForWarehouse();
            $successCount = 0;
            $errorList = [];
            foreach ($services as $entry) {
                $courierService = $entry['service'];
                $partner = $entry['partner'];
                try {
                    $existingWarehouse = PickAddress::where('user_id', $user->id)
                        ->where('warehouse_name', $request->warehouse_name)
                        ->where('courier_partner_id', $partner->id)
                        ->first();

                    if ($existingWarehouse && $existingWarehouse->warehouse_id) {
                        $warehouseData['warehouseId'] = $existingWarehouse->warehouse_id;
                        $response = $courierService->updateWarehouse($warehouseData);
                    } else {
                        $response = $courierService->addWarehouse($warehouseData);
                    }

                    $res = is_array($response) ? $response : $response->json();

                    if (!($res['status'] ?? false)) {
                        $errorList[] = "{$partner->name} failed: " . ($res['message'] ?? 'Unknown error');
                        continue;
                    }

                    $warehouseId = $res['warehouseId'] ?? null;

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
                    $address->courier_partner_id = $partner->id;
                    $address->courier_code = $partner->code;
                    $address->save();

                    $successCount++;
                } catch (\Exception $e) {
                    \Log::error("Warehouse add error for {$partner->name}", ['error' => $e->getMessage()]);
                    $errorList[] = "{$partner->name} error: " . $e->getMessage();
                }
            }

            if ($successCount > 0) {
                return response()->json([
                    'status' => true,
                    'message' => "Warehouse saved with {$successCount} courier partners.",
                    'errors' => $errorList,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => "Failed to add warehouse to all courier partners.",
                    'errors' => $errorList,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Pick Address Store Error', ['exception' => $e]);
            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function pickAddressedit($id)
    {
        $address = PickAddress::findOrFail(decrypt($id));

        return response()->json($address);
    }

    // update on updateWarehouse
    public function pickAddressupdate(Request $request, $id)
    {

        $request->validate([
            'first_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-Za-z\s]+$/'
            ],
            'last_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-Za-z\s]+$/'
            ],
            'mobile_number' => 'required|digits:10',
            'pincode' => 'required|digits:6',
            'address' => 'required|string',
            'state' => 'required|string',
            'city' => 'required|string',
        ], [
            'first_name.regex' => 'Only letters allow as first name',
            'last_name.regex' => 'Only letters allow as last name',
        ]);



        $user = Auth::user();
        if (!$user) {
            return back()->with('error', 'User not authenticated.');
        }

        $pickAddress = PickAddress::where('id', $id)->first();

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
        return view('shipping.pincode-serviceable', compact('partner'));
    }

    // public function pincodeCheckAvailability(Request $request)
    // {
    //     try {
    //         $response = CourierServiceManager::checkServiceAvailableFromAllCouriers($request->all());

    //         return response()->json([
    //             'success' => $response['success'],
    //             'data' => $response['data'] ?? [],
    //         ]);
    //     } catch (\InvalidArgumentException $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage(),
    //         ], 422);
    //     } catch (\Exception $e) {
    //         \Log::error('Courier API Error (Pincode Check): ' . $e->getMessage());

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Something went wrong. Please try again later.',
    //         ], 500);
    //     }
    // }

    public function pincodeCheckAvailability(Request $request)
    {
        try {
            $response = CourierServiceManager::checkServiceAvailableFromAllCouriers($request->all());

            return response()->json([
                'success' => $response['success'],
                'courier' => $response['courier'] ?? null,  // ✅ Include courier name
                'data' => $response['data'] ?? [],
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
            $get_couier = CustomerOrders::select('courier_partner_code')->where('tracking_number', $request->track_no)->first();
            if ($get_couier) {
                $courierService = \App\Services\CourierServiceManager::getServiceByCode($get_couier->courier_partner_code);
                $response = $courierService->trackPackage($request->track_no);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'Tracking Id Not Found',
                ], 200);
            }

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

    public function lorrigoWebhook(Request $request)
    {
        try {
            $order = CustomerOrders::where('tracking_number', $request->awb)->first();

            // // lorrigo status code
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

            $stageActions = [
                "New" => "pickup",
                "Courier Assigned" => "pickup",
                "Picked Up" => "in_transit",
                "Shipped" => "in_transit",
                "In Transit" => "in_transit",
                "Out For Delivery" => "ofd"
            ];

            $services = CourierServiceManager::getAllServicesForTracking();

            $partnerCode = $order->courier_partner_code;

            $courierService = $services[$partnerCode];
            $response = $courierService->trackPackage($order->tracking_number);

            $bucket_id = $response['order']['bucket'];
            $key = $bucketKeyMap[$bucket_id] ?? null;
            $bucket_status = $key ? ($statusTextMap[$key] ?? '') : 'unknown';
            $dateColumn = $stageDateMap[$bucket_status] ?? null;

            $latestStage = collect($response['order']['orderStages'] ?? [])->last();
            $status = isset($stageActions[$latestStage['action']]) ? $stageActions[$latestStage['action']] : $bucket_status;
            $stage_reason  = $latestStage['activity'] ?? '';

            $updateData = [
                'shipment_status' => $latestStage['action'],
                'fulfilledby' => $response['order']['carrierName'] ?? $order->fulfilledby,
                'shipment_activity' => $stage_reason
            ];

            if ($dateColumn && Schema::hasColumn('customer_orders', $dateColumn)) {
                $updateData[$dateColumn] = now();
            }
            $order->update($updateData);
            if ($order && $order->retailer) {
                $statusService = new OrderStatusService();

                // IN-TRANSIT
                if ($status === 'in_transit') {

                    [$success, $msg, $finalStatus] = $statusService->handleInTransitStatus($order);

                    if ($success) {
                        Log::info("🎯 Success : In Transit processed for order #{$order->order_id}: {$msg}");
                    } else {
                        Log::error("🚫 Failed : In Transit processed for order #{$order->order_id}: {$msg}");
                    }
                }
                // Out for delivery 
                if ($status === 'ofd') {

                    [$success, $msg, $finalStatus] = $statusService->handleOutForDeliveryStatus($order);

                    if ($success) {
                        Log::info("🎯 Success : In Transit processed for order #{$order->order_id}: {$msg}");
                    } else {
                        Log::error("🚫 Failed : In Transit processed for order #{$order->order_id}: {$msg}");
                    }
                }
                // DELIVERED
                elseif ($status === 'delivered') {
                    if ($order->status === 'delivered' && $order->delivered_at) {
                        Log::info("🚫 Order #{$order->order_id} already delivered. Skipping update.");
                    }

                    [$success, $msg, $finalStatus] = $statusService->handleDeliveredOrder($order->retailer, $order);

                    if ($success) {
                        DB::commit();
                        Log::info("🎯 Success : Delivered processed for order #{$order->order_id}: {$msg}");
                    } else {
                        DB::rollBack();
                        Log::error("🚫 Failed : Delivered processed for order #{$order->order_id}: {$msg}");
                    }
                }
                // ndr  customer not accept

                elseif ($status === 'ndr') {
                    if ($order->status === 'ndr' && $order->ndr_at) {
                        Log::info("🚫 Order #{$order->order_id} already Non Delivered Report. Skipping update.");
                    }

                    [$success, $msg, $finalStatus] = $statusService->handleNdrOrder($order);

                    if ($success) {
                        DB::commit();
                        Log::info("🎯 Success : NDR processed for order #{$order->order_id}: {$msg}");
                    } else {
                        DB::rollBack();
                        Log::error("🚫 Failed : NDR processed for order #{$order->order_id}: {$msg}");
                    }
                }
                // rto
                elseif ($status === 'rto') {
                    if ($order->status === 'rto' && $order->rto_at) {
                        Log::info("🚫 Order #{$order->order_id} already RTO. Skipping update.");
                    }

                    [$success, $msg, $finalStatus] = $statusService->handleRtoOrder($order);

                    if ($success) {
                        DB::commit();
                        Log::info("🎯 Success : RTO processed for order #{$order->order_id}: {$msg}");
                    } else {
                        DB::rollBack();
                        Log::error("🚫 Failed : RTO processed for order #{$order->order_id}: {$msg}");
                    }
                }
                // rto to seller
                elseif ($status === 'rtn_to_seller') {
                    if ($order->status === 'rtn_to_seller' && $order->rtn_to_seller_at) {
                        Log::info("🚫 Order #{$order->order_id} already Return to Seller. Skipping update.");
                    }

                    [$success, $msg, $finalStatus] = $statusService->NdrtoRto($order->retailer, $order);

                    if ($success) {
                        DB::commit();
                        Log::info("🎯 Success : Return to Seller processed for order #{$order->order_id}: {$msg}");
                    } else {
                        DB::rollBack();
                        Log::error("🚫 Failed : Return to Seller processed for order #{$order->order_id}: {$msg}");
                    }
                }
                // CANCEL
                elseif ($status === 'cancel') {
                    if ($order->status === 'cancel' && $order->cancel_at) {
                        Log::info("🚫 Order #{$order->order_id} already cancelled. Skipping update.");
                    }

                    $reject_reason_select = 'Other';
                    $reject_reason_input = 'Rejected from the courier service';

                    [$success, $msg, $finalStatus] = $statusService->handleCancelledOrderWithCharges($order->retailer, $order, $reject_reason_select, $reject_reason_input);

                    if ($success) {
                        DB::commit();

                        $cancelled_reason = ($reject_reason_select == 'Other')
                            ? $reject_reason_input
                            : $reject_reason_select;

                        $customer = [
                            'name' => $order->customer->firstname,
                            'email' => $order->customer->email,
                        ];
                        Mail::to($order->customer->email)->send(new CancelOrderMailToCustomer($order, $customer, $cancelled_reason));

                        Log::info("🎯 Success : Cancel processed for order #{$order->order_id}: {$msg}");
                    } else {
                        DB::rollBack();
                        Log::error("🚫 Failed : Cancel processed for order #{$order->order_id}: {$msg}");
                    }
                }
            }
            return response('OK', 201);
        } catch (Exception $error) {
            Log::info($error->getMessage());
        }
    }
}
