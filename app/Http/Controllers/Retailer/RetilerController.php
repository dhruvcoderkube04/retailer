<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\RetailerProducts;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $wholesaler_list = User::where('user_type', 2)->where('status', 1)->get();
        return view('retailers.wholesaler-list', ['wholesalers' => $wholesaler_list]);
    }

    public function wholesalerWiseProductList(string $id)
    {
        $retailer = Auth::user();

        $wholesaler_wise_product = Product::where('wholesaler_id', $id)->get();
        $added_product_list = RetailerProducts::where('retailer_id', $retailer->id)
            ->pluck('product_id')
            ->toArray();

        return view('retailers.retailer-product-list', ['productlist' => $wholesaler_wise_product, 'added_product_list' => $added_product_list]);
    }

    public function retailerProduct()
    {
        return view('retailers.retailer-own-product');
    }

    // Product details view (while retailer add the product)
    public function addProductView(Request $request, $product_id)
    {
        try {
            $retailer = Auth::user();

            $product = Product::where('id', $product_id)->first();
            $retailer_product = RetailerProducts::where('retailer_id', $retailer->id)
                ->where('product_id', $product_id)
                ->first();

            return view('retailers.add-product-view', compact('product', 'retailer_product'));
        } catch (Exception $e) {
            return redirect()->route('retailer.dashboard');
        }
    }

    // add product (by retailer in his wishlist)
    public function addProduct(Request $request, $product_id)
    {
        $request->validate([
            'margin' => 'required|integer|max:100'
        ]);

        DB::beginTransaction();
        try {
            $retailer = Auth::user();

            RetailerProducts::updateOrCreate([
                'retailer_id' => $retailer->id,
                'wholesaler_id' => $request->wholesaler_id,
                'product_id' => $request->product_id,
            ], [
                'margin' => $request->margin
            ]);
            DB::commit();

            return redirect()->route('retailer.wholesalerwise.productlist', $request->wholesaler_id)
                ->with('success', 'Product added/updated successfully');
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Something went wrong');
            return redirect()->route('retailer.add-product', $product_id);
        }
    }

    // remove product (by retailer from his wishlist)
    public function removeProduct(Request $request, $retailer_product_id)
    {
        DB::beginTransaction();
        try {
            $retailer_product = RetailerProducts::where('id', $retailer_product_id)->first();
            
            if (!$retailer_product) {
                session()->flash('error', 'Product not exist or already deleted');
                return redirect()->back();
            }
            
            $retailer_product->delete();

            DB::commit();

            return redirect()->route('retailer.wholesalerwise.productlist', $retailer_product->wholesaler_id)
                ->with('success', 'Product removed successfully');
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Something went wrong');
            return redirect()->back();
        }
    }

    public function retailerOrder()
    {
        return view('retailers.reatiler-orders-list');
    }

    public function Profile()
    {
        $id = Auth::user()->id;
        $user = User::with('userDetail')->findOrFail($id);
        return view('retailers.profile.profile', ['userprofile' => $user]);
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
