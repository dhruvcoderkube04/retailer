<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RetilerController extends Controller
{
    public function retailerDashboard()
    {
        // $auth_id = Auth::user()->id;
        // $data['wholesaer_total_product'] = Product::where('status','active')->where('wholesaler_id',$auth_id)->count();
        return view('retailers.dashboard');
    }

    public function wholesalerList()
    {
        $wholesaler_list = User::where('user_type',2)->where('status',1)->get();
        return view('retailers.wholesaler-list',['wholesalers'=>$wholesaler_list]);
    }

    public function wholesalerWiseProductList(string $id)
    {
        $wholesaler_wise_product = Product::where('wholesaler_id',$id)->get();
        return view('retailers.retailer-product-list',['productlist'=>$wholesaler_wise_product]);
    }

    public function retailerProduct()
    {
        return view('retailers.retailer-own-product');
    }

    public function retailerOrder()
    {
        return view('retailers.reatiler-orders-list');
    }

    public function Profile()
    {
        $id = Auth::user()->id;
        $user = User::with('userDetail')->findOrFail($id);
        return view('retailers.profile.profile',['userprofile'=>$user]);
    }

    public function profileUpdate(Request $request)
    {
        $id = Auth::user()->id;
        $request->validate([
            'firstname'     => 'nullable|string|max:255',
            'lastname'      => 'nullable|string|max:255',
            'company'  => 'nullable|string|max:255',
            'phone'  => 'nullable|string|min:6|max:20|regex:/^[0-9\-+()\s]*$/',
            'address'       => 'nullable|string|max:500',
            'country'       => 'nullable|string|max:255',
            'state'         => 'nullable|string|max:255',
            'city'          => 'nullable|string|max:255',
            'pincode'   => 'nullable|string|max:10|regex:/^[0-9]{4,10}$/',
            'profile'       => 'mimes:jpeg,png,jpg|max:1048',
        ]);
        // dd($request->all());
        // Find the wholesaler
        $wholesaler = User::with('userDetail')->findOrFail($id);

        // Update only the fields that are filled
        $updateData = [];
        if ($request->filled('firstname')) {
            $updateData['firstname'] = $request->firstname;
        }
        if ($request->filled('lastname')) {
            $updateData['lastname'] = $request->lastname;
        }
        if ($request->filled('phone')) {
            $updateData['phone_number'] = $request->phone_number;
        }
        // if ($request->filled('status')) {
        //     $updateData['status'] = $request->status;
        // }
        // else
        // {
        //     $updateData['status'] = 0;
        // }

        if (!empty($updateData)) {
            $wholesaler->update($updateData);
        }

        // Update password if provided
        // if ($request->filled('password')) {
        //     $wholesaler->update([
        //         'password' => bcrypt($request->password),
        //     ]);
        // }

         // Handle profile image upload
         if ($request->hasFile('profile')) {
            $file = $request->file('profile');  // Get file
            $filename = time() . '_' . $file->getClientOriginalName(); // Generate unique filename
            $file->move(public_path('uploads/company_profile'), $filename); // Save to public/uploads/company_logos
        } else {
            $filename = null; // No file uploaded
        }

        // Update userDetail fields only if they are filled
        if ($wholesaler->userDetail) {
            $userDetailUpdate = [];

            if ($request->filled('company')) {
                $userDetailUpdate['company_name'] = $request->company_name;
            }
            if ($request->filled('address')) {
                $userDetailUpdate['address'] = $request->address;
            }
            if ($request->filled('country')) {
                $userDetailUpdate['country'] = $request->country;
            }
            if ($request->filled('state')) {
                $userDetailUpdate['state'] = $request->state;
            }
            if ($request->filled('city')) {
                $userDetailUpdate['city'] = $request->city;
            }
            if ($request->filled('address')) {
                $userDetailUpdate['address'] = $request->address;
            }
            if ($request->filled('pincode')) {
                $userDetailUpdate['postal_code'] = $request->pincode;
            }
            if ($request->hasFile('profile')) {
                $userDetailUpdate['company_logo'] = $filename;
            }

            if (!empty($userDetailUpdate)) {
                $wholesaler->userDetail->update($userDetailUpdate);
            }
        }

        return redirect()->back()->with('success', 'Wholesaler updated successfully.');
    }

}
