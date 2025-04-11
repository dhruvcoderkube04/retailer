<?php

namespace App\Http\Controllers\API\Retailer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CustomerDetails;
use App\Models\CustomerOrders;
use App\Models\RetailerProducts;
use App\Models\RetailerCloneProduct;
use App\Models\RetailerWebManagement;
use App\Models\UserDetail;
use App\Models\Otp;
use App\Models\Product;
use App\Models\User;
use App\Models\RetailerCategory;
use App\Models\SubCategory;
use Dotenv\Util\Regex;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class RetailerProductController extends Controller
{
    public function storeInfo(Request $request)
    {
        try {
            $apiKey = $request->header('API-KEY');
            if (!$apiKey) {
                return response()->json(['error' => 'API Key is required.'], 401);
            }

            $storeinfo = RetailerWebManagement::select('store_name','logo','brand_name','store_time','mobile_no','email','address','facebook_url',
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
            'apple_store_id',
            'play_store_url',
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
            )->where('product_listing_key', $apiKey)->first();
            if (!$storeinfo) {
                return response()->json(['error' => 'Unauthorized: Invalid API Key.'], 403);
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
                $subNames = $subCategories
                    ->where('category_id', $category->id)
                    ->pluck('sub_category_name')
                    ->unique()
                    ->values()
                    ->toArray();

                $categoryList[$category->category_name] = $subNames;
            }

            return response()->json([
                'success' => true,
                'storeinfo' => $storeinfo,
                'category_list' => $categoryList
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching retailer company info: ' . $e->getMessage());
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    public function getProducts(Request $request)
    {

        try {
            // Step 1: Validate API Key
            $apiKey = $request->header('API-KEY');

            if (!$apiKey) {
                return response()->json(['error' => 'API Key is required.'], 401);
            }

            // Step 2: Validate Retailer
            $retailer = RetailerWebManagement::where('product_listing_key', $apiKey)->first();

            if (!$retailer) {
                return response()->json(['error' => 'Unauthorized: Invalid API Key.'], 403);
            }


            $retailerId = $retailer->retailer_id;

            $retailerUser = User::find($retailerId);
            if (!$retailerUser) {
                return response()->json(['error' => 'Retailer user not found.'], 404);
            }

            $retailerProducts = collect(); // default empty collection

            if ($retailerUser->is_all_wholesaler_visible == 1) {
                $retailerProducts = RetailerProducts::with(['wholesaler.products'])
                    ->where('retailer_id', $retailerId)
                    ->get();
            }


            $retailerCloneProducts = RetailerCloneProduct::where('retailer_id', $retailer->retailer_id)->get();

            // Step 4: Combine both safely
            $allProducts = collect($retailerProducts)->concat($retailerCloneProducts);

            // Step 5: If product_id filter is present
            if ($request->has('product_id')) {
                $productId = $request->product_id;
                $allProducts = $allProducts->filter(fn($item) => $item->id == $productId);
            }

            // Step 6: Normalize all products to a consistent array
            $products = $allProducts->flatMap(function ($item) {
                if ($item instanceof RetailerProducts) {
                    if (!$item->wholesaler || !$item->wholesaler->products) {
                        return []; // skip if no wholesaler or products
                    }

                    return $item->wholesaler->products->map(function ($product) use ($item) {
                        return $this->formatProductFromRetailerProduct($product, $item);
                    });
                } else {
                    return [$this->formatProductFromClone($item)];
                }
            })->values();

            // Step 7: Extract unique categories
            $categoryIds = $products->pluck('category_id')->filter()->unique();
            $categories = Category::whereIn('id', $categoryIds)->pluck('category_name')->toArray();

            // Step 8: Handle single product response
            if ($request->has('product_id')) {
                $single = $products->first();
                if (!$single) {
                    return response()->json(['error' => 'Product not found.'], 404);
                }

                return response()->json([
                    'success' => true,
                    'product' => $single,
                ]);
            }

            // Step 9: Paginate the full product list
            $perPage = 8;
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $productsCollection = new Collection($products);
            $currentPageItems = $productsCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();
            $paginatedProducts = new LengthAwarePaginator($currentPageItems, $productsCollection->count(), $perPage);
            $paginatedProducts->setPath(url()->current());

            return response()->json([
                'success'    => true,
                'products'   => $paginatedProducts,
                'categories' => $categories,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getRetailerProducts: ' . $e->getMessage(), [
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    public function getSingalProductDetails(Request $request)
    {

        try {
            $apiKey = $request->header('API-KEY');
            if (!$apiKey) {
                return response()->json(['error' => 'API Key is required.'], 401);
            }

            $retailer = RetailerWebManagement::where('product_listing_key', $apiKey)->first();
            if (!$retailer) {
                return response()->json(['error' => 'Unauthorized: Invalid API Key.'], 403);
            }


            $w_productId = $request->w_product_id;   // for wholesaler product id
            $r_productId = $request->r_product_id; // for retailer product id

            if (!$w_productId && !$r_productId) {
                return response()->json(['error' => 'Either w_product_id or r_product_id is required.'], 422);
            }

            if ($w_productId) {
                $retailerProduct = RetailerProducts::with(['wholesaler.products'])->where('retailer_id', $retailer->retailer_id)->first();

                // if (!$retailerProduct || !$retailerProduct->wholesaler) {
                //     return response()->json(['error' => 'Retailer product not found or missing wholesaler.'], 404);
                // }

                $product = $retailerProduct->wholesaler->products->where('id', $w_productId)->first();
                if (!$product) {
                    return response()->json(['error' => 'Product not found.'], 404);
                }

                $formatted = $this->formatProductFromRetailerProduct($product, $retailerProduct);
            } else {
                $cloneProduct = RetailerCloneProduct::where('retailer_id', $retailer->retailer_id)
                    ->where('id', $r_productId)
                    ->first();

                if (!$cloneProduct) {
                    return response()->json(['error' => 'Clone product not found.'], 404);
                }

                $formatted = $this->formatProductFromClone($cloneProduct);
            }

            return response()->json([
                'success' => true,
                'product' => $formatted
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Get product detail error: ' . $e->getMessage());
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
            'final_amount' => 'required|numeric|min:0',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'nullable',
            'products.*.retailer_product_id' => 'nullable',
            'products.*.wholesaler_id' => 'nullable',
            'products.*.retailer_id' => 'nullable',
            'products.*.quantity' => 'required|integer|min:1'
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

        $retailer = RetailerWebManagement::where('product_listing_key', $apiKey)->first();
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

            $orderID = 'ORD' . now()->timestamp . rand(10000, 99999);

            $orderItems = [];

            foreach ($request->products as $product) {
                $wholesalerId = $product['wholesaler_id'] ?? null;
                $retailerId = $product['retailer_id'] ?? $retailer->retailer_id;
                $productId = !empty($product['product_id']) ? $product['product_id'] : null;
                $cloneId = !empty($product['retailer_product_id']) ? $product['retailer_product_id'] : null;
                $quantity = $product['quantity'];

                if (!$productId && !$cloneId) {
                    throw new \Exception('Either product_id or retailer_product_id must be provided.');
                }

                if ($productId) {
                    // Ensure the product exists
                    $productModel = Product::find($productId);
                    if (!$productModel) {
                        throw new \Exception("Product ID $productId not found.");
                    }
                    if ($productModel->quantity < $quantity) {
                        throw new \Exception("Insufficient stock for Product ID $productId");
                    }
                    $productModel->quantity -= $quantity;
                    $productModel->save();
                } else {
                    $retailerProduct = RetailerCloneProduct::where('retailer_id', $retailerId)
                        ->where('id', $cloneId)
                        ->first();
                    if (!$retailerProduct) {
                        throw new \Exception("Retailer Product ID $cloneId not found.");
                    }
                    if ($retailerProduct->quantity < $quantity) {
                        throw new \Exception("Insufficient stock for Retailer Product ID $cloneId");
                    }
                    $retailerProduct->quantity -= $quantity;
                    $retailerProduct->save();
                }

                $orderItems[] = [
                    'order_id' => $orderID,
                    'customer_id' => $customerDetail->id,
                    'product_id' => $productId,
                    'retailer_clone_product_id' => $cloneId,
                    'retailer_id' => $retailerId,
                    'wholesaler_id' => $wholesalerId,
                    'quantity' => $quantity,
                    'final_amount' => $request->final_amount,
                    'payment_method' => $request->payment_method,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            CustomerOrders::insert($orderItems);

            DB::commit();

            return response()->json([
                'success' => true,
                'order_id' => $orderID,
                'message' => 'Your order has been placed successfully!'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout Error:', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong!',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function formatProductFromRetailerProduct($product, $retailerProduct)
    {
        $finalPrice = $product->new_price + $retailerProduct->margin;

        return [
            'id'              => $product->id,
            'sku'             => $product->sku,
            'name'            => $product->name,
            'slug'            => $product->slug,
            'description'     => $product->description,
            'category_id'     => $product->category_id,
            'wholesaler_id'   => $product->wholesaler_id,
            'new_price'       => $product->new_price,
            'final_price'     => $finalPrice,
            'quantity'        => $product->quantity,
            'product_images'  => $product->images ?? null,
            'product_video'   => $product->videos ?? null,
            'product_url'     => $product->url,
            'color'           => $product->color ?? null,
            'size'            => $product->size,
            'specifications'  => $product->specifications,
            'retailer_id'     => $product->retailer_id ?? null,
        ];
    }

    private function formatProductFromClone($cloneProduct)
    {
        $finalPrice = $cloneProduct->new_price + $cloneProduct->margin;

        return [
            'id'              => $cloneProduct->id,
            'sku'             => $cloneProduct->sku,
            'name'            => $cloneProduct->name,
            'slug'            => $cloneProduct->slug,
            'description'     => $cloneProduct->description,
            // If RetailerCloneProduct does not store category, this can be null or a default value.
            'category_id'     => $cloneProduct->category_id ?? null,
            'wholesaler_id'   => null, // No wholesaler relation here.
            'new_price'       => $cloneProduct->new_price,
            'final_price'     => $finalPrice,
            'quantity'        => $cloneProduct->quantity,
            'product_images'  => $cloneProduct->images ?? null,
            'product_video'   => $cloneProduct->videos ?? null,
            'product_url'     => $cloneProduct->url,
            'color'           => $cloneProduct->color ?? null,
            'size'            => $cloneProduct->size,
            'specifications'  => $cloneProduct->specifications,
            'retailer_id'     => $cloneProduct->retailer_id ?? null,
        ];
    }

    // public function sendOtp(Request $request)
    // {
    //     $request->validate([
    //         'phone_number' => 'required|numeric|digits:10'
    //     ]);

    //     $otpCode = rand(100000, 999999);

    //     Otp::updateOrCreate(
    //         ['phone_number' => $request->phone_number],
    //         ['otp' => $otpCode, 'verified' => false]
    //     );

    //     // Send OTP using SMS service (example implementation)
    //     // SmsService::sendOtp($request->phone_number, $otpCode);

    //     return response()->json(['message' => 'OTP sent successfully!', 'otp' => $otpCode]);
    // }

    /**
     * Verify OTP
     */
    // public function verifyOtp(Request $request)
    // {
    //     $request->validate([
    //         'phone_number' => 'required|numeric|digits:10',
    //         'otp' => 'required|numeric|digits:6'
    //     ]);

    //     $otpRecord = Otp::where('phone_number', $request->phone_number)
    //                     ->where('otp', $request->otp)
    //                     ->first();

    //     if (!$otpRecord) {
    //         return response()->json(['error' => 'Invalid OTP!'], 400);
    //     }

    //     $otpRecord->update(['verified' => true]);

    //     return response()->json(['message' => 'OTP verified successfully!']);
    // }


    // public function checkout(Request $request)
    // {
    //     // Validate input data
    //     $validator = Validator::make($request->all(), [
    //         'firstname' => 'required|max:30',
    //         'lastname' => 'required|max:30',
    //         'phone_number' => 'required|numeric|digits:10',
    //         'email' => 'required|email',
    //         'address' => 'required|max:250',
    //         'payment_method' => 'required|in:cod,upi',
    //         'final_amount' => 'required|numeric|min:0',
    //         'products' => 'required|array|min:1',
    //         'products.*.product_id' => 'nullable',
    //         'products.*.retailer_clone_product_id' => 'nullable',
    //         'products.*.wholesaler_id' => 'nullable',
    //         'products.*.retailer_id' => 'required',
    //         'products.*.quantity' => 'required|integer|min:1'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'message' => 'Validation failed',
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     // Validate API key
    //     $apiKey = $request->header('API-KEY');
    //     if (!$apiKey) {
    //         return response()->json(['error' => 'API Key is required.'], 401);
    //     }

    //     // Validate retailer based on API key
    //     $retailer = RetailerWebManagement::where('product_listing_key', $apiKey)->first();
    //     if (!$retailer) {
    //         return response()->json(['error' => 'Unauthorized: Invalid API Key.'], 403);
    //     }

    //     // $otpRecord = Otp::where('phone_number', $request->phone_number)
    //     // ->where('verified', true)
    //     // ->first();


    //     // if (!$otpRecord) {
    //     // return response()->json(['error' => 'OTP verification required!'], 403);
    //     // }

    //     DB::beginTransaction();
    //     try {
    //         // Store customer details
    //         $customerDetail = CustomerDetails::create([
    //             'firstname' => $request->firstname,
    //             'lastname' => $request->lastname,
    //             'phone_number' => $request->phone_number,
    //             'email' => $request->email ?? null,
    //             'address' => $request->address,
    //             'state' => $request->state,
    //             'city' => $request->city,
    //             'pincode' => $request->pincode
    //         ]);

    //         // Generate a unique order ID
    //         $orderID = 'ORD' . now()->timestamp . rand(10000, 99999);

    //         // Prepare order items
    //         $orderItems = collect($request->products)->map(function ($product) use ($orderID, $customerDetail, $retailer) {

    //             $wholesalerId = $product['wholesaler_id'] ?? null;
    //             $retailerId = $product['retailer_id'] ?? $retailer->retailer_id;
    //             $productId = $product['product_id'] ?? null;
    //             $retailercloneproductId = $product['retailer_clone_product_id'] ?? null;

    //             return [
    //                 'order_id' => $orderID,
    //                 'customer_id' => $customerDetail->id,
    //                 'product_id' =>  $productId ?? null,
    //                 'retailer_clone_product_id' =>  !is_null($retailercloneproductId) ? $retailercloneproductId : null,
    //                 'retailer_id' => !is_null($retailerId) ? $retailerId : null,
    //                 'wholesaler_id' => !is_null($wholesalerId) ? $wholesalerId : null,
    //                 'quantity' => $product['quantity'],
    //                 'final_amount' => request()->final_amount,
    //                 'payment_method' => request()->payment_method,
    //                 'created_at' => now(),
    //                 'updated_at' => now()
    //             ];
    //         })->toArray();

    //         foreach ($request->products as $product) {
    //             $quantity = $product['quantity'];

    //             if (!empty($product['wholesaler_id'])) {
    //                 // Wholesaler product stock update
    //                 $productModel = Product::find($product['product_id']);
    //                 if ($productModel) {
    //                     if ($productModel->quantity < $quantity) {
    //                         throw new \Exception('Insufficient stock for product ID ' . $product['product_id']);
    //                     }
    //                     $productModel->quantity -= $quantity;
    //                     $productModel->save();
    //                 }
    //             } else {
    //                 // Retailer clone product stock update
    //                 $retailerProduct = RetailerCloneProduct::where('retailer_id', $product['retailer_id'])
    //                     ->where('id', $product['retailer_clone_product_id']) // or use product_id if id not available
    //                     ->first();

    //                 if ($retailerProduct) {
    //                     if ($retailerProduct->quantity < $quantity) {
    //                         throw new \Exception('Insufficient stock for retailer product ID ' . $product['retailer_clone_product_id']);
    //                     }
    //                     $retailerProduct->quantity -= $quantity;
    //                     $retailerProduct->save();
    //                 }
    //             }
    //         }

    //         // Bulk insert orders
    //         CustomerOrders::insert($orderItems);

    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'order_id' => $orderID,
    //             'message' => 'Your order has been placed successfully!'
    //         ], 200);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Checkout Error:', ['error' => $e->getMessage()]);
    //         return response()->json(['error' => 'Something went wrong! Please try again.'], 500);
    //     }
    // }

     // public function getRetailerProducts(Request $request)
    // {
    //     try {
    //         $apiKey = $request->header('API-KEY');
    //         if (!$apiKey) {
    //             return response()->json(['error' => 'API Key is required.'], 401);
    //         }

    //         $retailer = RetailerWebManagement::where('product_listing_key', $apiKey)->first();
    //         if (!$retailer) {
    //             return response()->json(['error' => 'Unauthorized: Invalid API Key.'], 403);
    //         }

    //         // Fetch retailer products with wholesaler relation
    //         $retailerProducts = RetailerProducts::with(['wholesaler.products'])
    //             ->where('retailer_id', $retailer->retailer_id)
    //             ->get();

    //         // Fetch retailer clone products
    //         $retailerCloneProducts = RetailerCloneProduct::where('retailer_id', $retailer->retailer_id)
    //             ->get();

    //         // Merge both collections
    //         $allProducts = $retailerProducts->merge($retailerCloneProducts);

    //         // Process each record differently based on its source.
    //         $products = $allProducts->flatMap(function ($item) {
    //             if ($item instanceof RetailerProducts) {
    //                 if (!$item->wholesaler) {
    //                     return [];
    //                 }
    //                 return $item->wholesaler->products->map(function ($product) use ($item) {
    //                     return $this->formatProductFromRetailerProduct($product, $item);
    //                 });
    //             } else {
    //                 return [$this->formatProductFromClone($item)];
    //             }
    //         })->values();

    //         // Extract categories separately
    //         $categoryIds = $products->pluck('category_id')->filter()->unique()->toArray();
    //         $categories = Category::whereIn('id', $categoryIds)->pluck('category_name')->toArray();

    //         // Implement pagination (8 products per page)
    //         $perPage = 8;
    //         $currentPage = LengthAwarePaginator::resolveCurrentPage();
    //         $productsCollection = new Collection($products);
    //         $currentPageItems = $productsCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();
    //         $paginatedProducts = new LengthAwarePaginator($currentPageItems, $productsCollection->count(), $perPage);
    //         $paginatedProducts->setPath(url()->current());

    //         return response()->json([
    //             'success'    => true,
    //             'products'   => $paginatedProducts,
    //             'categories' => $categories,
    //         ]);

    //     } catch (\Exception $e) {
    //         \Log::error('Error fetching retailer products: ' . $e->getMessage());
    //         return response()->json(['error' => 'An unexpected error occurred.'], 500);
    //     }
    // }



}
