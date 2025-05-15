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
use App\Models\ProductVariation;
use App\Models\RetailerCategory;
use App\Models\SubCategory;
use App\Models\User;
use App\Models\UserDetail;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
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
use Illuminate\Support\Facades\Storage;

class RetilerController extends Controller
{
    public function retailerDashboard()
    {
        $from = Carbon::now()->startOfMonth();
        $to = Carbon::now()->endOfMonth();
        $user = Auth::user();

        $data = [
            'new_orders_count' => CustomerOrders::where('retailer_id', $user->id)->where('status', 'pending')
                ->where('order_process_by', 'retailer')
                ->whereBetween('created_at', [$from, $to])
                ->count(),

            'confirmed_orders_count' => CustomerOrders::where('retailer_id', $user->id)->where('status', 'approved_by_retailer')
                ->where('order_process_by', 'retailer')
                ->whereBetween('created_at', [$from, $to])
                ->count(),

            'ready_for_ship_orders_count' => CustomerOrders::where('retailer_id', $user->id)->where('status', 'pickup')
                ->where('order_process_by', 'retailer')
                ->whereBetween('created_at', [$from, $to])
                ->count(),

            'delivered_orders_count' => CustomerOrders::where('retailer_id', $user->id)->where('status', 'delivered')
                ->where('order_process_by', 'retailer')
                ->whereBetween('created_at', [$from, $to])
                ->count(),

            'total_sales' => CustomerOrders::where('retailer_id', $user->id)->whereBetween('created_at', [$from, $to])
                ->where('order_process_by', 'retailer')
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
            'order_product_detail',
            'wholesaler.userDetail',
        ])
            ->where('retailer_id', $user->id)
            ->where('status', 'pending')
            ->where('order_process_by', 'retailer')
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
                ->where('order_process_by', 'retailer')
                ->whereBetween('created_at', [$from, $to])->count(),

            'confirmed_orders_count' => CustomerOrders::where('retailer_id', $user->id)->where('status', 'approved_by_retailer')
                ->where('order_process_by', 'retailer')
                ->whereBetween('created_at', [$from, $to])->count(),

            'ready_for_ship_orders_count' => CustomerOrders::where('retailer_id', $user->id)->where('retailer_id', $user->id)->where('status', 'pickup')
                ->where('order_process_by', 'retailer')
                ->whereBetween('created_at', [$from, $to])->count(),

            'delivered_orders_count' => CustomerOrders::where('retailer_id', $user->id)->where('status', 'delivered')
                ->where('order_process_by', 'retailer')
                ->whereBetween('created_at', [$from, $to])->count(),

