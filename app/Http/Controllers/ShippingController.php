<?php

namespace App\Http\Controllers;

use App\Models\CourierPartner;
use App\Models\PickAddress;
use App\Models\RTOAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Services\CourierServiceManager;
use Illuminate\Support\Facades\Log;

class ShippingController extends Controller
{
    public function index(){
        return view('shipping.shipping-list');
    }
    public function directShipping(){
        return view('shipping.direct-shipping');
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
