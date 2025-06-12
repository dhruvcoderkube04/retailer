<?php

namespace App\Http\Controllers\API\Retailer;

use App\Http\Controllers\Controller;
use App\Mail\RetailerOrderMail;
use App\Models\Category;
use App\Models\CustomerDetails;
use App\Models\CustomerOrders;
use App\Models\OrderProductDetails;
use App\Models\RetailerProducts;
use App\Models\RetailerCloneProduct;
use App\Models\RetailerWebManagement;
use App\Models\UserDetail;
use App\Models\Otp;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\User;
use App\Models\RetailerCategory;
use App\Models\SubCategory;
use App\Models\OrderNotification;
use Dotenv\Util\Regex;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class RetailerProductController extends Controller
{
    public function storeInfo(Request $request)
    {
        try {
            $apiKey = $request->header('API-KEY');
            if (!$apiKey) {
                return response()->json(['error' => 'API Key is required.'], 401);
            }

            $storeinfo = RetailerWebManagement::select(
                'store_name',
                'logo',
                'brand_name',
                'store_time',
                'mobile_no',
                'email',
                'address',
                'facebook_url',
                'retailer_id',
                'twitter_url',
                'instagram_url',
                'instagram_id',
                'youtube_url',
                'pinterest_url',
                'linkedin_url',
                'google_plus_url',
                'google_analytics_id',
                'facebook_pixel_id',
                'app_store_url',
                // 'apple_store_id',
                // 'play_store_url',
                'meta_title',
                'meta_keywords',
                'meta_description',
                'cod_charge',
                'shipping_charge',
                'cart_limit',
                'sms_service',
                'enquiry_whatsapp',
                'hide_pickup_address',
                'request_offer',
                'favicon',
                'banner',
                'offer_text',
                'banner_title',
                'banner_sub_title',
                'banner_button_title',
                'theme',
            )
                ->with([
                    'theme_data:id,theme_name,theme_image',
                    'retailer' => function ($query) {
                        $query->where('is_delete', 0)
                            ->where('status', 1);
                    }
                ])
                ->whereHas('retailer', function ($query) {
                    $query->where('is_delete', 0)
                        ->where('status', 1);
                })
                ->where('product_listing_key', $apiKey)->first();
            if (!$storeinfo) {
                return response()->json(['error' => 'Unauthorized: Invalid API Key.'], 403);
            }
            $storeinfo->logo = $storeinfo->logo ? Storage::disk('spaces')->url($storeinfo->logo) : '';
            $storeinfo->favicon = $storeinfo->favicon ? Storage::disk('spaces')->url($storeinfo->favicon) : '';
            $storeinfo->banner = $storeinfo->banner ? Storage::disk('spaces')->url($storeinfo->banner) : '';
            if ($storeinfo->theme_data) {
                $storeinfo->theme_data->theme_image = $storeinfo->theme_data->theme_image ? Storage::disk('spaces')->url($storeinfo->theme_data->theme_image) : '';
            }

            $categoryIds = RetailerCategory::where('retailer_id', $storeinfo->retailer_id)
                ->pluck('category_id')
                ->toArray();

            $categories = Category::whereIn('id', $categoryIds)->get(); // fetch id + name

            // Get subcategories related to those categories
            $subCategories = SubCategory::whereIn('category_id', $categoryIds)->get();

            // Group subcategories by category name

            $categoryList = [];

            foreach ($categories as $category) {
                $subList = $subCategories
                    ->where('category_id', $category->id)
                    ->map(function ($sub) {
                        return [
                            'id' => $sub->id,
                            'name' => $sub->sub_category_name,
                            'image' => $sub->sub_category_image ? Storage::disk('spaces')->url($sub->sub_category_image) : '',
                        ];
                    })
                    ->values()
                    ->toArray();

                $categoryList[] = [
                    'id' => $category->id,
                    'name' => $category->category_name,
                    'image' => $category->category_image ? Storage::disk('spaces')->url($category->category_image) : '',
                    'sub_category_list' => $subList,
                ];
            }

            return response()->json([
                'success' => true,
                'storeinfo' => $storeinfo,
                'category_list' => $categoryList
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching retailer company info: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }

    public function getProducts(Request $request)
    {
        try {
            //<------------- validate user ---------------->
            $apiKey = $request->header('API-KEY');

            if (!$apiKey) {
                return response()->json(['error' => 'API Key is required.'], 401);
            }

            $retailer = RetailerWebManagement::with([
                'retailer' => function ($query) {
                    $query->where('is_delete', 0)->where('status', 1);
                }
            ])
                ->whereHas('retailer', function ($query) {
                    $query->where('is_delete', 0)->where('status', 1);
                })
                ->where('product_listing_key', $apiKey)
                ->first();
            if (!$retailer) {
                return response()->json(['error' => 'Unauthorized: Invalid API Key.'], 403);
            }

            $retailerId = $retailer->retailer_id;
            $retailerUser = User::where('id', $retailerId)->where('status', 1)->where('is_delete', 0)->first();
            if (!$retailerUser) {
                return response()->json(['error' => 'Retailer user not found.'], 404);
            }

            // <---------------- get product data ---------------------->
            $retailerProducts = collect();
            $retailerSubscribedProducts = collect();
            if ($retailerUser->is_all_wholesaler_visible == 1) {
                $retailerSubscribedProducts = RetailerProducts::where('retailer_id', $retailerId)->get();

                $retailerProducts = $retailerSubscribedProducts->flatMap(function ($data) {
                    return Product::with('productVariations:id,product_id,product_variation,old_price,price,stock')
                        ->where('wholesaler_id', $data->wholesaler_id)
                        ->where('category_id', $data->category_id)
                        ->where('status', 'active')
                        ->get();
                });
            }

            $retailerCloneProducts = RetailerCloneProduct::with('productVariations:id,product_id,product_variation,old_price,price,stock')
                ->where('retailer_id', $retailer->retailer_id)
                ->where('status', 'active')
                ->get();

            $allProducts = collect($retailerProducts)->concat($retailerCloneProducts);

            $categoryIds = json_decode($request->category, true);
            $subCategoryIds = json_decode($request->sub_category, true);
            $minPrice = (float) $request->min_price;
            $maxPrice = (float) $request->max_price;

            // <---------------- map product data ---------------------->
            $products = $allProducts->map(function ($item) use ($retailerSubscribedProducts) {
                $margin = 0;

                foreach ($retailerSubscribedProducts as $pair) {
                    if ($pair->wholesaler_id == $item->wholesaler_id && $pair->category_id == $item->category_id) {
                        $margin = $pair->margin;
                        break;
                    }
                }

                if (!empty($item->productVariations) && $item->productVariations->isNotEmpty()) {
                    foreach ($item->productVariations as $variation) {
                        $variation->price = (float) $variation->price;
                        $variation->old_price = (float) $variation->old_price;
                        $variation->final_price = $variation->price + $margin;
                        $variation->old_price += $margin;
                    }
                } else {
                    $item->new_price = (float) $item->new_price;
                    $item->old_price = (float) $item->old_price;
                    $item->final_price = $item->new_price + $margin;
                    $item->old_price += $margin;
                }

                return $item;
            })->filter(function ($item) use (
                $categoryIds,
                $subCategoryIds,
                $minPrice,
                $maxPrice,
            ) {
                //<------ FILTER - category ------>
                if (!empty($categoryIds) && !in_array($item->category_id, $categoryIds)) {
                    return false;
                }

                //<------ FILTER - sub_category ------>
                if (!empty($subCategoryIds) && !in_array($item->sub_category_id, $subCategoryIds)) {
                    return false;
                }

                //<------ FILTER - min_price ------>
                if ($minPrice) {
                    if (!empty($item->productVariations) && $item->productVariations->isNotEmpty()) {
                        foreach ($item->productVariations as $variation) {
                            if ($variation->final_price <= $minPrice) {
                                return false;
                            }
                        }
                    } else {
                        if ($item->final_price <= $minPrice) {
                            return false;
                        }
                    }
                }

                //<------ FILTER - max_price ------>
                if ($maxPrice) {
                    if (!empty($item->productVariations) && $item->productVariations->isNotEmpty()) {
                        foreach ($item->productVariations as $variation) {
                            if ($variation->final_price >= $maxPrice) {
                                return false;
                            }
                        }
                    } else {
                        if ($item->final_price >= $maxPrice) {
                            return false;
                        }
                    }
                }

                return true;
            })->map(function ($item) {
                return $this->formatProductFromClone($item, $item->final_price);
            })->values();

            //<------ FILTER - short_by ------>
            $sortBy = $request->sort_by;
            if ($sortBy === 'price_high_to_low') {
                $products = $products->sortByDesc('final_price')->values();
            } elseif ($sortBy === 'price_low_to_high') {
                $products = $products->sortBy('final_price')->values();
            } elseif ($sortBy === 'recently_added') {
                $products = $products->sortByDesc('created_at')->values();
            }

            //<------ Default Filters ------>
            $products = $products->sortBy([
                // retailer own product at top and wholesaler's subscribed product at last
                fn($a, $b) => is_null($b['wholesaler_id']) <=> is_null($a['wholesaler_id']),

                // out-of-stock products at last
                fn($a, $b) => ($a['quantity'] == 0) <=> ($b['quantity'] == 0),
            ])->values();

            // <--------- get category and sub-category list as per product data ------------>
            $categoryIds = $products->pluck('category_id')->filter()->unique();
            $categories = Category::select('id', 'category_name')
                ->whereIn('id', $categoryIds)
                ->where('status', 1)
                ->get();

            $subCategoryIds = $products->pluck('sub_category_id')->filter()->unique();
            $subCategories = SubCategory::select('id', 'category_id', 'sub_category_name')
                ->whereIn('id', $subCategoryIds)
                ->where('status', 1)
                ->get();

            //<------------ handle single product response --------------->
            if ($request->has('product_id')) {
                $single = $products->where('id', $request->product_id)->first();
                if (!$single) {
                    return response()->json(['error' => 'Product not found.'], 404);
                }

                return response()->json([
                    'success' => true,
                    'product' => $single,
                ]);
            }

            // <---------------- pagination ---------------------->
            $perPage = 12;
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $productsCollection = new Collection($products);
            $currentPageItems = $productsCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();
            $paginatedProducts = new LengthAwarePaginator($currentPageItems, $productsCollection->count(), $perPage);
            $paginatedProducts->setPath(url()->current());

            $items = $paginatedProducts->items();
            foreach ($items as &$product) {
                $product_image = explode(',', $product['product_images'] ?? '');
                $product_image_array = [];

                foreach ($product_image as $image) {
                    if (!empty($image)) {
                        $product_image_array[] = Storage::disk('spaces')->url($image);
                    }
                }

                $product['product_images'] = implode(',', $product_image_array);
                $product['product_video'] = $product['product_video'] ? Storage::disk('spaces')->url($product['product_video']) : '';
            }

            $paginatedProducts->setCollection(collect($items));

            // <---------------- return data ---------------------->
            return response()->json([
                'success'    => true,
                'products'   => $paginatedProducts,
                'categories' => $categories,
                'sub_categories' => $subCategories,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getRetailerProducts: ' . $e->getMessage(), [
                'line'  => $e->getLine(),
                'file'  => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    public function searchProducts(Request $request)
    {
        try {
            // validate API key
            $apiKey = $request->header('API-KEY');
            if (!$apiKey) {
                return response()->json(['error' => 'API Key is required.'], 401);
            }

            // validate retailer

            $retailer = RetailerWebManagement::with([
                'retailer' => function ($query) {
                    $query->where('is_delete', 0)
                        ->where('status', 1);
                }
            ])
                ->whereHas('retailer', function ($query) {
                    $query->where('is_delete', 0)
                        ->where('status', 1);
                })->where('product_listing_key', $apiKey)->first();

            if (!$retailer) {
                return response()->json(['error' => 'Unauthorized: Invalid API Key.'], 403);
            }

            $retailerId = $retailer->retailer_id;
            $retailerUser = User::where('id', $retailerId)->where('status', 1)->where('is_delete', 0)->first();
            if (!$retailerUser) {
                return response()->json(['error' => 'Retailer user not found.'], 404);
            }

            // <---------------- get product data ---------------------->
            $retailerProducts = collect();

            if ($retailerUser->is_all_wholesaler_visible == 1) {
                $pairs = RetailerProducts::where('retailer_id', $retailerId)
                    ->select('wholesaler_id', 'category_id', 'margin')
                    ->get();

                $sqlRetailerProducts = Product::with(['wholesaler', 'productVariations:id,product_id,product_variation,old_price,price,stock'])
                    ->where('status', 'active')
                    ->where(function ($query) use ($pairs) {
                        foreach ($pairs as $pair) {
                            $query->orWhere(function ($q) use ($pair) {
                                $q->where('wholesaler_id', $pair->wholesaler_id)
                                    ->where('category_id', $pair->category_id);
                            });
                        }
                    });

                // 🔍 Search for wholesaler products
                if (!empty($request->search)) {
                    $sqlRetailerProducts->where('name', 'like', '%' . $request->search . '%');
                }

                $retailerProducts = $sqlRetailerProducts->get();
            }

            // 🛒 Retailer cloned products with search
            $sqlRetailerCloneProducts = RetailerCloneProduct::with('productVariations:id,product_id,product_variation,old_price,price,stock')
                ->where('retailer_id', $retailerId)
                ->where('status', 'active');

            if (!empty($request->search)) {
                $sqlRetailerCloneProducts->where('name', 'like', '%' . $request->search . '%');
            }

            $retailerCloneProducts = $sqlRetailerCloneProducts->get();

            $allProducts = collect($retailerProducts)->concat($retailerCloneProducts);

            // <---------------- map product data ---------------------->
            $products = $allProducts->map(function ($item) use ($pairs) {
                $margin = 0;

                foreach ($pairs as $pair) {
                    if ($pair->wholesaler_id == $item->wholesaler_id && $pair->category_id == $item->category_id) {
                        $margin = $pair->margin;
                        break;
                    }
                }

                if (!empty($item->productVariations) && $item->productVariations->isNotEmpty()) {
                    foreach ($item->productVariations as $variation) {
                        $variation->price = (float) $variation->price;
                        $variation->old_price = (float) $variation->old_price;
                        $variation->final_price = $variation->price + $margin;
                        $variation->old_price += $margin;
                    }
                } else {
                    $item->new_price = (float) $item->new_price;
                    $item->old_price = (float) $item->old_price;
                    $item->final_price = $item->new_price + $margin;
                    $item->old_price += $margin;
                }

                return $item;
            })->map(function ($item) {
                return $this->formatProductFromClone($item, $item->final_price);
            })->values();

            //<------ Default Filters ------>
            $products = $products->sortBy([
                // retailer own product at top and wholesaler's subscribed product at last
                fn($a, $b) => is_null($b['wholesaler_id']) <=> is_null($a['wholesaler_id']),

                // out-of-stock products at last
                fn($a, $b) => ($a['quantity'] == 0) <=> ($b['quantity'] == 0),
            ])->values();

            // <--------- get category and sub-category list as per product data ------------>
            $categoryIds = $products->pluck('category_id')->filter()->unique();
            $categories = Category::select('id', 'category_name')
                ->whereIn('id', $categoryIds)
                ->where('status', 1)
                ->get();

            $subCategoryIds = $products->pluck('sub_category_id')->filter()->unique();
            $subCategories = SubCategory::select('id', 'category_id', 'sub_category_name')
                ->whereIn('id', $subCategoryIds)
                ->where('status', 1)
                ->get();

            //<------------ handle single product response --------------->
            if ($request->has('product_id')) {
                $single = $products->where('id', $request->product_id)->first();
                if (!$single) {
                    return response()->json(['error' => 'Product not found.'], 404);
                }

                return response()->json([
                    'success' => true,
                    'product' => $single,
                ]);
            }

            // <---------------- pagination ---------------------->
            $perPage = 12;
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $productsCollection = new Collection($products);
            $currentPageItems = $productsCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();
            $paginatedProducts = new LengthAwarePaginator($currentPageItems, $productsCollection->count(), $perPage);
            $paginatedProducts->setPath(url()->current());

            $items = $paginatedProducts->items();
            foreach ($items as &$product) {
                $product_image = explode(',', $product['product_images'] ?? '');
                $product_image_array = [];

                foreach ($product_image as $image) {
                    if (!empty($image)) {
                        $product_image_array[] = Storage::disk('spaces')->url($image);
                    }
                }

                $product['product_images'] = implode(',', $product_image_array);
                $product['product_video'] = $product['product_video'] ? Storage::disk('spaces')->url($product['product_video']) : '';
            }

            $paginatedProducts->setCollection(collect($items));

            // <---------------- return data ---------------------->
            return response()->json([
                'success'    => true,
                'products'   => $paginatedProducts,
                'categories' => $categories,
                'sub_categories' => $subCategories,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getRetailerProducts: ' . $e->getMessage(), [
                'line'  => $e->getLine(),
                'file'  => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }


    public function getSingalProductDetails(Request $request, $slug = null)
    {
        try {
            //<------------- validate user ---------------->
            $apiKey = $request->header('API-KEY');
            if (!$apiKey) {
                return response()->json(['error' => 'API Key is required.'], 401);
            }

            $retailer = RetailerWebManagement::with([
                'retailer' => function ($query) {
                    $query->where('is_delete', 0)
                        ->where('status', 1);
                }
            ])
                ->whereHas('retailer', function ($query) {
                    $query->where('is_delete', 0)
                        ->where('status', 1);
                })->where('product_listing_key', $apiKey)->first();
            if (!$retailer) {
                return response()->json(['error' => 'Unauthorized: Invalid API Key.'], 403);
            }

            if (!$slug) {
                return response()->json(['error' => 'Product slug is required.'], 422);
            }

            $retailerId = $retailer->retailer_id;
            $retailerUser = User::where('id', $retailerId)->where('status', 1)->where('is_delete', 0)->first();
            if (!$retailerUser) {
                return response()->json(['error' => 'Retailer user not found.'], 404);
            }

            // <---------------- get product data ---------------------->
            $retailerSubscribedProducts = collect();
            if ($retailerUser->is_all_wholesaler_visible == 1) {
                $retailerSubscribedProducts = RetailerProducts::where('retailer_id', $retailerId)
                    ->select('wholesaler_id', 'category_id', 'margin')
                    ->get();
            }

            $product = null;
            $cloneProduct = RetailerCloneProduct::with('productVariations:id,product_id,product_variation,old_price,price,stock')
                ->where('retailer_id', $retailer->retailer_id)
                ->where('slug', $slug)
                ->where('status', 'active')
                ->first();

            if ($cloneProduct) {
                $product = $cloneProduct;
            } else {
                if ($retailerUser->is_all_wholesaler_visible == 1) {
                    $sqlRetailerProducts = Product::with(['wholesaler', 'productVariations:id,product_id,product_variation,old_price,price,stock'])
                        ->where('status', 'active')
                        ->where('slug', $slug)
                        ->where(function ($query) use ($retailerSubscribedProducts) {
                            foreach ($retailerSubscribedProducts as $pair) {
                                $query->orWhere(function ($q) use ($pair) {
                                    $q->where('wholesaler_id', $pair->wholesaler_id)
                                        ->where('category_id', $pair->category_id);
                                });
                            }
                        })
                        ->first();

                    if ($sqlRetailerProducts) {
                        $product = $sqlRetailerProducts;
                    }
                }
            }

            if (!$product) {
                return response()->json(['error' => 'Product not found.'], 404);
            }


            $margin = 0;
            if ($retailerSubscribedProducts->isNotEmpty() && isset($product->wholesaler_id, $product->category_id)) {
                foreach ($retailerSubscribedProducts as $pair) {
                    if ($pair->wholesaler_id == $product->wholesaler_id && $pair->category_id == $product->category_id) {
                        $margin = $pair->margin;
                        break;
                    }
                }
            }

            // Apply margin
            if (!empty($product->productVariations) && $product->productVariations->isNotEmpty()) {
                $product->productVariations = $product->productVariations->map(function ($variation) use ($margin) {
                    $variation->price = (float) $variation->price;
                    $variation->old_price = (float) $variation->old_price;
                    $variation->final_price = $variation->price + $margin;
                    $variation->old_price += $margin;
                    return $variation;
                });
            } else {
                $product->new_price = (float) $product->new_price;
                $product->old_price = (float) $product->old_price;
                $product->final_price = $product->new_price + $margin;
                $product->old_price += $margin;
            }

            $formatted_product = $this->formatProductFromClone($product, $product->final_price ?? 0);

            // <---------------- return data ---------------------->
            return response()->json([
                'success' => true,
                'product' => $formatted_product
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Get product detail error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }

    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|max:30',
            'lastname' => 'required|max:30',
            'phone_number' => 'required|numeric|digits:10',
            'email' => 'required|email',
            'address' => 'required|max:250',
            'payment_method' => 'required|in:cod,upi',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'nullable',
            'products.*.retailer_product_id' => 'nullable',
            'products.*.wholesaler_id' => 'nullable',
            'products.*.retailer_id' => 'nullable',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.final_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $apiKey = $request->header('API-KEY');
        if (!$apiKey) {
            return response()->json(['error' => 'API Key is required.'], 401);
        }

        $retailer = RetailerWebManagement::with(['retailer' => function ($query) {
            $query->where('is_delete', 0)->where('status', 1);
        }])->whereHas('retailer', function ($query) {
            $query->where('is_delete', 0)->where('status', 1);
        })->where('product_listing_key', $apiKey)->first();

        if (!$retailer) {
            return response()->json(['error' => 'Unauthorized: Invalid API Key.'], 403);
        }

        DB::beginTransaction();
        try {
            $customerDetail = CustomerDetails::create([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'phone_number' => $request->phone_number,
                'email' => $request->email ?? null,
                'address' => $request->address,
                'state' => $request->state,
                'city' => $request->city,
                'pincode' => $request->pincode
            ]);

            $orderItems = [];
            $orderIDs = [];
            $orderItemsForMail = [];

            foreach ($request->products as $product) {
                $orderID = 'ORD' . now()->timestamp . rand(10000, 99999);
                $orderIDs[] = $orderID;

                $wholesalerId = $product['wholesaler_id'] ?? null;
                $retailerId = $product['retailer_id'] ?? $retailer->retailer_id;
                $productId = $product['product_id'] ?? null;
                $cloneId = $product['retailer_product_id'] ?? null;
                $quantity = $product['quantity'];

                if (!$productId && !$cloneId) {
                    return response()->json([
                        'error' => true,
                        'message' => 'Either product_id or retailer_product_id must be provided.'
                    ], 404);
                }
                if ($productId && !$wholesalerId) {
                    return response()->json([
                        'error' => true,
                        'message' => 'wholesaler_id is required for Product ID ' . $productId
                    ], 404);
                }

                $variation = null;
                $variationOldPrice = null;
                $variationNewPrice = null;
                $variationStock = null;
                if ($productId) {
                    $productModel = Product::with('productVariations')
                        ->where('id', $productId)
                        ->where('status', 'active')
                        ->first();
                    if (!$productModel) {
                        return response()->json([
                            'error' => true,
                            'message' => 'Product ID ' . $productId . ' not found.'
                        ], 404);
                    }

                    if ($productModel->productVariations->isNotEmpty()) {
                        if (empty($product['product_variation'])) {
                            return response()->json([
                                'error' => true,
                                'message' => 'Product variation is required for Product ID ' . $productId
                            ], 422);
                        }

                        $productVariation = ProductVariation::where('product_id', $productId)->where('product_variation', $product['product_variation'])->first();
                        if (!$productVariation) {
                            return response()->json([
                                'error' => true,
                                'message' => 'There is no any variation as ' . $product['product_variation'] . ' for Product ID ' . $productId
                            ], 404);
                        }
                        if ($productVariation->stock < $quantity) {
                            return response()->json([
                                'error' => true,
                                'message' => 'Insufficient variation stock for Product ID ' . $productId
                            ], 404);
                        }

                        $productVariation->stock -= $quantity;
                        $productVariation->save();

                        $variation = $productVariation->product_variation;
                        $variationOldPrice = $productVariation->old_price;
                        $variationNewPrice = $productVariation->price;
                        $variationStock = $productVariation->stock;
                    } else {
                        if ($productModel->quantity < $quantity) {
                            return response()->json([
                                'error' => true,
                                'message' => 'Insufficient stock for Product ID ' . $productId
                            ], 404);
                        }
                        $productModel->quantity -= $quantity;
                        $productModel->save();
                    }

                    // START: clone to order_product_details
                    $orderProductDetails = new OrderProductDetails();
                    $orderProductDetails->product_id = $productModel->id;
                    $orderProductDetails->sku = $productModel->sku;
                    $orderProductDetails->wholesaler_id = $productModel->wholesaler_id ?? null;
                    $orderProductDetails->retailer_id = $productModel->retailer_id ?? null;
                    $orderProductDetails->name = $productModel->name;
                    $orderProductDetails->slug = $productModel->slug;
                    $orderProductDetails->description = $productModel->description;
                    $orderProductDetails->brand_name = $productModel->brand_name;
                    $orderProductDetails->tags = $productModel->tags;
                    $orderProductDetails->product_variation = $variation ?? null;
                    $orderProductDetails->quantity = $variationStock ? $variationStock : $productModel->quantity;
                    $orderProductDetails->old_price = $variationOldPrice ? $variationOldPrice : $productModel->old_price;
                    $orderProductDetails->new_price = $variationNewPrice ? $variationNewPrice : $productModel->new_price;
                    $orderProductDetails->discount_price = $productModel->discount_price;
                    $orderProductDetails->images = $productModel->images;
                    $orderProductDetails->videos = $productModel->videos;
                    $orderProductDetails->url = $productModel->url;
                    $orderProductDetails->status = $productModel->status;
                    $orderProductDetails->color = $productModel->color;
                    $orderProductDetails->size = $productModel->size;
                    $orderProductDetails->specifications = $productModel->specifications;
                    $orderProductDetails->category_id = $productModel->category_id;
                    $orderProductDetails->category_name = $productModel->category->category_name ?? null;
                    $orderProductDetails->sub_category_id = $productModel->sub_category_id;
                    $orderProductDetails->sub_category_name = $productModel->sub_category->sub_category_name ?? null;
                    $orderProductDetails->meta_title = $productModel->meta_title;
                    $orderProductDetails->meta_description = $productModel->meta_description;
                    $orderProductDetails->meta_keywords = $productModel->meta_keywords;
                    $orderProductDetails->save();
                    // END: clone to order_product_details
                } else {
                    $retailerProduct = RetailerCloneProduct::with('productVariations')
                        ->where('id', $cloneId)
                        ->where('retailer_id', $retailerId)
                        ->where('status', 'active')
                        ->first();
                    if (!$retailerProduct) {
                        return response()->json([
                            'error' => true,
                            'message' => 'Retailer Product ID ' . $cloneId . ' not found.'
                        ], 404);
                    }

                    if ($retailerProduct->productVariations->isNotEmpty()) {
                        if (empty($product['product_variation'])) {
                            return response()->json([
                                'error' => true,
                                'message' => 'Product variation is required for Retailer Product ID ' . $cloneId
                            ], 422);
                        }

                        $productVariation = ProductVariation::where('product_id', $cloneId)->where('product_variation', $product['product_variation'])->first();
                        if (!$productVariation) {
                            return response()->json([
                                'error' => true,
                                'message' => 'There is no any variation as ' . $product['product_variation'] . ' for Retailer Product ID ' . $cloneId
                            ], 404);
                        }
                        if ($productVariation->stock < $quantity) {
                            return response()->json([
                                'error' => true,
                                'message' => 'Insufficient variation stock for Retailer Product ID ' . $cloneId
                            ], 404);
                        }

                        $productVariation->stock -= $quantity;
                        $productVariation->save();

                        $variation = $productVariation->product_variation;
                        $variationOldPrice = $productVariation->old_price;
                        $variationNewPrice = $productVariation->price;
                        $variationStock = $productVariation->stock;
                    } else {
                        if ($retailerProduct->quantity < $quantity) {
                            return response()->json([
                                'error' => true,
                                'message' => 'Insufficient stock for Retailer Product ID ' . $cloneId
                            ], 404);
                        }
                        $retailerProduct->quantity -= $quantity;
                        $retailerProduct->save();
                    }

                    // START: clone to order_product_details
                    $orderProductDetails = new OrderProductDetails();
                    $orderProductDetails->product_id = $retailerProduct->id;
                    $orderProductDetails->sku = $retailerProduct->sku;
                    $orderProductDetails->wholesaler_id = $retailerProduct->wholesaler_id ?? null;
                    $orderProductDetails->retailer_id = $retailerProduct->retailer_id ?? null;
                    $orderProductDetails->name = $retailerProduct->name;
                    $orderProductDetails->slug = $retailerProduct->slug;
                    $orderProductDetails->description = $retailerProduct->description;
                    $orderProductDetails->brand_name = $retailerProduct->brand_name;
                    $orderProductDetails->tags = $retailerProduct->tags;
                    $orderProductDetails->product_variation = $variation ?? null;
                    $orderProductDetails->quantity = $variationStock ? $variationStock : $retailerProduct->quantity;
                    $orderProductDetails->old_price = $variationOldPrice ? $variationOldPrice : $retailerProduct->old_price;
                    $orderProductDetails->new_price = $variationNewPrice ? $variationNewPrice : $retailerProduct->new_price;
                    $orderProductDetails->discount_price = $retailerProduct->discount_price;
                    $orderProductDetails->images = $retailerProduct->images;
                    $orderProductDetails->videos = $retailerProduct->videos;
                    $orderProductDetails->url = $retailerProduct->url;
                    $orderProductDetails->status = $retailerProduct->status;
                    $orderProductDetails->color = $retailerProduct->color;
                    $orderProductDetails->size = $retailerProduct->size;
                    $orderProductDetails->specifications = $retailerProduct->specifications;
                    $orderProductDetails->category_id = $retailerProduct->category_id;
                    $orderProductDetails->category_name = $retailerProduct->category->category_name ?? null;
                    $orderProductDetails->sub_category_id = $retailerProduct->sub_category_id;
                    $orderProductDetails->sub_category_name = $retailerProduct->sub_category->sub_category_name ?? null;
                    $orderProductDetails->meta_title = $retailerProduct->meta_title;
                    $orderProductDetails->meta_description = $retailerProduct->meta_description;
                    $orderProductDetails->meta_keywords = $retailerProduct->meta_keywords;
                    $orderProductDetails->save();
                    // END: clone to order_product_details
                }

                $orderItems[] = [
                    'order_id' => $orderID,
                    'customer_id' => $customerDetail->id,
                    'order_product_id' => $orderProductDetails->id,
                    'product_id' => $productId,
                    'retailer_clone_product_id' => $cloneId,
                    'retailer_id' => $retailerId,
                    'wholesaler_id' => $wholesalerId,
                    'product_variation' => $variation ?? null,
                    'quantity' => $quantity,
                    'final_amount' => $product['final_amount'],
                    'order_process_by' => 'retailer',
                    'payment_method' => $request->payment_method,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                $orderItemsForMail[] = [
                    'firstname' => $request->firstname,
                    'lastname' => $request->lastname,
                    'phone_number' => $request->phone_number,
                    'email' => $request->email ?? null,
                    'address' => $request->address,
                    'state' => $request->state,
                    'city' => $request->city,
                    'pincode' => $request->pincode,
                    'order_id' => $orderID,
                    'product_name' => $orderProductDetails->name,
                    'product_image' => $orderProductDetails->images,
                    'product_variation' => $variation ?? null,
                    'quantity' => $quantity,
                    'final_amount' => $product['final_amount'],
                    'payment_method' => $request->payment_method,
                ];
            }

            CustomerOrders::insert($orderItems);
            $userId = isset($retailerId) ? $retailerId : $wholesalerId;
            $type = isset($retailerId) ? 'retailer-notification' : 'wholesaler-notification';

            OrderNotification::insert([
                'user_id' => $userId,
                'order_id' => $orderID,
                'type' => $type,
                'message' => 'New Order Placed',
                'is_read' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            OrderNotification::insert([
                'user_id' => 1,
                'order_id' => $orderID,
                'type' => 'admin-notification',
                'message' => 'New Order Placed',
                'is_read' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            if (!empty($retailer->retailer->email)) {
                Mail::to($retailer->retailer->email)->send(new RetailerOrderMail($orderItemsForMail, $retailer->retailer));
            }

            return response()->json([
                'success' => true,
                'order_id' => $orderIDs,
                'message' => 'Your order has been placed successfully!'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout Error:', ['error' => $e->getMessage()]);
            return response()->json([
                // 'error' => 'Something went wrong!'
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function formatProductFromClone($product, $finalPrice)
    {
        $oldPriceRange = null;
        $newPriceRange = null;
        $finalPriceRange = null;
        $totalStock = 0;

        if (!empty($product->productVariations) && $product->productVariations->isNotEmpty()) {
            $oldPrices = $product->productVariations->pluck('old_price')->filter()->map(fn($v) => (float)$v);
            $newPrices = $product->productVariations->pluck('price')->filter()->map(fn($v) => (float)$v);
            $finalPrices = $product->productVariations->pluck('final_price')->filter()->map(fn($v) => (float)$v);
            $totalStock = $product->productVariations->sum('stock');

            $oldPriceRange = $oldPrices->isNotEmpty()
                ? number_format($oldPrices->min(), 2) . ' - ' . number_format($oldPrices->max(), 2)
                : null;

            $newPriceRange = $newPrices->isNotEmpty()
                ? number_format($newPrices->min(), 2) . ' - ' . number_format($newPrices->max(), 2)
                : null;

            $finalPriceRange = $finalPrices->isNotEmpty()
                ? number_format($finalPrices->min(), 2) . ' - ' . number_format($finalPrices->max(), 2)
                : null;
        }

        $product_image = explode(',', $product->images);
        $product_image_array = [];
        foreach ($product_image as $image) {
            if (!empty($image)) {
                $product_image_array[] = Storage::disk('spaces')->url($image);
            }
        }
        $product_images_implode = implode(',', $product_image_array);

        return [
            'id' => $product->id,
            'sku' => $product->sku,
            'name' => $product->name,
            'slug' => $product->slug,
            'wholesaler_id' => $product->wholesaler_id ?? null,
            'retailer_id' => $product->retailer_id ?? null,
            'description' => $product->description,
            'tags' => $product->tags,
            'quantity' => $totalStock ?: $product->quantity,
            'old_price' => $oldPriceRange ?: (float) $product->old_price,
            'new_price' => $newPriceRange ?: (float) $product->new_price,
            'final_price' => $finalPriceRange ?: (float) $finalPrice,
            'product_images' => $product_images_implode,
            'product_video' => $product->videos ? Storage::disk('spaces')->url($product->videos) : '',
            'product_url' => $product->url,
            'status' => $product->status,
            'color' => $product->color ?? null,
            'size' => $product->size ?? null,
            'specifications' => $product->specifications ?? null,
            'category_id' => $product->category_id ?? null,
            'sub_category_id' => $product->sub_category_id,
            'meta_title' => $product->meta_title,
            'meta_description' => $product->meta_description,
            'meta_keywords' => $product->meta_keywords,
            'created_at' => $product->created_at,
            'productVariations' => $product->productVariations,
        ];
    }

    // format for signle product
    private function singleFormatRetailerProduct($product, $retailerProduct)
    {
        $newfinalPrice = $product->new_price + $retailerProduct->margin;
        $oldfinalPrice = $product->old_price + $retailerProduct->margin;

        $product_image = explode(',', $product->images);
        $product_image_array = [];
        foreach ($product_image as $image) {
            if (!empty($image)) {
                $product_image_array[] = Storage::disk('spaces')->url($image);
            }
        }
        $product_images_implode = implode(',', $product_image_array);

        return [
            'id'              => $product->id,
            'sku'             => $product->sku,
            'name'            => $product->name,
            'slug'            => $product->slug,
            'description'     => $product->description,
            'tags'     => $product->tags,
            'category_id'     => $product->category_id,
            'sub_category_id'     => $product->sub_category_id,
            'wholesaler_id'   => $product->wholesaler_id,
            'old_price'       => $oldfinalPrice,
            'final_price'     => $newfinalPrice,
            'new_price'       => $product->new_price,
            'final_price'     => $newfinalPrice,
            'quantity'        => $product->quantity,
            'product_images'  => $product_images_implode,
            'product_video'   => $product->videos ? Storage::disk('spaces')->url($product->videos) : '',
            'product_url'     => $product->url,
            'status'     => $product->status,
            'specifications'  => $product->specifications,
            'retailer_id'     => $product->retailer_id ?? null,
            'variations'      => $product->productVariations->map(function ($var) {
                return [
                    'id' => $var->id,
                    'variation' => $var->product_variation,
                    'price'     => $var->price,
                    'stock'     => $var->stock,
                ];
            })->values()
        ];
    }
    private function singleFormatCloneProduct($cloneProduct)
    {
        $newfinalPrice = $cloneProduct->new_price + $cloneProduct->margin;
        $oldfinalPrice = $cloneProduct->old_price + $cloneProduct->margin;

        $product_image = explode(',', $cloneProduct->images);
        $product_image_array = [];
        foreach ($product_image as $image) {
            if (!empty($image)) {
                $product_image_array[] = Storage::disk('spaces')->url($image);
            }
        }
        $product_images_implode = implode(',', $product_image_array);

        return [
            'id'              => $cloneProduct->id,
            'sku'             => $cloneProduct->sku,
            'name'            => $cloneProduct->name,
            'slug'            => $cloneProduct->slug,
            'description'     => $cloneProduct->description,
            'tags'     => $cloneProduct->tags,
            'category_id'     => $cloneProduct->category_id ?? null,
            'sub_category_id'     => $cloneProduct->sub_category_id ?? null,
            'wholesaler_id'   => null,
            'old_price'       => $oldfinalPrice,
            'new_price'       => $cloneProduct->new_price,
            'final_price'     => $newfinalPrice,
            'quantity'        => $cloneProduct->quantity,
            'product_images'  => $product_images_implode,
            'product_video'   => $cloneProduct->videos ? Storage::disk('spaces')->url($cloneProduct->videos) : '',
            'product_url'     => $cloneProduct->url,
            'status'     => $cloneProduct->status,
            'specifications'  => $cloneProduct->specifications,
            'retailer_id'     => $cloneProduct->retailer_id ?? null,
            'variations'      => $cloneProduct->productVariations->map(function ($var) {
                return [
                    'id' => $var->id,
                    'variation' => $var->product_variation,
                    'price'     => $var->price,
                    'stock'     => $var->stock,
                ];
            })->values()
        ];
    }
}
