<?php

namespace App\Http\Controllers;

use App\Models\PickAddress;
use App\Models\RTOAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $addresses = PickAddress::where('retailer_id',$user->id)->get();

        return view('shipping.pick-up-address-list',['addresses' => $addresses]);
    }
    public function rtoAddress(){

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

    public function pickAddressStore(Request $request)
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
        PickAddress::create([
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

    public function pickAddressedit($id)
    {
        $address = PickAddress::findOrFail($id);

        // dd($address);
        return response()->json($address);
    }


    public function pickAddressupdate(Request $request, $id)
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

        $pickAddress = PickAddress::findOrFail($id);
        $pickAddress->update($request->all());

        return redirect()->back()->with('success', 'Address updated successfully!');
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

}
