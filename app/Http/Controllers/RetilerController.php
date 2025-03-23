<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Imports\ProductImport;
use App\Models\Category;
use App\Models\CustomerDetails;
use App\Models\CustomerOrders;
use App\Models\Product;
use App\Models\RetailerCloneProduct;
use App\Models\RetailerProducts;
use App\Models\User;
use App\Models\UserDetail;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

class RetilerController extends Controller
{
    public function retailerDashboard()
    {
        // $auth_id = Auth::user()->id;
        // $data['wholesaer_total_product'] = Product::where('status','active')->where('wholesaler_id',$auth_id)->count();
        return view('dashboard');
    }

    // wholesaler list
    public function wholesalerList()
    {
        $isAllWholesalerVisibleCheck = Auth::user()->is_all_wholesaler_visible;
        $wholesaler_list = User::with('userDetail')->where('user_type', 2)->where('status', 1)->get();
        return view('wholesaler-list', ['is_all_wholesaler_visible' => $isAllWholesalerVisibleCheck, 'wholesalers' => $wholesaler_list]);
    }

    // <--------------------- START : Add category margin ---------------------->
    // add category margin view page
    public function viewCategoryMargin(string $wholesaler_id)
    {
        $retailer = Auth::user();

        $wholesaler = UserDetail::select('user_id', 'company_name')->where('user_id', $wholesaler_id)->first();

        $addedCategories = RetailerProducts::where('wholesaler_id', $wholesaler_id)
            ->where('retailer_id', $retailer->id)
            ->distinct('category_id')
            ->pluck('category_id');

        $categories = Product::select(
            'categories.id',
            'categories.category_name'
        )
            ->join('categories', 'categories.id', 'products.category_id')
            ->where('wholesaler_id', $wholesaler_id)
            ->whereNotIn('categories.id', $addedCategories)
            ->distinct('category_id')
            ->get();

        $addedMarginDetails = RetailerProducts::with(['category'])
            ->where('wholesaler_id', $wholesaler_id)
            ->where('retailer_id', $retailer->id)
            ->get();

        return view('product.retailer-product-list', [
            'wholesaler' => $wholesaler,
            'categories' => $categories,
            'addedMarginDetails' => $addedMarginDetails
        ]);
    }

    // add category margin store
    public function storeCategoryMargin(Request $request, $wholesaler_id)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'margin' => 'required|integer|min:1',
            'payment_method' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $retailer = Auth::user();

            RetailerProducts::updateOrCreate([
                'retailer_id' => $retailer->id,
                'wholesaler_id' => $wholesaler_id,
                'category_id' => $request->category_id,
            ], [
                'payment_method' => implode(',', $request->payment_method),
                'margin' => $request->margin
            ]);

            DB::commit();

