<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RetailerController extends Controller
{
    public function index()
    {
        $retailers = User::with('userDetail')
            // ->select('id', 'firstname', 'lastname', 'email')
            ->where('user_type', 3)
            ->where('status', 1)
            ->get();

        return view('admin.retailer.retailer-list', compact('retailers'));
    }

    public function addRetailer()
    {
        return view('admin.retailer.add-retailer');
    }

    public function postRetailer(Request $request)
    {
        // Validate request
        $request->validate([
            'firstname'     => 'required|string|max:255',
            'lastname'      => 'required|string|max:255',
            'company_name'  => 'required|string|max:255',
            'phone_number'  => 'required|string|min:6|max:20|regex:/^[0-9\-+()\s]*$/',
            'email'         => 'required|email|max:255|unique:users,email',
            'password'      => 'required|string|min:8|max:20', // Must be confirmed with 'password_confirmation'
            'address'       => 'required|string|max:500',
            'country'       => 'required|string|max:255',
            'state'         => 'required|string|max:255',
            'city'          => 'required|string|max:255',
            'postal_code'   => 'required|string|max:10|regex:/^[0-9]{4,10}$/', // Numeric only, 4-10 digits
            'profile'       => 'nullable|image|mimes:jpeg,png,jpg|max:1048', // 1MB max
            // 'avatar_remove' => 'nullable|boolean',
        ]);

        try {

            DB::beginTransaction(); // Start transaction

            // Step 1: Create user
            $user = User::create([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'password' => Hash::make($request->password), // Set a default or send an email for password setup
                'user_type' => 3, // Set user type for Retailer
                'phone_number'=> $request->phone_number,
                'status' => $request->status ?? 0,
                'ip_address' => $request->ip(),
                'source'=> 'web',
            ]);

            // Step 2: Create user details

            if ($request->hasFile('company_logo')) {
                $file = $request->file('company_logo');  // Get file
                $filename = time() . '_' . $file->getClientOriginalName(); // Generate unique filename
                $file->move(public_path('uploads/company_logos'), $filename); // Save to public/uploads/company_logos
            } else {
                $filename = null; // No file uploaded
            }

            UserDetail::create([
                "user_id" => $user->id,
                "address" =>$request->address,
                "company_logo" =>$filename,
                "state" =>$request->state,
                "city" =>$request->city,
                "country" =>$request->country,
                "company_name" =>$request->company_name,
                "postal_code" =>$request->postal_code,
            ]);

            DB::commit(); // Commit transaction
            return redirect()->back()->with('success', 'Retailer added successfully.');
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction in case of error
            return back()->withErrors(['error' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified wholesaler.
     */
    public function retailerDetail(string $id)
    {
        $retailer = User::with('userDetail')
        // ->select('id', 'firstname', 'lastname', 'email')
        ->where('user_type', 3)   // for retialer
        // ->where('status', 1)
        ->where('id',$id)
        ->first();
        return view('admin.retailer.retailer-detail', ['retailer'=>$retailer]);
    }

    /**
     * Update the specified wholesaler in the database
     */
    public function retailerUpdate(Request $request, string $id)
    {

        $request->validate([
            'firstname'     => 'nullable|string|max:255',
            'lastname'      => 'nullable|string|max:255',
            'company_name'  => 'nullable|string|max:255',
            'phone_number'  => 'nullable|string|min:6|max:20|regex:/^[0-9\-+()\s]*$/',
            'status'        => 'in:0,1',
            'password'      => 'nullable|string|min:8|max:20', // Optional password
            'address'       => 'nullable|string|max:500',
            'country'       => 'nullable|string|max:255',
            'state'         => 'nullable|string|max:255',
            'city'          => 'nullable|string|max:255',
            'postal_code'   => 'nullable|string|max:10|regex:/^[0-9]{4,10}$/',
            'profile'       => 'nullable|image|mimes:jpeg,png,jpg|max:1048',
        ]);

        // Find the wholesaler
        $retailer = User::with('userDetail')->findOrFail($id);
        // dd($request->all());
        // Update only the fields that are filled
        $updateData = [];
        if ($request->filled('firstname')) {
            $updateData['firstname'] = $request->firstname;
        }
        if ($request->filled('lastname')) {
            $updateData['lastname'] = $request->lastname;
        }
        if ($request->filled('phone_number')) {
            $updateData['phone_number'] = $request->phone_number;
        }
        if ($request->filled('status')) {
            $updateData['status'] = $request->status;
        }
        else
        {
            $updateData['status'] = 0;
        }

        if (!empty($updateData)) {
            $retailer->update($updateData);
        }

        // Update password if provided
        if ($request->filled('password')) {
            $retailer->update([
                'password' => bcrypt($request->password),
            ]);
        }

        // Handle profile image upload
        if ($request->hasFile('profile')) {
            $file = $request->file('profile');  // Get file
            $filename = time() . '_' . $file->getClientOriginalName(); // Generate unique filename
            $file->move(public_path('uploads/company_profile'), $filename); // Save to public/uploads/company_logos
        } else {
            $filename = null; // No file uploaded
        }

        // Update userDetail fields only if they are filled
        if ($retailer->userDetail) {
            $userDetailUpdate = [];

            if ($request->filled('company_name')) {
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
            if ($request->filled('postal_code')) {
                $userDetailUpdate['postal_code'] = $request->postal_code;
            }
            if ($request->hasFile('profile')) {
                $userDetailUpdate['company_logo'] = $filename;
            }

            if (!empty($userDetailUpdate)) {
                $retailer->userDetail->update($userDetailUpdate);
            }
        }

        return redirect()->back()->with('success', 'Wholesaler updated successfully.');
    }

    /**
     * Pending supplier list.
     */
    public function pendingRetailerList()
    {
        $retailers = User::with('userDetail')
        // ->select('id', 'firstname', 'lastname', 'email')
        ->where('user_type', 3)
        ->where('status', 0)
        ->get();

        return view('admin.retailer.pending-retailer-list', compact('retailers'));
    }
}
