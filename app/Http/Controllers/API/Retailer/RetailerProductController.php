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


            $productId = $request->product_id;
            $retailerCloneProductId = $request->retailer_clone_product_id;

            if (!$productId && !$retailerCloneProductId) {
                return response()->json(['error' => 'Either product_id or retailer_clone_product_id is required.'], 422);
            }

            if ($productId) {
                $retailerProduct = RetailerProducts::with(['wholesaler.products'])->where('retailer_id', $retailer->retailer_id)->first();
                // dd($retailerProduct);
                // if (!$retailerProduct || !$retailerProduct->wholesaler) {
                //     return response()->json(['error' => 'Retailer product not found or missing wholesaler.'], 404);
                // }

                $product = $retailerProduct->wholesaler->products->where('id', $productId)->first();
                if (!$product) {
                    return response()->json(['error' => 'Product not found.'], 404);
                }

                $formatted = $this->formatProductFromRetailerProduct($product, $retailerProduct);
            } else {
                $cloneProduct = RetailerCloneProduct::where('retailer_id', $retailer->retailer_id)
                    ->where('id', $retailerCloneProductId)
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



    public function getRetailerProducts(Request $request)
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

            // Fetch retailer products with wholesaler relation
            $retailerProducts = RetailerProducts::with(['wholesaler.products'])
                ->where('retailer_id', $retailer->retailer_id)
                ->get();

            // Fetch retailer clone products
            $retailerCloneProducts = RetailerCloneProduct::where('retailer_id', $retailer->retailer_id)
                ->get();

            // Merge both collections
            $allProducts = $retailerProducts->merge($retailerCloneProducts);

            // Process each record differently based on its source.
            $products = $allProducts->flatMap(function ($item) {
                if ($item instanceof RetailerProducts) {
                    if (!$item->wholesaler) {
                        return [];
                    }
                    return $item->wholesaler->products->map(function ($product) use ($item) {
                        return $this->formatProductFromRetailerProduct($product, $item);
                    });
                } else {
                    return [$this->formatProductFromClone($item)];
                }
            })->values();

            // Extract categories separately
            $categoryIds = $products->pluck('category_id')->filter()->unique()->toArray();
            $categories = Category::whereIn('id', $categoryIds)->pluck('category_name')->toArray();

            // Implement pagination (8 products per page)
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
            \Log::error('Error fetching retailer products: ' . $e->getMessage());
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
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


    public function getRetailerWebInfo(Request $request)
    {
        try {
            $apiKey = $request->header('API-KEY');
            if (!$apiKey) {
                return response()->json(['error' => 'API Key is required.'], 401);
            }

            $storeinfo = RetailerWebManagement::where('product_listing_key', $apiKey)->first();
            if (!$storeinfo) {
                return response()->json(['error' => 'Unauthorized: Invalid API Key.'], 403);
            }
            return response()->json([
                'success' => true,
                'storeinfo' => $storeinfo
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching retailer company info: ' . $e->getMessage());
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
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


    public function checkout(Request $request)
    {
        // Validate input data
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
            'products.*.retailer_clone_product_id' => 'nullable',
            'products.*.wholesaler_id' => 'nullable',
            'products.*.retailer_id' => 'required',
            'products.*.quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validate API key
        $apiKey = $request->header('API-KEY');
        if (!$apiKey) {
            return response()->json(['error' => 'API Key is required.'], 401);
        }

        // Validate retailer based on API key
        $retailer = RetailerWebManagement::where('product_listing_key', $apiKey)->first();
        if (!$retailer) {
            return response()->json(['error' => 'Unauthorized: Invalid API Key.'], 403);
        }

        // $otpRecord = Otp::where('phone_number', $request->phone_number)
        // ->where('verified', true)
        // ->first();


        // if (!$otpRecord) {
        // return response()->json(['error' => 'OTP verification required!'], 403);
        // }

        DB::beginTransaction();
        try {
            // Store customer details
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

            // Generate a unique order ID
            $orderID = 'ORD' . now()->timestamp . rand(10000, 99999);

            // Prepare order items
            $orderItems = collect($request->products)->map(function ($product) use ($orderID, $customerDetail, $retailer) {

                $wholesalerId = $product['wholesaler_id'] ?? null;
                $retailerId = $product['retailer_id'] ?? $retailer->retailer_id;
                $productId = $product['product_id'] ?? null;
                $retailercloneproductId = $product['retailer_clone_product_id'] ?? null;

                return [
                    'order_id' => $orderID,
                    'customer_id' => $customerDetail->id,
                    'product_id' =>  $productId ?? null,
                    'retailer_clone_product_id' =>  !is_null($retailercloneproductId) ? $retailercloneproductId : null,
                    'retailer_id' => !is_null($retailerId) ? $retailerId : null,
                    'wholesaler_id' => !is_null($wholesalerId) ? $wholesalerId : null,
                    'quantity' => $product['quantity'],
                    'final_amount' => request()->final_amount,
                    'payment_method' => request()->payment_method,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            })->toArray();

            foreach ($request->products as $product) {
                $quantity = $product['quantity'];
            
                if (!empty($product['wholesaler_id'])) {
                    // Wholesaler product stock update
                    $productModel = Product::find($product['product_id']);
                    if ($productModel) {
                        if ($productModel->quantity < $quantity) {
                            throw new \Exception('Insufficient stock for product ID ' . $product['product_id']);
                        }
                        $productModel->quantity -= $quantity;
                        $productModel->save();
                    }
                } else {
                    // Retailer clone product stock update
                    $retailerProduct = RetailerCloneProduct::where('retailer_id', $product['retailer_id'])
                        ->where('id', $product['retailer_clone_product_id']) // or use product_id if id not available
                        ->first();
            
                    if ($retailerProduct) {
                        if ($retailerProduct->quantity < $quantity) {
                            throw new \Exception('Insufficient stock for retailer product ID ' . $product['retailer_clone_product_id']);
                        }
                        $retailerProduct->quantity -= $quantity;
                        $retailerProduct->save();
                    }
                }
            }

            // Bulk insert orders
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
            return response()->json(['error' => 'Something went wrong! Please try again.'], 500);
        }
    }

}