            return redirect()->route('retailer.view-category-margin', $wholesaler_id)
                ->with('success', 'Category margin added successfully');
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Something went wrong');
            return redirect()->route('retailer.view-category-margin', $wholesaler_id);
        }
    }

    // edit category margin store
    public function editCategoryMargin($wholesaler_id, $margin_id)
    {
        dd('wholesaler_id = ' . $wholesaler_id, 'margin_id = ' . $margin_id);
    }

    // remove category margin store
    public function removeCategoryMargin($wholesaler_id, $margin_id)
    {
        DB::beginTransaction();
        try {
            $marginDetail = RetailerProducts::findOrFail($margin_id);
            $marginDetail->delete();

            DB::commit();

            return redirect()->route('retailer.view-category-margin', $wholesaler_id)
                ->with('success', 'Category margin deleted successfully');
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Something went wrong');
            return redirect()->route('retailer.view-category-margin', $wholesaler_id);
        }
    }

    // get category wise product
    // public function getCategoryWiseProducts(Request $request)
    // {
    //     try {
    //         $products = Product::where('wholesaler_id', $request->wholesale_id)
    //             ->where('category_id', $request->category_id)
    //             ->get(['id', 'name']);

    //         return response()->json(['status' => true, 'msg' => 'Success', 'data' => $products]);
    //     } catch (Exception $e) {
    //         return response()->json(['status' => false, 'msg' => $e->getMessage()]);
    //     }
    // }
    // <--------------------- END : Add category margin ---------------------->


    // <--------------------- START : Retailer product (Added, Clone, Own) ---------------------->
    public function retailerProduct()
    {
        try {
            $retailer = Auth::user()->id;

            $retailerProducts = RetailerProducts::with(['wholesaler.products', 'wholesaler.userDetail'])
                ->where('retailer_id', $retailer)
                ->get();

            $filteredRetailerProducts = $retailerProducts->map(function ($retailerProduct) {
                $products = Product::where('wholesaler_id', $retailerProduct->wholesaler_id)
                    ->where('category_id', $retailerProduct->category_id)
                    ->distinct('id')
                    ->get();

                $retailerProduct->setRelation('products', $products);
                return $retailerProduct;
            });

            $retailerCloneProducts = RetailerCloneProduct::with('category')
                ->where('retailer_id', $retailer)
                ->get();

            $clonedProducts = RetailerCloneProduct::where('retailer_id', $retailer)
                ->pluck('product_id')
                ->toArray();

            $category_list = Category::select('category_name','id')->where('status',1)->get();


            // Pass the filtered data to the view.
            return view('product.retailer-own-product', [
                'retailerProducts' => $filteredRetailerProducts,
                'retailerCloneProducts' => $retailerCloneProducts,
                'clonedProducts' => $clonedProducts,
                'category_list' => $category_list
            ]);
        } catch (\Exception $e) {
            // Log the error (optional)
            Log::error('Error in retailerProduct: ' . $e->getMessage());
            session()->flash('error', 'Something went wrong');
            // return redirect()->route('retailer.dashboard');

            // Return an error view or redirect with an error message
            // return view('errors.retailer_product_error', ['error' => $e->getMessage()]); //create error.retailer_product_error.blade.php
            //or
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }

    // clone product view
    public function cloneProductView(Request $request, $product_id)
    {
        try {
            $product = Product::where('id', $product_id)->first();

            return view('product.clone-product-view', compact('product'));
        } catch (Exception $e) {
            Log::error('Error in cloneProductView: ' . $e->getMessage());
            session()->flash('error', $e->getMessage());
            return redirect()->route('retailer.product');
        }
    }

    // Add Product
    public function retailerAddProduct(Request $request)
    {
        $category_list = Category::select('category_name','id')->where('status',1)->get();
        return view('product.add-product-view',['category_list' => $category_list]);
    }

    public function retailerPostProduct(Request $request)
    {

        $request->validate([
            'product_name' =>'required|min:3|max:100',
            'product_description' =>'required|min:5|max:100',
            'product_tags' =>'required|min:3|max:255',
            'categories'=> 'required|numeric',
            'quantity' =>'required|integer|min:1',
            'new_price' =>'required|numeric|min:1',
            'sku' =>'required|string',
            // 'discount_price' => 'nullable|numeric|min:0.01|max:100'
            'image_1' => 'required|mimes:jpeg,png,jpg|max:4096',
            'image_2' => 'nullable|mimes:jpeg,png,jpg|max:4096',
            'image_3' => 'nullable|mimes:jpeg,png,jpg|max:4096',
            'video' => 'nullable|mimes:mp4|max:10240',
        ]);

        // all image set in array comman seprated store
        // dd($request->all());
        $imagePaths = []; // Initialize the array

        foreach (['image_1', 'image_2', 'image_3'] as $imageField) {
            if ($request->hasFile($imageField)) {
                $file = $request->file($imageField);

                // Generate a unique name using a combination of time, random string, and original name
                $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();

                // Store the file in 'storage/app/public/products'
                $file->move(public_path('uploads/products'), $fileName); // Save to public/uploads/product

                // Store the path to save in the database
                $imagePaths[] = "products/" . $fileName;
            }
        }

        try {

            DB::beginTransaction();
            $reatielr_id = Auth::user()->id;
            // $category = Category::where('id', $request->category)->first();
            $product = new RetailerCloneProduct();
            $product->name = $request->product_name;

            $product->description = $request->product_description;
            $product->slug = '';
            // $product->brand_name = $request->brand_name;
            $product->tags = $request->product_tags;
            $product->quantity = $request->quantity;
            $product->new_price = $request->new_price;
            $product->old_price = 0;
            $product->images = $request->images ? implode(',', $request->images) : null;
            $product->videos = $request->videos ? $request->videos : null;
            $product->sku = $request->sku;
            $product->retailer_id = $reatielr_id;
            $product->category_id = $request->categories;
            $product->save();
            DB::commit();
            session()->flash('success', 'Product added successfully');
            return redirect()->route('retailer.product');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error in retailerPostProduct: '. $e->getMessage());
            session()->flash('error', 'Something went wrong');
            return redirect()->route('retailer.product');
        }

    }

    // clone product store
    public function cloneProductStore(Request $request, $product_id)
    {
        $request->validate([
            'description' => 'required|min:10|max:500',
            'old_price' => 'required|numeric|min:0.01',
            'new_price' => 'required|numeric|min:0.01'
        ]);

        DB::beginTransaction();
        try {
            $retailer = Auth::user();

            $product = Product::where('id', $product_id)->first();

            $cloneProduct = new RetailerCloneProduct();
            $cloneProduct->product_id = $product->id;
            $cloneProduct->sku = $product->sku;
            $cloneProduct->retailer_id = $retailer->id;
            $cloneProduct->name = $product->name;
            $cloneProduct->slug = $product->slug;
            $cloneProduct->description = $request->description ?? $product->description;
            $cloneProduct->brand_name = $product->brand_name;
            $cloneProduct->tags = $product->tags;
            $cloneProduct->quantity = $product->quantity;
            $cloneProduct->old_price = $request->old_price ?? $product->old_price;
            $cloneProduct->new_price = $request->new_price ?? $product->new_price;
            $cloneProduct->discount_price = $product->discount_price;

            if ($request->images) {
                $cloneProduct->images = $product->images;
            }
            if ($request->videos) {
                $cloneProduct->videos = $product->videos;
            }

            $cloneProduct->url = $product->url;
            $cloneProduct->status = $product->status;
            $cloneProduct->color = $product->color;
            $cloneProduct->size = $product->size;
            $cloneProduct->specifications = $product->specifications;
            $cloneProduct->category_id = $product->category_id;
            $cloneProduct->meta_title = $product->meta_title;
            $cloneProduct->meta_description = $product->meta_description;
            $cloneProduct->meta_keywords = $product->meta_keywords;
            $cloneProduct->save();

            DB::commit();
            return redirect()->route('retailer.product')->with('success', 'Product cloned successfully');
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Something went wrong');
            return redirect()->route('retailer.product');
        }
    }

    //
    public function cloneProductRemove(Request $request, $clone_product_id)
    {
        DB::beginTransaction();
        try {
            $cloneProduct = RetailerCloneProduct::where('id', $clone_product_id)->first();

            if ($cloneProduct) {
                $cloneProduct->delete();
            }

            DB::commit();
            return redirect()->route('retailer.product')->with('success', 'Product removed from clone successfully');
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Something went wrong');
            return redirect()->route('retailer.product');
        }
    }
    // <--------------------- START : Retailer product (Added, Clone, Own) ---------------------->



    // place-order page view
    public function placeOrderView(Request $request)
    {
        // $retailer = Auth::user();
        // $retailerProducts = RetailerProducts::with([
        //     'product',
        //     'wholesaler.userDetail'
        // ])
        //     ->where('retailer_id', $retailer->id)
        //     ->get();

        $retailer = Auth::user()->id;

        $retailerProducts = RetailerProducts::with(['wholesaler.products', 'wholesaler.userDetail'])
            ->where('retailer_id', $retailer)
            ->get();

        $filteredRetailerProducts = $retailerProducts->map(function ($retailerProduct) {
            $products = Product::where('wholesaler_id', $retailerProduct->wholesaler_id)
                ->where('category_id', $retailerProduct->category_id)
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
            'payment_method' => 'required'
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

            $customerOrder = new CustomerOrders();
            $customerOrder->customer_id = $customerDetail->id;
            $customerOrder->product_id = $request->product_id;
            $customerOrder->retailer_id = $request->retailer_id;
            $customerOrder->wholesaler_id = $request->wholesaler_id;
            $customerOrder->quantity = $request->quantity;
            $customerOrder->payment_method = $request->payment_method;
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
    public function orderList($type = 'new')
    {
        $retailer = Auth::user();
        $sql = CustomerOrders::with([
            'customer',
            'product',
            'wholesaler.userDetail'
        ])
            ->where('retailer_id', $retailer->id);

        if ($type == 'new') {
            $sql->where('status', 'pending');
        } else if ($type == 'transfered-retailer-to-wholesaler') {
            $sql->where('status', 'transfered_retailer_to_wholesaler');
        } else if ($type == 'confirmed-by-retailer') {
            $sql->where('status', 'confirmed_by_retailer');
        } else if ($type == 'ready-to-ship') {
            $sql->where('status', 'shipped_by_retailer');
        } else if ($type == 'delivered-by-retailer') {
            $sql->where('status', 'delivered_by_retailer');
        } else if ($type == 'cancelled-by-retailer') {
            $sql->where('status', 'cancelled_by_retailer');
        } else if ($type == 'cancelled-by-customer') {
            $sql->where('status', 'cancelled_by_customer');
        } else {
            return redirect()->route('retailer.order.list');
        }

        $retailerOrders = $sql->orderBy('id', 'DESC')
            ->get();

        return view('orders.orders-list', compact('retailerOrders'));
    }

    // order action
    public function orderAction(Request $request)
    {
        $request->validate([
            'status' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $retailer = Auth::user();
            $customerOrder = CustomerOrders::find($request->order_id);

            if (!$customerOrder) {
                session()->flash('error', 'Order not found');
                return redirect()->route('retailer.order.list');
            }

            $updateData = [];
            $message = '';
            $type = '';
            if ($request->status == 'confirmed_by_retailer') {
                $updateData = [
                    'status' => $request->status,
                    'confirmed_by_retailer_at' => Carbon::now()
                ];
                $message = 'Order has been confirmed successfully';
                $type = 'confirmed-by-retailer';
            } else if ($request->status == 'shipped_by_retailer') {
                $updateData = [
                    'status' => $request->status,
                    'shipped_by_retailer_at' => Carbon::now()
                ];
                $message = 'Order has been ready to ship (by supplier)';
                $type = 'ready-to-ship';
            } else if ($request->status == 'delivered_by_retailer') {
                $updateData = [
                    'status' => $request->status,
                    'delivered_by_retailer_at' => Carbon::now(),
                    'delivered_by' => $retailer->id
                ];
                $message = 'Order has been delivered by supplier';
                $type = 'delivered-by-retailer';
            } else if ($request->status == 'transfered_retailer_to_wholesaler') {
                $updateData = [
                    'status' => $request->status,
                    'transfered_retailer_to_wholesaler_at' => Carbon::now()
                ];
                $message = 'Wholesaler will ship this product';
                $type = 'transfered-retailer-to-wholesaler';
            } else if ($request->status == 'cancelled_by_retailer') {
                $updateData = [
                    'status' => $request->status,
                    'cancelled_by_retailer_at' => Carbon::now(),
                    'cancelled_by' => $retailer->id
                ];
                $message = 'Order has been cancelled by retailer';
                $type = 'cancelled-by-retailer';
            }

            if (!empty($updateData)) {
                $customerOrder->update($updateData);
                DB::commit();
                session()->flash('success', $message);
            } else {
                session()->flash('error', 'Invalid order status');
            }

            return redirect()->route('retailer.order.list', ['type' => $type]);
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Something went wrong!');
            return redirect()->route('retailer.order.list');
        }
    }

    public function Profile()
    {
        $id = Auth::user()->id;
        $user = User::with('userDetail')->findOrFail($id);
        return view('profile.profile', ['userprofile' => $user]);
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


    //<---------------------- START : NOTE IN USE ------------------------>
    // // Product details view (while retailer add the product)
    // public function addProductView(Request $request, $product_id)
    // {
    //     try {
    //         $retailer = Auth::user();

    //         $product = Product::where('id', $product_id)->first();
    //         $retailer_product = RetailerProducts::where('retailer_id', $retailer->id)
    //             ->where('product_id', $product_id)
    //             ->first();

    //         return view('retailers.product.add-product-view', compact('product', 'retailer_product'));
    //     } catch (Exception $e) {
    //         return redirect()->route('retailer.dashboard');
    //     }
    // }
    // // add product (by retailer in his wishlist)
    // public function addProduct(Request $request, $product_id)
    // {
    //     $request->validate([
    //         'margin' => 'required|integer|max:100'
    //     ]);

    //     DB::beginTransaction();
    //     try {
    //         $retailer = Auth::user();

    //         RetailerProducts::updateOrCreate([
    //             'retailer_id' => $retailer->id,
    //             'wholesaler_id' => $request->wholesaler_id,
    //             'product_id' => $request->product_id,
    //         ], [
    //             'margin' => $request->margin
    //         ]);
    //         DB::commit();

    //         return redirect()->route('retailer.view-category-margin', $request->wholesaler_id)
    //             ->with('success', 'Product added/updated successfully');
    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         session()->flash('error', 'Something went wrong');
    //         return redirect()->route('retailer.add-product', $product_id);
    //     }
    // }
    // // remove product (by retailer from his wishlist)
    // public function removeProduct(Request $request, $retailer_product_id)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $retailer_product = RetailerProducts::where('id', $retailer_product_id)->first();

    //         if (!$retailer_product) {
    //             session()->flash('error', 'Product not exist or already deleted');
    //             return redirect()->back();
    //         }

    //         $retailer_product->delete();

    //         DB::commit();

    //         return redirect()->route('retailer.view-category-margin', $retailer_product->wholesaler_id)
    //             ->with('success', 'Product removed successfully');
    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         session()->flash('error', 'Something went wrong');
    //         return redirect()->back();
    //     }
    // }
    //<---------------------- END : NOTE IN USE ------------------------>


    public function downloadStockSample()
    {
        $filePath = public_path('samplestock/sample_products.xlsx');

        if (!file_exists($filePath)) {
            return back()->with('error', 'File not found.');
        }
        return Response::download($filePath, 'stock_sample.xlsx');
    }

    public function uploadBulkProduct(Request $request)
    {
        $request->validate([
            'product_file' => 'required|mimes:xlsx',
            'categories' => 'required|integer',
        ]);

        $file = $request->file('product_file');

        try {
            $import = new ProductImport();
            $results = Excel::import($import, $file);
            $headings = collect(Excel::toArray(new ProductImport, $file)[0][0]);
            dd($headings);
            dd($import->checkColumns($headings));
            if(!$import->checkColumns($headings)){
                return response()->json(['error' => 'Invalid file structure. Required columns are missing.'], 400);
            }

            $data = [
                'valid' => $import->collection(collect(Excel::toArray(new ProductImport, $file)[0]))['valid'],
                'invalid' => $import->collection(collect(Excel::toArray(new ProductImport, $file)[0]))['invalid'],
            ];

            return response()->json($data);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];

            foreach ($failures as $failure) {
                $errors[] = [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                    'values' => $failure->values(),
                ];
            }

            return response()->json(['errors' => $errors], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred during file processing: ' . $e->getMessage()], 500);
        }
    }
}
