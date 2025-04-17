<?php

namespace App\Http\Controllers;

use App\Models\PickAddress;
use App\Models\RTOAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

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

    // normal only store in mydatabse
    // public function pickAddressStore(Request $request)
    // {
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

    //     // Get authenticated retailer
    //     $retailer = Auth::user();


    //     // dd($retailer);
    //     // Store data with retailer_id
    //     PickAddress::create([
    //         'first_name' => $request->first_name,
    //         'last_name' => $request->last_name,
    //         'mobile_number' => $request->mobile,
    //         'pincode' => $request->pincode,
    //         'address' => $request->address,
    //         'state' => $request->state,
    //         'city' => $request->city,
    //         'retailer_id' => $retailer->id, // Store retailer_id
    //     ]);

    //     return back()->with('success', 'Address added successfully!');
    // }

    // store in both my databse and fship
    public function pickAddressStore(Request $request)
    {
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
        $existingWarehouse = PickAddress::where('user_id', $user->id)
            ->where('warehouse_name', $request->warehouse_name)
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
            if ($existingWarehouse && $existingWarehouse->warehouse_id) {
                // If name matches and warehouse ID exists, update FShip warehouse
                $warehouseData['warehouseId'] = $existingWarehouse->warehouse_id;

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'signature' => '085c36066064af83c66b9dbf44d190d40feec79f437bc1c1cb',
                ])->post('https://capi-qc.fship.in/api/updatewarehouse', $warehouseData);

                $isUpdate = true;
            } else {
                // Create new warehouse on FShip
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'signature' => '085c36066064af83c66b9dbf44d190d40feec79f437bc1c1cb',
                ])->post('https://capi-qc.fship.in/api/addwarehouse', $warehouseData);
            }

            $res = $response->json();

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
                $address->save();

                $message = $isUpdate ? 'Address updated successfully!' : 'Address added successfully!';
                return back()->with('success', $message);
            }

            return back()->with('error', $res['response'] ?? 'FShip API failed.');
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function pickAddressedit($id)
    {
        $address = PickAddress::findOrFail($id);

        return response()->json($address);
    }

    // public function pickAddressupdate(Request $request, $id)
    // {
    //     $request->validate([
    //         'first_name' => 'required|string|max:255',
    //         'last_name'  => 'required|string|max:255',
    //         'mobile_number'     => 'required|digits:10',
    //         'pincode'    => 'required|digits:6',
    //         'address'    => 'required|string',
    //         'state'      => 'required|string',
    //         'city'       => 'required|string',
    //     ]);

    //     $pickAddress = PickAddress::findOrFail($id);
    //     $pickAddress->update($request->all());

    //     return redirect()->back()->with('success', 'Address updated successfully!');
    // }

    public function pickAddressupdate(Request $request, $id)
    {
        $request->validate([
            // 'warehouse_name' => 'required|string|max:255',
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'mobile_number'  => 'required|digits:10',
            'pincode'        => 'required|digits:6',
            'address'        => 'required|string',
            'state'          => 'required|string',
            'city'           => 'required|string',
        ]);

        $user = Auth::user();
        $pickAddress = PickAddress::where('warehouse_id', $id)->first();

        if (!$pickAddress) {
            return back()->with('error', 'Warehouse not found.');
        }

        $warehouseData = [
            "warehouseId"    => $pickAddress->warehouse_id,
            "warehouseName" => $pickAddress->warehouse_name,
            "contactName"    => $request->first_name . $request->last_name,
            "addressLine1"   => $request->address,
            "addressLine2"   => "",
            "pincode"        => $request->pincode,
            "city"           => $request->city,
            "phoneNumber"    => $request->mobile_number,
            "email"          => $user->email,
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'signature' => '085c36066064af83c66b9dbf44d190d40feec79f437bc1c1cb',
            ])->post('https://capi-qc.fship.in/api/updatewarehouse', $warehouseData);

            $res = $response->json();

            if (isset($res['status']) && $res['status'] === true) {
                $pickAddress->first_name = $request->first_name;
                $pickAddress->last_name = $request->last_name;
                $pickAddress->mobile_number = $request->mobile_number;
                $pickAddress->pincode = $request->pincode;
                $pickAddress->address = $request->address;
                $pickAddress->state = $request->state;
                $pickAddress->city = $request->city;
                $pickAddress->user_id = $user->id;
                $pickAddress->save();

                return back()->with('success', 'Warehouse updated successfully!');
            }

            return back()->with('error', $res['response'] ?? 'FShip API update failed.');
        } catch (\Exception $e) {
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
        return view('shipping.pincode-serviceable');
    }

    public function pincodeCheckAvailability(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                // 'signature' => env('FSHIP_SIGNATURE_TEST_KEY'),
                'signature' => '085c36066064af83c66b9dbf44d190d40feec79f437bc1c1cb',
            ])->post('https://capi-qc.fship.in/api/pincodeserviceability', [
                'source_Pincode' => $request->source_pincode,
                'destination_Pincode' => $request->destination_pincode,
            ]);
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'API responded with an error.',
                    'status_code' => $response->status(),
                    'error' => $response->json()
                ], $response->status());
            }
        } catch (\Exception $e) {
            // Optionally log the error for debugging
            Log::error('FShip API Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function trackOrder()
    {
        return view('shipping.track-order-status');
    }

    public function trackOrderStatus(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'signature' => '085c36066064af83c66b9dbf44d190d40feec79f437bc1c1cb',
            ])->post('https://capi-qc.fship.in/api/trackinghistory', [
                'waybill' => $request->track_no,
            ]);
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'API responded with an error.',
                    'status_code' => $response->status(),
                    'error' => $response->json()
                ], $response->status());
            }
        } catch (\Exception $e) {
            // Optionally log the error for debugging
            Log::error('FShip API Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
