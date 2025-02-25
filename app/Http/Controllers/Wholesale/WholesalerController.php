<?php

namespace App\Http\Controllers\Wholesale;

use App\Http\Controllers\Controller;
use App\Models\CustomerOrders;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WholesalerController extends Controller
{
    public function wholesalerDashboard()
    {
        $auth_id = Auth::user()->id;
        $data['wholesaer_total_product'] = Product::where('status','active')->where('wholesaler_id',$auth_id)->count();
        return view('wholesale.dashboard',['data'=>$data]);
    }

    public function productList()
    {
        $product = Product::all();
        return view('wholesale.product-list',['products'=>$product]);
    }

    public function addProductview()
    {
        return view('wholesale.add-product');
    }

    public function postNewproduct(Request $request)
    {

        $request->validate([
            'sku' => 'required|string|unique:products,sku',
            'product_tags' => 'required|string|max:255',
            'slug' => 'required|string|unique:products,slug',
            'status' => 'required|string|in:active,inactive',
            'product_name' => 'required|string|max:255',
            'categories' => 'required|string|max:255',
            'product_description' => 'required|string|min:10',
            'new_price' => 'required|numeric|min:1',
            'old_price' => 'required|numeric|min:1',
            'quantity' => 'required|integer|min:1',
            'meta_title' => 'required|string|max:255',
            'meta_description' => 'required|string|max:255',
            'product_meta_keywords' => 'nullable|string',

            // Image validations
            'image_1' => 'required|image|mimes:jpeg,png,jpg|max:4096',
            'image_2' => 'required|image|mimes:jpeg,png,jpg|max:4096',
            'image_3' => 'required|image|mimes:jpeg,png,jpg|max:4096',
            'video' => 'required|mimes:mp4|max:10240',
        ]);

        // dd($request->all());

        $imagePaths = []; // Initialize the array

        foreach (['image_1', 'image_2', 'image_3'] as $imageField) {
            if ($request->hasFile($imageField)) {
                $file = $request->file($imageField);

                // Generate a unique name using a combination of time, random string, and original name
                $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();

                // Store the file in 'storage/app/public/products'
                $filePath = $file->storeAs('products', $fileName, 'public');

                // Store the path to save in the database
                $imagePaths[] = "products/" . $fileName;
            }
        }


        $imagePathsString = implode(',', $imagePaths);

        $videoPath = null;
        if ($request->hasFile('video')) {
            $file = $request->file('video');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('products/videos', $fileName, 'public');
            $videoPath = "products/videos/" . $fileName;
        }

        Product::create([
            'wholesaler_id' => Auth::id(),
            'name' => $request->product_name,
            'description' => $request->product_description,
            'old_price' => $request->old_price,
            'new_price' => $request->new_price,
            'slug' => $request->slug,
            'discount_option' => null,
            'discount_price' => null,
            'sku' => $request->sku,
            'videos' => $videoPath, // Store the video path
            'url' => '',
            'status' => $request->status,
            'color' => '',
            'size' => '',
            'specifications' => '',
            'quantity' => $request->quantity,
            'tags' => $request->product_tags, // Ensure this is valid JSON
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->product_meta_keywords,
            'images' => $imagePathsString, // Store image paths as a comma-separated string
        ]);

        return redirect()->back()->with('success', 'Product added successfully!');
    }

    public function editProduct(string $id)
    {
        $product_detail = Product::where('wholesaler_id',Auth::id())->where('id',$id)->first();
        return view('wholesale.product-edit',['product_detail' => $product_detail]);
    }

    public function updateProduct(Request $request, $id)
    {
        // Find the product
        $product = Product::where('wholesaler_id', Auth::id())->where('id', $id)->firstOrFail();
        // dd($product);
        $request->validate([
            'product_name' => 'nullable|string|max:255',
            'product_tags' => 'required|string|max:255',
            'categories'=>'required|string|max:255',
            'status' => 'required|string|in:active,inactive',
            'product_description' => 'nullable|string|min:10|max:255',
            'new_price' => 'nullable|numeric|min:1',
            'old_price' => 'nullable|numeric|min:1',
            'quantity' => 'nullable|integer|min:1',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'product_meta_keywords' => 'nullable|string|max:255',

            'image_1' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            'image_2' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            'image_3' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            'video' => 'nullable|mimes:mp4|max:10240',
        ]);

        // sku
        // Retrieve existing images from the database
        $existingImages = explode(',', $product->images);

        $imagePaths = $existingImages; // Start with existing images

        foreach (['image_1', 'image_2', 'image_3'] as $index => $imageField) {
            if ($request->hasFile($imageField)) {
                $file = $request->file($imageField);
                $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('products', $fileName, 'public');

                // Replace the image in the same position
                $imagePaths[$index] = "products/" . $fileName;
            }
        }

        // Remove empty values & convert array to a comma-separated string
        $imagePathsString = implode(',', array_filter($imagePaths));

        // Handle video upload only if a new file is provided
        $videoPath = $product->videos; // Keep existing video if no new one is uploaded
        if ($request->hasFile('video')) {
            $file = $request->file('video');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('products/videos', $fileName, 'public');
            $videoPath = "products/videos/" . $fileName;
        }

        // Prepare data for update
        $updateData = [];

        if ($request->filled('product_name')) {
            $updateData['name'] = $request->product_name;
        }
        if ($request->filled('product_description')) {
            $updateData['description'] = $request->product_description;
        }
        if ($request->filled('price')) {
            $updateData['price'] = $request->price;
        }
        if ($request->filled('quantity')) {
            $updateData['quantity'] = $request->quantity;
        }
        if ($request->filled('status')) {
            $updateData['status'] = $request->status;
        }
        if ($request->filled('meta_title')) {
            $updateData['meta_title'] = $request->meta_title;
        }
        if ($request->filled('meta_description')) {
            $updateData['meta_description'] = $request->meta_description;
        }
        if ($request->filled('product_meta_keywords')) {
            $updateData['meta_keywords'] = $request->product_meta_keywords;
        }

        // Convert tags JSON to array if provided
        if ($request->has('tags')) {
            $updateData['tags'] = $request->tags;
        }

        // Update images only if they exist
        if (!empty($imagePathsString)) {
            $updateData['images'] = $imagePathsString;
        }

        // Update video only if a new one is uploaded
        if ($videoPath !== $product->videos) {
            $updateData['videos'] = $videoPath;
        }

        // Perform the update
        $product->update($updateData);

        return redirect()->back()->with('success', 'Product updated successfully!');
    }


    public function deleteProduct(Request $request, $id)
    {
        $id = Auth::user()->id;
        // Find the product
        $product = Product::where('wholesaler_id', Auth::id())->where('id', $id)->firstOrFail();
        // Delete images from storage
        if (!empty($product->images)) {
            $imagePaths = explode(',', $product->images);
            foreach ($imagePaths as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        // Delete video from storage
        if (!empty($product->videos)) {
            Storage::disk('public')->delete($product->videos);
        }

        // Delete product from database
        $product->delete();

        return redirect()->back()->with('success', 'Product deleted successfully!');
    }

    public function orderList()
    {
        $wholesaler = Auth::user();
        $wholesalerOrders = CustomerOrders::with([
            'customer',
            'product',
            'retailer.userDetail'
        ])
        ->where('wholesaler_id', $wholesaler->id)
        ->whereIn('status', [
            'transfered_retailer_to_wholesaler', 
            'confirmed_by_wholesaler', 
            'shipped_by_wholesaler', 
            'delivered_by_wholesaler', 
            'cancelled_by_wholesaler'
        ])
        ->orderBy('id', 'DESC')
        ->get();

        return view('wholesale.orders.orders-list', compact('wholesalerOrders'));
    }


    public function orderItem()
    {
        return view('wholesale.order-item');
    }


    public function paymentHistory()
    {
        return view('wholesale.payment-history');
    }

    public function Profile()
    {
        $id = Auth::user()->id;
        $user = User::with('userDetail')->findOrFail($id);
        return view('wholesale.profile.profile',['userprofile'=>$user]);
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