            'total_sales' => CustomerOrders::where('retailer_id', $user->id)->whereBetween('created_at', [$from, $to])->where('order_process_by', 'retailer')->sum('final_amount'),
        ];

        return response()->json(['status' => true, 'data' => $data]);
    }

    //<------------------------- START : wholesaler list --------------------------->
    // wholesaler list
    public function wholesalerList()
    {
        $isAllWholesalerVisibleCheck = Auth::user()->is_all_wholesaler_visible;

        return view('wholesaler.wholesaler-list', ['is_all_wholesaler_visible' => $isAllWholesalerVisibleCheck]);
    }

    // AJAX : server-side data-table to fetch record of wholesaler list
    public function wholesalerFetchRecord(Request $request)
    {
        $limit = ($request->has('length') ? $request->input('length') : 10);
        $page = ($request->has('start') ? $request->input('start') : 0);
        $search = ($request->has('search') ? $request->input('search')['value'] : '');

        $query = User::with('userDetail')
            ->where('user_type', 2)
            ->where('status', 1);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', '%' . $search . '%')
                    ->orWhere('lastname', 'like', '%' . $search . '%')
                    ->orWhereHas('userDetail', function ($q) use ($search) {
                        $q->where('company_name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->has('order') && isset($request->order[0])) {
            $columnIndex = $request->order[0]['column'];  // get column index
            $columnName = $request->columns[$columnIndex]['data'];  // get column name
            $direction = $request->order[0]['dir'];  // get sort direction (asc or desc)

            $query->orderBy($columnName, $direction);
        } else {
            $query->orderBy('id', 'desc');
        }

        $cntFilter = clone $query;
        $query->offset($page)->limit($limit);
        $wholesaler = $query->get();

        $queryTotal = User::where('user_type', 2)
            ->where('status', 1)
            ->count('id');

        $data = [];
        $i = $page;
        foreach ($wholesaler as $key => $item) {
            $i++;

            $category_count_fetch = Product::where('wholesaler_id', $item->id)
                ->where('status', 'active')
                ->distinct('category_id')
                ->count('category_id');
            $product_count_fetch = Product::where('wholesaler_id', $item->id)
                ->where('status', 'active')
                ->count('id');
            $details = '
                <div>
                    <span>Total Category : </span>
                    <div class="badge ' . ($category_count_fetch > 0 ? 'badge-light-success' : 'badge-light-danger') . ' fs-6">
                                    ' . $category_count_fetch . '
                    </div>
                </div>
                <div>
                    <span>Total Products : </span>
                    <div class="badge ' . ($product_count_fetch > 0 ? 'badge-light-success' : 'badge-light-danger') . ' fs-6">
                                    ' . $product_count_fetch . '
                    </div>
                </div>';

            if (@$item->userDetail->company_logo) {
                $company_logo = '<div>
                    <img src="' . $item->userDetail->company_logo . '" style="height: 80px; width: 80px;" />
                </div>';
            } else {
                $company_logo = '<div>
                    <img src="' . asset('/assets/media/avatars/no-profile.png') . '" style="height: 75px; width: 75px;" />
                </div>';
            }

            $action = '<a href="' . route('retailer.view-category-margin', $item->id) . '" class="btn btn-primary" style="' . ($category_count_fetch > 0 ? '' : 'pointer-events: none; opacity: 0.6; cursor: not-allowed;') . '">Add Margin</a>';

            $data[] = array(
                "company_logo" => @$company_logo,
                "company_name" => @$item->userDetail->company_name,
                "wholesaler_name" => $item->firstname . ' ' . $item->lastname,
                "details" => $details,
                "action" => $action
            );
        }
        return response()->json(array("draw" => $_POST['draw'], "recordsTotal" => $queryTotal, "recordsFiltered" => $cntFilter->count(), 'data' => $data));
    }
    //<------------------------- END : wholesaler list --------------------------->


    //<------------------------- START : subscribed category list --------------------------->
    // subscribed category index
    public function subscribedCategoryIndex()
    {
        $isAllWholesalerVisibleCheck = Auth::user()->is_all_wholesaler_visible;

        return view('subscribed-category.index', ['is_all_wholesaler_visible' => $isAllWholesalerVisibleCheck]);
    }

    // AJAX : server-side data-table to fetch record of subscribed category list
    public function subscribedCategoryFetchRecord(Request $request)
    {
        $limit = ($request->has('length') ? $request->input('length') : 10);
        $page = ($request->has('start') ? $request->input('start') : 0);
        $search = ($request->has('search') ? $request->input('search')['value'] : '');
        $retailer = Auth::user();

        $query = RetailerProducts::with('wholesaler', 'category', 'wholesaler.userDetail')
            ->where('retailer_id', $retailer->id);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->orWhere('payment_method', 'like', '%' . $search . '%')
                    ->orWhere('margin', 'like', '%' . $search . '%')
                    ->orWhere('notes', 'like', '%' . $search . '%')
                    ->orWhereHas('wholesaler', function ($q) use ($search) {
                        $q->where('firstname', 'like', '%' . $search . '%')
                            ->orWhere('lastname', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('category', function ($q) use ($search) {
                        $q->where('category_name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('wholesaler.userDetail', function ($q) use ($search) {
                        $q->where('company_name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->has('order') && isset($request->order[0])) {
            $columnIndex = $request->order[0]['column'];  // get column index
            $columnName = $request->columns[$columnIndex]['data'];  // get column name
            $direction = $request->order[0]['dir'];  // get sort direction (asc or desc)

            $query->orderBy($columnName, $direction);
        } else {
            $query->orderBy('id', 'desc');
        }

        $cntFilter = clone $query;
        $query->offset($page)->limit($limit);
        $subscribedCategories = $query->get();

        $queryTotal = RetailerProducts::where('retailer_id', $retailer->id)
            ->count('id');

        $data = [];
        $i = $page;
        foreach ($subscribedCategories as $key => $item) {
            $i++;

            if ($item->category->category_image) {
                $category_image = '<div>
                    <img src="' . $item->category->category_image . '" style="height: 80px; width: 80px;" />
                </div>';
            } else {
                $category_image = '<div>
                    <img src="' . asset('/assets/media/images/no_image.jpg') . '" style="height: 75px; width: 75px;" />
                </div>';
            }

            $margin = '<div class="badge badge-light-primary">
                            ₹ ' . $item->margin . '
                        </div>';

            $data[] = array(
                "category_image" => $category_image,
                "category_name" => $item->category->category_name,
                "wholesaler_name" => $item->wholesaler->userDetail->company_name,
                "payment_method" => $item->payment_method,
                "margin" => $margin,
            );
        }
        return response()->json(array("draw" => $_POST['draw'], "recordsTotal" => $queryTotal, "recordsFiltered" => $cntFilter->count(), 'data' => $data));
    }
    //<------------------------- END : subscribed category list --------------------------->


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

        return view('wholesaler.retailer-product-list', [
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

            $retailerCloneProducts = RetailerCloneProduct::with('category', 'productVariations')
                ->where('retailer_id', $retailer)
                ->get();

            $clonedProducts = RetailerCloneProduct::where('retailer_id', $retailer)
                ->pluck('product_id')
                ->toArray();

            // category_list
            $category_ids = RetailerCategory::where('retailer_id', $retailer)
                ->pluck('category_id');
            $category_list = Category::select('category_name', 'id')
                ->where('status', 1)
                ->whereIn('id', $category_ids)
                ->get();

            // sub_category_list
            $sub_category_ids = RetailerCategory::where('retailer_id', $retailer)
                ->pluck('sub_category_id');
            $sub_category_list = SubCategory::select('category_id', 'sub_category_name', 'id')
                ->where('status', 1)
                ->whereIn('id', $sub_category_ids)
                ->get();

            return view('product.retailer-own-product', [
                'retailerProducts' => $filteredRetailerProducts,
                'retailerCloneProducts' => $retailerCloneProducts,
                'clonedProducts' => $clonedProducts,
                'sub_category_list' => $sub_category_list,
                'category_list' => $category_list
            ]);
        } catch (\Exception $e) {
            Log::error('Error in retailerProduct: ' . $e->getMessage());
            session()->flash('error', 'Something went wrong');
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }

    // product add view
    public function retailerAddProduct(Request $request)
    {
        $retailer = Auth::user(); // Assume retailer is logged in

        // Get category_ids linked to this retailer
        $sub_category_ids = RetailerCategory::where('retailer_id', $retailer->id)
            ->pluck('sub_category_id');

        // Fetch only categories which are active and assigned to this retailer
        $sub_category_list = SubCategory::select('category_id', 'sub_category_name', 'id')
            ->where('status', 1)
            ->whereIn('id', $sub_category_ids)
            ->get();

        return view('product.add-product-view', ['sub_category_list' => $sub_category_list]);
    }

    // product store
    public function retailerPostProduct(Request $request)
    {
        $request->validate([
            'product_name' => 'required|min:3|max:100',
            'slug' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $existsInRetailerCloneProduct = DB::table('retailer_clone_products')
                        ->where('slug', $value)
                        ->exists();

                    $existsInProducts = DB::table('products')
                        ->where('slug', $value)
                        ->exists();

                    if ($existsInRetailerCloneProduct || $existsInProducts) {
                        $fail('The slug must be unique across all products.');
                    }
                },
            ],
            // 'categories' => 'required|numeric|exists:categories,id',
            'sub_category_id' => 'required|numeric|exists:sub_categories,id',
            'product_tags' => 'nullable|string|max:255',
            'status' => 'required|string|in:active,inactive',
            'old_price' => 'required|numeric|min:1|max:99999999.99',
            'new_price' => 'required|numeric|min:1|max:99999999.99',
            'product_description' => 'nullable|string|max:1000',
            'images' => 'required|array|max:3',
            'images.*' => 'mimes:jpeg,png,jpg|max:4096',
            'video' => 'nullable|mimes:mp4|max:10240',  // Max file size 10MB (10240 KB)
            'sku' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    $existsInRetailerCloneProduct = DB::table('retailer_clone_products')
                        ->where('sku', $value)
                        ->exists();

                    $existsInProducts = DB::table('products')
                        ->where('sku', $value)
                        ->exists();

                    if ($existsInRetailerCloneProduct || $existsInProducts) {
                        $fail('The SKU must be unique across all products.');
                    }
                },
            ],
            'quantity' => 'required|integer|min:1|max:999999',
            'meta_title' => 'nullable|string|max:255',
            'product_meta_keywords' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:2000',
            'variation' => 'array',
            'variation_price' => 'array'
        ]);

        DB::beginTransaction();
        try {
            $retailer_id = Auth::user()->id;

            $product = new RetailerCloneProduct();

            // digital ocean
            // IMAGE
            $imagePaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $file) {
                    if ($index >= 3) break;
                    $originalExtension = $file->getClientOriginalExtension();
                    $filename = 'product_image_' . now()->timestamp . '_' . $index . '.' . $originalExtension;
                    $directory = 'products/images/';
                    $path = $directory . $filename;

                    Storage::disk('spaces')->putFileAs($directory, $file, $filename, 'public');
                    $imagePaths[] = Storage::disk('spaces')->url($path);
                }
                $product->images = implode(',', $imagePaths);
            }

            // VIDEO
            if ($request->hasFile('video')) {
                $videoFile = $request->file('video');
                $videoOriginalExtension = $videoFile->getClientOriginalExtension();
                $videoFilename = 'product_video_' . now()->timestamp . '.' . $videoOriginalExtension;
                $videoDirectory = 'products/videos/';
                $videoPath = $videoDirectory . $videoFilename;

                Storage::disk('spaces')->putFileAs($videoDirectory, $videoFile, $videoFilename, 'public');
                $product->videos = Storage::disk('spaces')->url($videoPath);
            } else {
                $product->videos = null;
            }

            // sub-category & category
            $subCategory = SubCategory::find($request->sub_category_id);

            // SKU number check else generated unique
            if ($request->sku) {
                $sku = $request->sku;
            } else {
                do {
                    // Generate a 14-digit random number (padded if needed)
                    $sku = str_pad(mt_rand(111, 99999999999999), 14, '0', STR_PAD_LEFT);
                } while (Product::where('sku', $sku)->exists());
            }

            // add time stape with uuid in slug
            $slug = Str::slug($request->slug) . '-' . now()->timestamp . '-' . uniqid();

            $product->retailer_id = $retailer_id;
            $product->name = $request->product_name;
            $product->slug = $slug;
            $product->category_id = $subCategory->category_id;
            $product->sub_category_id = $request->sub_category_id;
            $product->tags = $request->product_tags;
            $product->status = $request->status;
            $product->old_price = $request->old_price;
            $product->new_price = $request->new_price;
            $product->description = $request->product_description;
            $product->sku = $sku;
            $product->quantity = $request->quantity;
            $product->meta_title = $request->meta_title;
            $product->meta_description = $request->meta_description;
            $product->meta_keywords = $request->product_meta_keywords;
            $product->save();

            // Store variations
            if (!empty($request->variation)) {
                foreach ($request->variation as $index => $variation) {
                    // Only save if price is provided
                    if (!empty($request->variation_price[$index])) {
                        ProductVariation::create([
                            'product_id' => $product->id,
                            'product_variation' => $variation,
                            'price' => $request->variation_price[$index],
                            'stock' => $request->variation_stock[$index],
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Product added successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error in retailerPostProduct: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // product edit view
    public function retailerEditProduct(Request $request, $product_id)
    {
        try {
            $retailer = Auth::user();

            $product_detail = RetailerCloneProduct::with('productVariations')
                ->where('retailer_id', $retailer->id)
                ->where('id', $product_id)
                ->first();

            // Get category_ids linked to this retailer
            $sub_category_ids = RetailerCategory::where('retailer_id', $retailer->id)
                ->pluck('sub_category_id');

            // Fetch only categories which are active and assigned to this retailer
            $sub_category_list = SubCategory::select('category_id', 'sub_category_name', 'id')
                ->where('status', 1)
                ->whereIn('id', $sub_category_ids)
                ->get();

            return view('product.edit-product-view', compact('product_detail', 'sub_category_list'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error in retailerEditProduct: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }

    // update retailer product
    public function retailerUpdateProduct(Request $request, $product_id)
    {
        $request->validate([
            'product_name' => 'nullable|string|max:255',
            // 'categories' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'product_tags' => 'nullable|string|max:255',
            'status' => 'required|string|in:active,inactive',
            'old_price' => 'required|numeric|min:1|max:99999999.99',
            'new_price' => 'required|numeric|min:1|max:99999999.99',
            'product_description' => 'nullable|string|max:1000',
            'images' => 'nullable|array|max:3',
            'images.*' => 'mimes:jpeg,png,jpg|max:4096',
            'video' => 'nullable|mimes:mp4|max:10240',  // Max file size 10MB (10240 KB)
            'sku' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) use ($product_id) {
                    $existsInRetailerCloneProduct = DB::table('retailer_clone_products')
                        ->where('sku', $value)
                        ->where('id', '!=', $product_id)
                        ->exists();

                    $existsInProducts = DB::table('products')
                        ->where('sku', $value)
                        ->where('id', '!=', $product_id)
                        ->exists();

                    if ($existsInRetailerCloneProduct || $existsInProducts) {
                        $fail('The SKU must be unique across all products.');
                    }
                },
            ],
            'quantity' => 'nullable|integer|min:1|max:999999',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:2000',
            'product_meta_keywords' => 'nullable|string|max:255',
            'variation' => 'array',
            'variation_price' => 'array'
        ]);

        DB::beginTransaction();
        try {
            $product = RetailerCloneProduct::findOrFail($request->product_id);

            // digital ocean
            // IMAGE
            $existingImages = explode(',', $product->images);
            $imagePaths = [];
            if ($request->hasFile('images')) { // Check if the 'images' field has files
                $files = $request->file('images'); // Get the array of uploaded files

                foreach ($files as $file) { // Iterate through each uploaded file
                    try {
                        $originalExtension = $file->getClientOriginalExtension();
                        $imageFileName = 'product_image_' . now()->timestamp . '_' . uniqid() . '.' . $originalExtension;
                        $imageDirectory = 'products/images/'; // Directory in DigitalOcean Spaces for images
                        $imagePathInSpaces = $imageDirectory . $imageFileName;

                        // Upload image to DigitalOcean Spaces
                        Storage::disk('spaces')->putFileAs($imageDirectory, $file, $imageFileName, 'public');

                        // Store the public URL for the image
                        $imagePaths[] = Storage::disk('spaces')->url($imagePathInSpaces); // Use array_push or direct assignment with []

                    } catch (\Exception $e) {
                        Log::error('Image Upload Failed to Spaces: ' . $e->getMessage());
                        return back()->with('error', 'One or more image uploads failed.')->withInput();
                    }
                }

                // Delete old images from Spaces
                foreach ($existingImages as $image) {
                    if (!empty($image)) {
                        try {
                            $oldImagePath = str_replace(Storage::disk('spaces')->url(''), '', $image);
                            Storage::disk('spaces')->delete($oldImagePath);
                            Log::info('Old Image Removed: ' . $oldImagePath);
                        } catch (\Exception $deleteException) {
                            Log::error('Failed to Remove Old Image: ' . $deleteException->getMessage());
                        }
                    }
                }
            } else {
                $imagePaths = $existingImages;
            }
            $imagePathsString = implode(',', $imagePaths);

            // VIDEO
            $videoPath = $product->videos ?? null;
            if ($request->hasFile('video')) {
                try {
                    $file = $request->file('video');
                    $originalExtension = $file->getClientOriginalExtension();
                    $fileName = 'product_video_' . now()->timestamp . '_' . uniqid() . '.' . $originalExtension;
                    $directory = 'products/videos/'; // Directory in DigitalOcean Spaces
                    $path = $directory . $fileName;

                    // Upload to DigitalOcean Spaces
                    Storage::disk('spaces')->putFileAs($directory, $file, $fileName, 'public');

                    // Store the public URL
                    $videoPath = Storage::disk('spaces')->url($path);

                    // Delete old video if exists
                    if (!empty($product->video)) {
                        try {
                            $oldVideoPath = str_replace(Storage::disk('spaces')->url(''), '', $product->video);
                            Storage::disk('spaces')->delete($oldVideoPath);
                            Log::info('Old video deleted: ' . $oldVideoPath);
                        } catch (\Exception $deleteEx) {
                            Log::error('Failed to delete old video: ' . $deleteEx->getMessage());
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Video Upload Failed: ' . $e->getMessage());
                    return back()->with('error', 'Video upload failed.')->withInput();
                }
            }

            // sub-category & category
            $subCategory = SubCategory::find($request->sub_category_id);

            // SKU number check else generated unique
            if ($request->sku) {
                $sku = $request->sku;
            } else {
                do {
                    // Generate a 14-digit random number (padded if needed)
                    $sku = str_pad(mt_rand(111, 99999999999999), 14, '0', STR_PAD_LEFT);
                } while (Product::where('sku', $sku)->exists());
            }

            // Update product details
            $product->name = $request->product_name;
            $product->sku = $sku;
            $product->category_id = $subCategory->category_id ?? null;
            $product->sub_category_id = $request->sub_category_id;
            $product->description = $request->product_description;
            $product->old_price = $request->old_price;
            $product->new_price = $request->new_price;
            $product->quantity = $request->quantity;
            $product->status = $request->status;
            $product->meta_title = $request->meta_title;
            $product->meta_description = $request->meta_description;
            $product->meta_keywords = $request->product_meta_keywords;
            $product->tags = $request->product_tags;
            $product->images = $imagePathsString;
            $product->videos = $videoPath;
            $product->save();

            // Handle Variations
            if (!empty($request->variation)) {
                $incomingVariations = $request->variation;
                $variationPrices = $request->variation_price;
                $variationStocks = $request->variation_stock;

                $existingVariations = ProductVariation::where('product_id', $product->id)
                    ->pluck('product_variation')
                    ->toArray();

                // Delete removed variations
                $variationsToDelete = array_diff($existingVariations, $incomingVariations);
                if (!empty($variationsToDelete)) {
                    ProductVariation::where('product_id', $product->id)
                        ->whereIn('product_variation', $variationsToDelete)
                        ->delete();
                }

                foreach ($incomingVariations as $index => $variation) {
                    $price = $variationPrices[$index] ?? null;
                    $stock = $variationStocks[$index] ?? null;

                    if (!empty($variation) && !is_null($price)) {
                        ProductVariation::updateOrCreate(
                            [
                                'product_id' => $product->id,
                                'product_variation' => $variation,
                            ],
                            [
                                'price' => $price,
                                'stock' => $stock
                            ]
                        );
                    } elseif (!is_null($variation)) {
                        // Delete if price is missing for this variation
                        ProductVariation::where('product_id', $product->id)
                            ->where('product_variation', $variation)
                            ->delete();
                    }
                }
            } else {
                // No variation sent, remove all existing
                ProductVariation::where('product_id', $product->id)->delete();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Product updated successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Something went wrong!']);
        }
    }

    // AJAX : get list of variations of selected sub-category
    public function getSubCategoryVariations(Request $request)
    {
        $request->validate([
            'sub_category_id' => 'required|exists:sub_categories,id'
        ]);

        try {
            $subCategory = SubCategory::select('sub_category_variation')
                ->where('id', $request->sub_category_id)
                ->where('status', 1)
                ->first();

            if (!$subCategory) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Not found',
                ]);
            }

            return response()->json([
                'status' => true,
                'msg' => 'Success',
                'sub_category_variation' => $subCategory->sub_category_variation
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => $e
            ]);
        }
    }

    // AJAX : product slug unique check
    public function productUniqueSlugCheck(Request $request)
    {
        $slug = Str::slug($request->slug);

        $exists = Product::where('slug', $slug)->exists()
            || RetailerCloneProduct::where('slug', $slug)->exists();

        return response()->json([
            'exists' => $exists,
            'slug' => $slug,
        ]);
    }

    // AJAX : get sub-category from selected category_id
    public function getSubCategories(Request $request)
    {
        $retailer_id = auth()->id(); // Or pass retailer id from frontend

        $subCategoryIds = DB::table('retailer_categories')
            ->where('retailer_id', $retailer_id)
            ->where('category_id', $request->category_id)
            ->pluck('sub_category_id');

        $subCategories = DB::table('sub_categories')
            ->whereIn('id', $subCategoryIds)
            ->where('status', 1)
            ->get(['id', 'sub_category_name']);

        return response()->json($subCategories);
    }

    //------------------------

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

    // delete retailer product / clone product
    public function cloneProductRemove(Request $request, $clone_product_id)
    {
        DB::beginTransaction();
        try {
            $cloneProduct = RetailerCloneProduct::where('id', $clone_product_id)->first();

            if ($cloneProduct) {
                $cloneProduct->delete();

                ProductVariation::where('product_id', $clone_product_id)->delete();
            }

            DB::commit();
            return redirect()->route('retailer.product')->with('success', 'Product removed from clone successfully');
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Something went wrong');
            return redirect()->route('retailer.product');
        }
    }

    // store clone product
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
            $updateData['phone_number'] = $request->phone;
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
            try {
                $file = $request->file('profile');  // Get file
                $filename = time() . '_' . $file->getClientOriginalName(); // Generate unique filename
                $directory = 'uploads/company_profile/'; // Directory in DigitalOcean Spaces
                $path = $directory . $filename;

                // Upload to DigitalOcean Spaces
                Storage::disk('spaces')->putFileAs($directory, $file, $filename, 'public');

                // Store the public URL
                $profilePath = Storage::disk('spaces')->url($path);

                // You would typically store $profilePath in your database column
                // instead of just $filename.
                // Example: $company->profile_url = $profilePath;

            } catch (\Exception $e) {
                // Handle upload errors
                Log::error('Profile Upload to Spaces Failed: ' . $e->getMessage());
                return back()->with('error', 'Profile upload to DigitalOcean Spaces failed.');
            }
        } else {
            $profilePath = null; // No file uploaded
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
            if ($request->filled('pincode')) {
                $userDetailUpdate['postal_code'] = $request->pincode;
            }
            if ($request->hasFile('profile')) {
                $userDetailUpdate['company_logo'] = $profilePath;
            }

            if (!empty($userDetailUpdate)) {
                $user->userDetail->update($userDetailUpdate);
            }
        }

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }


    public function downloadStockSample()
    {
        $filePath = public_path('samplestock/stock_sample.xlsx');

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
            Log::error('File processing error: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return response()->json(['error' => 'An error occurred during file processing'], 500);
        }
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
        $userId = Auth::user()->id;

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

        // Upload to DigitalOcean Spaces
        foreach (['pan_image', 'aadhar_image', 'cancel_cheque'] as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $filename = time() . '_' . $file->getClientOriginalName();
                $directory = 'uploads/account/';
                $path = $directory . $filename;

                Storage::disk('spaces')->putFileAs($directory, $file, $filename, 'public');

                $data[$field] = Storage::disk('spaces')->url($path);
            }
        }

        $userDetail = UserDetail::where('user_id', $userId)->first();

        if ($userDetail) {
            $userDetail->update($data);
        }

        return back()->with('success-account-info', 'Account information saved successfully!');
    }

    // use couire service manager
    public function ratecCalculationPost(Request $request)
    {
        $data = $request->validate([
            'source_Pincode' => 'required|digits:6',
            'destination_Pincode' => 'required|digits:6',
            'payment_Mode' => 'required|string',
            'amount' => 'required|numeric',
            'shipment_Weight' => 'required|numeric',
            'shipment_Length' => 'nullable|numeric',
            'shipment_Width' => 'nullable|numeric',
            'shipment_Height' => 'nullable|numeric',
            'volumetric_Weight' => 'nullable|numeric',
        ]);

        try {
            $courierService = \App\Services\CourierServiceManager::getService();
            $response = $courierService->calculateRate($data);

            if (!empty($response['status']) && $response['status'] === true) {
                return response()->json($response);
            }

            if (!empty($response['valid']) && $response['valid'] === true) {
                return response()->json($response);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error communicating with courier service.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    //<----------------------- START : Customer ---------------------->
    // index
    public function indexCustomers(Request $request)
    {
        return view('customers.index');
    }

    // AJAX : server side data-table fetch-record
    public function fetchRecordsCustomers(Request $request)
    {
        $limit = ($request->has('length') ? $request->input('length') : 10);
        $page = ($request->has('start') ? $request->input('start') : 0);
        $search = ($request->has('search') ? $request->input('search')['value'] : '');

        $retailer = Auth::user();

        $query = CustomerDetails::select(
            'customer_details.id',
            'customer_details.firstname',
            'customer_details.lastname',
            'customer_details.phone_number',
            'customer_details.email',
            'customer_details.state',
            'customer_details.city',
            'customer_details.pincode'
        )
            ->join('customer_orders', 'customer_orders.customer_id', '=', 'customer_details.id')
            ->where('customer_orders.order_process_by', 'retailer')
            ->where('customer_orders.retailer_id', $retailer->id)
            ->distinct();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_details.firstname', 'like', '%' . $search . '%')
                    ->orWhere('customer_details.lastname', 'like', '%' . $search . '%')
                    ->orWhere('customer_details.phone_number', 'like', '%' . $search . '%')
                    ->orWhere('customer_details.email', 'like', '%' . $search . '%')
                    ->orWhere('customer_details.state', 'like', '%' . $search . '%')
                    ->orWhere('customer_details.city', 'like', '%' . $search . '%')
                    ->orWhere('customer_details.pincode', 'like', '%' . $search . '%');
            });
        }

        if ($request->has('order') && isset($request->order[0])) {
            $columnIndex = $request->order[0]['column'];  // get column index
            $columnName = $request->columns[$columnIndex]['data'];  // get column name
            $direction = $request->order[0]['dir'];  // get sort direction (asc or desc)

            $query->orderBy("customer_details.$columnName", $direction);
        } else {
            $query->orderBy('customer_details.id', 'desc');
        }

        $cntFilter = clone $query;
        $query->offset($page)->limit($limit);
        $customers = $query->get();

        $queryTotal = CustomerDetails::join('customer_orders', 'customer_orders.customer_id', '=', 'customer_details.id')
            ->where('customer_orders.order_process_by', 'retailer')
            ->where('customer_orders.retailer_id', $retailer->id)
            ->distinct('customer_details.id')
            ->count('customer_details.id');

        $data = [];
        $i = $page;
        foreach ($customers as $item) {
            $i++;

            $data[] = array(
                "sr_no" => $i,
                "name" => $item->firstname . ' ' . $item->lastname,
                "mobile_no" => $item->phone_number,
                "email" => $item->email,
                "state" => $item->state,
                "city" => $item->city,
                "pincode" => $item->pincode
            );
        }
        return response()->json(array("draw" => $_POST['draw'], "recordsTotal" => $queryTotal, "recordsFiltered" => $cntFilter->count(), 'data' => $data));
    }
    //<----------------------- END : Customer ---------------------->
}
