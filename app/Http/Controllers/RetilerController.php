<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Imports\ProductImport;
use App\Models\Category;
use App\Models\CustomerDetails;
use App\Models\CustomerOrders;
use App\Models\PickAddress;
use App\Models\Product;
use App\Models\RetailerCloneProduct;
use App\Models\RetailerProducts;
use App\Models\RTOAddress;
use App\Models\Ticket;
use App\Models\COrders;
use App\Models\User;
use App\Models\UserDetail;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class RetilerController extends Controller
{
    public function retailerDashboard()
    {
        $from = Carbon::now()->startOfMonth();
        $to = Carbon::now()->endOfMonth();
        $user = Auth::user();

        $data = [
            'new_orders_count' => CustomerOrders::where('retailer_id', $user->id)->where('status', 'pending')
                ->whereBetween('created_at', [$from, $to])
                ->count(),

            'confirmed_orders_count' => CustomerOrders::where('retailer_id', $user->id)->where('status', 'confirmed_by_retailer')
                ->whereBetween('created_at', [$from, $to])
                ->count(),

            'ready_for_ship_orders_count' => CustomerOrders::where('retailer_id', $user->id)->where('status', 'shipped_by_retailer')
                ->whereBetween('created_at', [$from, $to])
                ->count(),

            'delivered_orders_count' => CustomerOrders::where('retailer_id', $user->id)->where('status', 'delivered_by_retailer')
                ->whereBetween('created_at', [$from, $to])
                ->count(),

            'total_sales' => CustomerOrders::where('retailer_id', $user->id)->whereBetween('created_at', [$from, $to])
                ->sum('final_amount'),
        ];

        $wholesaler_product = 0;
        $retailerProducts = RetailerProducts::where('retailer_id', $user->id)->get();
        $retailerProducts->map(function ($retailerProduct) use (&$wholesaler_product) {
            $products = Product::where('wholesaler_id', $retailerProduct->wholesaler_id)
                ->where('category_id', $retailerProduct->category_id)
                ->distinct('id')
                ->count();

            $wholesaler_product += $products;
        });

        $retailer_own_product = RetailerCloneProduct::where('retailer_id', $user->id)->count();

        $data['wholesaler_product_count'] = $wholesaler_product;
        $data['retailer_product_count'] = $retailer_own_product;


        // Fetch filtered orders based on type
        $retailerOrders = CustomerOrders::with([
            'customer',
            'product',
            'retailerCloneProduct',
            'wholesaler.userDetail',
        ])
        ->where('retailer_id', $user->id)
        ->where('status', 'pending')
        ->orderBy('id', 'DESC')
        ->take(5)
        ->get();

        return view('dashboard', compact('data', 'user', 'retailerOrders'));
    }

    public function dashboardReload(Request $request)
    {
        $from = Carbon::createFromFormat('d/m/Y', $request->from)->startOfDay();
        $to = Carbon::createFromFormat('d/m/Y', $request->to)->endOfDay();
        $user = Auth::user();

        $data = [
            'new_orders_count' => CustomerOrders::where('retailer_id', $user->id)->where('status', 'pending')
                ->whereBetween('created_at', [$from, $to])->count(),

            'confirmed_orders_count' => CustomerOrders::where('retailer_id', $user->id)->where('status', 'confirmed_by_retailer')
                ->whereBetween('created_at', [$from, $to])->count(),

            'ready_for_ship_orders_count' => CustomerOrders::where('retailer_id', $user->id)->where('retailer_id', $user->id)->where('status', 'shipped_by_retailer')
                ->whereBetween('created_at', [$from, $to])->count(),

            'delivered_orders_count' => CustomerOrders::where('retailer_id', $user->id)->where('status', 'delivered_by_retailer')
                ->whereBetween('created_at', [$from, $to])->count(),

            'total_sales' => CustomerOrders::where('retailer_id', $user->id)->whereBetween('created_at', [$from, $to])->sum('final_amount'),
        ];

        return response()->json(['status' => true, 'data' => $data]);
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

    public function editCategoryMargin(Request $request)
    {
        $retailer = Auth::user();
        $wholesaler_id = $request->wholesaler_id;
        // 1. Get the margin record to edit

        $margin = RetailerProducts::where('wholesaler_id', $wholesaler_id)
            ->where('id', $request->margin_id)
            ->first();

        if (!$margin) {
            return response()->json(['error' => 'Margin not found.'], 404);
        }

        $addedCategories = RetailerProducts::where('wholesaler_id', $wholesaler_id)
            ->where('retailer_id', $retailer->id)
            ->where('category_id', '!=', $margin->category_id)
            ->pluck('category_id');



        // 3. Get the list of categories - include the one already selected in this margin

        $categories = Product::select('categories.id', 'categories.category_name')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->where('products.wholesaler_id', $wholesaler_id)
            ->where(function ($query) use ($addedCategories, $margin) {
                $query->whereNotIn('categories.id', $addedCategories)
                    ->orWhere('categories.id', $margin->category_id); // include selected
            })
            ->distinct()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $margin,
            'categories' => $categories

        ]);

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

    public function updateCategoryMargin(Request $request)
    {

        // dd($request->all());
        $request->validate([
            'margin_id' => 'required|exists:retailer_products,id',
            'margin' => 'required|numeric|min:0',
            'wholesaler_id' => 'required|exists:retailer_products,wholesaler_id',
        ]);

        $margin = RetailerProducts::where('id', $request->margin_id)
                    ->where('wholesaler_id', $request->wholesaler_id)
                    ->first();

        if (!$margin) {
            return response()->json([
                'success' => false,
                'message' => 'Margin record not found.',
            ], 404);
        }

        // $margin->margin = $request->margin;
        $margin->category_id = $request->category_id;
        $margin->margin = $request->margin;
        $margin->payment_method = implode(',', $request->payment_method);
        $margin->save();

        return response()->json([
            'success' => true,
            'message' => 'Margin updated successfully.',
            'data' => [
                'margin_id' => $margin->id,
                'updated_margin' => $margin->margin,
            ]
        ]);
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
    // public function retailerProduct()
    // {
    //     try {
    //         $retailer = Auth::user()->id;

    //         $retailerProducts = RetailerProducts::with(['wholesaler.products', 'wholesaler.userDetail'])
    //             ->where('retailer_id', $retailer)
    //             ->get();

    //         $filteredRetailerProducts = $retailerProducts->map(function ($retailerProduct) {
    //             $products = Product::where('wholesaler_id', $retailerProduct->wholesaler_id)
    //                 ->where('category_id', $retailerProduct->category_id)
    //                 ->distinct('id')
    //                 ->get();

    //             $retailerProduct->setRelation('products', $products);
    //             return $retailerProduct;
    //         });

    //         $retailerCloneProducts = RetailerCloneProduct::with('category')
    //             ->where('retailer_id', $retailer)
    //             ->get();

    //         $clonedProducts = RetailerCloneProduct::where('retailer_id', $retailer)
    //             ->pluck('product_id')
    //             ->toArray();

    //         // $category_list = Category::select('category_name', 'id')->where('status', 1)->get();


    //             // Get category_ids linked to this retailer
    //             $category_ids = DB::table('retailer_categories')
    //             ->where('retailer_id', $retailer)
    //             ->pluck('category_id');

    //             // Fetch only categories which are active and assigned to this retailer
    //             $category_list = Category::select('category_name', 'id')
    //             ->where('status', 1)
    //             ->whereIn('id', $category_ids)
    //             ->get();
    //         // Pass the filtered data to the view.
    //         return view('product.retailer-own-product', [
    //             'retailerProducts' => $filteredRetailerProducts,
    //             'retailerCloneProducts' => $retailerCloneProducts,
    //             'clonedProducts' => $clonedProducts,
    //             'category_list' => $category_list
    //         ]);
    //     } catch (\Exception $e) {
    //         // Log the error (optional)
    //         Log::error('Error in retailerProduct: ' . $e->getMessage());
    //         session()->flash('error', 'Something went wrong');
    //         // return redirect()->route('retailer.dashboard');

    //         // Return an error view or redirect with an error message
    //         // return view('errors.retailer_product_error', ['error' => $e->getMessage()]); //create error.retailer_product_error.blade.php
    //         //or
    //         return redirect()->back()->with('error', 'An error occurred. Please try again.');
    //     }
    // }

    public function retailerProduct()
    {
        try {
            $retailer = Auth::user()->id;
            $isAllWholesalerVisible = Auth::user()->is_all_wholesaler_visible;

            $filteredRetailerProducts = collect(); // default to empty

            if ($isAllWholesalerVisible == 1) {
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
            }

            $retailerCloneProducts = RetailerCloneProduct::with('category')
                ->where('retailer_id', $retailer)
                ->get();

            $clonedProducts = RetailerCloneProduct::where('retailer_id', $retailer)
                ->pluck('product_id')
                ->toArray();

            // Get category_ids linked to this retailer
            $category_ids = DB::table('retailer_categories')
                ->where('retailer_id', $retailer)
                ->pluck('category_id');

            // Fetch only categories which are active and assigned to this retailer
            $category_list = Category::select('category_name', 'id')
                ->where('status', 1)
                ->whereIn('id', $category_ids)
                ->get();

            return view('product.retailer-own-product', [
                'retailerProducts' => $filteredRetailerProducts,
                'retailerCloneProducts' => $retailerCloneProducts,
                'clonedProducts' => $clonedProducts,
                'category_list' => $category_list
            ]);
        } catch (\Exception $e) {
            Log::error('Error in retailerProduct: ' . $e->getMessage());
            session()->flash('error', 'Something went wrong');
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
        // $category_list = Category::select('category_name', 'id')->where('status', 1)->get();

        $retailer_id = auth()->id(); // Assume retailer is logged in

        // Get category_ids linked to this retailer
        $category_ids = DB::table('retailer_categories')
            ->where('retailer_id', $retailer_id)
            ->pluck('category_id');

        // Fetch only categories which are active and assigned to this retailer
        $category_list = Category::select('category_name', 'id')
            ->where('status', 1)
            ->whereIn('id', $category_ids)
            ->get();

        return view('product.add-product-view', ['category_list' => $category_list]);
    }

    public function getSubCategories(Request $request)
    {
        $retailer_id = auth()->id(); // Or pass retailer id from frontend

        $subCategoryIds = DB::table('retailer_categories')
            ->where('retailer_id', $retailer_id)
            ->where('category_id', $request->category_id)
            ->pluck('sub_category_id');

        $subCategories = DB::table('sub_categories')
            ->whereIn('id', $subCategoryIds)
            ->get(['id', 'sub_category_name']);

        return response()->json($subCategories);
    }

    public function retailerPostProduct(Request $request)
    {

        // dd($request->all());
        $request->validate([
            'product_name' => 'required|min:3|max:100',
            'product_description' => 'required|min:5|max:100',
            'product_tags' => 'required|min:3|max:255',
            'categories' => 'required|numeric',
            'categories' => 'required|numeric|exists:categories,id',
            'sub_category' => 'required|numeric|exists:sub_categories,id',
            'new_price' => 'required|numeric|min:1',
            // 'discount_price' => 'nullable|numeric|min:0.01|max:100',
            // 'image_1' => 'required|mimes:jpeg,png,jpg|max:4096',
            // 'image_2' => 'nullable|mimes:jpeg,png,jpg|max:4096',
            // 'image_3' => 'nullable|mimes:jpeg,png,jpg|max:4096',
            'images' => 'required|array|max:3', // Limit to 3 images
            'images.*' => 'mimes:jpeg,png,jpg|max:4096',
            'video' => 'required|mimes:mp4|max:3072',
            'sku' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);



        try {

            DB::beginTransaction();
            $reatielr_id = Auth::user()->id;
            // $category = Category::where('id', $request->category)->first();
            $product = new RetailerCloneProduct();
            $product->name = $request->product_name;

            $product->description = $request->product_description;
            // Generate a unique slug using product name and current timestamp
            $slugBase = Str::slug($request->product_name);
            $uniqueSuffix = now()->timestamp; // or use uniqid() for more uniqueness
            $product->slug = $slugBase . '-' . $uniqueSuffix;

            // $product->brand_name = $request->brand_name;
            // $product->tags = $request->product_tags;
            $tags = json_decode($request->product_tags, true); // decode JSON to array
            $product->tags = collect($tags)->pluck('value')->implode(',');
            // dd($product->tags);

            $product->quantity = $request->quantity;
            $product->sub_category_id = $request->sub_category;
            $product->new_price = $request->new_price;
            $product->old_price = 0;
            // $product->images = $request->images ? implode(',', $request->images) : null;
            $imagePaths = [];

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $file) {
                    if ($index >= 3) break; // Just in case someone bypasses validation

                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads/products'), $filename);
                    $imagePaths[] = $filename;
                }
                $product->images = implode(',', $imagePaths);
            }

            $product->videos = $request->videos ? $request->videos : null;
            $product->sku = $request->sku;
            $product->retailer_id = $reatielr_id;
            $product->category_id = $request->categories;
            $product->status = 'active';
            $product->save();
            DB::commit();
            session()->flash('success', 'Product added successfully');
            return redirect()->route('retailer.product');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error in retailerPostProduct: ' . $e->getMessage());
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

        // count
        $count = CustomerOrders::where('retailer_id', $retailer->id)
            ->selectRaw("
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as new,
            SUM(CASE WHEN status = 'transfered_retailer_to_wholesaler' THEN 1 ELSE 0 END) as transfered_retailer_to_wholesaler,
            SUM(CASE WHEN status = 'confirmed_by_retailer' THEN 1 ELSE 0 END) as confirmed_by_retailer,
            SUM(CASE WHEN status = 'shipped_by_retailer' THEN 1 ELSE 0 END) as ready_to_ship,
            SUM(CASE WHEN status = 'delivered_by_retailer' THEN 1 ELSE 0 END) as delivered_by_retailer,
            SUM(CASE WHEN status = 'cancelled_by_retailer' THEN 1 ELSE 0 END) as cancelled_by_retailer,
            SUM(CASE WHEN status = 'cancelled_by_customer' THEN 1 ELSE 0 END) as cancelled_by_customer
        ")->first()->toArray();
        // customer orders
        $sql = CustomerOrders::with([
            'customer',
            'product',
            'retailerCloneProduct',
            'wholesaler.userDetail',
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

        // pickup address
        $pickupAddress = PickAddress::where('retailer_id', $retailer->id)->get();

        // rto address
        $rtoAddress = RTOAddress::where('retailer_id', $retailer->id)->get();

        // courier list from API
        $response = Http::withHeaders([
            'signature' => '085c36066064af83c66b9dbf44d190d40feec79f437bc1c1cb'
        ])->get('https://capi-qc.fship.in/api/getallcourier');
        $courierServices = $response->json();
        // $courierServices = [];



        return view('orders.orders-list', compact('retailerOrders', 'count', 'pickupAddress', 'rtoAddress', 'courierServices'));
    }

    public function newOrderList($type = 'new')
    {
        $retailer = Auth::user();

        // Count order statuses
        $count = COrders::where('retailer_id', $retailer->id)
            ->selectRaw("
                SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new,
                SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing,
                SUM(CASE WHEN status = 'pickups' THEN 1 ELSE 0 END) as pickups,
                SUM(CASE WHEN status = 'ready_to_ship' THEN 1 ELSE 0 END) as ready_to_ship,
                SUM(CASE WHEN status = 'transit' THEN 1 ELSE 0 END) as transit,
                SUM(CASE WHEN status = 'ofd' THEN 1 ELSE 0 END) as ofd,
                SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN status = 'rto' THEN 1 ELSE 0 END) as rto,
                SUM(CASE WHEN status = 'received' THEN 1 ELSE 0 END) as received,
                SUM(CASE WHEN status = 'cancel' THEN 1 ELSE 0 END) as cancel,
                SUM(CASE WHEN status = 'close' THEN 1 ELSE 0 END) as close
            ")->first()->toArray();

        // Fetch filtered orders based on type
        $sql = COrders::with([
            'customer',
            'product',
            'retailerCloneProduct',
            'wholesaler.userDetail',
        ])->where('retailer_id', $retailer->id);

        // Map the $type to corresponding status
        $statusMap = [
            'new' => 'new',
            'processing' => 'processing',
            'pickups' => 'pickups',
            'ready_to_ship' => 'ready_to_ship',
            'transit' => 'transit',
            'ofd' => 'ofd',
            'delivered' => 'delivered',
            'rto' => 'rto',
            'received' => 'received',
            'cancel' => 'cancel',
            'close' => 'close',
        ];

        if (array_key_exists($type, $statusMap)) {
            $sql->where('status', $statusMap[$type]);
        } else {
            return redirect()->route('retailer.order.list');
        }

        $retailerOrders = $sql->orderBy('id', 'DESC')->get();

        // Pickup and RTO addresses
        $pickupAddress = PickAddress::where('retailer_id', $retailer->id)->get();
        $rtoAddress = RTOAddress::where('retailer_id', $retailer->id)->get();

        // Courier services (currently empty or from API if uncommented)
        $courierServices = [];

        // dd($count);

        return view('orders.new-order-list', compact('retailerOrders', 'count', 'pickupAddress', 'rtoAddress', 'courierServices'));
    }


    // order action
    // public function orderAction(Request $request)
    // {
    //     $request->validate([
    //         'status' => 'required',
    //     ]);

    //     DB::beginTransaction();
    //     try {
    //         $retailer = Auth::user();
    //         $customerOrder = CustomerOrders::find($request->order_id);

    //         if (!$customerOrder) {
    //             session()->flash('error', 'Order not found');
    //             return redirect()->route('retailer.order.list');
    //         }

    //         $updateData = [];
    //         $message = '';
    //         $type = '';
    //         if ($request->status == 'confirmed_by_retailer') {
    //             $updateData = [
    //                 'status' => $request->status,
    //                 'confirmed_by_retailer_at' => Carbon::now()
    //             ];
    //             $message = 'Order has been confirmed successfully';
    //             $type = 'confirmed-by-retailer';
    //         } else if ($request->status == 'shipped_by_retailer') {
    //             $updateData = [
    //                 'status' => $request->status,
    //                 'shipped_by_retailer_at' => Carbon::now()
    //             ];
    //             $message = 'Order has been ready to ship (by supplier)';
    //             $type = 'ready-to-ship';
    //         } else if ($request->status == 'delivered_by_retailer') {
    //             $updateData = [
    //                 'status' => $request->status,
    //                 'delivered_by_retailer_at' => Carbon::now(),
    //                 'delivered_by' => $retailer->id
    //             ];
    //             $message = 'Order has been delivered by supplier';
    //             $type = 'delivered-by-retailer';
    //         } else if ($request->status == 'transfered_retailer_to_wholesaler') {
    //             $updateData = [
    //                 'status' => $request->status,
    //                 'transfered_retailer_to_wholesaler_at' => Carbon::now()
    //             ];
    //             $message = 'Wholesaler will ship this product';
    //             $type = 'transfered-retailer-to-wholesaler';
    //         } else if ($request->status == 'cancelled_by_retailer') {
    //             $updateData = [
    //                 'status' => $request->status,
    //                 'cancelled_by_retailer_at' => Carbon::now(),
    //                 'cancelled_by' => $retailer->id
    //             ];
    //             $message = 'Order has been cancelled by retailer';
    //             $type = 'cancelled-by-retailer';
    //         }

    //         if (!empty($updateData)) {
    //             $customerOrder->update($updateData);
    //             DB::commit();
    //             session()->flash('success', $message);
    //         } else {
    //             session()->flash('error', 'Invalid order status');
    //         }

    //         return redirect()->route('retailer.order.list', ['type' => $type]);
    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         session()->flash('error', 'Something went wrong!');
    //         return redirect()->route('retailer.order.list');
    //     }
    // }

    //
    public function newOrderAction(Request $request)
    {
        $request->validate([
            'status' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $retailer = Auth::user();
            $customerOrder = CustomerOrders::find($request->order_id);

            if (!$customerOrder) {
                return response()->json(['status' => false, 'msg' => 'Invalid Order ID']);
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
            } else if ($request->status == 'transfered_retailer_to_wholesaler') {
                $updateData = [
                    'status' => $request->status,
                    'transfered_retailer_to_wholesaler_at' => Carbon::now()
                ];
                $message = 'Wholesaler will ship this product';
                $type = 'transfered-retailer-to-wholesaler';
            } else if ($request->status == 'cancelled_by_retailer') {
                if ($request->reject_reason_select_new == 'Other') {
                    $cancelled_reason = $request->reject_reason_input_new;
                } else {
                    $cancelled_reason = $request->reject_reason_select_new;
                }
                $updateData = [
                    'status' => $request->status,
                    'cancelled_by_retailer_at' => Carbon::now(),
                    'cancelled_by' => $retailer->id,
                    'cancelled_reason' => $cancelled_reason
                ];
                $message = 'Order has been cancelled by retailer';
                $type = 'cancelled-by-retailer';
            }

            if (!empty($updateData)) {
                $customerOrder->update($updateData);
                DB::commit();
                return response()->json(['status' => true, 'msg' => $message, 'type' => $type]);
            } else {
                return response()->json(['status' => false, 'msg' => 'Invalid Order Status']);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'msg' => 'Something went wrong, Plase try later!']);
        }
    }

    public function confirmedOrderAction(Request $request)
    {
        $request->validate([
            'status' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $retailer = Auth::user();
            $customerOrder = CustomerOrders::find($request->order_id);

            if (!$customerOrder) {
                return response()->json(['status' => false, 'msg' => 'Invalid Order ID']);
            }

            $updateData = [];
            $message = '';
            $type = '';
            if ($request->status == 'shipped_by_retailer') {
                $updateData = [
                    'status' => $request->status,
                    'shipped_by_retailer_at' => Carbon::now(),
                    'pickup_address_id' => $request->pickup_address_id,
                    'product_weight' => $request->product_weight
                ];
                $message = 'Order has been ready to ship (by supplier)';
                $type = 'ready-to-ship';
            } else if ($request->status == 'transfered_retailer_to_wholesaler') {
                $updateData = [
                    'status' => $request->status,
                    'transfered_retailer_to_wholesaler_at' => Carbon::now()
                ];
                $message = 'Wholesaler will ship this product';
                $type = 'transfered-retailer-to-wholesaler';
            } else if ($request->status == 'cancelled_by_retailer') {
                if ($request->reject_reason_select_confirmed == 'Other') {
                    $cancelled_reason = $request->reject_reason_input_confirmed;
                } else {
                    $cancelled_reason = $request->reject_reason_select_confirmed;
                }
                $updateData = [
                    'status' => $request->status,
                    'cancelled_by_retailer_at' => Carbon::now(),
                    'cancelled_by' => $retailer->id,
                    'cancelled_reason' => $cancelled_reason
                ];
                $message = 'Order has been cancelled by retailer';
                $type = 'cancelled-by-retailer';
            }

            if (!empty($updateData)) {
                $customerOrder->update($updateData);
                DB::commit();
                return response()->json(['status' => true, 'msg' => $message, 'type' => $type]);
            } else {
                return response()->json(['status' => false, 'msg' => 'Invalid Order Status']);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'msg' => 'Something went wrong, Plase try later!']);
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
        // Find the user
        $user = User::with('userDetail')->findOrFail($id);
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
            $user->update($updateData);
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

        if ($user->userDetail) {
            $userDetailUpdate = [];
            if ($request->filled('company')) {
                $userDetailUpdate['company_name'] = $request->company;
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
                $user->userDetail->update($userDetailUpdate);
            }
        }

        return redirect()->back()->with('success', 'Profile updated successfully.');
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

        // dd($request->all());
        $request->validate([
            'product_file' => 'required|mimes:xlsx',
            'categories' => 'required|integer',
        ]);

        $file = $request->file('product_file');
        $categoryId = $request->input('categories');

        try {
            $import = new ProductImport($categoryId);

            // Read the first row to check for column headings
            $headings = array_keys(Excel::toArray(new ProductImport($categoryId), $file)[0][0]);

            // Check if required columns are present
            $missingColumns = $import->checkColumns($headings);

            if ($missingColumns !== true) {
                return response()->json([
                    'error' => 'The uploaded file is missing the following required columns: ' . implode(', ', $missingColumns),
                ], 422);
            }

            // Process data after validating columns
            $collection = collect(Excel::toArray(new ProductImport($categoryId), $file)[0]);

            $result = $import->collection($collection); // Process once

            $data = [
                'valid' => $result['valid'],
                'invalid' => $result['invalid'],
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
            // return response()->json(['error' => 'An error occurred during file processing: ' . $e->getMessage()], 500);
            return response()->json(['error' => 'An error occurred during file processing check product name and slug is unique'], 500);
        }
    }


    public function updateCloneProduct(Request $request)
    {
        $product = RetailerCloneProduct::findOrFail($request->product_id);
        $product->name = $request->product_name;
        $product->description = $request->description;
        // $product->tags = $request->tags;
        $tags = json_decode($request->tags, true); // decode JSON to array
        $product->tags = collect($tags)->pluck('value')->implode(',');
        $product->category_id = $request->categories;
        $product->sub_category_id = $request->sub_category;
        $product->new_price = $request->price;
        $product->sku = $request->sku;
        $product->quantity = $request->quantity;

        // Handle images (limit to 3)
        if ($request->hasFile('images')) {
            $files = $request->file('images');
            $imagePaths = [];

            foreach ($files as $index => $file) {
                if ($index >= 3) break; // Allow only 3 images

                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/products'), $filename);
                $imagePaths[] = $filename;
            }

            // Store images as a comma-separated string in the 'images' field
            $product->images = implode(',', $imagePaths);
        }

        // Handle video upload
        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $videoName = time() . '_' . $video->getClientOriginalName();
            $video->move(public_path('uploads/videos'), $videoName);
            $product->videos = $videoName;
        }

        $product->save();

        return response()->json(['success' => true, 'message' => 'Product updated successfully!']);
    }

    public function prohibitedItem()
    {
        return view('prohibiteditem');
    }

    public function ticketList()
    {
        $user_id =  Auth::user()->id;
        $tickets = Ticket::where('user_id', $user_id)->get();
        return view('support.ticketlist', compact('tickets'));
    }
    public function generateTicket(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'ticket_description' => 'required|string',
            'ticket_image_ref'   => 'nullable|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Genrate random tikcet id 10 digit  with  TM add tm in prefix
        $ticket_id = 'TM' . mt_rand(100000, 999999);

        // Create a new ticket
        $ticket = new Ticket;
        $ticket->subject = $request->subject;
        $ticket->description = $request->ticket_description;
        $ticket->status = 'Pending';
        $ticket->category = '';
        $ticket->ticket_id = $ticket_id;

        $ticket->user_id = Auth::user()->id;

        if ($request->hasFile('ticket_image_ref')) {
            $files = $request->file('ticket_image_ref');

            $filename = time() . '_' . $files->getClientOriginalName();
            $files->move(public_path('uploads/ticket'), $filename);
            $ticket->ref_image = $filename;
        }
        $ticket->save();

        // Return JSON response
        return response()->json(['success' => true, 'message' => 'Ticket Created Successfully', 'ticket' => $ticket]);
    }

    public function deleteTicket(Request $request)
    {
        $user_id = Auth::user()->id;
        $request->validate([
            'ticket_id' => 'required|exists:tickets,ticket_id'
        ]);

        // Find the ticket and delete it
        $ticket = Ticket::where('user_id', $user_id)->where('ticket_id', $request->ticket_id)->first();
        $ticket->delete();

        // Return JSON response
        return response()->json(['success' => true, 'message' => 'Ticket Deleted Successfully']);
    }

    public function editTicket($ticketId)
    {
        $ticket = Ticket::where('ticket_id', $ticketId)->first();
        // return json
        return response()->json($ticket);
    }
    public function updateTicket(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,ticket_id',
            'subject' => 'nullable|string|max:255',
            'ticket_description' => 'nullable|string',
            'ticket_image_ref'   => 'nullable|mimes:jpeg,png,jpg|max:2048'
        ]);

        $user_id = Auth::user()->id;
        $ticket = Ticket::where('user_id', $user_id)->where('ticket_id', $request->ticket_id)->first();
        $ticket->subject = $request->subject;
        $ticket->description = $request->ticket_description;
        if ($request->hasFile('ticket_image_ref')) {
            $files = $request->file('ticket_image_ref');
            $filename = time() . '_' . $files->getClientOriginalName();
            $files->move(public_path('uploads/ticket'), $filename);
            $ticket->ref_image = $filename;
        }
        $ticket->save();
        // Return JSON response
        return response()->json(['success' => true, 'message' => 'Ticket Updated Successfully']);
    }

    public function ratecCalculation()
    {
        return view('rateccalculation');
    }

    public function storeAccoutinfo(Request $request)
    {
        $user = Auth::user()->id;
        $request->validate([
            'account_number' => 'nullable|string|max:50',
            'ifsc_code' => 'nullable|string|max:20',
            'account_holder_name' => 'nullable|string|max:100',
            'pancard_number' => 'nullable|string|max:20',

            'pan_image' => 'nullable|mimes:jpeg,png,jpg|max:2048',
            'aadhar_image' => 'nullable|mimes:jpeg,png,jpg|max:2048',
            'cancel_cheque' => 'nullable|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only([
            'account_number',
            'ifsc_code',
            'account_holder_name',
            'pancard_number'
        ]);

        // Handle uploads

        foreach (['pan_image', 'aadhar_image', 'cancel_cheque'] as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/account'), $filename);
                $data[$field === 'pan_card' ? 'pan_image' : $field] = 'uploads/account/' . $filename;
            }
        }

        // Only update if record exists
        $userDetail = UserDetail::where('user_id', $user)->first();

        if ($userDetail) {
            $userDetail->update($data);
        }
        return back()->with('success-account-info', 'Account information saved successfully!');
    }
}
