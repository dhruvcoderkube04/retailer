<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Imports\ProductImport;
use App\Models\AccountTransaction;
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
use Maatwebsite\Excel\Validators\ValidationException;

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
                ->whereIn('status', ['delivered', 'close'])
                ->where('order_process_by', 'retailer')
                ->sum('final_amount'),
        ];

        $total_earning = AccountTransaction::whereNotNull('customer_order_id')
            ->where('user_type', 'retailer')
            ->where('user_id', $user->id)
            ->where('type', 'success')
            ->where('status', 1)
            ->whereBetween('created_at', [$from, $to])
            ->sum('final_transaction_amount');

        $data['total_earning'] = $total_earning;

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

            'total_sales' => CustomerOrders::where('retailer_id', $user->id)->whereBetween('created_at', [$from, $to])->whereIn('status', ['delivered', 'close'])->where('order_process_by', 'retailer')->sum('final_amount'),
        ];

        $total_earning = AccountTransaction::whereNotNull('customer_order_id')
            ->where('user_type', 'retailer')
            ->where('user_id', $user->id)
            ->where('type', 'success')
            ->where('status', 1)
            ->whereBetween('created_at', [$from, $to])
            ->sum('final_transaction_amount');

        $data['total_earning'] = $total_earning;

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
                $q->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ["%$search%"])
                    ->orWhere('firstname', 'like', '%' . $search . '%')
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

            if (@$item->userDetail->company_logo && Storage::disk('spaces')->exists($item->userDetail->company_logo)) {
                $imagePath = Storage::disk('spaces')->url($item->userDetail->company_logo);
            } else {
                $imagePath = asset('/assets/media/avatars/no-profile.png');
            }
            $company_logo = '<div>
                <img src="' . $imagePath . '" style="height: 75px; width: 75px;" />
            </div>';

            $action = '<a href="' . route('retailer.view-category-margin', encryptId($item->id)) . '" class="btn btn-primary" style="' . ($category_count_fetch > 0 ? '' : 'pointer-events: none; opacity: 0.6; cursor: not-allowed;') . '">Add Margin</a>';

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

            $category_image = '<div>
                <img src="' . ($item->category->category_image
                ? Storage::disk('spaces')->url($item->category->category_image)
                : asset('assets/media/images/no_image.jpg')) . '"
                    onerror="this.onerror=null;this.src=\'' . asset('assets/media/images/no_image.jpg') . '\';"
                    style="height: 75px; width: 75px;" />
            </div>';

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
        $wholesaler_id = decryptId($wholesaler_id);
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
        $wholesaler_id = decryptId($wholesaler_id);
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

            return redirect()->route('retailer.view-category-margin', encryptId($wholesaler_id))
                ->with('success', 'Category margin added successfully');
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Something went wrong');
            return redirect()->route('retailer.view-category-margin', encryptId($wholesaler_id));
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

            return redirect()->route('retailer.view-category-margin', encryptId($wholesaler_id))
                ->with('success', 'Category margin deleted successfully');
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Something went wrong');
            return redirect()->route('retailer.view-category-margin', encryptId($wholesaler_id));
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

    public function retailerProduct(Request $request) // retailerProductss
    {
        try {
            $retailer = Auth::user()->id;

            // sub_category_list
            $sub_category_ids = RetailerCategory::where('retailer_id', $retailer)
                ->pluck('sub_category_id');
            $sub_category_list = SubCategory::select('category_id', 'sub_category_name', 'id')
                ->where('status', 1)
                ->whereIn('id', $sub_category_ids)
                ->get();

            return view('product.retailer-own-product', [
                'sub_category_list' => $sub_category_list
            ]);
        } catch (\Exception $e) {
            Log::error('Error in retailerProduct: ' . $e->getMessage());
            session()->flash('error', 'Something went wrong');
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }

    // AJAX : server-side datatable to fetch record of wholesaler's product
    public function fetchRecordWholesalersProduct(Request $request)
    {
        $limit = ($request->has('length') ? $request->input('length') : 10);
        $page = ($request->has('start') ? $request->input('start') : 0);
        $search = ($request->has('search') ? $request->input('search')['value'] : '');

        $retailer = Auth::user();

        $isAllWholesalerVisible = $retailer->is_all_wholesaler_visible;
        if ($isAllWholesalerVisible !== 1) {
            return response()->json([
                "draw" => $_POST['draw'],
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => []
            ]);
        }

        $clonedProducts = RetailerCloneProduct::where('retailer_id', $retailer->id)
            ->pluck('product_id')
            ->toArray();

        $query = RetailerProducts::with('category')
            ->join('products', function ($join) {
                $join->on('products.wholesaler_id', '=', 'retailer_products.wholesaler_id')
                    ->on('products.category_id', '=', 'retailer_products.category_id');
            })
            ->join('users', 'products.wholesaler_id', '=', 'users.id')
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->where('retailer_products.retailer_id', $retailer->id)
            ->select(
                'products.*',
                'retailer_products.id as retailer_products_id',
                'retailer_products.margin',
                'retailer_products.payment_method',
                'user_details.company_name'
            );

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('products.name', 'like', '%' . $search . '%')
                    ->orWhere('products.sku', 'like', '%' . $search . '%')
                    ->orWhere('products.new_price', 'like', '%' . $search . '%')
                    ->orWhere('products.status', 'like', '%' . $search . '%')
                    ->orWhere('retailer_products.margin', 'like', '%' . $search . '%')
                    ->orWhere('user_details.company_name', 'like', '%' . $search . '%');
            });
        }

        $cntFilter = clone $query;

        if ($request->has('order') && isset($request->order[0])) {
            $columnIndex = $request->order[0]['column'];  // get column index
            $columnName = $request->columns[$columnIndex]['data'];  // get column name
            $direction = $request->order[0]['dir'];  // get sort direction (asc or desc)

            if ($columnName == 'new_price') {
                $query->orderBy('products.' . $columnName, $direction);
            } else if ($columnName == 'margin') {
                $query->orderBy('retailer_products.' . $columnName, $direction);
            } else {
                $query->orderBy('products.' . $columnName, $direction);
            }
        } else {
            $query->orderBy('products.id', 'desc');
        }

        $products = $query->distinct('products.id')
            ->offset($page)
            ->limit($limit)
            ->get();

        $queryTotal = RetailerProducts::join('products', function ($join) {
            $join->on('products.wholesaler_id', '=', 'retailer_products.wholesaler_id')
                ->on('products.category_id', '=', 'retailer_products.category_id');
        })
            ->join('users', 'products.wholesaler_id', '=', 'users.id')
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->where('retailer_products.retailer_id', $retailer->id)
            ->select(
                'products.*',
                'retailer_products.id as retailer_products_id',
                'retailer_products.margin',
                'retailer_products.payment_method',
                'user_details.company_name'
            )
            ->count('products.id');

        $data = [];
        $i = $page;
        foreach ($products as $product) {
            $i++;

            $action = !in_array($product->id, $clonedProducts)
                ? '<a href="' . route('retailer.clone-product-view', encryptId($product->id)) . '" class="btn btn-primary btn-sm">Clone</a>'
                : '';

            $image = explode(',', $product->images)[0] ?? '';
            $image = trim(stripslashes($image), "\"' ");
            $imageUrl = $image
                ? Storage::disk('spaces')->url($image)
                : asset('assets/media/images/no_image.jpg');
            $defaultImage = asset('assets/media/images/no_image.jpg');
            $product_detail = '<div class="d-flex align-items-center ms-4">
                <div class="symbol symbol-50px">
                    <span class="symbol-label">
                        <img src="' . $imageUrl . '"
                            onerror="this.onerror=null;this.src=\'' . $defaultImage . '\';"
                            style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;"
                            alt="Product Image">
                    </span>
                </div>
                <div class="ms-5">
                    <div class="text-gray-800 fs-5 fw-bold" data-kt-ecommerce-product-filter="product_name">'
                . htmlspecialchars(ucfirst($product->name ?? 'N/A'), ENT_QUOTES, 'UTF-8') .
                '</div>
                </div>
            </div>';

            $wholesaler_detail = '<div class="ms-5">
                            <a href="' . route('retailer.view-category-margin', encryptId($product->wholesaler_id) ?? 0) . '" class="text-gray-800 text-hover-primary fs-5 fw-bold" data-kt-ecommerce-product-filter="product_name">' . htmlspecialchars(ucfirst($product->company_name ?? 'N/A'), ENT_QUOTES, 'UTF-8') . '</a>
                        </div>';

            $new_price = '<div class="badge badge-light-primary">' . ($product->new_price ? '₹ ' . $product->new_price : 'N/A') . '</div>';

            $margin = '<div class="badge badge-light-info">' . ($product->margin ? '₹ ' . $product->margin : 'N/A') . '</div>';

            $status = $product->status === 'active'
                ? '<div class="badge badge-light-success">Active</div>'
                : '<div class="badge badge-light-danger">Inactive</div>';

            $data[] = [
                'action' => $action,
                'product' => $product_detail,
                'wholesaler' => $wholesaler_detail,
                'sku' => $product->sku ?? 'N/A',
                'new_price' => $new_price,
                'margin' => $margin,
                'status' => $status
            ];
        }
        return response()->json(array("draw" => $_POST['draw'], "recordsTotal" => $queryTotal, "recordsFiltered" => $cntFilter->count(), 'data' => $data));
    }

    // AJAX : server-side datatable to fetch record of retailer's clone/own product
    public function fetchRecordRetailerCloneProduct(Request $request)
    {
        $limit = ($request->has('length') ? $request->input('length') : 10);
        $page = ($request->has('start') ? $request->input('start') : 0);
        $search = ($request->has('search') ? $request->input('search')['value'] : '');

        $retailer = Auth::user();

        $query = RetailerCloneProduct::with('category', 'productVariations')
            ->where('retailer_id', $retailer->id);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('sku', 'like', "%$search%")
                    ->orWhere('new_price', 'like', "%$search%")
                    ->orWhere('status', 'like', "%$search%")
                    ->orWhereHas('sub_category', function ($q) use ($search) {
                        $q->where('sub_category_name', 'like', '%' . $search . '%');
                    });
            });
        }

        $cntFilter = clone $query;

        if ($request->has('order') && isset($request->order[0])) {
            $columnIndex = $request->order[0]['column'];  // get column index
            $columnName = $request->columns[$columnIndex]['data'];  // get column name
            $direction = $request->order[0]['dir'];  // get sort direction (asc or desc)

            $query->orderBy($columnName, $direction);
        } else {
            $query->orderBy('id', 'desc');
        }

        $products = $query->offset($page)->limit($limit)->get();

        $queryTotal = RetailerCloneProduct::with('category', 'productVariations')
            ->where('retailer_id', $retailer->id)
            ->count('id');

        $data = [];
        $i = $page;
        foreach ($products as $product) {
            $i++;

            $action = '<div class="text-center d-flex justify-content-center align-items-center gap-2">
                <button type="button"
                    class="btn btn-icon btn-danger btn-active-light-danger w-30px h-30px delete-product"
                    data-id="' . $product->id . '">
                    <i class="ki-duotone ki-trash fs-3">
                        <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                        <span class="path4"></span><span class="path5"></span>
                    </i>
                </button>
                <a href="' . route('retailer.edit.product', encryptId($product->id)) . '" title="Edit"
                    class="btn btn-icon btn-primary btn-active-light-primary w-30px h-30px">
                    <i class="ki-duotone ki-pencil fs-4">
                        <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                        <span class="path4"></span><span class="path5"></span>
                    </i>
                </a>
                <a href="' . route('retailer.details.product', encryptId($product->id)) . '" title="View"
                    class="btn btn-icon btn-success btn-active-light-success w-30px h-30px">
                    <i class="ki-duotone ki-eye fs-4">
                        <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                        <span class="path4"></span><span class="path5"></span>
                    </i>
                </a>
            </div>';

            $image = explode(',', $product->images)[0] ?? '';
            $image = trim(stripslashes($image), "\"' ");
            $imageUrl = $image
                ? Storage::disk('spaces')->url($image)
                : asset('assets/media/images/no_image.jpg');
            $defaultImage = asset('assets/media/images/no_image.jpg');
            $product_detail = '<div class="d-flex align-items-center ms-4">
                <div class="symbol symbol-50px">
                    <span class="symbol-label">
                        <img src="' . $imageUrl . '"
                            onerror="this.onerror=null;this.src=\'' . $defaultImage . '\';"
                            style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;"
                            alt="Product Image">
                    </span>
                </div>
                <div class="ms-5">
                    <div class="text-gray-800 fs-5 fw-bold" data-kt-ecommerce-product-filter="product_name">'
                . htmlspecialchars(ucfirst($product->name ?? 'N/A'), ENT_QUOTES, 'UTF-8') .
                '</div>
                </div>
            </div>';

            $new_price = '<div class="badge badge-light-primary">' . ($product->new_price ? '₹ ' . $product->new_price : 'N/A') . '</div>';

            $status = '<div class="badge ' . ($product->status === 'inactive' ? 'badge-light-danger' : 'badge-light-success') . '">' . ucfirst($product->status) . '</div>';

            $data[] = [
                'action' => $action,
                'product' => $product_detail,
                'sku' => $product->sku ?? 'N/A',
                'sub_category' => $product->sub_category->sub_category_name ?? 'N/A',
                'new_price' => $new_price,
                'status' => $status,
            ];
        }
        return response()->json(array("draw" => $_POST['draw'], "recordsTotal" => $queryTotal, "recordsFiltered" => $cntFilter->count(), 'data' => $data));
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
                    $imagePaths[] = uploadOrUpdateImageToSpaces($file, 'products/images');
                }
                $product->images = implode(',', $imagePaths);
            }

            // VIDEO
            if ($request->hasFile('video')) {
                $file = $request->file('video');
                $product->videos = uploadOrUpdateVideoToSpaces($file, 'products/videos');
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
            $product_id = decryptId($product_id);
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

    // product details page
    public function retailerDetailsProduct(Request $request, $product_id)
    {
         try {
            $product_id = decryptId($product_id);
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

            return view('product.product-view-page', compact('product_detail', 'sub_category_list'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error in retailerEditProduct: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }

    // update retailer product
    public function retailerUpdateProduct(Request $request, $product_id)
    {
        $product_id = decryptId($product_id);
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
            // 'sku' => [
            //     'nullable',
            //     'string',
            //     function ($attribute, $value, $fail) use ($product_id) {
            //         $existsInRetailerCloneProduct = DB::table('retailer_clone_products')
            //             ->where('sku', $value)
            //             ->where('id', '!=', $product_id)
            //             ->exists();

            //         $existsInProducts = DB::table('products')
            //             ->where('sku', $value)
            //             ->where('id', '!=', $product_id)
            //             ->exists();

            //         if ($existsInRetailerCloneProduct || $existsInProducts) {
            //             $fail('The SKU must be unique across all products.');
            //         }
            //     },
            // ],
            'quantity' => 'nullable|integer|min:1|max:999999',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:2000',
            'product_meta_keywords' => 'nullable|string|max:255',
            'variation' => 'array',
            'variation_price' => 'array'
        ]);

        DB::beginTransaction();
        try {
            $product = RetailerCloneProduct::findOrFail($product_id);
            // digital ocean
            // IMAGE
            $existingImages = explode(',', $product->images);
            $imagePaths = [];
            if ($request->hasFile('images')) { // Check if the 'images' field has files
                $files = $request->file('images'); // Get the array of uploaded files

                foreach ($files as $file) { // Iterate through each uploaded file
                    try {
                        $imagePaths[] = uploadOrUpdateImageToSpaces($file, 'products/images');
                    } catch (\Exception $e) {
                        Log::error('Image Upload Failed to Spaces: ' . $e->getMessage());
                        return back()->with('error', 'One or more image uploads failed.')->withInput();
                    }
                }

                //<---- NO DELETE as per discussed with nilesh sir on 27-05-2025 ----->
                // foreach ($existingImages as $image) {
                //     if (!empty($image)) {
                //         try {
                //             deleteImageToSpaces($image);
                //         } catch (\Exception $deleteException) {
                //             Log::error('Failed to Remove Old Image: ' . $deleteException->getMessage());
                //         }
                //     }
                // }
            } else {
                $imagePaths = $existingImages;
            }
            $imagePathsString = implode(',', $imagePaths);

            // VIDEO
            $videoPath = $product->videos ?? null;
            if ($request->hasFile('video')) {
                try {
                    $file = $request->file('video');

                    //<---- NO DELETE as per discussed with nilesh sir on 27-05-2025 ----->
                    $videoPath = uploadOrUpdateVideoToSpaces($file, 'products/videos');
                } catch (\Exception $e) {
                    Log::error('Video Upload Failed: ' . $e->getMessage());
                    return back()->with('error', 'Video upload failed.')->withInput();
                }
            }

            // sub-category & category
            $subCategory = SubCategory::find($request->sub_category_id);

            // SKU number check else generated unique
            // if ($request->sku) {
            //     $sku = $request->sku;
            // } else {
            //     do {
            //         // Generate a 14-digit random number (padded if needed)
            //         $sku = str_pad(mt_rand(111, 99999999999999), 14, '0', STR_PAD_LEFT);
            //     } while (Product::where('sku', $sku)->exists());
            // }

            // Update product details
            $product->name = $request->product_name;
            // $product->sku = $sku;
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
            //return response()->json(['success' => false, 'message' => $e->getMessage()]);
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
            $product_id = decryptId($product_id);
            $product = Product::where('id', $product_id)->orderBy('id', 'desc')->first();

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
            if (!$cloneProduct) {
                session()->flash('error', 'Invalid product details or alredy deleted');
                return redirect()->route('retailer.product');
            }

            //<---- NO DELETE as per discussed with nilesh sir on 27-05-2025 ----->
            // if (!empty($cloneProduct->images)) {
            //     $imagePaths = explode(',', $cloneProduct->images);
            //     foreach ($imagePaths as $image) {
            //         deleteImageToSpaces($image);
            //     }
            // }
            // if (!empty($cloneProduct->videos)) {
            //     deleteImageToSpaces($cloneProduct->videos);
            // }

            $cloneProduct->delete();

            ProductVariation::where('product_id', $clone_product_id)->delete();

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
        $product_id = decryptId($product_id);
        $request->validate([
            'description' => 'required|max:1000',
            'old_price' => 'required|numeric|min:0.01',
            'new_price' => 'required|numeric|min:0.01'
        ]);

        DB::beginTransaction();
        try {
            $retailer = Auth::user();

            $product = Product::where('id', $product_id)->first();

            // // Handle image cloning
            // $cloneImages = [];
            // if ($product->images) {
            //     $images = explode(',', $product->images);
            //     foreach ($images as $image) {
            //         if (Storage::disk('spaces')->exists($image)) {
            //             $newImageName = 'products/images/' . now()->timestamp . '_' . Str::random(6) . '.' . pathinfo($image, PATHINFO_EXTENSION);
            //             Storage::disk('spaces')->copy($image, $newImageName);
            //             $cloneImages[] = $newImageName;
            //         }
            //     }
            // }

            // // Handle video cloning
            // $cloneVideo = null;
            // if ($product->videos && Storage::disk('spaces')->exists($product->videos)) {
            //     $newVideoName = 'products/videos/' . now()->timestamp . '_' . Str::random(6) . '.' . pathinfo($product->videos, PATHINFO_EXTENSION);
            //     Storage::disk('spaces')->copy($product->videos, $newVideoName);
            //     $cloneVideo = $newVideoName;
            // }

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
            $cloneProduct->images = $product->images;
            $cloneProduct->videos = $product->videos;
            $cloneProduct->url = $product->url;
            $cloneProduct->status = $product->status;
            $cloneProduct->color = $product->color;
            $cloneProduct->size = $product->size;
            $cloneProduct->specifications = $product->specifications;
            $cloneProduct->category_id = $product->category_id;
            $cloneProduct->sub_category_id = $product->sub_category_id;
            $cloneProduct->meta_title = $product->meta_title;
            $cloneProduct->meta_description = $product->meta_description;
            $cloneProduct->meta_keywords = $product->meta_keywords;
            $cloneProduct->save();

            DB::commit();
            return redirect()->route('retailer.product', ['active-tab' => 2])->with('success', 'Product cloned successfully');
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
                $filename = uploadOrUpdateImageToSpaces($file, 'company_profile',  $user->userDetail->company_logo);
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
                $userDetailUpdate['company_logo'] = $filename;
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

    // IMPORT : Retailer own product (retailer_clone_products) import
    public function uploadBulkProduct(Request $request)
    {
        $request->validate([
            'product_file' => 'required|file|mimes:xlsx',
            'sub_category' => 'required|exists:sub_categories,id',
        ]);

        $file = $request->file('product_file');
        $subCategoryId = $request->input('sub_category');
        $images_and_video_update = $request->images_and_video_update ? true : false;

        try {
            $import = new ProductImport($subCategoryId, $images_and_video_update);

            // Check headers (column names)
            $excelData = Excel::toArray($import, $file);
            $headings = array_keys($excelData[0][0] ?? []);

            $missingColumns = $import->checkColumns($headings);

            if ($missingColumns !== true) {
                return response()->json([
                    'error' => 'The uploaded file is missing the following required columns: <br>' . implode(', ', $missingColumns),
                ], 422);
            }

            // Process collection
            $collection = collect($excelData[0]);
            $result = $import->collection($collection);

            if (!empty($result['invalid'])) {
                return response()->json([
                    'error_type' => 'row_validation',
                    'error' => implode('<br>', $result['invalid']),
                ], 422);
            }

            return response()->json([
                'message' => 'Products imported successfully.',
                'valid_count' => count($result['valid']),
            ]);
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $messages = [];

            foreach ($failures as $failure) {
                $messages[] = "Row <strong>{$failure->row()}</strong>: " . implode(', ', $failure->errors());
            }

            return response()->json([
                'error_type' => 'row_validation',
                'error' => implode('<br>', $messages),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Bulk Product Upload Error: ' . $e->getMessage());

            return response()->json([
                'error' => 'An unexpected error occurred during file processing. Please try again.'
            ], 500);
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

        $userDetail = UserDetail::where('user_id', $userId)->first();

        // Upload to DigitalOcean Spaces
        foreach (['pan_image', 'aadhar_image', 'cancel_cheque'] as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $data[$field] = uploadOrUpdateImageToSpaces($file, 'account_documents', $userDetail->$field);
            }
        }


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
