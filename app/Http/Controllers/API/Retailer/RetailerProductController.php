<?php

namespace App\Http\Controllers\API\Retailer;

use App\Http\Controllers\Controller;
use App\Mail\RetailerOrderMail;
use App\Mail\WelcomeCustomerMail;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\CustomerCart;
use App\Models\CustomerDetails;
use App\Models\CustomerOrders;
use App\Models\OrderProductDetails;
use App\Models\RetailerProducts;
use App\Models\RetailerCloneProduct;
use App\Models\RetailerWebManagement;
use App\Models\StoreCustomersDetails;
use App\Models\UserDetail;
use App\Models\Otp;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\User;
use App\Models\RetailerCategory;
use App\Models\SubCategory;
use App\Models\OrderNotification;
use Auth;
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
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Services\OtpService;
use Illuminate\Support\Facades\Cache;
use App\Helpers\ApiResponse;
use App\Models\RetailerWebsiteEnquiry;

use function Laravel\Prompts\error;

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
            $subCategories = SubCategory::with([
                'retailer_categories' => function ($q) use ($storeinfo) {
                    $q->where('retailer_id', $storeinfo->retailer_id);
                }
            ])
                ->whereHas('retailer_categories', function ($q) use ($storeinfo) {
                    $q->where('retailer_id', $storeinfo->retailer_id);
                })
                ->get();

            $subCategoryList = [];

            foreach ($subCategories as $sub_category) {
                $retailerCategory = $sub_category->retailer_categories->first();

                $image = '';

                if ($retailerCategory && $retailerCategory->category_image) {
                    // ✅ Priority 1: image from retailer_categories
                    $image = Storage::disk('spaces')->url($retailerCategory->category_image);
                } elseif ($retailerCategory && $retailerCategory->category_id) {
                    // 🔁 Priority 2: image from categories table
                    $category = Category::find($retailerCategory->category_id);
                    if ($category && $category->category_image) {
                        $image = Storage::disk('spaces')->url($category->category_image);
                    }
                } elseif ($retailerCategory && $retailerCategory->sub_category_id) {
                    // 🔁 Priority 3: image from sub_categories table
                    $fallbackSubCategory = SubCategory::find($retailerCategory->sub_category_id);
                    if ($fallbackSubCategory && $fallbackSubCategory->category_image) {
                        $image = Storage::disk('spaces')->url($fallbackSubCategory->category_image);
                    }
                }

                $subCategoryList[] = [
                    'id' => $sub_category->id,
                    'name' => $sub_category->sub_category_name,
                    'image' => $image,
                ];
            }


            return response()->json([
                'success' => true,
                'storeinfo' => $storeinfo,
                'sub_category_list' => $subCategoryList
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching retailer company info: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getProducts(Request $request)
    {
        try {
            //<------------- validate user ---------------->
            $apiKey = $request->header('API-KEY');

            if (!$apiKey) {
                return ApiResponse::error('API Key is required');
            }

            $retailer = RetailerWebManagement::with([
                'retailer' => function ($query) {
                    $query->where('is_delete', 0)->where('status', 1);
                }
            ])
                ->whereHas('retailer', function ($query): void {
                    $query->where('is_delete', 0)->where('status', 1);
                })
                ->where('product_listing_key', $apiKey)
                ->first();
            if (!$retailer) {
                return ApiResponse::error('Unauthorized: Invalid API Key');
            }

            $retailerId = $retailer->retailer_id;
            $retailerUser = User::where('id', $retailerId)->where('status', 1)->where('is_delete', 0)->first();
            if (!$retailerUser) {
                return ApiResponse::error('Retailer user not found');
            }

            // <---------------- get product data ---------------------->
            $retailerProducts = collect();
            $retailerSubscribedProducts = collect();
            $retailerEditedProducts = collect();
            if ($retailerUser->is_all_wholesaler_visible == 1) {
                $retailerProductQuery = RetailerProducts::where('retailer_id', $retailerId)->get();

                // to get edited wholesaler products-list
                $retailerEditedProducts = $retailerProductQuery->whereNotNull('product_id')->values();

                // to get non-edited wholesaler products
                $retailerSubscribedProducts = $retailerProductQuery->whereNull('product_id')->values();
                $retailerProducts = $retailerSubscribedProducts->flatMap(function ($data) {
                    return Product::with('productVariations:id,product_id,product_variation,old_price,price,stock')
                        ->where('wholesaler_id', $data->wholesaler_id)
                        ->where('sub_category_id', $data->sub_category_id)
                        ->where('status', 'active')
                        ->get();
                });
            }

            $retailerCloneProducts = RetailerCloneProduct::with('productVariations:id,product_id,product_variation,old_price,price,stock')
                ->where('retailer_id', $retailer->retailer_id)
                ->where('status', 'active')
                ->get();

            $allProducts = collect($retailerProducts)->concat($retailerCloneProducts);

            $subCategoryIds = $request->sub_category ? explode(',', $request->sub_category) : '';
            $minPrice = (float) $request->min_price;
            $maxPrice = (float) $request->max_price;
            $sizes = $request->has('size') ? explode(',', $request->size) : [];


            // <---------------- map product data ---------------------->
            $products = $allProducts->map(function ($item) use ($retailerSubscribedProducts, $retailerEditedProducts) {
                $margin = 0;
                foreach ($retailerEditedProducts as $editedProduct) {
                    if ($item->id == $editedProduct->product_id) {
                        if ($editedProduct->margin) {
                            $margin = $editedProduct->margin;
                            break;
                        }
                    }
                }
                if ($margin == 0) {
                    foreach ($retailerSubscribedProducts as $pair) {
                        if ($pair->wholesaler_id == $item->wholesaler_id && $pair->sub_category_id == $item->sub_category_id) {
                            $margin = $pair->margin;
                            break;
                        }
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
            })->filter(function ($item) use ($subCategoryIds, $minPrice, $maxPrice, $sizes, $retailerEditedProducts,) {
                //<------ DEFAULT FILTER - single product Inactive or Delete check ------>
                foreach ($retailerEditedProducts as $editedProduct) {
                    if ($item->id == $editedProduct->product_id) {
                        if ($editedProduct->product_status == 'inactive' || $editedProduct->is_deleted_product == 1) {
                            return false;
                        }
                    }
                }

                //<------ FILTER - sub_category ------>
                if (!empty($subCategoryIds) && !in_array($item->sub_category_id, $subCategoryIds)) {
                    return false;
                }

                //<------ FILTER - min_price ------>
                if ($minPrice) {
                    if (!empty($item->productVariations) && $item->productVariations->isNotEmpty()) {
                        $hasMinMatch = $item->productVariations->contains(function ($variation) use ($minPrice) {
                            return $variation->final_price >= $minPrice;
                        });
                        if (!$hasMinMatch) {
                            return false;
                        }
                    } else {
                        if ($item->final_price < $minPrice) {
                            return false;
                        }
                    }
                }

                //<------ FILTER - max_price ------>
                if ($maxPrice) {
                    if (!empty($item->productVariations) && $item->productVariations->isNotEmpty()) {
                        $hasMaxMatch = $item->productVariations->contains(function ($variation) use ($maxPrice) {
                            return $variation->final_price <= $maxPrice;
                        });
                        if (!$hasMaxMatch) {
                            return false;
                        }
                    } else {
                        if ($item->final_price > $maxPrice) {
                            return false;
                        }
                    }
                }

                // ✅ Size filter (newly added)
                if (!empty($sizes)) {
                    if (!empty($item->productVariations) && $item->productVariations->isNotEmpty()) {
                        $hasSizeMatch = $item->productVariations->contains(function ($variation) use ($sizes) {
                            return in_array(strtoupper(trim($variation->product_variation)), $sizes);
                        });
                        if (!$hasSizeMatch) {
                            return false;
                        }
                    } else {
                        return false;
                    }
                }

                return true;
            })->map(function ($item) use ($retailerEditedProducts) {
                return $this->formatProductFromClone($item, $item->final_price, $retailerEditedProducts);
            })->values();

            //<------ Default Filters ------>
            $products = $products->sortBy([
                // retailer own product at top and wholesaler's subscribed product at last
                fn($a, $b) => is_null($b['wholesaler_id']) <=> is_null($a['wholesaler_id']),

                // out-of-stock products at last
                fn($a, $b) => ($a['quantity'] == 0) <=> ($b['quantity'] == 0),
            ])->values();

            //<------ FILTER - short_by ------>
            $sortBy = $request->sort_by;
            if ($sortBy === 'price_high_to_low') {
                $products = $products->sortByDesc('final_price')->values();
            } elseif ($sortBy === 'price_low_to_high') {
                $products = $products->sortBy('final_price')->values();
            } elseif ($sortBy === 'recently_added') {
                $products = $products->sortByDesc('created_at')->values();
            }

            $subCategoryIds = $products->pluck('sub_category_id')->filter()->unique();
            $subCategories = SubCategory::select('id', 'category_id', 'sub_category_name')
                ->whereIn('id', $subCategoryIds)
                ->where('status', 1)
                ->get();

            //<------------ handle single product response --------------->
            if ($request->has('product_id')) {
                $single = $products->where('id', $request->product_id)->first();
                if (!$single) {
                    return ApiResponse::error('Product not found');
                }
                return ApiResponse::success(['product' => $single], 'Product fetched successfully');
            }

            //<------------ FILTER : By Size (S,M,XL etc) --------------->



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
            return ApiResponse::success([
                'products' => $paginatedProducts,
                'sub_categories' => $subCategories,
            ], 'Products fetched successfully');
        } catch (Exception $e) {
            Log::error('Error in getRetailerProducts: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);

            // return response()->json(['error' => $e->getMessage(), $e->getLine()], 500);
            return ApiResponse::error('An unexpected error occurred');
        }
    }

    public function getNewArrivals(Request $request)
    {
        try {
            // <------------- Validate API Key ---------------->
            $apiKey = $request->header('API-KEY');

            if (!$apiKey) {
                return ApiResponse::error('API Key is required');
            }

            $retailer = RetailerWebManagement::with(['retailer' => function ($query) {
                $query->where('is_delete', 0)->where('status', 1);
            }])
                ->whereHas('retailer', function ($query) {
                    $query->where('is_delete', 0)->where('status', 1);
                })
                ->where('product_listing_key', $apiKey)
                ->first();

            if (!$retailer) {
                return ApiResponse::error('Unauthorized: Invalid API Key');
            }

            $retailerId = $retailer->retailer_id;

            // <------------- Fetch Latest 12 In-Stock Products (Clone + Subscribed) -------------->
            $products = collect();

            // Fetch retailer’s own (cloned) products
            $cloneProducts = RetailerCloneProduct::with('productVariations')
                ->where('retailer_id', $retailerId)
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->get();

            // Filter in-stock cloned products
            $cloneProducts = $cloneProducts->filter(function ($item) {
                if ($item->productVariations->isNotEmpty()) {
                    return $item->productVariations->sum('stock') > 0;
                }
                return $item->stock > 0;
            });

            $products = $products->concat($cloneProducts);

            // If `is_all_wholesaler_visible`, fetch wholesaler products
            $retailerUser = User::find($retailerId);
            if ($retailerUser && $retailerUser->is_all_wholesaler_visible == 1) {
                $subscriptions = RetailerProducts::where('retailer_id', $retailerId)->get();

                foreach ($subscriptions as $sub) {
                    $wholesalerProducts = Product::with('productVariations')
                        ->where('wholesaler_id', $sub->wholesaler_id)
                        ->where('sub_category_id', $sub->sub_category_id)
                        ->where('status', 'active')
                        ->orderBy('created_at', 'desc')
                        ->get();

                    $inStock = $wholesalerProducts->filter(function ($item) {
                        if ($item->productVariations->isNotEmpty()) {
                            return $item->productVariations->sum('stock') > 0;
                        }
                        return $item->stock > 0;
                    });

                    $products = $products->concat($inStock);
                }
            }

            // Sort all combined by latest created
            $products = $products->sortByDesc('created_at')->take(12)->values();

            // Format each product (reuse existing logic if available)
            $formatted = $products->map(function ($item) {

                $productType = $item instanceof \App\Models\RetailerCloneProduct ? 'retailer' : 'wholesaler';
                $formatted = $this->formatProductFromClone($item, $item->final_price ?? $item->new_price ?? 0, collect(), true);

                // Get first image
                $images = explode(',', $formatted['product_images'] ?? '');
                $firstImage = !empty($images[0]) ? trim($images[0]) : '';

                if ($firstImage) {
                    $formatted['product_images'] = Storage::disk('spaces')->url($firstImage);
                } else {
                    // Mark as no image
                    $formatted['product_images'] = null;
                }

                // Format video if exists
                $formatted['product_video'] = !empty($formatted['product_video'])
                    ? Storage::disk('spaces')->url($formatted['product_video'])
                    : '';

                $formatted['product_type'] = $productType;
                return $formatted;
            })->filter(function ($product) {
                // Only keep products that have at least one image
                return !empty($product['product_images']);
            })->values();


            return ApiResponse::success([
                'products' => $formatted,
            ], 'New arrival products fetched successfully');
        } catch (Exception $e) {
            Log::error('Error in getNewArrivals: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ApiResponse::error('An unexpected error occurred');
        }
    }

    public function searchProducts(Request $request)
    {
        try {
            // validate API key
            $apiKey = $request->header('API-KEY');
            if (!$apiKey) {
                return ApiResponse::error('API Key is required');
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
                })->where('product_listing_key', $apiKey)
                ->first();
            if (!$retailer) {
                return ApiResponse::error('Unauthorized: Invalid API Key');
            }

            $retailerId = $retailer->retailer_id;
            $retailerUser = User::where('id', $retailerId)->where('status', 1)->where('is_delete', 0)->first();
            if (!$retailerUser) {
                return response()->json(['error' => 'Retailer user not found.'], 404);
            }

            // <---------------- get product data ---------------------->
            $retailerProducts = collect();
            $retailerEditedProducts = collect();
            if ($retailerUser->is_all_wholesaler_visible == 1) {
                $retailerProductQuery = RetailerProducts::where('retailer_id', $retailerId)->get();

                // to get edited wholesaler products-list
                $retailerEditedProducts = $retailerProductQuery->whereNotNull('product_id')->values();

                // to get non-edited wholesaler products
                $pairs = $retailerProductQuery->whereNull('product_id')->values();
                $sqlRetailerProducts = Product::with(['wholesaler', 'productVariations:id,product_id,product_variation,old_price,price,stock'])
                    ->where('status', 'active')
                    ->where(function ($query) use ($pairs) {
                        foreach ($pairs as $pair) {
                            $query->orWhere(function ($q) use ($pair) {
                                $q->where('wholesaler_id', $pair->wholesaler_id)
                                    ->where('sub_category_id', $pair->sub_category_id);
                            });
                        }
                    });

                // search product by its name
                $searchProductIds = collect();
                if (!empty($request->search)) {
                    $searchProductIds = $retailerProductQuery
                        ->whereNotNull('product_id')
                        ->filter(function ($item) use ($request) {
                            return stripos($item->product_name, $request->search) !== false;
                        })
                        ->pluck('product_id')
                        ->unique()
                        ->values();
                }
                if (!empty($request->search)) {
                    $sqlRetailerProducts->where(function ($query) use ($request, $searchProductIds) {
                        $query->where('name', 'like', '%' . $request->search . '%');

                        if ($searchProductIds->isNotEmpty()) {
                            $query->orWhereIn('id', $searchProductIds);
                        }
                    });
                }

                $retailerProducts = $sqlRetailerProducts->get();
            }

            // Retailer cloned products with search
            $sqlRetailerCloneProducts = RetailerCloneProduct::with('productVariations:id,product_id,product_variation,old_price,price,stock')
                ->where('retailer_id', $retailerId)
                ->where('status', 'active');

            if (!empty($request->search)) {
                $sqlRetailerCloneProducts->where('name', 'like', '%' . $request->search . '%');
            }

            $retailerCloneProducts = $sqlRetailerCloneProducts->get();

            $allProducts = collect($retailerProducts)->concat($retailerCloneProducts);

            // <---------------- map product data ---------------------->
            $products = $allProducts->map(function ($item) use ($pairs, $retailerEditedProducts) {
                $margin = 0;
                foreach ($retailerEditedProducts as $editedProduct) {
                    if ($item->id == $editedProduct->product_id) {
                        if ($editedProduct->margin) {
                            $margin = $editedProduct->margin;
                            break;
                        }
                    }
                }
                if ($margin == 0) {
                    foreach ($pairs as $pair) {
                        if ($pair->wholesaler_id == $item->wholesaler_id && $pair->sub_category_id == $item->sub_category_id) {
                            $margin = $pair->margin;
                            break;
                        }
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
            })->filter(function ($item) use ($retailerEditedProducts) {
                //<------ DEFAULT FILTER - single product Inactive or Delete check ------>
                foreach ($retailerEditedProducts as $editedProduct) {
                    if ($item->id == $editedProduct->product_id) {
                        if ($editedProduct->product_status == 'inactive' || $editedProduct->is_deleted_product == 1) {
                            return false;
                        }
                    }
                }
                return true;
            })->map(function ($item) use ($retailerEditedProducts) {
                return $this->formatProductFromClone($item, $item->final_price, $retailerEditedProducts);
            })->values();

            //<------ Default Filters ------>
            $products = $products->sortBy([
                // retailer own product at top and wholesaler's subscribed product at last
                fn($a, $b) => is_null($b['wholesaler_id']) <=> is_null($a['wholesaler_id']),

                // out-of-stock products at last
                fn($a, $b) => ($a['quantity'] == 0) <=> ($b['quantity'] == 0),
            ])->values();

            $subCategoryIds = $products->pluck('sub_category_id')->filter()->unique();
            $subCategories = SubCategory::select('id', 'category_id', 'sub_category_name')
                ->whereIn('id', $subCategoryIds)
                ->where('status', 1)
                ->get();

            //<------------ handle single product response --------------->
            if ($request->has('product_id')) {
                $single = $products->where('id', $request->product_id)->first();
                if (!$single) {
                    return ApiResponse::error('Product not found');
                }

                return ApiResponse::success(['product' => $single], 'Product fetched successfully');
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
            return ApiResponse::success([
                'products' => $paginatedProducts,
                'sub_categories' => $subCategories,
            ], 'Products fetched successfully');
        } catch (\Exception $e) {
            \Log::error('Error in getRetailerProducts: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ApiResponse::error('An unexpected error occurred');
        }
    }

    public function getSingalProductDetails(Request $request, $slug = null)
    {

        try {
            //<------------- validate user ---------------->
            $apiKey = $request->header('API-KEY');
            if (!$apiKey) {
                return ApiResponse::error('API Key is required.');
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
                })->where('product_listing_key', $apiKey)
                ->first();
            if (!$retailer) {
                return ApiResponse::error('Unauthorized: Invalid API Key.');
            }

            if (!$slug) {
                return ApiResponse::error('Product slug is required.');
            }

            $retailerId = $retailer->retailer_id;
            $retailerUser = User::where('id', $retailerId)->where('status', 1)->where('is_delete', 0)->first();
            if (!$retailerUser) {
                return ApiResponse::error('Retailer user not found.');
            }

            // <---------------- get product data ---------------------->
            $retailerSubscribedProducts = collect();
            $retailerEditedProducts = collect();
            if ($retailerUser->is_all_wholesaler_visible == 1) {
                $retailerProductQuery = RetailerProducts::where('retailer_id', $retailerId)->get();

                // to get edited wholesaler products-list
                $retailerEditedProducts = $retailerProductQuery->whereNotNull('product_id')->values();

                // to get non-edited wholesaler products
                $retailerSubscribedProducts = $retailerProductQuery->whereNull('product_id')->values();
            }

            $product = RetailerCloneProduct::with('productVariations:id,product_id,product_variation,old_price,price,stock')
                ->where('retailer_id', $retailerId)
                ->where('slug', $slug)
                ->where('status', 'active')
                ->first();

            if (!$product && $retailerUser->is_all_wholesaler_visible == 1) {
                $product = Product::with(['wholesaler', 'productVariations:id,product_id,product_variation,old_price,price,stock'])
                    ->where('status', 'active')
                    ->where('slug', $slug)
                    ->where(function ($query) use ($retailerSubscribedProducts) {
                        foreach ($retailerSubscribedProducts as $pair) {
                            $query->orWhere(fn($q) => $q->where('wholesaler_id', $pair->wholesaler_id)
                                ->where('sub_category_id', $pair->sub_category_id));
                        }
                    })
                    ->first();

                if (!$product) {
                    $signleProudct = RetailerProducts::where('product_slug', $slug)
                        ->where('product_status', 'active')
                        ->where('is_deleted_product', 0)
                        ->first();

                    if ($signleProudct) {
                        $product = Product::with(['wholesaler', 'productVariations:id,product_id,product_variation,old_price,price,stock'])
                            ->where('status', 'active')
                            ->where('id', $signleProudct->product_id)
                            ->first();
                    }
                }
            }

            if (!$product) {
                return ApiResponse::error('Product not found.');
            }

            $margin = 0;
            foreach ($retailerEditedProducts as $editedProduct) {
                if ($product->id == $editedProduct->product_id) {
                    if ($editedProduct->margin) {
                        $margin = $editedProduct->margin;
                        break;
                    }
                }
            }
            if ($margin == 0) {
                if ($retailerSubscribedProducts->isNotEmpty() && isset($product->wholesaler_id, $product->sub_category_id)) {
                    foreach ($retailerSubscribedProducts as $pair) {
                        if ($pair->wholesaler_id == $product->wholesaler_id && $pair->sub_category_id == $product->sub_category_id) {
                            $margin = $pair->margin;
                            break;
                        }
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

            $formatted_product = $this->formatProductFromClone($product, $product->final_price ?? 0, $retailerEditedProducts);

            // <---------------- return data ---------------------->

            return ApiResponse::success(['product' => $formatted_product], 'Products fetched successfully');
        } catch (\Exception $e) {
            \Log::error('Get product detail error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);
            return ApiResponse::error('An unexpected error occurred');
        }
    }

    public function checkoutNew(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {

            // ========== GUEST USER FLOW ==========

            if (!$user) {
                $request->validate([
                    'phone_number' => 'required|digits:10',
                ]);

                // Check if this number has been verified
                if (!Cache::get('otp_verified_' . $request->phone_number)) {

                    return ApiResponse::error('Phone number not verified. Please verify via OTP first.');
                }

                // Optionally clear verification after use
                // Cache::forget('otp_verified_' . $request->phone_number);
            }

            // Step 3: Check if customer already exists
            $existCustomer = CustomerDetails::where('phone_number', $request->phone_number)->first();

            if ($existCustomer) {

                $missingFields = [];

                $existCustomer->address = $request->input('address', $existCustomer->address);
                $existCustomer->state = $request->input('state', $existCustomer->state);
                $existCustomer->city = $request->input('city', $existCustomer->city);
                $existCustomer->pincode = $request->input('pincode', $existCustomer->pincode);

                $missingFields = [];

                if (empty(trim($existCustomer->address))) {
                    $missingFields[] = 'Address is required.';
                }
                if (empty(trim($existCustomer->state))) {
                    $missingFields[] = 'State is required.';
                }
                if (empty(trim($existCustomer->city))) {
                    $missingFields[] = 'City is required.';
                }
                if (empty(trim($existCustomer->pincode))) {
                    $missingFields[] = 'Pincode is required.';
                }

                if (!empty($missingFields)) {
                    return ApiResponse::error("Missing required fields:", $missingFields);
                }

                $existCustomer->save();

                $customerId = $existCustomer->id;
                $customerDetails = $existCustomer;
            } else {
                // Step 4: Create new customer

                $rules = [
                    'firstname'   => 'required|string|max:30',
                    'lastname'    => 'required|string|max:30',
                    'email'       => 'required|email',
                    'address'     => 'required|string|max:250',
                    'state'       => 'required|string',
                    'city'        => 'required|string',
                    'pincode'     => 'required|digits:6',
                    'user_token'  => 'required|string',
                ];

                $messages = [
                    'firstname.required'  => 'First name is required.',
                    'firstname.string'    => 'First name must be a string.',
                    'firstname.max'       => 'First name cannot exceed 30 characters.',
                    'lastname.required'   => 'Last name is required.',
                    'lastname.string'     => 'Last name must be a string.',
                    'lastname.max'        => 'Last name cannot exceed 30 characters.',
                    'email.required'      => 'Email is required.',
                    'email.email'         => 'Please provide a valid email address.',
                    'address.required'    => 'Address is required.',
                    'address.string'      => 'Address must be a valid string.',
                    'address.max'         => 'Address cannot exceed 250 characters.',
                    'state.required'      => 'State is required.',
                    'city.required'       => 'City is required.',
                    'pincode.required'    => 'Pincode is required.',
                    'pincode.digits'      => 'Pincode must be exactly 6 digits.',
                    'user_token.required' => 'User token is required.',
                ];

                $validator = Validator::make($request->all(), $rules, $messages);

                if ($validator->fails()) {
                    return ApiResponse::error(
                        'Validation failed. Please correct the following fields:',
                        $validator->errors()->all() // or use ->errors() for field-wise array
                    );
                }


                // ✅ Check if email already exists in store_customers_details
                $existingCustomer = StoreCustomersDetails::where('email', $request->email)->first();

                if ($existingCustomer) {
                    return ApiResponse::error('Email already registered. Please log in or use a different email.');
                }

                // ✅ Validate user token
                $retailer = RetailerWebManagement::where('product_listing_key', $request->user_token)
                    ->where('is_active', 1)
                    ->first();

                if (!$retailer) {
                    return ApiResponse::error('Invalid user token.');
                }

                $randomPassword = Str::random(10) . '@' . rand(10, 99);
                $hashedPassword = Hash::make($randomPassword);

                // ✅ Create customerDetails
                $customerDetails = CustomerDetails::create([
                    'user_id' => $retailer->retailer_id,
                    'firstname' => $request->firstname,
                    'lastname' => $request->lastname,
                    'email' => $request->email,
                    'phone_number' => $request->phone_number,
                    'address' => $request->address,
                    'state' => $request->state,
                    'city' => $request->city,
                    'pincode' => $request->pincode,
                ]);

                // ✅ Create storeCustomerDetails
                $storeCustomerDetails = StoreCustomersDetails::create([
                    'user_id' => $retailer->retailer_id,
                    'firstname' => $request->firstname,
                    'lastname' => $request->lastname,
                    'phone_number' => $request->phone_number,
                    'email' => $request->email,
                    'password' => $hashedPassword,
                    'customer_id' => $customerDetails->id,
                    'user_token' => $request->user_token,
                    'is_active' => true,
                    'email_verification_token' => null,
                    'email_verified_at' => now(),
                ]);

                // ✅ Send welcome email
                if ($request->email) {
                    Mail::to($storeCustomerDetails->email)
                        ->send(new WelcomeCustomerMail($storeCustomerDetails, $randomPassword));
                }

                $customerId = $customerDetails->id;
            }
        } else {
            // ========== LOGGED-IN USER FLOW ==========
            $customerId = $user->id;
            $customerDetails = CustomerDetails::find($customerId);
            // Check if any required fields are missing in DB
            $missingFields = [];

            if (empty($customerDetails->address)) {
                $missingFields[] = 'address';
            }
            if (empty($customerDetails->state)) {
                $missingFields[] = 'state';
            }
            if (empty($customerDetails->city)) {
                $missingFields[] = 'city';
            }
            if (empty($customerDetails->pincode)) {
                $missingFields[] = 'pincode';
            }

            // If any field is missing in DB, validate from request
            if (!empty($missingFields)) {
                $validationRules = [];
                $customMessages = [];

                foreach ($missingFields as $field) {
                    switch ($field) {
                        case 'pincode':
                            $validationRules[$field] = 'required|digits:6';
                            $customMessages["$field.required"] = 'Pincode is required.';
                            $customMessages["$field.digits"] = 'Pincode must be exactly 6 digits.';
                            break;

                        default:
                            $validationRules[$field] = 'required|string|max:250';
                            $customMessages["$field.required"] = ucfirst($field) . ' is required.';
                            $customMessages["$field.string"] = ucfirst($field) . ' must be a string.';
                            $customMessages["$field.max"] = ucfirst($field) . ' cannot be longer than 250 characters.';
                            break;
                    }
                }

                $validator = Validator::make($request->all(), $validationRules, $customMessages);

                if ($validator->fails()) {
                    return ApiResponse::error(
                        'Order not placed. Required address details are missing or invalid.',
                        $validator->errors()->all()
                    );
                }

                // Update only the missing fields from request
                $updateData = [];
                foreach ($missingFields as $field) {
                    $updateData[$field] = $request->$field;
                }
                $customerDetails->update($updateData);
            }
        }

        $apiKey = $request->header('API-KEY');
        if (!$apiKey) {
            return ApiResponse::error('API Key is required.');
        }

        $retailer = RetailerWebManagement::with([
            'retailer' => function ($query) {
                $query->where('is_delete', 0)->where('status', 1);
            }
        ])->whereHas('retailer', function ($query) {
            $query->where('is_delete', 0)->where('status', 1);
        })->where('product_listing_key', $apiKey)->first();

        if (!$retailer) {
            return ApiResponse::error('Unauthorized: Invalid API Key.');
        }

        DB::beginTransaction();

        try {
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
                $couponid = @$product['coupon_id'];

                if (!$productId && !$cloneId) {
                    return ApiResponse::error('Either product_id or retailer_product_id must be provided.');
                }
                if ($productId && !$wholesalerId) {
                    return ApiResponse::error('wholesaler_id is required for Product ID ' . $productId);
                }
                $variation = null;
                $variationOldPrice = null;
                $variationNewPrice = null;
                $variationStock = null;
                $retailer_margin_amount = 0;
                if ($productId) {
                    $productModel = Product::with('productVariations')
                        ->where('id', $productId)
                        ->where('status', 'active')
                        ->first();
                    if (!$productModel) {

                        return ApiResponse::error('Product ID ' . $productId . ' not found.');
                    }

                    // START : get retailer's margin of this product
                    $retailerMargin = RetailerProducts::where('product_id', $productId)
                        ->where('retailer_id', $retailerId)
                        ->first();
                    if ($retailerMargin) {
                        if ($retailerMargin->product_status == 'inactive' || $retailerMargin->is_deleted_product == 1) {
                            return ApiResponse::success('Product ID ' . $productId . ' is unavailable for sell.');
                        }

                        $retailer_margin_amount = $retailerMargin->margin;
                    } else {
                        $retailerMargin = RetailerProducts::where('retailer_id', $retailerId)
                            ->where('wholesaler_id', $productModel->wholesaler_id)
                            ->where('sub_category_id', $productModel->sub_category_id)
                            ->whereNull('product_id')
                            ->first();

                        if ($retailerMargin) {
                            $retailer_margin_amount = $retailerMargin->margin;
                        }
                    }
                    // END : get retailer's margin of this product

                    if ($productModel->productVariations->isNotEmpty()) {
                        if (empty($product['product_variation'])) {
                            return ApiResponse::error('Product variation is required for Product ID ' . $productId);
                        }

                        $productVariation = ProductVariation::where('product_id', $productId)->where('product_variation', $product['product_variation'])->first();
                        if (!$productVariation) {
                            return ApiResponse::error('There is no any variation as ' . $product['product_variation'] . ' for Product ID ' . $productId);
                        }
                        if ($productVariation->stock < $quantity) {
                            return ApiResponse::error('Insufficient variation stock for Product ID ' . $productId);
                        }

                        $productVariation->stock -= $quantity;
                        $productVariation->save();

                        $variation = $productVariation->product_variation;
                        $variationOldPrice = $productVariation->old_price;
                        $variationNewPrice = $productVariation->price;
                        $variationStock = $productVariation->stock;
                    } else {
                        if ($productModel->quantity < $quantity) {
                            return ApiResponse::error('Insufficient stock for Product ID ' . $productId);
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
                        return ApiResponse::error('Retailer Product ID ' . $cloneId . ' not found.');
                    }

                    if ($retailerProduct->productVariations->isNotEmpty()) {
                        if (empty($product['product_variation'])) {
                            return ApiResponse::error('Product variation is required for Retailer Product ID ' . $cloneId);
                        }

                        $productVariation = ProductVariation::where('product_id', $cloneId)->where('product_variation', $product['product_variation'])->first();
                        if (!$productVariation) {
                            return ApiResponse::error('There is no any variation as ' . $product['product_variation'] . ' for Retailer Product ID ' . $cloneId);
                        }
                        if ($productVariation->stock < $quantity) {
                            return ApiResponse::error('Insufficient variation stock for Retailer Product ID ' . $cloneId);
                        }

                        $productVariation->stock -= $quantity;
                        $productVariation->save();

                        $variation = $productVariation->product_variation;
                        $variationOldPrice = $productVariation->old_price;
                        $variationNewPrice = $productVariation->price;
                        $variationStock = $productVariation->stock;
                    } else {
                        if ($retailerProduct->quantity < $quantity) {
                            return ApiResponse::error('Insufficient stock for Retailer Product ID ' . $cloneId);
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
                    'customer_id' => $customerId,
                    'order_product_id' => $orderProductDetails->id,
                    'product_id' => $productId,
                    'retailer_clone_product_id' => $cloneId,
                    'retailer_id' => $retailerId,
                    'wholesaler_id' => $wholesalerId,
                    'product_variation' => $variation ?? null,
                    'quantity' => $quantity,
                    'retailer_margin_amount' => $retailer_margin_amount,
                    'final_amount' => $product['final_amount'],
                    'order_process_by' => 'retailer',
                    'payment_method' => $request->payment_method,
                    'coupon_applied_id' => @$couponid,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                $coupon = Coupon::find($couponid);
                if ($coupon && $coupon->used_count < $coupon->usage_limit) {
                    $coupon->used_count += 1;
                    $coupon->save();
                }

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

            if ($customerDetails) {
                $token = $customerDetails->createToken('customer-token')->plainTextToken;
                $filtercustomerDetails = collect($customerDetails)->except(['id', 'user_id', 'created_at', 'updated_at']);

                // Get all cart/wishlist entries
                $customerCartItems = CustomerCart::where('customer_id', $customerDetails->id)->get();

                $wishlistItems = [];
                $cartItems = [];

                foreach ($customerCartItems as $item) {
                    $product = null;

                    if (!is_null($item->product_id) && is_null($item->retailer_product_id)) {
                        $product = Product::select('name', 'slug', 'new_price')->find($item->product_id);
                    } elseif (!is_null($item->retailer_product_id) && is_null($item->product_id)) {
                        $product = RetailerCloneProduct::select('name', 'slug', 'new_price')->find($item->retailer_product_id);
                    }

                    if ($product) {
                        if ($item->type === 'wishlist') {
                            $wishlistItems[] = $product;
                        } elseif ($item->type === 'cart') {
                            $cartItems[] = $product;
                        }
                    }
                }
            } else {
                $token = null;
                $filtercustomerDetails = [];
                $wishlistItems = [];
                $cartItems = [];
            }
            $token = $customerDetails->createToken('customer-token')->plainTextToken;

            return ApiResponse::success([
                'order_id' => $orderIDs,
                'token' => $token,
                'customer' => $filtercustomerDetails,
                'wishlist_items' => $wishlistItems,
                'cart_items' => $cartItems,
            ], 'Your order has been placed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout Error:', ['error' => $e->getMessage()]);
            return ApiResponse::error('Something went wrong!');
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
            'products.*.final_amount' => 'required|numeric|min:0'
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

        $retailer = RetailerWebManagement::with([
            'retailer' => function ($query) {
                $query->where('is_delete', 0)->where('status', 1);
            }
        ])->whereHas('retailer', function ($query) {
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
                $couponid = @$product['coupon_id'];

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
                $retailer_margin_amount = 0;
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

                    // START : get retailer's margin of this product
                    $retailerMargin = RetailerProducts::where('product_id', $productId)
                        ->where('retailer_id', $retailerId)
                        ->first();
                    if ($retailerMargin) {
                        if ($retailerMargin->product_status == 'inactive' || $retailerMargin->is_deleted_product == 1) {
                            return response()->json([
                                'error' => true,
                                'message' => 'Product ID ' . $productId . ' is unavailable for sell.'
                            ], 404);
                        }

                        $retailer_margin_amount = $retailerMargin->margin;
                    } else {
                        $retailerMargin = RetailerProducts::where('retailer_id', $retailerId)
                            ->where('wholesaler_id', $productModel->wholesaler_id)
                            ->where('sub_category_id', $productModel->sub_category_id)
                            ->whereNull('product_id')
                            ->first();

                        if ($retailerMargin) {
                            $retailer_margin_amount = $retailerMargin->margin;
                        }
                    }
                    // END : get retailer's margin of this product

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
                    'retailer_margin_amount' => $retailer_margin_amount,
                    'final_amount' => $product['final_amount'],
                    'order_process_by' => 'retailer',
                    'payment_method' => $request->payment_method,
                    'coupon_applied_id' => @$couponid,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                $coupon = Coupon::find($couponid);
                if ($coupon && $coupon->used_count < $coupon->usage_limit) {
                    $coupon->used_count += 1;
                    $coupon->save();
                }

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

    public function applyCoupon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Validation failed', $validator->errors());
        }

        $apiKey = $request->header('API-KEY');
        if (!$apiKey) {
            return ApiResponse::error('API Key is required.');
        }

        // Get retailer by API key
        $retailer = RetailerWebManagement::with([
            'retailer' => function ($query) {
                $query->where('is_delete', 0)->where('status', 1);
            }
        ])->whereHas('retailer', function ($query) {
            $query->where('is_delete', 0)->where('status', 1);
        })->where('product_listing_key', $apiKey)->first();

        if (!$retailer) {
            return ApiResponse::error('Unauthorized: Invalid API Key.');
        }

        // Get coupon by code and retailer
        $coupon = Coupon::where('coupon_code', $request->coupon_code)
            ->where('retailer_id', $retailer->retailer_id)
            ->first();

        if (!$coupon) {
            return ApiResponse::error('Invlaid Coupon code.');
        }

        // Check status
        if ($coupon->status != 1) {
            return ApiResponse::error('This coupon is Invalid.');
        }

        // Check if coupon is expired
        if ($coupon->valid_until && Carbon::now()->gt($coupon->valid_until)) {
            return ApiResponse::error('This coupon has expired.');
        }

        // Check usage limit
        if ($coupon->used_count >= $coupon->usage_limit) {
            return ApiResponse::error('This coupon has been fully used.');
        }

        // All good — "apply" the coupon
        return ApiResponse::success([
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->coupon_code,
                'discount' => $coupon->discount,
                // 'valid_from' => $coupon->valid_from,
                // 'valid_until' => $coupon->valid_until,
            ]
        ], 'Coupon applied successfully.');
    }

    private function formatProductFromClone($product, $finalPrice, $retailerEditedProducts, $filterStock = false)
    {
        $product_name = null;
        $product_slug = null;
        $product_description = null;
        $product_images_fetch = null;
        $product_videos_fetch = null;
        $product_status = null;
        foreach ($retailerEditedProducts as $editedProduct) {
            if ($product->id == $editedProduct->product_id) {
                $product_name = $editedProduct->product_name ?? null;
                $product_slug = $editedProduct->product_slug ?? null;
                $product_description = $editedProduct->product_description ?? null;
                $product_images_fetch = $editedProduct->product_images ?? null;
                $product_videos_fetch = $editedProduct->product_videos ?? null;
                $product_status = $editedProduct->product_status ?? null;
            }
        }
        $oldPriceRange = null;
        $newPriceRange = null;
        $finalPriceRange = null;
        $totalStock = 0;

        if (!empty($product->productVariations) && $product->productVariations->isNotEmpty()) {
            $oldPrices = $product->productVariations->pluck('old_price')->filter()->map(fn($v) => (float) $v);
            $newPrices = $product->productVariations->pluck('price')->filter()->map(fn($v) => (float) $v);

            // FIXED FINAL PRICE HANDLING
            $finalPrices = $product->productVariations->map(function ($v) {
                return isset($v->final_price) && $v->final_price !== null
                    ? (float) $v->final_price
                    : (isset($v->price) ? (float) $v->price : null);
            })->filter();

            $totalStock = $product->productVariations->sum('stock');

            $oldPriceRange = $oldPrices->isNotEmpty() ? round($oldPrices->min(), 2) : null;
            $newPriceRange = $newPrices->isNotEmpty() ? round($newPrices->min(), 2) : null;
            $finalPriceRange = $finalPrices->isNotEmpty() ? round($finalPrices->min(), 2) : 0.0;
        }


        $product_image = explode(',', $product_images_fetch ?? $product->images);
        $product_image_array = [];
        foreach ($product_image as $image) {
            if (!empty($image)) {
                $product_image_array[] = Storage::disk('spaces')->url($image);
            }
        }
        $product_images_implode = implode(',', $product_image_array);

        $product_video = $product_videos_fetch ?? $product->videos;

        return [
            'id' => $product->id,
            'sku' => $product->sku,
            'name' => $product_name ?? $product->name,
            'slug' => $product_slug ?? $product->slug,
            'wholesaler_id' => $product->wholesaler_id ?? null,
            'retailer_id' => $product->retailer_id ?? null,
            'description' => $product_description ?? $product->description,
            'tags' => $product->tags,
            'quantity' => $totalStock ?: $product->quantity,
            'old_price' => $oldPriceRange ?: (float) $product->old_price,
            'new_price' => $newPriceRange ?: (float) $product->new_price,
            'final_price' => $finalPriceRange ?: (float) $finalPrice,
            'product_images' => $product_images_implode,
            'product_video' => $product_video ? Storage::disk('spaces')->url($product_video) : '',
            'product_url' => $product->url,
            'status' => $product_status ?? $product->status,
            'color' => $product->color ?? null,
            'size' => $product->size ?? null,
            'specifications' => $product->specifications ?? null,
            'category_id' => $product->category_id ?? null,
            'sub_category_id' => $product->sub_category_id,
            'meta_title' => $product->meta_title,
            'meta_description' => $product->meta_description,
            'meta_keywords' => $product->meta_keywords,
            'created_at' => $product->created_at,
            'productVariations' => $filterStock
                ? $product->productVariations->filter(fn($v) => $v->stock > 0)->values()
                : $product->productVariations,
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
            'id' => $product->id,
            'sku' => $product->sku,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'tags' => $product->tags,
            'category_id' => $product->category_id,
            'sub_category_id' => $product->sub_category_id,
            'wholesaler_id' => $product->wholesaler_id,
            'old_price' => $oldfinalPrice,
            'final_price' => $newfinalPrice,
            'new_price' => $product->new_price,
            'final_price' => $newfinalPrice,
            'quantity' => $product->quantity,
            'product_images' => $product_images_implode,
            'product_video' => $product->videos ? Storage::disk('spaces')->url($product->videos) : '',
            'product_url' => $product->url,
            'status' => $product->status,
            'specifications' => $product->specifications,
            'retailer_id' => $product->retailer_id ?? null,
            'variations' => $product->productVariations->map(function ($var) {
                return [
                    'id' => $var->id,
                    'variation' => $var->product_variation,
                    'price' => $var->price,
                    'stock' => $var->stock,
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
            'id' => $cloneProduct->id,
            'sku' => $cloneProduct->sku,
            'name' => $cloneProduct->name,
            'slug' => $cloneProduct->slug,
            'description' => $cloneProduct->description,
            'tags' => $cloneProduct->tags,
            'category_id' => $cloneProduct->category_id ?? null,
            'sub_category_id' => $cloneProduct->sub_category_id ?? null,
            'wholesaler_id' => null,
            'old_price' => $oldfinalPrice,
            'new_price' => $cloneProduct->new_price,
            'final_price' => $newfinalPrice,
            'quantity' => $cloneProduct->quantity,
            'product_images' => $product_images_implode,
            'product_video' => $cloneProduct->videos ? Storage::disk('spaces')->url($cloneProduct->videos) : '',
            'product_url' => $cloneProduct->url,
            'status' => $cloneProduct->status,
            'specifications' => $cloneProduct->specifications,
            'retailer_id' => $cloneProduct->retailer_id ?? null,
            'variations' => $cloneProduct->productVariations->map(function ($var) {
                return [
                    'id' => $var->id,
                    'variation' => $var->product_variation,
                    'price' => $var->price,
                    'stock' => $var->stock,
                ];
            })->values()
        ];
    }

    public function customerOrders()
    {
        try {
            $user = auth()->user();
            $customerDetails = CustomerDetails::where('id', $user->id)->first();
            $orders = CustomerOrders::where('customer_id', $customerDetails->id)->get();

            if ($orders->isEmpty()) {
                return ApiResponse::error('You have not placed any orders yet.');
            }

            $orderList = [];
            $customerData = [];


            foreach ($orders as $order) {
                $product = OrderProductDetails::find($order->order_product_id);

                if (!$product) {
                    Log::warning("OrderProductDetails not found for order_id: {$order->id}");
                    continue;
                }

                $product_link = Product::find($product->product_id ?? $product->id);

                if (!$product_link) {
                    Log::warning("Product not found for order_product_id: {$product->id}");
                    continue;
                }

                $productUrl = url('/api/singal-product-details/' . $product_link->slug);

                $imageString = $product->images ?? '';
                $imageArray = explode(',', $imageString);



                $orderList[] = [
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_variation' => $product->product_variation,
                    'product_name' => $product_link->name ?? null,
                    'image' => $imageArray,
                    'price' => $product_link->new_price ?? null,
                    'order_date' => $order->created_at->format('d F Y'),
                    'product_link' => $productUrl,
                    'checkout_type' => $order->checkout_type,
                    'status' => $order->status
                ];
            }

            $customerData[] = [
                'fullName' => $customerDetails->firstname . ' ' . $customerDetails->lastname,
                'email' => $customerDetails->email,
                'phone_number' => $customerDetails->phone_number,
                'shipping_address' => $customerDetails->address . ' ' . $customerDetails->state . ' ' . $customerDetails->city . ' ' . $customerDetails->pincode
            ];

            return ApiResponse::success(['customerData' => $customerData, 'orders' => $orderList], 'Order Fetched Successfully');
        } catch (\Throwable $e) {
            Log::error('Error in customerOrders(): ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return ApiResponse::error('Something went wrong while fetching orders.');
        }
    }

    public function shippingAddress(Request $request)
    {
        try {
            $customer = auth()->user();

            $request->validate([
                'address' => 'required|string|max:255',
                'state' => 'required|string|max:100',
                'city' => 'required|string|max:100',
                'pincode' => 'required|digits_between:4,10',
            ]);

            $customerDetails = CustomerDetails::find($customer->id);

            if (!$customerDetails) {

                return ApiResponse::error('Customer record not found.');
            }

            // ✅ Update address in customer_details
            $customerDetails->update([
                'address' => $request->address,
                'state' => $request->state,
                'city' => $request->city,
                'pincode' => $request->pincode,
            ]);

            return ApiResponse::success([
                'firstname' => $customerDetails->firstname,
                'lastname' => $customerDetails->lastname,
                'address' => $customerDetails->address,
                'state' => $customerDetails->state,
                'city' => $customerDetails->city,
                'pincode' => $customerDetails->pincode,
            ], 'Shipping address updated successfully.');
        } catch (\Throwable $e) {
            Log::error('Error in shippingAddress(): ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ApiResponse::error('Something went wrong while processing the address.');
        }
    }

    public function accountDetails(Request $request)
    {
        try {
            $customer = auth()->user();

            // Validate request
            $request->validate([
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
            ]);

            $customerDetails = CustomerDetails::find($customer->id);

            if (!$customerDetails) {
                return ApiResponse::error('Customer record not found.');
            }

            $updateData = [
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
            ];

            $customerDetails->update($updateData);

            return ApiResponse::success([
                'firstname' => $customerDetails->firstname,
                'lastname' => $customerDetails->lastname,
            ], 'Account details updated successfully.');
        } catch (\Throwable $e) {
            Log::error('Error in accountDetails(): ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ApiResponse::error('Something went wrong while processing account details.');
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'old_password' => 'required',
                'new_password' => 'required|min:6',
                'confirm_password' => 'required|same:new_password'
            ]);

            $customer = auth()->user();

            $storeCustomer = StoreCustomersDetails::where('customer_id', $customer->id)->first();

            if (!Hash::check($request->old_password, $storeCustomer->password)) {
                return ApiResponse::error('Your current password is incorrect.');
            }
            $storeCustomer->password = Hash::make($request->new_password);
            $storeCustomer->save();

            return ApiResponse::success('Password has been successfully updated.');
        } catch (\Throwable $e) {
            Log::error('Error in resetPassword(): ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ApiResponse::error('Something went wrong while resetting the password.');
        }
    }

    public function addToWishlist(Request $request)
    {
        try {
            $customer = auth()->user();
            $customerDetails = CustomerDetails::where('id', $customer->id)->first();

            $request->validate([
                'product_id' => 'required|integer',
                'wholesaler_id' => 'nullable|integer',
                'retailer_id' => 'nullable|integer',
            ]);

            $productId = $request->product_id;
            $wholesalerId = $request->wholesaler_id;
            $retailerId = $request->retailer_id;

            // Ensure only one source is provided
            if ((!$wholesalerId && !$retailerId) || ($wholesalerId && $retailerId)) {
                return ApiResponse::error('Provide either wholesaler_id or retailer_id, but not both.');
            }

            // Validate product source
            if ($wholesalerId) {
                $exists = Product::where('id', $productId)
                    ->where('wholesaler_id', $wholesalerId)
                    ->exists();

                if (!$exists) {
                    return ApiResponse::error('Invalid wholesaler product.');
                }
            }

            if ($retailerId) {
                $exists = RetailerCloneProduct::where('id', $productId)
                    ->where('retailer_id', $retailerId)
                    ->exists();

                if (!$exists) {
                    return ApiResponse::error('Invalid retailer product.');
                }
            }

            // Prevent duplicate
            $alreadyExists = CustomerCart::where('customer_id', $customerDetails->id)
                ->where(function ($query) use ($productId, $wholesalerId, $retailerId) {
                    if ($wholesalerId) {
                        $query->where('product_id', $productId);
                    } else {
                        $query->where('retailer_product_id', $productId);
                    }
                })
                ->where('type', 'wishlist')
                ->where('status', 'active')
                ->exists();

            if ($alreadyExists) {
                return ApiResponse::success('Product is already in your wishlist.');
            }

            // Create record with correct field
            $wishlistData = [
                'customer_id' => $customerDetails->id,
                'type' => 'wishlist',
                'status' => 'active',
            ];

            if ($wholesalerId) {
                $wishlistData['product_id'] = $productId;
            } else {
                $wishlistData['retailer_product_id'] = $productId;
            }

            $wishlist = CustomerCart::create($wishlistData);

            return ApiResponse::success(['wishlist_id' => $wishlist->id], 'Product added to wishlist successfully.');
        } catch (\Throwable $e) {

            Log::error('Error in addToWishlist(): ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ApiResponse::error('Something went wrong.');
        }
    }

    public function wishlist(Request $request)
    {
        try {
            $customer = auth()->user();
            $customerDetails = CustomerDetails::where('id', $customer->id)->first();

            $wishlistItems = CustomerCart::where('customer_id', $customerDetails->id)
                ->where('type', 'wishlist')
                ->where('status', 'active')
                ->get();

            if ($wishlistItems->isEmpty()) {
                return ApiResponse::success('Your wishlist is empty.');
            }

            $wishList = [];

            foreach ($wishlistItems as $item) {
                $product = null;
                $productType = null;
                $selectedVariant = null;
                $finalPrice = null;

                // Check for retailer product first
                if ($item->retailer_product_id) {
                    $retailerProduct = RetailerCloneProduct::with('productVariations')->find($item->retailer_product_id);
                    if ($retailerProduct) {
                        $product = $retailerProduct;
                        $productType = 'retailer';
                    }
                }

                // If not a retailer, check wholesaler product using product_id
                if (!$product && $item->product_id) {
                    $wholesalerProduct = Product::with('productVariations')->find($item->product_id);
                    if ($wholesalerProduct) {
                        $product = $wholesalerProduct;
                        $productType = 'wholesaler';
                    }
                }

                // Handle selected variant
                if ($item->product_variations_id) {
                    $selectedVariant = $product->productVariations
                        ->where('id', $item->product_variations_id)
                        ->first();

                    // If variant has margin logic, calculate final_price accordingly
                    if ($selectedVariant) {
                        $finalPrice = $selectedVariant->final_price ?? $selectedVariant->price ?? null;
                    }
                }

                if (!$finalPrice) {
                    $finalPrice = $product->final_price ?? $product->new_price ?? null;
                }

                if (!$product) {
                    Log::warning("Product not found for wishlist item ID: {$item->id}");
                    continue;
                }


                $wishList[] = [
                    'wishlist_id' => $item->id,
                    'product_id' => $productType === 'wholesaler' ? $product->id : null,
                    'retailer_product_id' => $productType === 'retailer' ? $product->id : null,
                    'product_name' => $product->name ?? "",
                    'product_image' => explode(',', $product->images),
                    'price' => $product->new_price ?? "",
                    'product_link' => url('/api/singal-product-details/' . $product->slug),
                    'added_on' => \Carbon\Carbon::parse($item->created_at)->diffForHumans(),

                    'selected_variant' => $selectedVariant ? [
                        'id' => $selectedVariant->id,
                        'product_variation' => $selectedVariant->product_variation,
                        'price' => $selectedVariant->price,
                        'stock' => $selectedVariant->stock,
                        'final_price' => $finalPrice,
                    ] : null,

                    'all_variants' => $product->productVariations->map(function ($variant) {
                        return [
                            'id' => $variant->id,
                            'product_id' => $variant->product_id,
                            'product_variation' => $variant->product_variation,
                            'variation_type' => $variant->variation_type,
                            'old_price' =>  $variant->old_price,
                            'price' => $variant->price,
                            'stock' => $variant->stock,
                            'final_price' => $variant->final_price ?? $variant->price,
                        ];
                    })->values(),
                ];
            }

            return ApiResponse::success(['wishlist' => $wishList], 'WishList Fetched Succesfully');
        } catch (\Throwable $e) {
            Log::error('Error in wishlist(): ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return ApiResponse::error('Something went wrong while fetching your wishlist.');
        }
    }

    public function removeToWishlist(Request $request)
    {

        try {
            $customer = auth()->user();
            $customerDetails = CustomerDetails::find($customer->id);

            $request->validate([
                'product_id' => 'nullable|integer',
                'retailer_product_id' => 'nullable|integer',
            ], [
                'product_id.integer' => 'Product ID must be an integer.',
                'retailer_product_id.integer' => 'Retailer Product ID must be an integer.',
            ]);

            $productId = $request->product_id;
            $retailerProductId = $request->retailer_product_id;

            // Ensure only one source is provided
            if ((!$productId && !$retailerProductId) || ($productId && $retailerProductId)) {

                return ApiResponse::error('Provide either product_id or retailer_product_id, but not both.');
            }

            // Build base query
            $wishlistQuery = CustomerCart::where('customer_id', $customerDetails->id)
                ->where('type', 'wishlist')
                ->where('status', 'active');

            // Add correct condition based on product type
            if ($productId) {
                $exists = Product::where('id', $productId)
                    ->exists();
                if (!$exists) {
                    return ApiResponse::error('Invalid wholesaler product.');
                }

                $wishlistQuery->where('product_id', $productId);
            } elseif ($retailerProductId) {
                $exists = RetailerCloneProduct::where('id', $retailerProductId)
                    ->exists();

                if (!$exists) {
                    return ApiResponse::error('Invalid retailer product.');
                }

                $wishlistQuery->where('retailer_product_id', $retailerProductId);
            }

            // Get wishlist item
            $wishlistItem = $wishlistQuery->first();

            if (!$wishlistItem) {
                return ApiResponse::error('Wishlist item not found.');
            }

            // Soft delete (set status to inactive)
            $wishlistItem->update(['status' => 'inactive']);

            return ApiResponse::success('Wishlist item removed successfully.');
        } catch (\Throwable $e) {
            Log::error('Error in removeToWishlist(): ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return ApiResponse::error('Something went wrong while removing the wishlist item.');
        }
    }



    public function cart(Request $request)
    {
        try {
            $customer = auth()->user();

            $cartItems = CustomerCart::where('customer_id', $customer->id)
                ->where('type', 'cart')
                ->where('status', 'active')
                ->get();

            if ($cartItems->isEmpty()) {
                return ApiResponse::success('Your cart is empty.');
            }

            $cart = [];

            foreach ($cartItems as $item) {
                $product = null;
                $productType = null;
                $selectedVariant = null;
                $finalPrice = null;

                // Retailer product
                if ($item->retailer_product_id) {
                    $product = RetailerCloneProduct::with('productVariations')->find($item->retailer_product_id);
                    $productType = 'retailer';
                }

                // Wholesaler product
                if (!$product && $item->product_id) {
                    $product = Product::with('productVariations')->find($item->product_id);
                    $productType = 'wholesaler';
                }

                if (!$product) {
                    Log::warning("Product not found for cart item ID: {$item->id}");
                    continue;
                }

                // Handle selected variant
                if ($item->product_variations_id) {
                    $selectedVariant = $product->productVariations
                        ->where('id', $item->product_variations_id)
                        ->first();

                    // If variant has margin logic, calculate final_price accordingly
                    if ($selectedVariant) {
                        $finalPrice = $selectedVariant->final_price ?? $selectedVariant->price ?? null;
                    }
                }

                if (!$finalPrice) {
                    $finalPrice = $product->final_price ?? $product->new_price ?? null;
                }

                $cart[] = [
                    'cart_id' => $item->id,
                    'product_id' => $productType === 'wholesaler' ? $product->id : null,
                    'retailer_product_id' => $productType === 'retailer' ? $product->id : null,
                    'product_name' => $product->name ?? null,
                    'quantity' => $item->quantity,
                    'final_price' => $finalPrice,
                    'retailer_id' => $product->retailer_id ?? null,
                    'product_link' => url('/api/singal-product-details/' . $product->slug),
                    'selected_variant' => $selectedVariant ? [
                        'id' => $selectedVariant->id,
                        'product_variation' => $selectedVariant->product_variation,
                        'price' => $selectedVariant->price,
                        'stock' => $selectedVariant->stock,
                        'final_price' => $finalPrice,
                    ] : null,

                ];
            }

            return ApiResponse::success(['cart' => $cart], 'Cart Fetched Successfully');
        } catch (\Throwable $e) {
            Log::error('Error in cart(): ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return ApiResponse::error('Something went wrong while fetching your cart.');
        }
    }


    public function removeToCart(Request $request)
    {
        try {
            $customer = auth()->user();
            $customerDetails = CustomerDetails::find($customer->id);

            // Validate input
            $request->validate([
                'product_id' => 'nullable|integer',
                'retailer_product_id' => 'nullable|integer',
            ]);

            $productId = $request->product_id;
            $retailerProductId = $request->retailer_product_id;

            // Ensure only one product source is selected
            if ((!$productId && !$retailerProductId) || ($productId && $retailerProductId)) {
                return ApiResponse::error('Provide either wholesaler_product_id or retailer_product_id, but not both.');
            }

            // Validate product existence
            if ($productId) {
                $exists = Product::where('id', $productId)
                    ->exists();

                if (!$exists) {
                    return ApiResponse::error('Invalid wholesaler product.');
                }
            } elseif ($retailerProductId) {
                $exists = RetailerCloneProduct::where('id', $retailerProductId)
                    ->exists();

                if (!$exists) {
                    return ApiResponse::error('Invalid retailer product.');
                }
            }

            // Build cart query
            $cartQuery = CustomerCart::where('customer_id', $customerDetails->id)
                ->where('type', 'cart')
                ->where('status', 'active');

            if ($productId) {
                $cartQuery->where('product_id', $productId);
            } else {
                $cartQuery->where('retailer_product_id', $retailerProductId);
            }

            $cartItem = $cartQuery->first();

            if (!$cartItem) {
                return ApiResponse::error('Cart item not found.');
            }

            // Soft delete the cart item
            $cartItem->update(['status' => 'inactive']);

            return ApiResponse::success('Cart item removed successfully.');
        } catch (\Throwable $e) {
            Log::error('Error in removeToCart(): ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return ApiResponse::error('Something went wrong while removing the cart item.');
        }
    }

    public function addToCart(Request $request)
    {

        try {
            $customer = auth()->user();
            $customerDetails = CustomerDetails::find($customer->id);
            $rawItems = [];

            // Detect single vs array input
            if ($request->has('cart_items') && is_array($request->cart_items)) {
                // Validate array structure
                $request->validate([
                    'cart_items' => 'required|array|min:1',
                    'cart_items.*.product_id' => 'required|integer',
                    'cart_items.*.id' => 'nullable|integer',
                    'cart_items.*.wholesaler_id' => 'nullable|integer',
                    'cart_items.*.retailer_id' => 'nullable|integer',
                    'cart_items.*.quantity' => 'nullable|integer|min:1',
                ]);
                $rawItems = $request->cart_items;
            } else {
                // Single item mode
                $request->validate([
                    'product_id' => 'required|integer',
                    'wholesaler_id' => 'nullable|integer',
                    'id' => 'nullable|integer',
                    'retailer_id' => 'nullable|integer',
                    'quantity' => 'nullable|integer|min:1',
                ]);
                $rawItems[] = [
                    'product_id' => $request->product_id,
                    'id' => $request->id,
                    'wholesaler_id' => $request->wholesaler_id,
                    'retailer_id' => $request->retailer_id,
                    'quantity' => $request->quantity ?? 1,
                ];
            }


            // Merge duplicate items before processing
            $mergedItems = $this->mergeDuplicateCartItems($rawItems);

            $results = [];

            foreach ($mergedItems as $item) {
                $result = $this->processCartItem($item, $customerDetails);
                $results[] = $result;
            }

            return ApiResponse::success(['results' => $results], 'Add To Cart Successfully');
        } catch (\Throwable $e) {
            Log::error('Error in addToCart(): ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ApiResponse::error('Something went wrong while adding to cart.');
        }
    }

    private function processCartItem(array $item, $customerDetails)
    {
        $productId = $item['product_id'];
        $variantId = $item['id'] ?? null;
        $wholesalerId = $item['wholesaler_id'] ?? null;
        $retailerId = $item['retailer_id'] ?? null;
        $quantity = $item['quantity'] ?? 1;

        if ((!$wholesalerId && !$retailerId) || ($wholesalerId && $retailerId)) {
            return [
                'product_id' => $productId,
                'status' => 'failed',
                'message' => 'Provide either wholesaler_id or retailer_id, not both.',
            ];
        }

        $isValid = false;


        if ($wholesalerId) {
            $isValid = Product::where('id', $productId)
                ->where('wholesaler_id', $wholesalerId)
                ->exists();

            if ($isValid && $variantId) {
                $isValid = ProductVariation::where('id', $variantId)
                    ->where('product_id', $productId)
                    ->exists();
            }
        } elseif ($retailerId) {
            $isValid = RetailerCloneProduct::where('id', $productId)
                ->where('retailer_id', $retailerId)
                ->exists();

            if ($isValid && $variantId) {
                $isValid = ProductVariation::where('id', $variantId)
                    ->where('product_id', $productId)
                    ->exists();
            }
        }

        if (!$isValid) {
            return [
                'product_id' => $productId,
                'status' => 'failed',
                'message' => 'Invalid product or variant.',
            ];
        }


        // 🔁 Check if already in cart
        $existingCart = CustomerCart::where('customer_id', $customerDetails->id)
            ->where('type', 'cart')
            ->where('status', 'active')
            ->when(!empty($wholesalerId), function ($q) use ($productId) {
                $q->where('product_id', $productId);
            })
            ->when(!empty($retailerId), function ($q) use ($productId) {
                $q->where('retailer_product_id', $productId);
            })
            ->when(!empty($variantId), function ($q) use ($variantId) {
                $q->where('product_variations_id', $variantId);
            })
            ->first();

        if ($existingCart) {
            $existingCart->quantity += $quantity;
            $existingCart->save();

            return [
                'product_id' => $productId,
                'product_variations_id' => $variantId,
                'quantity' => $existingCart->quantity,
                'status' => 'updated',
                'message' => 'Cart quantity updated.',
                'wishlist_id' => $existingCart->id,
            ];
        }

        // ♻️ Reactivate inactive cart if exists
        $inactiveCart = CustomerCart::where('customer_id', $customerDetails->id)
            ->when($wholesalerId, fn($q) => $q->where('product_id', $productId))
            ->when($retailerId, fn($q) => $q->where('retailer_product_id', $productId))
            ->when($variantId, fn($q) => $q->where('product_variations_id', $variantId))
            ->where('type', 'cart')
            ->where('status', 'inactive')
            ->first();

        if ($inactiveCart) {
            $inactiveCart->status = 'active';
            $inactiveCart->quantity = $quantity;
            $inactiveCart->save();

            return [
                'product_id' => $productId,
                'quantity' => $inactiveCart->quantity,
                'status' => 'reactivated',
                'message' => 'Add To Cart Successfully.',
                'wishlist_id' => $inactiveCart->id,
            ];
        }

        // ➕ Create new cart record
        $cartData = [
            'customer_id' => $customerDetails->id,
            'quantity' => $quantity,
            'type' => 'cart',
            'status' => 'active',
            'product_variations_id' => $variantId,
        ];

        if ($wholesalerId) {
            $cartData['product_id'] = $productId;
        } else {
            $cartData['retailer_product_id'] = $productId;
        }

        $cart = CustomerCart::create($cartData);

        return [
            'product_id' => $productId,
            'quantity' => $cart->quantity,
            'product_variations_id' => $variantId,
            'status' => 'success',
            'message' => 'Product added to cart.',
            'wishlist_id' => $cart->id,
        ];
    }

    private function mergeDuplicateCartItems(array $items): array
    {
        $merged = [];

        foreach ($items as $item) {
            $productId = $item['product_id'];
            $id = $item['id'] ?? null;
            $wholesalerId = $item['wholesaler_id'] ?? null;
            $retailerId = $item['retailer_id'] ?? null;
            $quantity = $item['quantity'] ?? 1;

            // Unique key includes variant ID
            $key = $productId . '_' . ($id ?? 'noid') . '_' . ($wholesalerId ? 'w_' . $wholesalerId : 'r_' . $retailerId);

            if (isset($merged[$key])) {
                $merged[$key]['quantity'] += $quantity;
            } else {
                $merged[$key] = [
                    'product_id' => $productId,
                    'id' => $id,
                    'wholesaler_id' => $wholesalerId,
                    'retailer_id' => $retailerId,
                    'quantity' => $quantity,
                ];
            }
        }

        return array_values($merged);
    }


    public function contactUs(Request $request)
    {
        try {
            $retailer = RetailerWebManagement::where('product_listing_key', $request->user_token)
                ->where('is_active', 1)
                ->first();

            if (!$retailer) {
                return ApiResponse::error('Invalid user token.');
            }

            // Step 2: Validate input
            $validator = Validator::make($request->all(), [
                'firstname'     => 'required|string|max:100',
                'lastname'      => 'nullable|string|max:100',
                'email'         => 'required|email|max:255',
                'phone_number'  => 'nullable|string|max:10',
                'subject'       => 'required|string|max:255',
                'message'       => 'required|string',
                'subscribe'     => 'nullable|boolean',
            ], [
                'firstname.required'   => 'Please enter your first name.',
                'firstname.max'        => 'First name cannot exceed 100 characters.',
                'lastname.max'         => 'Last name cannot exceed 100 characters.',
                'email.required'       => 'Email address is required.',
                'email.email'          => 'Please provide a valid email address.',
                'email.max'            => 'Email cannot exceed 255 characters.',
                'phone_number.max'     => 'Phone number cannot exceed 10 characters.',
                'subject.required'     => 'Subject is required.',
                'subject.max'          => 'Subject cannot exceed 255 characters.',
                'message.required'     => 'Please enter your message.',
                'subscribe.boolean'    => 'Invalid value for subscribe checkbox.',
            ]);

            if ($validator->fails()) {
                return ApiResponse::error(
                    'Validation failed',
                    $validator->errors()->toArray()
                );
            }

            // Step 3: Create the enquiry
            $enquiry = RetailerWebsiteEnquiry::create([
                'retailer_id'   => $retailer->retailer_id,
                'firstname'     => $request->firstname,
                'lastname'      => $request->lastname,
                'email'         => $request->email,
                'phone_number'  => $request->phone_number,
                'subject'       => $request->subject,
                'message'       => $request->message,
                'subscribe'     => $request->has('subscribe') ? true : false,
            ]);

            // Step 4: Return success response
            return ApiResponse::success(
                ['data' => $enquiry],
                'Your enquiry has been submitted successfully.'
            );
        } catch (\Exception $e) {
            // Optional: Log the error
            \Log::error('Retailer contact form error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return ApiResponse::error('Something went wrong. Please try again later.');
        }
    }
}
